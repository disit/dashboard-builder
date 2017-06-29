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
        var widgetPropertiesString, widgetProperties, thresholdObject, infoJson, styleParameters, metricType, pattern, totValues, shownValues, 
            descriptions, udm, threshold, thresholdEval, stopsArray, delta, deltaPerc, seriesObj, dataObj, pieObj, legendLength,
            rangeMin, rangeMax, widgetParameters, sizeRowsWidget, desc, plotArray, value, day, dayParts, timeParts, date, maxValue1, maxValue2, nInterval,
            valueAtt, valuePrec, alarmSet = null;
        var metricId = "<?= $_GET['metric'] ?>";
        var elToEmpty = $("#<?= $_GET['name'] ?>_chartContainer");
        var url = "<?= $_GET['link_w'] ?>";
        var range = "<?= $_GET['tmprange'] ?>"; 
        var seriesData1 = [];
        var valuesData1 = [];
        var seriesData2 = [];
        var valuesData2 = [];
        
        var unitsWidget = [['millisecond', // unit name
            [1, 2, 5, 10, 20, 25, 50, 100, 200, 500] // allowed multiples
        ], [
            'second',
            [1, 2, 5, 10, 15, 30]
        ], [
            'minute',
            [1, 2, 5, 10, 15, 30]
        ], [
            'hour',
            [1, 2, 3, 4, 6, 8, 12]
        ], [
            'day',
            [1]
        ], [
            'week',
            [1]
        ], [
            'month',
            [1, 3, 4, 6, 8, 10, 12]
        ], [
            'year',
            null
        ]];
        
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
        
        if((widgetProperties !== null) && (widgetProperties !== ''))
        {
            //Inizio eventuale codice ad hoc basato sulle proprietà del widget
            styleParameters = getStyleParameters();//Restituisce null finché non si usa il campo per questo widget
            widgetParameters = widgetProperties.param.parameters;
            sizeRowsWidget = parseInt(widgetProperties.param.size_rows);
            
            //Per ora non usato
            /*if(widgetParameters !== null)
            {
                
            }
            else
            {
                
            }*/
            
            //Fine eventuale codice ad hoc basato sulle proprietà del widget
            $.ajax({
                url: "../widgets/getDataMetricsForTimeTrend.php",
                data: {"IdMisura": ["<?= $_GET['metric'] ?>"], "time": "<?= $_GET['tmprange'] ?>", "compare": 1},
                type: "GET",
                async: true,
                dataType: 'json',
                success: function(metricData) 
                {       
                    if(metricData.data.length > 0)
                    {
                        thresholdEval = metricData.data[0].commit.author.thresholdEval;
                        threshold = metricData.data[0].commit.author.threshold;
                        
                        for(var i = 0; i < metricData.data.length; i++) 
                        {
                            day = metricData.data[i].commit.author.computationDate;
                            if((metricData.data[i].commit.author.value !== null) && (metricData.data[i].commit.author.value !== "")) 
                            {
                                value = parseFloat(parseFloat(metricData.data[i].commit.author.value).toFixed(1));
                                flagNumeric = true;
                            } 
                            else if((metricData.data[i].commit.author.value_perc1 !== null) && (metricData.data[i].commit.author.value_perc1 !== "")) 
                            {
                                if (value >= 100) 
                                {
                                    value = parseFloat(parseFloat(metricData.data[i].commit.author.value_perc1).toFixed(0));
                                } 
                                else 
                                {
                                    value = parseFloat(parseFloat(metricData.data[i].commit.author.value_perc1).toFixed(1));
                                }
                                flagNumeric = true;
                            }
                            
                            dayParts = day.substring(0, day.indexOf(' ')).split('-');
                            timeParts = day.substr(day.indexOf(' ') + 1, 5).split(':');

                            if(metricData.data[i].commit.range_dates === 0) 
                            {
                                if (range === '1/DAY' || range.split("/")[1] === "HOUR")
                                {
                                    seriesData1.push([Date.UTC(dayParts[0], dayParts[1] - 1, dayParts[2], timeParts[0], timeParts[1]), value]);
                                } 
                                else
                                {
                                    seriesData1.push([Date.UTC(dayParts[0], dayParts[1] - 1, dayParts[2]), value]);
                                }
                                
                                valuesData1.push(value);
                            }
                            else 
                            {
                                if (range === '30/DAY') 
                                {
                                    seriesData2.push([Date.UTC(dayParts[0], dayParts[1], dayParts[2]), value]);
                                } 
                                else if (range === '7/DAY') 
                                {
                                    seriesData2.push([Date.UTC(dayParts[0], parseInt(dayParts[1]) - 1, parseInt(dayParts[2]) + 7), value]);
                                } 
                                else if (range === '365/DAY') 
                                {
                                    seriesData2.push([Date.UTC(parseInt(dayParts[0]) + 1, dayParts[1] - 1, dayParts[2]), value]);
                                } 
                                else if (range === '1/DAY' || range === '4/HOUR' || range === '12/HOUR') 
                                {
                                    seriesData2.push([Date.UTC(dayParts[0], dayParts[1] - 1, parseInt(dayParts[2]) + 1, timeParts[0], timeParts[1]), value]);
                                }
                                
                                valuesData2.push(value);
                            }
                        }

                        maxValue1 = parseFloat((Math.max.apply(Math, valuesData1)).toFixed(1));
                        maxValue2 = parseFloat((Math.max.apply(Math, valuesData2)).toFixed(1));
                        nInterval = parseFloat((Math.max(maxValue1, maxValue2) / 4).toFixed(1));

                        if(flagNumeric && (threshold !== null) && (thresholdEval !== null))
                        {
                            plotArray = [{
                               color: '#FF9933', 
                               dashStyle: 'shortdash', 
                               value: threshold, 
                               width: 2,
                               zIndex: 5
                            }];
                            delta = Math.abs(value - threshold);
                            //Distinguiamo in base all'operatore di confronto
                            switch(thresholdEval)
                            {
                               //Allarme attivo se il valore attuale è sotto la soglia
                               case '<':
                                   if(value < threshold)
                                   {
                                      alarmSet = true;
                                   }
                                   break;

                               //Allarme attivo se il valore attuale è sopra la soglia
                               case '>':
                                   if(value > threshold)
                                   {
                                      alarmSet = true;
                                   }
                                   break;

                               //Allarme attivo se il valore attuale è uguale alla soglia (errore sui float = 0.1% la distanza dalla soglia rispetto alla soglia stessa)
                               case '=':
                                   deltaPerc = (delta / threshold)*100;
                                   if(deltaPerc < 0.01)
                                   {
                                       alarmSet = true;
                                   }
                                   break;    

                               //Non gestiamo altri operatori 
                               default:
                                   break;
                            }
                        }//Errore o mancanza da copia/incolla?
                        
                        if(firstLoad !== false)
                        {
                            showWidgetContent(widgetName);
                        }
                        else
                        {
                            elToEmpty.empty();
                        }

                        //Disegno del diagramma
                        $('#<?= $_GET['name'] ?>_chartContainer').highcharts({
                            credits: {
                                enabled: false
                            },
                            chart: {
                                backgroundColor: '<?= $_GET['color'] ?>',
                                type: 'spline',
                                style: {
                                    fontFamily: 'Verdana'
                                }

                            },
                            exporting: {
                                enabled: false
                            },
                            title: {
                                text: ''

                            },
                            xAxis: {
                                type: 'datetime',
                                units: unitsWidget,
                                labels: {
                                    enabled: true,
                                    style: {
                                        fontFamily: 'Verdana',
                                        color: fontColor,
                                        fontSize: fontSize + "px",
                                        "text-shadow": "1px 1px 1px rgba(0,0,0,0.12)",
                                        "textOutline": "1px 1px contrast"
                                    }
                                }
                            },
                            yAxis: {
                                title: {
                                    text: ''
                                },
                                min: 0,
                                max: Math.max(maxValue1, maxValue2),
                                tickInterval: nInterval,
                                plotLines: plotArray,
                                labels: {
                                    enabled: true,
                                    style: {
                                        fontFamily: 'Verdana',
                                        color: fontColor,
                                        fontSize: fontSize + "px",
                                        "text-shadow": "1px 1px 1px rgba(0,0,0,0.12)",
                                        "textOutline": "1px 1px contrast"
                                    }
                                }
                            },
                            tooltip: {
                                //valueSuffix: 'Valori',
                                formatter: function () {
                                    var val2 = Highcharts.dateFormat('%a %d %b %H:%M:%S', this.x);
                                    var valX = new Date(this.x);
                                    var nameSerie = this.series.name;
                                    var mod = range;
                                    
                                    if(nameSerie === 'Previous') 
                                    {
                                        if (mod === '1/DAY') 
                                        {
                                            valueAtt = valX.getDate();
                                            valuePrec = valX.getDate() - 1;
                                            valX.setDate(valuePrec);
                                        } 
                                        else if (mod === '30/DAY')
                                        {
                                            valueAtt = valX.getMonth();
                                            valuePrec = valX.getMonth() - 1;
                                            valX.setMonth(valuePrec);
                                        } 
                                        else if (mod === '7/DAY') 
                                        {
                                            valueAtt = valX.getDate();
                                            valuePrec = valX.getDate() - 7;
                                            valX.setDate(valuePrec);
                                        } 
                                        else if (mod === '365/DAY') 
                                        {
                                            valueAtt = valX.getYear();
                                            valuePrec = valX.getYear() - 1;
                                            valX.setYear(valuePrec);
                                        } 
                                        else if (mod === '4/HOUR') 
                                        {
                                            valueAtt = valX.getHours();
                                            valuePrec = valX.getHours() - 4;
                                            valX.setHours(valuePrec);
                                        } 
                                        else if (mod === '12/HOUR') 
                                        {
                                            valueAtt = valX.getHours();
                                            valuePrec = valX.getHours() - 12;
                                            valX.setHours(valuePrec);
                                        }
                                    }
                                    return Highcharts.dateFormat('%A, %b %d, %H:%M', valX.getTime()) + '<br><span style="color:'+ this.color + '">\u25CF</span> ' + nameSerie + ': <b>' + this.y + '</b>';
                                },
                                headerFormat: '<b>{series.name}</b><br>',
                                pointFormat: '{point.x:%e. %b}: {point.y:.2f}'
                            },
                            plotOptions: {
                                series: {
                                    events: 
                                    {
                                        legendItemClick: function () {
                                            return false;
                                        }
                                    },
                                    lineWidth: 2
                                }
                            },
                            legend: {
                                layout: 'vertical',
                                align: 'right',
                                verticalAlign: 'middle',
                                borderWidth: 0,
                                margin: 2,
                                itemStyle: {
                                        fontFamily: 'Verdana',
                                        color: fontColor,
                                        fontSize: fontSize + "px",
                                        "text-shadow": "1px 1px 1px rgba(0,0,0,0.35)",
                                        "textOutline": "1px 1px contrast"
                                    }
                            },
                            series: [{
                                    showInLegend: true,
                                    name: "Previous",
                                    data: seriesData2,
                                    color: "#CECECE",
                                    zIndex: 999999

                                }, {
                                    showInLegend: true,
                                    name: "Current",
                                    data: seriesData1,
                                    //color: "#7cb5ea",
                                    color: "#33ccff",
                                    zIndex: 999999
                                }]
                        });
                    }
                    else
                    {
                        showWidgetContent(widgetName);
                        console.log("Chiamata di getDataMetricsForTimeTrend.php OK ma nessun dato restituito.");
                        $('#<?= $_GET['name'] ?>_noDataAlert').show();
                    }
                },
                error: function()
                {
                    showWidgetContent(widgetName);
                    console.log("Errore in chiamata di getDataMetricsForTimeTrend.php.");
                    $('#<?= $_GET['name'] ?>_noDataAlert').show();
                }
            });    
        }
        else
        {
            alert("Error while loading widget properties");
        }
        startCountdown(widgetName, timeToReload, <?= $_GET['name'] ?>, elToEmpty, "widgetTimeTrend", null, null);
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