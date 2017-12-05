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
    function hexToR(h) {return parseInt((cutHex(h)).substring(0,2),16)}
    function hexToG(h) {return parseInt((cutHex(h)).substring(2,4),16)}
    function hexToB(h) {return parseInt((cutHex(h)).substring(4,6),16)}
    function cutHex(h) {return (h.charAt(0)=="#") ? h.substring(1,7):h}
    function hexToRgb(hex)
    {
        var r = hexToR(hex);
        var g = hexToG(hex);
        var b = hexToB(hex);
        return r + ", " + g + ", " + b;
    }
    
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
        var widgetPropertiesString, widgetProperties, thresholdObject, infoJson, styleParameters, metricType, pattern, totValues, shownValues, 
            descriptions, udm, threshold, thresholdEval, stopsArray, delta, deltaPerc, seriesObj, dataObj, pieObj, legendLength,
            rangeMin, rangeMax, widgetParameters, sizeRowsWidget, widgetColor, fontSize,rowColor, rowColorRgb = null;
        var metricId = "<?= $_GET['metric'] ?>";
        var elToEmpty = $("#<?= $_GET['name'] ?>_chartContainer");
        var url = "<?= $_GET['link_w'] ?>";
        var embedWidget = <?= $_GET['embedWidget'] ?>;
        var embedWidgetPolicy = '<?= $_GET['embedWidgetPolicy'] ?>';	
        var headerHeight = 25;
        var showTitle = "<?= $_GET['showTitle'] ?>";
	var showHeader = null;
        
        //Specifiche per questo widget
        var metricsList = "<?= $_GET['metric'] ?>";
        var metrics = metricsList.split('+');
        var metricsTypesList = "<?= $_GET['type_metric'] ?>";
        var metricsTypes = metricsTypesList.split(',');
        var alarmCount = 0;
        var alarms = [];
        
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
        
        if((widgetProperties !== null) && (widgetProperties !== 'undefined'))
        {
            //Inizio eventuale codice ad hoc basato sulle proprietà del widget
            styleParameters = getStyleParameters();//Restituisce null finché non si usa il campo per questo widget
            
            if(widgetProperties !== null) 
            {
                widgetParameters = widgetProperties.param.parameters;
                sizeRowsWidget = parseInt(widgetProperties.param.size_rows);
                widgetColor = widgetProperties.param.color_w;
                fontSize = parseInt(sizeRowsWidget * 3.5);
                manageInfoButtonVisibility(widgetProperties.param.infoMessage_w, $('#<?= $_GET['name'] ?>_header'));
                //Per ora non usato
                /*if(widgetParameters !== null)
                {
                    
                }
                else 
                { 
                }*/
            } 
            
            //Fine eventuale codice ad hoc basato sulle proprietà del widget
            //Qui lasciamo la chiamata originaria
            $.ajax({
                url: "../widgets/getDataMetrics.php",
                data: {"IdMisura": metrics},
                type: "GET",
                async: true,
                dataType: 'json',
                success: function (metricData) 
                {
                    threshold = [];
                    thresholdEval = [];
                    var flagNumeric = [];
                    rowColor = null;
                    rowColorRgb = null;
                    
                    if(firstLoad !== false)
                    {
                        showWidgetContent(widgetName);
                    }
                    else
                    {
                        elToEmpty.empty();
                    }
        
                    /*Inizio eventuale codice ad hoc basato sui dati della metrica*/
                    for(var i = 0; i < metricData.data.length; i++) 
                    {
                        var value = null;
                        flagNumeric[i] = false;
                        alarms[i] = false;
                        var udm = "";
                        var pattern = /Percentuale\//;
                        threshold[i] = metricData.data[i].commit.author.threshold;
                        thresholdEval[i] = metricData.data[i].commit.author.thresholdEval;

                        if((metricsTypes[i] === "Percentuale") || (pattern.test(metricsTypes[i])))
                        {
                            udm = "%";
                            value = parseFloat(parseFloat(metricData.data[i].commit.author.value_perc1).toFixed(1));
                            if(value > 100)
                            {
                                value = 100;
                            }
                            flagNumeric[i] = true;
                        }
                        else
                        {
                            switch(metricsTypes[i])
                            {
                                case "Intero":
                                    value = parseInt(metricData.data[i].commit.author.value_num);
                                    flagNumeric[i] = true;
                                    break;

                                case "Float":
                                    value = parseFloat(parseFloat(metricData.data[i].commit.author.value_num).toFixed(1));
                                    flagNumeric[i] = true;
                                    break;

                                case "Testuale":
                                    value = metricData.data[i].commit.author.value_text;
                                    break;
                            }
                        }

                        if(flagNumeric[i] && (threshold[i] !== null) && (thresholdEval[i] !== null))
                        {
                            delta = Math.abs(value - threshold[i]);
                            //Distinguiamo in base all'operatore di confronto
                            switch(thresholdEval[i])
                            {
                               //Allarme attivo se il valore attuale è sotto la soglia
                               case '<':
                                   if(value < threshold[i])
                                   {
                                      alarms[i] = true;
                                      alarmCount++;
                                   }
                                   break;

                               //Allarme attivo se il valore attuale è sopra la soglia
                               case '>':
                                   if(value > threshold[i])
                                   {
                                      alarms[i] = true;
                                      alarmCount++;
                                   }
                                   break;

                               //Allarme attivo se il valore attuale è uguale alla soglia (errore sui float = 0.1% la distanza dalla soglia rispetto alla soglia stessa)
                               case '=':
                                   deltaPerc = (delta / threshold[i])*100;
                                   if(deltaPerc < 0.01)
                                   {
                                       alarms[i] = true;
                                       alarmCount++;
                                   }
                                   break;    

                               //Non gestiamo altri operatori 
                               default:
                                   break;
                            }
                        }
                        
                        rowColorHex = "<?= $_GET['color'] ?>";
                        rowColorRgb = hexToRgb(rowColorHex);
                        var rowColor = "linear-gradient(to right, rgba(" + rowColorRgb + ",0.6), rgba(" + rowColorRgb + ",1))";
                        
                        var rowHeight = parseInt(($('#<?= $_GET['name'] ?>_content').height() / 3) - 5);
                        var newRow = $("<div class='row_data_line'></div>");
                        newRow.css("height", rowHeight + "px");
                        var marginBottom = Math.ceil(($('#<?= $_GET['name'] ?>_content').height() - 3 * rowHeight) / 2);
                        if(i !== 2)
                        {
                            newRow.css("margin-bottom", marginBottom + "px");
                        }
                        else
                        {
                            newRow.css("margin-bottom", "0px");
                        }
                        
                        var newWhiteBackground = $("<div class='rowWhiteBackground'></div>");
                        var newDescContainer = $("<div class='row_data_desc'>" + metricData.data[i].commit.author.descrip + "</div>");
                        newWhiteBackground.append(newDescContainer);
                        newDescContainer.css("background", rowColor);
                        newDescContainer.css("line-height", rowHeight + "px")
                        newRow.append(newWhiteBackground);
                        var newValueContainer = $("<div class='row_data_value'><div class='alarmDivGc'>" + value + " " + udm  + "</div></div>");
                        newValueContainer.css("background", rowColor);
                        newValueContainer.css("line-height", rowHeight + "px");
                        newWhiteBackground.append(newValueContainer);
                        newRow.append(newWhiteBackground);
                        newRow.attr("title", metricData.data[i].commit.author.descrip);
                        newRow.css("fontSize", fontSize);
                        $("#<?= $_GET['name'] ?>_chartContainer").append(newRow);
                        
                        if(alarms[i])
                        {
                            $(newValueContainer).children(".alarmDivGc").addClass("alarmDivGcActive");
                        }
                    }
                    //Fine eventuale codice ad hoc basato sui dati della metrica
                },
                error: function () 
                {
                    showWidgetContent(widgetName);
                    $('#<?= $_GET['name'] ?>_noDataAlert').show();
                }
            });//Fine AJAX    
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
            <div id="<?= $_GET['name'] ?>_chartContainer" class="chartContainer"></div>
        </div>
    </div>	
</div> 