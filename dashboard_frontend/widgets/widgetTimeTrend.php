<?php
/* Dashboard Builder.
   Copyright (C) 2016 DISIT Lab http://www.disit.org - University of Florence

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
                
        var widgetName = "<?= $_GET['name'] ?>";       
        var hostFile = "<?= $_GET['hostFile'] ?>";
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
            rangeMin, rangeMax, widgetParameters, sizeRowsWidget, desc, plotArray, value, day, dayParts, timeParts, date, maxValue, nInterval, alarmSet = null;
        var metricId = "<?= $_GET['metric'] ?>";
        var elToEmpty = $("#<?= $_GET['name'] ?>_chartContainer");
        var url = "<?= $_GET['link_w'] ?>";
        var range="<?= $_GET['tmprange'] ?>"; 
        var seriesData = [];
        var valuesData = [];
        
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
                data: {"IdMisura": ["<?= $_GET['metric'] ?>"], "time": "<?= $_GET['tmprange'] ?>", "compare": 0},
                type: "GET",
                async: true,
                dataType: 'json',
                success: function(metricData) 
                {       
                    if(metricData.data.length > 0)
                    {
                        desc = metricData.data[0].commit.author.descrip;
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

                            if('<?= $_GET['tmprange']=='1/DAY' || explode("/",$_GET['tmprange'])[1] =='HOUR' ?>' == '1') 
                            {
                                timeParts = day.substr(day.indexOf(' ') + 1, 5).split(':');
                                date = Date.UTC(dayParts[0], dayParts[1]-1, dayParts[2], timeParts[0], timeParts[1]);
                            }
                            else 
                            {
                                date = Date.UTC(dayParts[0], dayParts[1] - 1, dayParts[2]);
                            }

                            seriesData.push([date, value]);
                            valuesData.push(value);
                        }

                        maxValue = Math.max.apply(Math, valuesData);
                        nInterval = parseFloat((maxValue / 4).toFixed(1));

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
                                      //alarmSet = true;
                                   }
                                   break;

                               //Allarme attivo se il valore attuale è sopra la soglia
                               case '>':
                                   if(value > threshold)
                                   {
                                      //alarmSet = true;
                                   }
                                   break;

                               //Allarme attivo se il valore attuale è uguale alla soglia (errore sui float = 0.1% la distanza dalla soglia rispetto alla soglia stessa)
                               case '=':
                                   deltaPerc = (delta / threshold)*100;
                                   if(deltaPerc < 0.01)
                                   {
                                       //alarmSet = true;
                                   }
                                   break;    

                               //Non gestiamo altri operatori 
                               default:
                                   break;
                            }
                        }
                        
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
                                backgroundColor: '<?= $_GET['color'] ?>'
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
                                max: maxValue,
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
                                valueSuffix: ''
                            },
                            series: [{
                                    showInLegend: false,
                                    name: '<?= $_GET['metric'] ?>',
                                    data: seriesData
                                }]
                        });
                    }
                    else
                    {
                        showWidgetContent(widgetName);
                        $('#<?= $_GET['name'] ?>_noDataAlert').show();
                        console.log("Chiamata di getDataMetricsForTimeTrend.php OK ma nessun dato restituito.");
                    }
                },
                error: function()
                {
                    showWidgetContent(widgetName);
                    $('#<?= $_GET['name'] ?>_noDataAlert').show();
                    console.log("Errore in chiamata di getDataMetricsForTimeTrend.php.");
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
                <a id ="info_modal" href="#" class="info_source"><img id="source_<?= $_GET['name'] ?>" src="../management/img/info.png" class="source_button"></a>
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