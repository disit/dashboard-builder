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
    $(document).ready(function <?= $_REQUEST['name_w'] ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef) 
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
        
        var hostFile = "<?= $_REQUEST['hostFile'] ?>";
        var widgetName = "<?= $_REQUEST['name_w'] ?>";
        var divContainer = $("#<?= $_REQUEST['name_w'] ?>_content");
        var widgetContentColor = "<?= $_REQUEST['color_w'] ?>";
        var widgetHeaderColor = "<?= $_REQUEST['frame_color_w'] ?>";
        var widgetHeaderFontColor = "<?= $_REQUEST['headerFontColor'] ?>";
        var nome_wid = "<?= $_REQUEST['name_w'] ?>_div";
        var linkElement = $('#<?= $_REQUEST['name_w'] ?>_link_w');
        var color = '<?= $_REQUEST['color_w'] ?>';
        var fontSize = "<?= $_REQUEST['fontSize'] ?>";
        var fontColor = "<?= $_REQUEST['fontColor'] ?>";
        var timeToReload = <?= $_REQUEST['frequency_w'] ?>;
        var widgetPropertiesString, widgetProperties, thresholdObject, infoJson, styleParameters, metricType, metricData, pattern, totValues, shownValues, 
            descriptions, udm, threshold, thresholdEval, stopsArray, delta, deltaPerc, seriesObj, dataObj, pieObj, legendLength,
            rangeMin, rangeMax, widgetParameters, height, sizeRowsWidget, fontRatio, countdownRef, fontRatioSmall, host, user, pass, jobName, status, date = null;
        var metricId = "<?= $_REQUEST['id_metric'] ?>";
        var elToEmpty = $("#<?= $_REQUEST['name_w'] ?>_chartContainer");
        var url = "<?= $_REQUEST['link_w'] ?>";
        var embedWidget = <?= $_REQUEST['embedWidget'] ?>;
        var embedWidgetPolicy = '<?= $_REQUEST['embedWidgetPolicy'] ?>';	
        var headerHeight = 25;
        var showTitle = "<?= $_REQUEST['showTitle'] ?>";
		var showHeader = null;
        var hasTimer = "<?= $_REQUEST['hasTimer'] ?>";
        if(((embedWidget === true)&&(embedWidgetPolicy === 'auto'))||((embedWidget === true)&&(embedWidgetPolicy === 'manual')&&(showTitle === "no"))||((embedWidget === false)&&(showTitle === "no")))
        {
                showHeader = false;
        }
        else
        {
                showHeader = true;
        }
        
        //Definizioni di funzione specifiche del widget
        /*Restituisce il JSON delle soglie se presente, altrimenti NULL*/
        function getThresholdsJson()
        {
            var thresholdsJson = null;
            if(jQuery.parseJSON(widgetProperties.param.parameters !== null))
            {
                thresholdsJson = widgetProperties.param.parameters; 
            }
            
            return thresholdsJson;
        }
        
        /*Restituisce il JSON delle info se presente, altrimenti NULL*/
        function getInfoJson()
        {
            var infoJson = null;
            if(jQuery.parseJSON(widgetProperties.param.infoJson !== null))
            {
                infoJson = jQuery.parseJSON(widgetProperties.param.infoJson); 
            }
            
            return infoJson;
        }
        
        /*Restituisce il JSON delle info se presente, altrimenti NULL*/
        function getStyleParameters()
        {
            var styleParameters = null;
            if(jQuery.parseJSON(widgetProperties.param.styleParameters !== null))
            {
                styleParameters = jQuery.parseJSON(widgetProperties.param.styleParameters); 
            }
            
            return styleParameters;
        }
        
        function resizeWidget()
        {
            setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
            
            var newHeight = null;
            if($('#<?= $_REQUEST['name_w'] ?>_header').is(':visible'))
            {
                newHeight = $('#<?= $_REQUEST['name_w'] ?>').height() - $('#<?= $_REQUEST['name_w'] ?>_header').height();
            }
            else
            {
                newHeight = $('#<?= $_REQUEST['name_w'] ?>').height();
            }

            $('#<?= $_REQUEST['name_w'] ?>_content').css('height', newHeight + 'px');
        }
		
        $(document).off('resizeHighchart_' + widgetName);
        $(document).on('resizeHighchart_' + widgetName, function(){
            showHeader = event.showHeader;
            resizeWidget();
        });
        
        //Fine definizioni di funzione 
        setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
        $('#<?= $_REQUEST['name_w'] ?>_div').parents('li.gs_w').off('resizeWidgets');
        $('#<?= $_REQUEST['name_w'] ?>_div').parents('li.gs_w').on('resizeWidgets', resizeWidget);
        if(firstLoad === false)
        {
            showWidgetContent(widgetName);
        }
        else
        {
            setupLoadingPanel(widgetName, widgetContentColor, firstLoad);
        }
        //addLink(widgetName, url, linkElement, divContainer, null);
        //$("#<?= $_REQUEST['name_w'] ?>_titleDiv").html("<?= preg_replace($titlePatterns, $replacements, $title) ?>");
        widgetProperties = getWidgetProperties(widgetName);
        
        if((widgetProperties !== null) && (widgetProperties !== ''))
        {
            //Inizio eventuale codice ad hoc basato sulle proprietà del widget
            styleParameters = getStyleParameters();//Restituisce null finché non si usa il campo per questo widget
            
            //Lasciamo la chiamata originaria, quella a comune dà problemi
            $.ajax({//Inizio AJAX getParametersWidgets.php
                url: "../widgets/getParametersWidgets.php",
                type: "GET",
                data: {"nomeWidget": ["<?= $_REQUEST['name_w'] ?>"]},
                async: true,
                dataType: 'json',
                success: function (msg) 
                {
                    var parametri = msg.param.parameters;
                    var contenuto = jQuery.parseJSON(parametri);
                    host = contenuto.host;
                    user = contenuto.user;
                    pass = contenuto.pass;
                    jobName = contenuto.jobName;
                    sizeRowsWidget = parseInt(msg.param.size_rows);

                    $.ajax({
                        url: "../widgets/getProcessStatus.php",
                        data: {action: "getSingleStatus", host: host, user: user, pass: pass, jobName: jobName},
                        type: "POST",
                        async: true,
                        dataType: 'json',
                        success: function (msg) 
                        {
                            if(msg === null)
                            {
                                showWidgetContent(widgetName);
                                $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                            }
                            else
                            {
                                status = msg[0].status;
                                date = msg[0].date;
                                
                                if(firstLoad !== false)
                                {
                                    showWidgetContent(widgetName);
                                }
                                else
                                {
                                    $("#" + widgetName + "_jobStateIcon").empty();
                                    $("#" + widgetName + "_jobState").empty();
                                    $("#" + widgetName + "_jobDate").empty();
                                }

                                contentHeight = parseInt($("#<?= $_REQUEST['name_w'] ?>_content").prop("offsetHeight") - $("#<?= $_REQUEST['name_w'] ?>_jobName").prop("offsetHeight")) - 8;
                                $("#<?= $_REQUEST['name_w'] ?>_content a").css("height", contentHeight + "px");
                                $("#<?= $_REQUEST['name_w'] ?>_chartContainer").css("height", contentHeight + "px");

                                //Fattore di ingrandimento font calcolato sull'altezza in righe, base 4.
                                fontRatio = parseInt((sizeRowsWidget / 4)*100);
                                var fontRatioState = parseInt(fontRatio*1.3);
                                var fontRatioDate = parseInt(fontRatio*0.9);
                                var fontRatioIcon = parseInt((sizeRowsWidget / 4)*42);
                                fontRatio = fontRatio.toString() + "%";
                                fontRatioState = fontRatioState.toString() + "%";
                                fontRatioDate = fontRatioDate.toString() + "%";
                                fontRatioIcon = fontRatioIcon.toString() + "px";

                                $("#<?= $_REQUEST['name_w'] ?>_jobName").css("font-size", fontRatio);
                                $("#<?= $_REQUEST['name_w'] ?>_jobName").html(contenuto.jobName);

                                switch(status)
                                {
                                    case "SUCCESS":
                                        $("#<?= $_REQUEST['name_w'] ?>_chartContainer").attr("class", "statoJobContainerOk");
                                        $("#<?= $_REQUEST['name_w'] ?>_jobStateIcon").html("<i class='fa fa-check' style='font-size:" + fontRatioIcon + "'></i>");
                                        break;

                                    case "RUNNING":
                                        $("#<?= $_REQUEST['name_w'] ?>_chartContainer").attr("class", "statoJobContainerRunning");
                                        $("#<?= $_REQUEST['name_w'] ?>_jobStateIcon").html("<i class='fa fa-circle-o-notch fa-spin' style='font-size:" + fontRatioIcon + "'></i>");
                                        break;

                                    case "MISFIRED":
                                        $("#<?= $_REQUEST['name_w'] ?>_chartContainer").attr("class", "statoJobContainerKo");
                                        $("#<?= $_REQUEST['name_w'] ?>_jobStateIcon").html("<i class='fa fa-close' style='font-size:" + fontRatioIcon + "'></i>");
                                        break;

                                    case "FAILED":
                                        $("#<?= $_REQUEST['name_w'] ?>_chartContainer").attr("class", "statoJobContainerKo");
                                        $("#<?= $_REQUEST['name_w'] ?>_jobStateIcon").html("<i class='fa fa-close' style='font-size:" + fontRatioIcon + "'></i>");
                                        break; 
                                }

                                $("#<?= $_REQUEST['name_w'] ?>_jobState").css("font-size", fontRatioState);
                                $("#<?= $_REQUEST['name_w'] ?>_jobState").html(status);
                                $("#<?= $_REQUEST['name_w'] ?>_jobDate").css("font-size", fontRatioDate);
                                $("#<?= $_REQUEST['name_w'] ?>_jobDate").html(date);
                            }
                        },
                        error: function()
                        {
                            console.log("Errore in caricamento stato del processo");
                        }
                    });
                }    
            });
        }
        else
        {
            console.log("Errore in caricamento proprietà widget");
        }
        
        $("#<?= $_REQUEST['name_w'] ?>").on('customResizeEvent', function(event){
            clearTimeout(countdownRef);
            <?= $_REQUEST['name_w'] ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef)
        });
        
        $("#<?= $_REQUEST['name_w'] ?>").off('updateFrequency');
        $("#<?= $_REQUEST['name_w'] ?>").on('updateFrequency', function(event){
                clearInterval(countdownRef);
                timeToReload = event.newTimeToReload;
                countdownRef = startCountdown(widgetName, timeToReload, <?= $_REQUEST['name_w'] ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef);
        });
        
        countdownRef = startCountdown(widgetName, timeToReload, <?= $_REQUEST['name_w'] ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef);
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
            <?php include '../widgets/commonModules/widgetDimControls.php'; ?>	
            <p id="<?= $_REQUEST['name_w'] ?>_noDataAlert" style='text-align: center; font-size: 18px; display:none'>Nessun dato disponibile</p>
            <div id='<?= $_REQUEST['name_w'] ?>_jobName'  class="nomeJob"></div>
            <div id="<?= $_REQUEST['name_w'] ?>_chartContainer" class="chartContainer" style="margin-left: auto; margin-right: auto">
                <div id='<?= $_REQUEST['name_w'] ?>_jobStateIcon' class="statoJobIcona"></div>
                <div id='<?= $_REQUEST['name_w'] ?>_jobState' class="statoJob"></div>
                <div id='<?= $_REQUEST['name_w'] ?>_jobDate' class="dataJob"></div>
            </div> 
        </div>
    </div>	
</div> 