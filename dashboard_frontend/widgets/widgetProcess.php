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
    $(document).ready(function <?= $_GET['name'] ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef) 
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
        
        var hostFile = "<?= $_GET['hostFile'] ?>";
        var widgetName = "<?= $_GET['name'] ?>";
        var divContainer = $("#<?= $_GET['name'] ?>_content");
        var widgetContentColor = "<?= $_GET['color'] ?>";
        var widgetHeaderColor = "<?= $_GET['frame_color'] ?>";
        var widgetHeaderFontColor = "<?= $_GET['headerFontColor'] ?>";
        var nome_wid = "<?= $_GET['name'] ?>_div";
        var linkElement = $('#<?= $_GET['name'] ?>_link_w');
        var color = '<?= $_GET['color'] ?>';
        var fontSize = "<?= $_GET['fontSize'] ?>";
        var fontColor = "<?= $_GET['fontColor'] ?>";
        var timeToReload = <?= $_GET['freq'] ?>;
        var widgetPropertiesString, widgetProperties, thresholdObject, infoJson, styleParameters, metricType, metricData, pattern, totValues, shownValues, 
            descriptions, udm, threshold, thresholdEval, stopsArray, delta, deltaPerc, seriesObj, dataObj, pieObj, legendLength,
            rangeMin, rangeMax, widgetParameters, height, sizeRowsWidget, fontRatio, fontRatioSmall, host, user, pass, jobName, status, date = null;
        var metricId = "<?= $_GET['metric'] ?>";
        var elToEmpty = $("#<?= $_GET['name'] ?>_chartContainer");
        var url = "<?= $_GET['link_w'] ?>";
        var embedWidget = <?= $_GET['embedWidget'] ?>;
        var embedWidgetPolicy = '<?= $_GET['embedWidgetPolicy'] ?>';	
        var headerHeight = 25;
        var showTitle = "<?= $_GET['showTitle'] ?>";
	var showHeader = null;
        
         if(((embedWidget === true)&&(embedWidgetPolicy === 'auto'))||((embedWidget === true)&&(embedWidgetPolicy === 'manual')&&(showTitle === "no"))||((embedWidget === false)&&(showTitle === "no")&&(hostFile === "index")))
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
        widgetProperties = getWidgetProperties(widgetName);
        
        if((widgetProperties !== null) && (widgetProperties !== ''))
        {
            //Inizio eventuale codice ad hoc basato sulle proprietà del widget
            styleParameters = getStyleParameters();//Restituisce null finché non si usa il campo per questo widget
            
            
            //Lasciamo la chiamata originaria, quella a comune dà problemi
            $.ajax({//Inizio AJAX getParametersWidgets.php
                url: "../widgets/getParametersWidgets.php",
                type: "GET",
                data: {"nomeWidget": ["<?= $_GET['name'] ?>"]},
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
                    manageInfoButtonVisibility(widgetProperties.param.infoMessage_w, $('#<?= $_GET['name'] ?>_header'));

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
                                $('#<?= $_GET['name'] ?>_noDataAlert').show();
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

                                contentHeight = parseInt($("#<?= $_GET['name'] ?>_content").prop("offsetHeight") - $("#<?= $_GET['name'] ?>_jobName").prop("offsetHeight")) - 8;
                                $("#<?= $_GET['name'] ?>_content a").css("height", contentHeight + "px");
                                $("#<?= $_GET['name'] ?>_chartContainer").css("height", contentHeight + "px");

                                //Fattore di ingrandimento font calcolato sull'altezza in righe, base 4.
                                fontRatio = parseInt((sizeRowsWidget / 4)*100);
                                var fontRatioState = parseInt(fontRatio*1.3);
                                var fontRatioDate = parseInt(fontRatio*0.9);
                                var fontRatioIcon = parseInt((sizeRowsWidget / 4)*42);
                                fontRatio = fontRatio.toString() + "%";
                                fontRatioState = fontRatioState.toString() + "%";
                                fontRatioDate = fontRatioDate.toString() + "%";
                                fontRatioIcon = fontRatioIcon.toString() + "px";

                                $("#<?= $_GET['name'] ?>_jobName").css("font-size", fontRatio);
                                $("#<?= $_GET['name'] ?>_jobName").html(contenuto.jobName);

                                switch(status)
                                {
                                    case "SUCCESS":
                                        $("#<?= $_GET['name'] ?>_chartContainer").attr("class", "statoJobContainerOk");
                                        $("#<?= $_GET['name'] ?>_jobStateIcon").html("<i class='fa fa-check' style='font-size:" + fontRatioIcon + "'></i>");
                                        break;

                                    case "RUNNING":
                                        $("#<?= $_GET['name'] ?>_chartContainer").attr("class", "statoJobContainerRunning");
                                        $("#<?= $_GET['name'] ?>_jobStateIcon").html("<i class='fa fa-circle-o-notch fa-spin' style='font-size:" + fontRatioIcon + "'></i>");
                                        break;

                                    case "MISFIRED":
                                        $("#<?= $_GET['name'] ?>_chartContainer").attr("class", "statoJobContainerKo");
                                        $("#<?= $_GET['name'] ?>_jobStateIcon").html("<i class='fa fa-close' style='font-size:" + fontRatioIcon + "'></i>");
                                        break;

                                    case "FAILED":
                                        $("#<?= $_GET['name'] ?>_chartContainer").attr("class", "statoJobContainerKo");
                                        $("#<?= $_GET['name'] ?>_jobStateIcon").html("<i class='fa fa-close' style='font-size:" + fontRatioIcon + "'></i>");
                                        break; 
                                }

                                $("#<?= $_GET['name'] ?>_jobState").css("font-size", fontRatioState);
                                $("#<?= $_GET['name'] ?>_jobState").html(status);
                                $("#<?= $_GET['name'] ?>_jobDate").css("font-size", fontRatioDate);
                                $("#<?= $_GET['name'] ?>_jobDate").html(date);
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
        startCountdown(widgetName, timeToReload, <?= $_GET['name'] ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef);
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
            <p id="<?= $_GET['name'] ?>_noDataAlert" style='text-align: center; font-size: 18px; display:none'>Nessun dato disponibile</p>
            <div id='<?= $_GET['name'] ?>_jobName'  class="nomeJob"></div>
            <div id="<?= $_GET['name'] ?>_chartContainer" class="chartContainer" style="margin-left: auto; margin-right: auto">
                <div id='<?= $_GET['name'] ?>_jobStateIcon' class="statoJobIcona"></div>
                <div id='<?= $_GET['name'] ?>_jobState' class="statoJob"></div>
                <div id='<?= $_GET['name'] ?>_jobDate' class="dataJob"></div>
            </div> 
        </div>
    </div>	
</div> 