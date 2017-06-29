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
?>

<script type="text/javascript">
    var colors = {
      GREEN: '#008800',
      ORANGE: '#FF9933',
      LOW_YELLOW: '#ffffcc',
      RED: '#FF0000'
    };

    $(document).ready(function <?= $_GET['name'] ?>(firstLoad) 
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
            rangeMin, rangeMax, widgetParameters, minGauge, maxGauge, shownValue, plotBandSet, paneObj, yObj, solidGaugeObj, alarmSet = null;
        var metricId = "<?= $_GET['metric'] ?>";
        var elToEmpty = $("#<?= $_GET['name'] ?>_chartContainer");
        var url = "<?= $_GET['link_w'] ?>";
        var areaColors = new Array();
        var pattern = /Percentuale\//;
        
        if(url === "null")
        {
            url = null;
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
        setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor);
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
            widgetParameters = JSON.parse(widgetProperties.param.parameters);
            if(widgetParameters !== null)
            {
                if((widgetParameters.rangeMin !== null) && (widgetParameters.rangeMin !== "") && (typeof widgetParameters.rangeMin !== "undefined"))
                {
                    rangeMin = widgetParameters.rangeMin;
                }
                else
                {
                    rangeMin = null;
                }

                if((widgetParameters.rangeMax !== null) && (widgetParameters.rangeMax !== "") && (typeof widgetParameters.rangeMax !== "undefined"))
                {
                    rangeMax = widgetParameters.rangeMax;
                }
                else
                {
                    rangeMax = null;
                }

                //I colori andranno spostati in styleParameters appena esponiamo la loro gestione sul form add/edit widget
                if((widgetParameters.color1 !== null) && (widgetParameters.color1 !== "") && (typeof widgetParameters.color1 !== "undefined")) 
                {
                    areaColors[0] = widgetParameters.color1;
                }
                else
                {
                    areaColors[0] = colors.GREEN; 
                }

                if((widgetParameters.color2 !== null) && (widgetParameters.color2 !== "") && (typeof widgetParameters.color2 !== "undefined")) 
                {
                    areaColors[1] = widgetParameters.color2;
                }
                else
                {
                    areaColors[1] = colors.ORANGE;
                }

                if((widgetParameters.color3 !== null) && (widgetParameters.color3 !== "") && (typeof widgetParameters.color3 !== "undefined")) 
                {
                    areaColors[2] = widgetParameters.color3;
                }
                else
                {
                    areaColors[2] = colors.RED;
                }
            }
            else
            {
                areaColors[0] = colors.GREEN;
                areaColors[1] = colors.ORANGE;
                areaColors[2] = colors.RED;
                rangeMin = null;
                rangeMax = null;
            }
            
            //Fine eventuale codice ad hoc basato sulle proprietà del widget
            metricData = getMetricData(metricId);
            if(metricData !== null)
            {
                if(metricData.data[0] !== 'undefined')
                {
                    if(metricData.data.length > 0)
                    {
                        /*Inizio eventuale codice ad hoc basato sui dati della metrica*/
                        metricType = metricData.data[0].commit.author.metricType;
                        threshold = parseInt(metricData.data[0].commit.author.threshold);
                        thresholdEval = metricData.data[0].commit.author.thresholdEval;

                        if(pattern.test(metricType))
                        {
                            minGauge = 0;
                            maxGauge = parseInt(metricType.substring(12));
                            if(metricData.data[0].commit.author.quant_perc1 !== null)
                            {
                                shownValue = parseFloat(parseFloat(msg.data[0].commit.author.quant_perc1).toFixed(1));
                            }
                        }
                        else
                        {
                            switch(metricType)
                            {
                                case "Intero":
                                    if(metricData.data[0].commit.author.value_num !== null)
                                    {
                                        shownValue = parseInt(metricData.data[0].commit.author.value_num);
                                    }
                                    
                                    if((rangeMin !== null) && (rangeMax !== null))
                                    {
                                        minGauge = parseInt(rangeMin);
                                        maxGauge = parseInt(rangeMax);
                                    }
                                    else
                                    {
                                        minGauge = 0;
                                        maxGauge = shownValue;
                                    }
                                    udm = "";
                                    break;

                                case "Float":
                                    if(metricData.data[0].commit.author.value_num !== null)
                                    {
                                        shownValue = parseFloat(parseFloat(metricData.data[0].commit.author.value_num).toFixed(1));
                                    }
                                
                                    if((rangeMin !== null) && (rangeMax !== null))
                                    {
                                        minGauge = parseInt(rangeMin);
                                        maxGauge = parseInt(rangeMax);
                                    }
                                    else
                                    {
                                        minGauge = 0;
                                        maxGauge = shownValue;
                                    }
                                    udm = "";
                                    break;

                                case "Percentuale":
                                    minGauge = 0;
                                    maxGauge = 100;
                                    udm = "%";
                                    if(metricData.data[0].commit.author.value_perc1 !== null)
                                    {
                                        shownValue = parseFloat(parseFloat(metricData.data[0].commit.author.value_perc1).toFixed(1));
                                        if(shownValue > 100)
                                        {
                                            shownValue = 100;
                                        }
                                    }
                                    break;
                            }
                        }

                        if((threshold === null) || (thresholdEval === null))
                        {
                            //In questo caso non mostriamo soglia d'allarme.
                            threshold = minGauge;
                            stopsArray = [
                                [0.0, areaColors[0]]  
                            ];
                        }
                        else
                        {
                            delta = Math.abs(shownValue - threshold);
                            //Distinguiamo in base all'operatore di confronto
                            switch(thresholdEval)
                            {
                               //Allarme attivo se il valore attuale è sotto la soglia
                               case '<':
                                   if(shownValue < threshold)
                                   {
                                      //Allarme
                                      stopsArray = [
                                           [0.0, areaColors[2]]
                                      ];
                                      alarmSet = true;
                                   }
                                   else
                                   {
                                        //Green
                                        stopsArray = [
                                            [0.0, areaColors[0]]
                                        ];
                                   }
                                   plotBandSet = [{
                                       color: 'yellow', 
                                       from: 0, 
                                       to: threshold, 
                                       innerRadius: "100%",
                                       outerRadius: "110%",
                                   }];
                                   break;

                               //Allarme attivo se il valore attuale è sopra la soglia
                               case '>':
                                   if(shownValue > threshold)
                                   {
                                      //Allarme
                                      stopsArray = [
                                           [0.0, areaColors[2]]
                                      ];
                                      alarmSet = true;
                                   }
                                   else
                                   {
                                        stopsArray = [
                                            [0.0, areaColors[0]]
                                        ];
                                   }

                                   plotBandSet = [{
                                       color: 'yellow', 
                                       from: threshold, 
                                       to: maxGauge, 
                                       innerRadius: "100%",
                                       outerRadius: "110%",
                                   }];
                                   break;

                               //Allarme attivo se il valore attuale è uguale alla soglia (errore sui float = 0.01%)
                               case '=':
                                   deltaPerc = ((delta / threshold)*100);
                                   if(deltaPerc < 0.01)
                                   {
                                       //Allarme
                                       alarmSet = true;
                                       stopsArray = [
                                               [0.0, areaColors[2]]
                                           ];   
                                   }
                                   else
                                   {
                                      stopsArray = [
                                               [0.0, areaColors[0]]
                                           ]; 
                                   }

                                   var increment = parseInt(threshold*0.03);
                                   var fromVal = threshold-increment;
                                   var toVal = parseInt(threshold) + increment;

                                   plotBandSet = [{
                                       color: 'yellow',
                                       from: fromVal,
                                       to: toVal,
                                       innerRadius: "100%",
                                       outerRadius: "110%",
                                   }];
                                   break;    

                               //Non gestiamo altri operatori 
                               default:
                                   threshold = 0;
                                   stopsArray = [
                                               [0.0, areaColors[0]]
                                           ];
                                   break;
                            }
                        }

                        paneObj = {
                            center: ['50%', '85%'],
                            size: '100%',
                            startAngle: -90,
                            endAngle: 90,
                            background: {
                                backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || '#EEE',
                                innerRadius: '60%',
                                outerRadius: '100%',
                                shape: 'arc'
                            }
                        };
                        yObj = {
                            stops: stopsArray,
                            lineWidth: 0,
                            minorTickWidth: 0,
                            tickPixelInterval: 400,
                            tickWidth: 0,
                            title: {
                                y: -70
                            },
                            labels: {
                                y: 12,
                                distance: -12
                            },
                            plotBands: plotBandSet
                        };
                        solidGaugeObj = {
                                dataLabels: {
                                    y: 12,
                                    borderWidth: 0,
                                    useHTML: true
                                }
                            };
                        seriesObj = [{
                            data: [shownValue],
                            dataLabels: {
                                format: '<div style="text-align:center"><span style="font-size:16px;color:' +
                                    ((Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black') + '">{y}</span>' +
                                       '<span style="font-size:12px;color:black; display:inline"> ' + udm + '</span></div>'
                            }
                        }];

                        //Creazione oggetto gaugeOptions per settare l'aspetto del diagramma.
                        var gaugeOptions = {
                            chart: {
                               backgroundColor: '<?= $_GET['color'] ?>',
                               type: 'solidgauge'
                            },
                            title: null,
                            pane: paneObj,
                            tooltip: {
                               enabled: false
                            },
                            xAxis: {

                            }, 
                            yAxis: yObj,
                            plotOptions: {
                               solidgauge: solidGaugeObj
                            }
                        };

                        if(firstLoad !== false)
                        {
                            showWidgetContent(widgetName);
                        }
                        else
                        {
                            elToEmpty.empty();
                        }

                        //Disegno del diagramma
                        $('#<?= $_GET['name'] ?>_content').highcharts(Highcharts.merge(gaugeOptions, {
                            yAxis: {
                                min: minGauge,
                                max: maxGauge,
                                tickPosition: 'outside',
                                tickPositioner:  function() {
                                    return [minGauge, maxGauge];
                                }   
                            },
                            credits: {
                                enabled: false
                            },
                            series: seriesObj,
                            exporting: {
                                enabled: false
                            }
                        }));
                    }
                    else
                    {
                        showWidgetContent(widgetName);
			$('#<?= $_GET['name'] ?>_noDataAlert').show();
                    }
                }
                else
                {   
                    showWidgetContent(widgetName);
                    $('#<?= $_GET['name'] ?>_noDataAlert').show();
                }
                /*Fine eventuale codice ad hoc basato sui dati della metrica*/
            }
            else
            {
                showWidgetContent(widgetName);
                $('#<?= $_GET['name'] ?>_noDataAlert').show();
            } 
        }
        else
        {
            alert("Error while loading widget properties");
        }
        startCountdown(widgetName, timeToReload, <?= $_GET['name'] ?>, elToEmpty, "widgetGaugeChart", null, null);
    });//Fine document ready
</script>

<div class="widget" id="<?= $_GET['name'] ?>_div">
    <div class='ui-widget-content'>
        <div id='<?= $_GET['name'] ?>_header' class="widgetHeader">
            <div id="<?= $_GET['name'] ?>_infoButtonDiv" class="infoButtonContainer">
                <!--<a id ="info_modal" href="#" class="info_source"><img id="source_<?= $_GET['name'] ?>" src="../management/img/info.png" class="source_button"></a>-->
               <a id ="info_modal" href="#" class="info_source"><i id="source_<?= $_GET['name'] ?>" class="source_button fa fa-info-circle" style="font-size: 22px"></i></a>
            </div>    
            <div id="<?= $_GET['name'] ?>_titleDiv" class="titleDiv"></div>
            <div id="<?= $_GET['name'] ?>_buttonsDiv" class="buttonsContainer">
                <a class="icon-cfg-widget" href="#"><span class="glyphicon glyphicon-cog glyphicon-modify-widget" aria-hidden="true"></span></a>
                <a class="icon-remove-widget" href="#"><span class="glyphicon glyphicon-remove glyphicon-modify-widget" aria-hidden="true"></span></a>
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

