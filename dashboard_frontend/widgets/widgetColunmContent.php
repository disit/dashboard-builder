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

<script type='text/javascript'>
    var colors = {
      GREEN: '#008800',
      ORANGE: '#FF9933',
      LOW_YELLOW: '#ffffcc',
      RED: '#FF0000'
    };
    var parameters = {};
    var barColors = {};
    var barColors = {};

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
            rangeMin, rangeMax, widgetParameters, alarmSet = null;
        var metricId = "<?= $_GET['metric'] ?>";
        var elToEmpty = $("#<?= $_GET['name'] ?>_chartContainer");
        var url = "<?= $_GET['link_w'] ?>";
        var barColors = new Array();
        
        
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
            widgetParameters = widgetProperties.param.parameters;

            if(widgetParameters !== null)
            {
                if((widgetParameters.rangeMin !== null) && (widgetParameters.rangeMin !== "") && (typeof widgetParameters.rangeMin !== "undefined"))
                {
                    rangeMin = widgetParameters.rangeMin;
                }

                if((widgetParameters.rangeMax !== null) && (widgetParameters.rangeMax !== "") && (typeof widgetParameters.rangeMax !== "undefined"))
                {
                    rangeMax = widgetParameters.rangeMax;
                }

                //I colori andranno spostati in styleParameters appena esponiamo la loro gestione sul form add/edit widget
                if((widgetParameters.color1 !== null) && (widgetParameters.color1 !== "") && (typeof widgetParameters.color1 !== "undefined")) 
                {
                    barColors[0] = widgetParameters.color1;
                }
                else
                {
                    barColors[0] = colors.GREEN; 
                }

                if((widgetParameters.color2 !== null) && (widgetParameters.color2 !== "") && (typeof widgetParameters.color2 !== "undefined")) 
                {
                    barColors[1] = widgetParameters.color2;
                }
                else
                {
                    barColors[1] = colors.LOW_YELLOW;
                }
            }
            else 
            {
                barColors[0] = colors.GREEN;
                barColors[1] = colors.LOW_YELLOW; 
            }
            
            //Fine eventuale codice ad hoc basato sulle proprietà del widget
            metricData = getMetricData(metricId);
            if(metricData !== null)
            {
                if(metricData.data[0] !== undefined)
                {
                    if(metricData.data.length > 0)
                    {
                        /*Inizio eventuale codice ad hoc basato sui dati della metrica*/
                        var pattern = /Percentuale\//;
                        var metricType = metricData.data[0].commit.author.metricType;
                        var threshold = parseInt(metricData.data[0].commit.author.threshold);
                        var thresholdEval = metricData.data[0].commit.author.thresholdEval;
                        var seriesMainData = [];
                        var seriesComplData = [];
                        var minGauge = null;
                        var maxGauge = null;
                        var yAxisObj = null;
                        var shownValue = null;
                        var shownValueCompl = null;
                        var udm = "";
                        var plotLineObj = [{
                                            color: '#000000', 
                                            dashStyle: 'shortdash', 
                                            value: threshold, 
                                            width: 1,
                                            zIndex: 5
                                    }];

                        if(pattern.test(metricType))
                        {
                            minGauge = 0;

                            if(metricData.data[0].commit.author.value_perc1 !== null)
                            {
                                maxGauge = 100;
                                udm = "%";
                                shownValue = parseFloat(parseFloat(metricData.data[0].commit.author.value_perc1).toFixed(1));
                                if(shownValue > 100)
                                {
                                    shownValue = 100;
                                }
                                shownValueCompl = maxGauge - shownValue;
                                shownValueCompl = parseFloat(parseFloat(shownValueCompl).toFixed(1));
                            }
                            else
                            {
                                maxGauge = parseInt(metricType.substring(12));
                                var part = parseFloat(parseFloat(metricData.data[0].commit.author.quant_perc1).toFixed(1));
                                shownValue = (part / maxGauge)*100;
                                shownValueCompl = maxGauge - shownValue;
                                shownValueCompl = parseFloat(parseFloat(shownValueCompl).toFixed(1));
                            }

                            yAxisObj = {
                                    visible: true,
                                    offset: 0,
                                    min: minGauge,
                                    max: maxGauge,
                                    //tickInterval: 25,
                                    tickPosition: 'inside',
                                    plotLines: plotLineObj,
                                    title: {
                                        text: ''
                                    }
                            };
                        }
                        else
                        {
                            switch(metricType)
                            {
                                case "Intero":
                                    if((rangeMin !== null) && (rangeMax !== null))
                                    {
                                        minGauge = parseInt(rangeMin);
                                        maxGauge = parseInt(rangeMax);
                                    }

                                    if(metricData.data[0].commit.author.value_num !== null)
                                    {
                                        shownValue = parseInt(metricData.data[0].commit.author.value_num);
                                        shownValueCompl = maxGauge - shownValue;
                                        shownValueCompl = parseInt(shownValueCompl);
                                        yAxisObj = {
                                            visible: true,
                                            offset: 0,
                                            min: minGauge,
                                            max: maxGauge,
                                            tickPosition: 'inside',
                                            plotLines: plotLineObj,
                                            title: {
                                                text: ''
                                            }
                                        };
                                    }
                                    break;

                                case "Float":
                                    if((rangeMin !== null) && (rangeMax !== null))
                                    {
                                        minGauge = parseInt(rangeMin);
                                        maxGauge = parseInt(rangeMax);    
                                    }

                                    if(metricData.data[0].commit.author.value_num !== null)
                                    {
                                        shownValue = parseFloat(parseFloat(metricData.data[0].commit.author.value_num).toFixed(1));
                                        shownValueCompl = maxGauge - shownValue;
                                        shownValueCompl = parseFloat(parseFloat(shownValueCompl).toFixed(1));
                                        yAxisObj = {
                                            visible: true,
                                            offset: 0,
                                            min: minGauge,
                                            max: maxGauge,
                                            //tickInterval: 25,
                                            tickPosition: 'inside',
                                            plotLines: plotLineObj,
                                            title: {
                                                text: ''
                                            }
                                        };
                                    }
                                    break;

                                case "Percentuale":
                                    minGauge = 0;
                                    maxGauge = 100;
                                    if(metricData.data[0].commit.author.value_perc1 !== null)
                                    {
                                        udm = "%";
                                        shownValue = parseFloat(parseFloat(metricData.data[0].commit.author.value_perc1).toFixed(1));
                                        if(shownValue > 100)
                                        {
                                            shownValue = 100;
                                        }
                                        shownValueCompl = maxGauge - shownValue;
                                        shownValueCompl = parseFloat(parseFloat(shownValueCompl).toFixed(1));
                                        yAxisObj = {
                                            visible: true,
                                            offset: 0,
                                            min: minGauge,
                                            max: maxGauge,
                                            tickInterval: 25,
                                            tickPosition: 'inside',
                                            plotLines: plotLineObj,
                                            title: {
                                                text: ''
                                            }
                                        };
                                    }
                                    break;

                                default:
                                    break;
                            }
                        }

                        if((shownValue !== null) && (minGauge !== null) && (maxGauge !== null))
                        {    
                            if((threshold === null) || (thresholdEval === null))
                            {
                                //In questo caso non mostriamo soglia d'allarme e mostriamo main verde.
                                plotLineObj = null;
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
                                          alarmSet = true;
                                       }
                                       break;

                                   //Allarme attivo se il valore attuale è sopra la soglia
                                   case '>':
                                       if(shownValue > threshold)
                                       {
                                          //Allarme
                                          alarmSet = true;
                                       }
                                       break;

                                   //Allarme attivo se il valore attuale è uguale alla soglia (errore sui float = 0.1%)
                                   case '=':
                                       if(delta <= 0.1)
                                       {
                                           //Allarme
                                           alarmSet = true;
                                       }
                                       break;    

                                   //Non gestiamo altri operatori 
                                   default:
                                       break;
                                }
                            }    

                            var desc = metricData.data[0].commit.author.descrip;
                            seriesMainData.push(['Green', shownValue]);
                            seriesComplData.push(['Red', shownValueCompl]);

                            yAxisObj = {
                                visible: true,
                                offset: 0,
                                min: minGauge,
                                max: maxGauge,
                                tickPosition: 'inside',
                                plotLines: plotLineObj,
                                title: {
                                    text: ''
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
                            $('#<?= $_GET['name'] ?>_content').highcharts({
                                credits: {
                                    enabled: false
                                },
                                exporting: {
                                    enabled: false
                                },
                                chart: {
                                    type: 'column',
                                    backgroundColor: '<?= $_GET['color'] ?>',
                                    spacingBottom: 10,
                                    spacingTop: 10
                                },
                                title: {
                                    text: ''
                                },
                                xAxis: {
                                    visible: false
                                },
                                yAxis: yAxisObj,
                                tooltip: {
                                    pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br/>',
                                    shared: true,
                                    enabled: false
                                },
                                plotOptions: {
                                    column: {
                                        stacking: 'normal',
                                        dataLabels: {
                                            formatter: function () {
                                                return this.y + udm;
                                            },
                                            enabled: true,
                                            color: fontColor,
                                            style: {
                                                fontFamily: 'Verdana',
                                                fontWeight: 'bold',
                                                fontSize: fontSize + "px",
                                                "textOutline": "1px 1px contrast",
                                                "text-shadow": "1px 1px 1px rgba(0,0,0,0.3)"
                                            }
                                        }
                                    }
                                },
                                series: [
                                    {
                                        showInLegend: false,
                                        name: '<?= $_GET['metric'] ?>',
                                        color: barColors[1],
                                        data: seriesComplData,
                                        pointWidth: 600
                                    },
                                    {
                                        showInLegend: false,
                                        name: '<?= $_GET['metric'] ?>',
                                        color: barColors[0],
                                        data: seriesMainData,
                                        pointWidth: 600
                                    }
                                    ]
                            }); //FINE HIGHCHARTS
                        }   
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
        startCountdown(widgetName, timeToReload, <?= $_GET['name'] ?>, elToEmpty,"widgetColumnContent", null, null);
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