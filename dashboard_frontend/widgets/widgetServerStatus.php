<?php
/* Dashboard Builder.
  Copyright (C) 2017 DISIT Lab http://www.disit.org - University of Florence

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
include '../config.php';
?>

<script type='text/javascript'>
    $(document).ready(function <?= $_GET['name'] ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef, fromGisFakeId)
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
        var headerHeight = 25;
        var hostFile = "<?= $_GET['hostFile'] ?>";
        var widgetName = "<?= $_GET['name'] ?>";
        var divContainer = $("#<?= $_GET['name'] ?>_content");
        var widgetContentColor = "<?= $_GET['color'] ?>";
        var nome_wid = "<?= $_GET['name'] ?>_div";
        var linkElement = $('#<?= $_GET['name'] ?>_link_w');
        var color = '<?= $_GET['color'] ?>';
        // var fontSize = "<?= $_GET['fontSize'] ?>";
        //var fontColor = "<?= $_GET['fontColor'] ?>";
        var timeToReload = <?= $_GET['freq'] ?>;
        var embedWidget = <?= $_GET['embedWidget'] ?>;
        var embedWidgetPolicy = '<?= $_GET['embedWidgetPolicy'] ?>';
        var showTitle = "<?= $_GET['showTitle'] ?>";
        var showHeader = null;
        var getMetricDataUrl = "../widgets/getDataMetrics.php";

        var widgetProperties, styleParameters, metricType, metricName, pattern, responseMsg, threshold, thresholdEval,
                delta, deltaPerc, sizeRowsWidget, fontSize, responseCode, metricType, countdownRef, widgetTitle, metricData, widgetHeaderColor,
                widgetHeaderFontColor, widgetOriginalBorderColor, urlToCall, geoJsonServiceData, showHeader = null;

        if (((embedWidget === true) && (embedWidgetPolicy === 'auto')) || ((embedWidget === true) && (embedWidgetPolicy === 'manual') && (showTitle === "no")) || ((embedWidget === false) && (showTitle === "no") && (hostFile === "index")))
        {
            showHeader = false;
        } else
        {
            showHeader = true;
        }

        if ((metricNameFromDriver === "undefined") || (metricNameFromDriver === undefined) || (metricNameFromDriver === "null") || (metricNameFromDriver === null))
        {
            metricName = "<?= $_GET['metric'] ?>";
            widgetTitle = "<?= preg_replace($titlePatterns, $replacements, $title) ?>";
            widgetHeaderColor = "<?= $_GET['frame_color'] ?>";
            widgetHeaderFontColor = "<?= $_GET['headerFontColor'] ?>";
        } else
        {
            metricName = metricNameFromDriver;
            widgetTitleFromDriver.replace(/_/g, " ");
            widgetTitleFromDriver.replace(/\'/g, "&apos;");
            widgetTitle = widgetTitleFromDriver;
            $("#" + widgetName).css("border-color", widgetHeaderColorFromDriver);
            widgetHeaderColor = widgetHeaderColorFromDriver;
            widgetHeaderFontColor = widgetHeaderFontColorFromDriver;
        }

        $(document).off('changeMetricFromButton_' + widgetName);
        $(document).on('changeMetricFromButton_' + widgetName, function (event)
        {
            if ((event.targetWidget === widgetName) && (event.newMetricName !== "noMetricChange"))
            {
                clearInterval(countdownRef);
                $("#<?= $_GET['name'] ?>_content").hide();
<?= $_GET['name'] ?>(true, event.newMetricName, event.newTargetTitle, event.newHeaderAndBorderColor, event.newHeaderFontColor, false, null, null, null, null, null, null);
            }
        });

        $(document).off('mouseOverLastDataFromExternalContentGis_' + widgetName);
        $(document).on('mouseOverLastDataFromExternalContentGis_' + widgetName, function (event)
        {
            widgetOriginalBorderColor = $("#" + widgetName).css("border-color");
            $("#<?= $_GET['name'] ?>_titleDiv").html(event.widgetTitle);
            $("#" + widgetName).css("border-color", event.color1);
            $("#<?= $_GET['name'] ?>_header").css("background", event.color1);
            $("#<?= $_GET['name'] ?>_header").css("background", "-webkit-linear-gradient(left, " + event.color1 + ", " + event.color2 + ")");
            $("#<?= $_GET['name'] ?>_header").css("background", "-o-linear-gradient(left, " + event.color1 + ", " + event.color2 + ")");
            $("#<?= $_GET['name'] ?>_header").css("background", "-moz-linear-gradient(left, " + event.color1 + ", " + event.color2 + ")");
            $("#<?= $_GET['name'] ?>_header").css("background", "linear-gradient(to left, " + event.color1 + ", " + event.color2 + ")");
            $("#<?= $_GET['name'] ?>_header").css("color", "black");
        });

        $(document).off('mouseOutLastDataFromExternalContentGis_' + widgetName);
        $(document).on('mouseOutLastDataFromExternalContentGis_' + widgetName, function (event)
        {
            $("#<?= $_GET['name'] ?>_titleDiv").html(widgetTitle);
            $("#" + widgetName).css("border-color", widgetOriginalBorderColor);
            $("#<?= $_GET['name'] ?>_header").css("background", widgetHeaderColor);
            $("#<?= $_GET['name'] ?>_header").css("color", widgetHeaderFontColor);
        });

        $(document).off('showLastDataFromExternalContentGis_' + widgetName);
        $(document).on('showLastDataFromExternalContentGis_' + widgetName, function (event)
        {
            if (event.targetWidget === widgetName)
            {
                clearInterval(countdownRef);
                $("#<?= $_GET['name'] ?>_content").hide();
<?= $_GET['name'] ?>(true, metricName, event.widgetTitle, event.color1, "black", true, event.serviceUri, event.field, null, /*event.randomSingleGeoJsonIndex,*/ event.marker, event.mapRef, event.fakeId);
            }
        });

        $(document).off('restoreOriginalLastDataFromExternalContentGis_' + widgetName);
        $(document).on('restoreOriginalLastDataFromExternalContentGis_' + widgetName, function (event)
        {
            if (event.targetWidget === widgetName)
            {
                clearInterval(countdownRef);
                $("#<?= $_GET['name'] ?>_content").hide();
<?= $_GET['name'] ?>(true, metricName, "<?= preg_replace($titlePatterns, $replacements, $title) ?>", "<?= $_GET['frame_color'] ?>", "<?= $_GET['headerFontColor'] ?>", false, null, null, null, null, /*null,*/ null, null, null);
            }
        });

        var elToEmpty = $("#<?= $_GET['name'] ?>_chartContainer");
        elToEmpty.css("font-family", "Verdana");
        var url = "<?= $_GET['link_w'] ?>";

        //Specifiche per questo widget
        var flagNumeric = false;
        var alarmSet = false;
       
        //var pattern = /Percentuale\//;


        //Definizioni di funzione specifiche del widget
        //Restituisce il JSON delle soglie se presente, altrimenti NULL
        /*
         function getThresholdsJson()
         {
         var thresholdsJson = null;
         if(jQuery.parseJSON(widgetProperties.param.parameters !== null))
         {
         thresholdsJson = widgetProperties.param.parameters; 
         }
         
         return thresholdsJson;
         }
         */
        //Restituisce il JSON delle info se presente, altrimenti NULL
        function getInfoJson()
        {
            var infoJson = null;
            if (jQuery.parseJSON(widgetProperties.param.infoJson !== null))
            {
                infoJson = jQuery.parseJSON(widgetProperties.param.infoJson);
            }

            return infoJson;
        }

        //Restituisce il JSON delle info se presente, altrimenti NULL
        function getStyleParameters()
        {
            var styleParameters = null;
            if (jQuery.parseJSON(widgetProperties.param.styleParameters !== null))
            {
                styleParameters = jQuery.parseJSON(widgetProperties.param.styleParameters);
            }

            return styleParameters;
        }

        function populateWidget()
        {
            if (metricData !== null)
            {
                if (metricData.data[0] !== 'undefined')
                {
                    if (metricData.data.length > 0)
                    {
                        //Inizio eventuale codice ad hoc basato sui dati della metrica


                        if (firstLoad !== false)
                        {
                            showWidgetContent(widgetName);
                            $('#<?= $_GET['name'] ?>_noDataAlert').hide();
                            $("#<?= $_GET['name'] ?>_loadErrorAlert").hide();
                            $("#<?= $_GET['name'] ?>_chartContainer").show();
                        } else
                        {
                            $("#" + widgetName + "_responseCode").empty();
                            $("#" + widgetName + "_serverStatusIcon").empty();
                            $("#" + widgetName + "_responseMsg").empty();
                        }

                        metricType = metricData.data[0].commit.author.metricType;
                        // threshold = metricData.data[0].commit.author.threshold;
                        //thresholdEval = metricData.data[0].commit.author.thresholdEval;



                        if ((metricData.data[0].commit.author.value_perc1 !== null) && (metricData.data[0].commit.author.value_perc1 !== "") && (typeof metricData.data[0].commit.author.value_perc1 !== "undefined"))
                        {
                            responseCode = parseInt(metricData.data[0].commit.author.value_perc1);
                        }
                        responseMsg = metricData.data[0].commit.author.value_text;
                        computationDate = metricData.data[0].commit.author.computationDate;

                        fontRatio = parseInt((sizeRowsWidget / 4) * 100);
                        var fontRatioMsg = parseInt(fontRatio * 1.3);
                        var fontRatioCode = parseInt(fontRatio * 0.9);
                        var fontRatioDate = parseInt(fontRatio * 0.7);
                        var fontRatioIcon = parseInt((sizeRowsWidget / 4) * 42);
                        fontRatio = fontRatio.toString() + "%";
                        fontRatioMsg = fontRatioMsg.toString() + "%";
                        fontRatioCode = fontRatioCode.toString() + "%";
                        fontRatioDate = fontRatioDate.toString() + "%";
                        fontRatioIcon = fontRatioIcon.toString() + "px";

                        var serverName = metricName.toString().replace("_status", "");

                        $("#<?= $_GET['name'] ?>_responseCode").css("font-size", fontRatioCode);
                        $("#<?= $_GET['name'] ?>_responseMsg").css("font-size", fontRatioMsg);
                        $("#<?= $_GET['name'] ?>_serverName").css("font-size", fontRatioCode);
                        $("#<?= $_GET['name'] ?>_date").css("font-size", fontRatioDate);
                        

                       
                        $("#<?= $_GET['name'] ?>_responseCode").css("height", "15%");
                        $("#<?= $_GET['name'] ?>_serverName").css("height", "15%");
                        $("#<?= $_GET['name'] ?>_date").css("height", "10%");
                        $("#<?= $_GET['name'] ?>_responseMsg").html(responseMsg);
                        $("#<?= $_GET['name'] ?>_responseCode").html(responseCode);
                        $("#<?= $_GET['name'] ?>_serverName").html(serverName);
                        $("#<?= $_GET['name'] ?>_date").html(computationDate);                      
                        $("#<?= $_GET['name'] ?>_serverName").css("font-variant", "normal");


                        if (responseMsg === "token found") {
                            $("#<?= $_GET['name'] ?>_responseCode").hide();
                            $("#<?= $_GET['name'] ?>_statusContainer").attr("class", "serverStatusContainerOk");
                            $("#<?= $_GET['name'] ?>_serverStatusIcon").html("<i class='fa fa-check' style='font-size:" + fontRatioIcon + "'></i>");
                            $("#<?= $_GET['name'] ?>_date").css("height", "25%");
                        } else if (responseMsg === "token not found") {
                            $("#<?= $_GET['name'] ?>_statusContainer").attr("class", "serverStatusContainerNotFound");
                            $("#<?= $_GET['name'] ?>_serverStatusIcon").html("<i class='fa fa-close' style='font-size:" + fontRatioIcon + "'></i>");
                        } else {
                            if (responseCode === -1) {
                                $("#<?= $_GET['name'] ?>_responseCode").hide();
                                $("#<?= $_GET['name'] ?>_date").css("height", "25%");
                            }
                            $("#<?= $_GET['name'] ?>_statusContainer").attr("class", "serverStatusContainerKo");
                            $("#<?= $_GET['name'] ?>_serverStatusIcon").html("<i class='fa fa-close' style='font-size:" + fontRatioIcon + "'></i>");
                        }
                       
                        $("#<?= $_GET['name'] ?>_serverStatusIcon").css("height", "40%");
                        $("#<?= $_GET['name'] ?>_date").css("font-weight", "normal");
                    } else
                    {
                        showWidgetContent(widgetName);
                        if (firstLoad !== false)
                        {
                            $("#<?= $_GET['name'] ?>_chartContainer").hide();
                            $('#<?= $_GET['name'] ?>_noDataAlert').show();
                        }
                    }
                } else
                {
                    showWidgetContent(widgetName);
                    if (firstLoad !== false)
                    {
                        $("#<?= $_GET['name'] ?>_chartContainer").hide();
                        $('#<?= $_GET['name'] ?>_noDataAlert').show();
                    }
                }
            } else
            {
                showWidgetContent(widgetName);
                if (firstLoad !== false)
                {
                    $("#<?= $_GET['name'] ?>_chartContainer").hide();
                    $('#<?= $_GET['name'] ?>_noDataAlert').show();
                }
            }
        }
        //Fine definizioni di funzione 

        setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight);
        if (firstLoad === false)
        {
            showWidgetContent(widgetName);
        } else
        {
            setupLoadingPanel(widgetName, widgetContentColor, firstLoad);
        }

        addLink(widgetName, url, linkElement, divContainer);
        $("#<?= $_GET['name'] ?>_titleDiv").html(widgetTitle);
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
                if ((widgetProperties !== null) && (widgetProperties !== ''))
                {
                    //Inizio eventuale codice ad hoc basato sulle proprietà del widget
                    styleParameters = getStyleParameters();//Restituisce null finché non si usa il campo per questo widget

                    sizeRowsWidget = parseInt(widgetProperties.param.size_rows);

                    $('#<?= $_GET['name'] ?>_infoButtonDiv i.gisDriverPin').hide();
                    $('#<?= $_GET['name'] ?>_infoButtonDiv a.info_source').show();
                    //metricData = getMetricData(metricName);

                    $.ajax({
                        url: getMetricDataUrl,
                        type: "GET",
                        data: {"IdMisura": [metricName]},
                        async: true,
                        dataType: 'json',
                        success: function (data)
                        {
                            metricData = data;
                            populateWidget();
                        },
                        error: function ()
                        {
                            console.log("Errore in caricamento proprietà widget");
                            console.log(JSON.stringify(errorData));
                            showWidgetContent(widgetName);
                            if (firstLoad !== false)
                            {
                                $("#<?= $_GET['name'] ?>_chartContainer").hide();
                                $('#<?= $_GET['name'] ?>_noDataAlert').show();
                            }
                        }
                    });
                } else
                {
                    console.log("Errore in caricamento proprietà widget");
                    console.log(JSON.stringify(errorData));
                    showWidgetContent(widgetName);
                    if (firstLoad !== false)
                    {
                        $("#<?= $_GET['name'] ?>_chartContainer").hide();
                        $('#<?= $_GET['name'] ?>_noDataAlert').show();
                    }
                }
            },
            error: function (errorData)
            {
                console.log("Errore in caricamento proprietà widget");
                console.log(JSON.stringify(errorData));
                showWidgetContent(widgetName);
                if (firstLoad !== false)
                {
                    $("#<?= $_GET['name'] ?>_chartContainer").hide();
                    $('#<?= $_GET['name'] ?>_noDataAlert').show();
                }
            },
            complete: function ()
            {
                countdownRef = startCountdown(widgetName, timeToReload, <?= $_GET['name'] ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef, fromGisFakeId);
            }
        });


    });//Fine document ready 
</script>

<div class="widget" id="<?= $_GET['name'] ?>_div">
    <div class='ui-widget-content'>
        <div id='<?= $_GET['name'] ?>_header' class="widgetHeader">
            <div id="<?= $_GET['name'] ?>_infoButtonDiv" class="infoButtonContainer">
                <a id="info_modal" href="#" class="info_source"><i id="source_<?= $_GET['name'] ?>" class="source_button fa fa-info-circle" style="font-size: 22px"></i></a>
                <i class="material-icons gisDriverPin" data-onMap="false">navigation</i>
            </div>    
            <div id="<?= $_GET['name'] ?>_titleDiv" class="titleDiv"></div>
            <div id="<?= $_GET['name'] ?>_buttonsDiv" class="buttonsContainer">
                <div class="singleBtnContainer"><a class="icon-cfg-widget" href="#"><span class="glyphicon glyphicon-cog glyphicon-modify-widget" aria-hidden="true"></span></a></div>
                <div class="singleBtnContainer"><a class="icon-remove-widget" href="#"><span class="glyphicon glyphicon-remove glyphicon-modify-widget" aria-hidden="true"></span></a></div>
            </div>
            <div id="<?= $_GET['name'] ?>_countdownContainerDiv" class="countdownContainer">
                <div id="<?= $_GET['name'] ?>_countdownDiv" class="countdown"></div> 
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
            <div id="<?= $_GET['name'] ?>_chartContainer" class="chartContainer centerWithFlex">
                <div id="<?= $_GET['name'] ?>_statusContainer" class="statusContainer">
                    <div id='<?= $_GET['name'] ?>_serverName' class="serverStatus">
                    </div>
                    <div id='<?= $_GET['name'] ?>_serverStatusIcon' class="statoJobIcona">
                    </div>
                    <div id='<?= $_GET['name'] ?>_responseCode' class="serverStatus">
                    </div>
                    <div id='<?= $_GET['name'] ?>_responseMsg' class="serverStatus">
                    </div>
                    <div id='<?= $_GET['name'] ?>_date' class="serverStatus">
                    </div>
                </div>


            </div>
        </div>
    </div>	
</div> 