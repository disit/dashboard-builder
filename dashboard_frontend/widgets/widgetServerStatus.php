<?php
/* Dashboard Builder.
  Copyright (C) 2018 DISIT Lab http://www.disit.org - University of Florence

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
    $(document).ready(function <?= $_REQUEST['name_w'] ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef, fromGisFakeId)
    {
        <?php
            $titlePatterns = array();
            $titlePatterns[0] = '/_/';
            $titlePatterns[1] = '/\'/';
            $replacements = array();
            $replacements[0] = ' ';
            $replacements[1] = '&apos;';
            $title = $_REQUEST['title_w'];
        ?>
        var headerHeight = 25;
        var hostFile = "<?= $_REQUEST['hostFile'] ?>";
        var widgetName = "<?= $_REQUEST['name_w'] ?>";
        var divContainer = $("#<?= $_REQUEST['name_w'] ?>_content");
        var widgetContentColor = "<?= $_REQUEST['color_w'] ?>";
        var nome_wid = "<?= $_REQUEST['name_w'] ?>_div";
        var linkElement = $('#<?= $_REQUEST['name_w'] ?>_link_w');
        var color = '<?= $_REQUEST['color_w'] ?>';
        var timeToReload = <?= $_REQUEST['frequency_w'] ?>;
        var embedWidget = <?= $_REQUEST['embedWidget'] ?>;
        var embedWidgetPolicy = '<?= $_REQUEST['embedWidgetPolicy'] ?>';
        var showTitle = "<?= $_REQUEST['showTitle'] ?>";
        var showHeader = null;
        var getMetricDataUrl = "../widgets/getDataMetrics.php";
		var hasTimer = "<?= $_REQUEST['hasTimer'] ?>";
        var widgetProperties, styleParameters, metricType, metricName, pattern, responseMsg, threshold, thresholdEval,
                delta, deltaPerc, sizeRowsWidget, fontSize, responseCode, metricType, countdownRef, widgetTitle, metricData, widgetHeaderColor,
                widgetHeaderFontColor, widgetOriginalBorderColor, urlToCall, geoJsonServiceData, showHeader = null;

        if(((embedWidget === true) && (embedWidgetPolicy === 'auto')) || ((embedWidget === true) && (embedWidgetPolicy === 'manual') && (showTitle === "no")) || ((embedWidget === false) && (showTitle === "no")))
        {
            showHeader = false;
        } else
        {
            showHeader = true;
        }

        if ((metricNameFromDriver === "undefined") || (metricNameFromDriver === undefined) || (metricNameFromDriver === "null") || (metricNameFromDriver === null))
        {
            metricName = "<?= $_REQUEST['id_metric'] ?>";
            widgetTitle = "<?= preg_replace($titlePatterns, $replacements, $title) ?>";
            widgetHeaderColor = "<?= $_REQUEST['frame_color_w'] ?>";
            widgetHeaderFontColor = "<?= $_REQUEST['headerFontColor'] ?>";
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
                $("#<?= $_REQUEST['name_w'] ?>_content").hide();
<?= $_REQUEST['name_w'] ?>(true, event.newMetricName, event.newTargetTitle, event.newHeaderAndBorderColor, event.newHeaderFontColor, false, null, null, null, null, null, null);
            }
        });

        $(document).off('mouseOverLastDataFromExternalContentGis_' + widgetName);
        $(document).on('mouseOverLastDataFromExternalContentGis_' + widgetName, function (event)
        {
            widgetOriginalBorderColor = $("#" + widgetName).css("border-color");
            $("#<?= $_REQUEST['name_w'] ?>_titleDiv").html(event.widgetTitle);
            $("#" + widgetName).css("border-color", event.color1);
            $("#<?= $_REQUEST['name_w'] ?>_header").css("background", event.color1);
            $("#<?= $_REQUEST['name_w'] ?>_header").css("background", "-webkit-linear-gradient(left, " + event.color1 + ", " + event.color2 + ")");
            $("#<?= $_REQUEST['name_w'] ?>_header").css("background", "-o-linear-gradient(left, " + event.color1 + ", " + event.color2 + ")");
            $("#<?= $_REQUEST['name_w'] ?>_header").css("background", "-moz-linear-gradient(left, " + event.color1 + ", " + event.color2 + ")");
            $("#<?= $_REQUEST['name_w'] ?>_header").css("background", "linear-gradient(to left, " + event.color1 + ", " + event.color2 + ")");
            $("#<?= $_REQUEST['name_w'] ?>_header").css("color", "black");
        });

        $(document).off('mouseOutLastDataFromExternalContentGis_' + widgetName);
        $(document).on('mouseOutLastDataFromExternalContentGis_' + widgetName, function (event)
        {
            $("#<?= $_REQUEST['name_w'] ?>_titleDiv").html(widgetTitle);
            $("#" + widgetName).css("border-color", widgetOriginalBorderColor);
            $("#<?= $_REQUEST['name_w'] ?>_header").css("background", widgetHeaderColor);
            $("#<?= $_REQUEST['name_w'] ?>_header").css("color", widgetHeaderFontColor);
        });

        $(document).off('showLastDataFromExternalContentGis_' + widgetName);
        $(document).on('showLastDataFromExternalContentGis_' + widgetName, function (event)
        {
            if (event.targetWidget === widgetName)
            {
                clearInterval(countdownRef);
                $("#<?= $_REQUEST['name_w'] ?>_content").hide();
<?= $_REQUEST['name_w'] ?>(true, metricName, event.widgetTitle, event.color1, "black", true, event.serviceUri, event.field, null, /*event.randomSingleGeoJsonIndex,*/ event.marker, event.mapRef, event.fakeId);
            }
        });

        $(document).off('restoreOriginalLastDataFromExternalContentGis_' + widgetName);
        $(document).on('restoreOriginalLastDataFromExternalContentGis_' + widgetName, function (event)
        {
            if (event.targetWidget === widgetName)
            {
                clearInterval(countdownRef);
                $("#<?= $_REQUEST['name_w'] ?>_content").hide();
<?= $_REQUEST['name_w'] ?>(true, metricName, "<?= preg_replace($titlePatterns, $replacements, $title) ?>", "<?= $_REQUEST['frame_color_w'] ?>", "<?= $_REQUEST['headerFontColor'] ?>", false, null, null, null, null, /*null,*/ null, null, null);
            }
        });

        var elToEmpty = $("#<?= $_REQUEST['name_w'] ?>_chartContainer");
        elToEmpty.css("font-family", "Verdana");
        var url = "<?= $_REQUEST['link_w'] ?>";

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
                        if(firstLoad !== false)
                        {
                            showWidgetContent(widgetName);
                            $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').hide();
                            $("#<?= $_REQUEST['name_w'] ?>_loadErrorAlert").hide();
                            $("#<?= $_REQUEST['name_w'] ?>_chartContainer").show();
                        } else
                        {
                            $("#" + widgetName + "_responseCode span").empty();
                            $("#" + widgetName + "_serverStatusIcon span").empty();
                            $("#" + widgetName + "_responseMsg span").empty();
                        }

                        metricType = metricData.data[0].commit.author.metricType;
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

                        var serverName = metricName.toString().replace("_status", "").toUpperCase();
                        $("#<?= $_REQUEST['name_w'] ?>_responseMsg span").html(responseMsg);
                        $("#<?= $_REQUEST['name_w'] ?>_responseCode span").html(responseCode);
                        $("#<?= $_REQUEST['name_w'] ?>_serverName span").html(serverName);
                        $("#<?= $_REQUEST['name_w'] ?>_date span").html(computationDate);

                        if(responseMsg === "token found") 
                        {
                            $("#<?= $_REQUEST['name_w'] ?>_responseCode").hide();
                            $("#<?= $_REQUEST['name_w'] ?>_statusContainer").attr("class", "serverStatusContainerOk");
                            $("#<?= $_REQUEST['name_w'] ?>_serverStatusIcon span").html("<i class='fa fa-check'></i>");
                        } 
                        else if(responseMsg === "token not found") 
                        {
                            $("#<?= $_REQUEST['name_w'] ?>_statusContainer").attr("class", "serverStatusContainerNotFound");
                            $("#<?= $_REQUEST['name_w'] ?>_serverStatusIcon span").html("<i class='fa fa-close'></i>");
                        } 
                        else 
                        {
                            if (responseCode === -1) 
                            {
                                $("#<?= $_REQUEST['name_w'] ?>_responseCode").hide();
                            }
                            $("#<?= $_REQUEST['name_w'] ?>_statusContainer").attr("class", "serverStatusContainerKo");
                            $("#<?= $_REQUEST['name_w'] ?>_serverStatusIcon span").html("<i class='fa fa-close'></i>");
                        }
                        
                        $('#<?= $_REQUEST['name_w'] ?>_responseMsg').textfill({
                            maxFontPixels: -20
                        });
                        
                        $('#<?= $_REQUEST['name_w'] ?>_responseCode').textfill({
                            maxFontPixels: -20
                        });
                        
                        $('#<?= $_REQUEST['name_w'] ?>_serverName').textfill({
                            maxFontPixels: -20
                        });
                        
                        $('#<?= $_REQUEST['name_w'] ?>_serverStatusIcon').textfill({
                            maxFontPixels: -20
                        });
                        
                        $('#<?= $_REQUEST['name_w'] ?>_date').textfill({
                            maxFontPixels: -20
                        });
                        
                        $("#<?= $_REQUEST['name_w'] ?>_serverName span").css('font-size', parseInt($('#<?= $_REQUEST['name_w'] ?>_serverName span').css('font-size').replace('px', ''))*0.9);
                        $("#<?= $_REQUEST['name_w'] ?>_responseMsg span").css('font-size', parseInt($('#<?= $_REQUEST['name_w'] ?>_responseMsg span').css('font-size').replace('px', ''))*0.9);
                        $("#<?= $_REQUEST['name_w'] ?>_date span").css('font-size', parseInt($('#<?= $_REQUEST['name_w'] ?>_date span').css('font-size').replace('px', ''))*0.8);
                    } 
                    else
                    {
                        showWidgetContent(widgetName);
                        if(firstLoad !== false)
                        {
                            $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                            $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                        }
                    }
                } 
                else
                {
                    showWidgetContent(widgetName);
                    if (firstLoad !== false)
                    {
                        $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                        $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                    }
                }
            } 
            else
            {
                showWidgetContent(widgetName);
                if (firstLoad !== false)
                {
                    $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                    $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                }
            }
        }
        
        function resizeWidget()
        {
            setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
            $('#<?= $_REQUEST['name_w'] ?>_responseMsg').textfill({
                maxFontPixels: -20
            });

            $('#<?= $_REQUEST['name_w'] ?>_responseCode').textfill({
                maxFontPixels: -20
            });
            
            $('#<?= $_REQUEST['name_w'] ?>_serverStatusIcon').textfill({
                maxFontPixels: -20
            });

            $('#<?= $_REQUEST['name_w'] ?>_serverName').textfill({
                maxFontPixels: -20
            });

            $('#<?= $_REQUEST['name_w'] ?>_date').textfill({
                maxFontPixels: -20
            });
            
            $("#<?= $_REQUEST['name_w'] ?>_serverName span").css('font-size', parseInt($('#<?= $_REQUEST['name_w'] ?>_serverName span').css('font-size').replace('px', ''))*0.9);
            $("#<?= $_REQUEST['name_w'] ?>_responseMsg span").css('font-size', parseInt($('#<?= $_REQUEST['name_w'] ?>_responseMsg span').css('font-size').replace('px', ''))*0.9);
            $("#<?= $_REQUEST['name_w'] ?>_date span").css('font-size', parseInt($('#<?= $_REQUEST['name_w'] ?>_date span').css('font-size').replace('px', ''))*0.8);
        }
        //Fine definizioni di funzione 

        $('#<?= $_REQUEST['name_w'] ?>_div').parents('li.gs_w').off('resizeWidgets');
        $('#<?= $_REQUEST['name_w'] ?>_div').parents('li.gs_w').on('resizeWidgets', resizeWidget);
        
        setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
        
        if(firstLoad === false)
        {
            showWidgetContent(widgetName);
        } 
        else
        {
            setupLoadingPanel(widgetName, widgetContentColor, firstLoad);
        }

        addLink(widgetName, url, linkElement, divContainer);
        $("#<?= $_REQUEST['name_w'] ?>_titleDiv").html(widgetTitle);
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

                    $('#<?= $_REQUEST['name_w'] ?>_infoButtonDiv i.gisDriverPin').hide();
                    $('#<?= $_REQUEST['name_w'] ?>_infoButtonDiv a.info_source').show();

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
                            if(firstLoad !== false)
                            {
                                $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                                $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                            }
                        }
                    });
                } 
                else
                {
                    console.log("Errore in caricamento proprietà widget");
                    console.log(JSON.stringify(errorData));
                    showWidgetContent(widgetName);
                    if(firstLoad !== false)
                    {
                        $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                        $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                    }
                }
            },
            error: function (errorData)
            {
                console.log("Errore in caricamento proprietà widget");
                console.log(JSON.stringify(errorData));
                showWidgetContent(widgetName);
                if(firstLoad !== false)
                {
                    $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                    $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                }
            },
            complete: function ()
            {
                countdownRef = startCountdown(widgetName, timeToReload, <?= $_REQUEST['name_w'] ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef, fromGisFakeId);
            }
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
            <div class ="loadingIconDiv">
                <i class='fa fa-spinner fa-spin'></i>
            </div>
        </div>

        <div id="<?= $_REQUEST['name_w'] ?>_content" class="content">
            <div id="<?= $_REQUEST['name_w'] ?>_noDataAlert" class="noDataAlert">
                <div id="<?= $_REQUEST['name_w'] ?>_noDataAlertText" class="noDataAlertText">
                    No data available
                </div>
                <div id="<?= $_REQUEST['name_w'] ?>_noDataAlertIcon" class="noDataAlertIcon">
                    <i class="fa fa-times"></i>
                </div>
            </div>
            <div id="<?= $_REQUEST['name_w'] ?>_chartContainer" class="chartContainer centerWithFlex">
                <div id="<?= $_REQUEST['name_w'] ?>_statusContainer" class="statusContainer">
                    <div id='<?= $_REQUEST['name_w'] ?>_serverName' class="serverName">
                        <span></span>
                    </div>
                    <div id='<?= $_REQUEST['name_w'] ?>_serverStatusIcon' class="serverTestResultIcon">
                        <span></span>
                    </div>
                    <div id='<?= $_REQUEST['name_w'] ?>_responseCode' class="serverTestResponseCode">
                        <span></span>
                    </div>
                    <div id='<?= $_REQUEST['name_w'] ?>_responseMsg' class="serverTestResponseMsg">
                        <span></span>
                    </div>
                    <div id='<?= $_REQUEST['name_w'] ?>_date' class="serverTestDate centerWithFlex">
                        <span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>	
</div> 