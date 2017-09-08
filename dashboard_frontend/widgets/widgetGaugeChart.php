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
        var widgetWidth = "<?= $_GET['sizeX'] ?>";
        var widgetHeight = "<?= $_GET['sizeY'] ?>";
        var timeToReload = <?= $_GET['freq'] ?>;
        var widgetProperties, infoJson, styleParameters, metricType, metricData, pattern, udm, seriesObj, widgetParameters, minGauge, maxGauge, shownValue, plotBands, 
            plotBandObj, paneObj, yObj, solidGaugeObj, alarmSet, labelsObj, labelObj, sizeRows, sizeCols, hasNegativeValues = null;
        var metricId = "<?= $_GET['metric'] ?>";
        var elToEmpty = $("#<?= $_GET['name'] ?>_chartContainer");
        var url = "<?= $_GET['link_w'] ?>";
        var areaColors = new Array();
        var pattern = /Percentuale\//;
        var thresholdObject = null;
        
        
        if(url === "null")
        {
            url = null;
        }
        
        //Definizioni di funzione specifiche del widget
        
        //Restituisce il JSON delle info se presente, altrimenti NULL
        function getInfoJson()
        {
            var infoJson = null;
            if(jQuery.parseJSON(widgetProperties.param.infoJson !== null))
            {
                infoJson = jQuery.parseJSON(widgetProperties.param.infoJson); 
            }
            
            return infoJson;
        }
        
        //Restituisce il JSON delle info se presente, altrimenti NULL
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
            sizeRows = widgetProperties.param.size_rows;
            sizeCols = widgetProperties.param.size_columns;
            
            styleParameters = getStyleParameters();//Restituisce null finché non si usa il campo per questo widget
            widgetParameters = JSON.parse(widgetProperties.param.parameters);
            if(widgetParameters !== null)
            {
               widgetParameters = JSON.parse(widgetProperties.param.parameters);
               if(widgetParameters.hasOwnProperty("thresholdObject"))
               {
                  thresholdObject = widgetParameters.thresholdObject; 
               }
            }
            
            //Fine eventuale codice ad hoc basato sulle proprietà del widget
            metricData = getMetricData(metricId);
            if(metricData !== null)
            {
                if(metricData.data[0] !== 'undefined')
                {
                    if(metricData.data.length > 0)
                    {
                        //Inizio eventuale codice ad hoc basato sui dati della metrica
                        metricType = metricData.data[0].commit.author.metricType;
                        hasNegativeValues = parseInt(metricData.data[0].commit.author.hasNegativeValues);

                        if(pattern.test(metricType))
                        {
                            minGauge = 0;
                            maxGauge = parseInt(metricType.substring(12));
                            if(metricData.data[0].commit.author.quant_perc1 !== null)
                            {
                                shownValue = parseFloat(parseFloat(metricData.data[0].commit.author.quant_perc1).toFixed(1));
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
                                    
                                    if(hasNegativeValues === 0)
                                    {
                                       minGauge = 0;
                                    }
                                    else
                                    {
                                       minGauge = Math.floor(shownValue - (Math.random() * 25) - 10);
                                    }
                                    
                                    if(minGauge > 0)
                                    {
                                       minGauge = 0;
                                    }
                                    
                                    maxGauge = Math.floor(shownValue + (Math.random() * 25) + 10);
                                    udm = "";
                                    break;

                                case "Float":
                                    if(metricData.data[0].commit.author.value_num !== null)
                                    {
                                        shownValue = parseFloat(parseFloat(metricData.data[0].commit.author.value_num).toFixed(1));
                                    }
                                
                                    if(hasNegativeValues === 0)
                                    {
                                       minGauge = 0;
                                    }
                                    else
                                    {
                                       minGauge = Math.floor(shownValue - (Math.random() * 25) - 10);
                                    }
                                    
                                    if(minGauge > 0)
                                    {
                                       minGauge = 0;
                                    }
                                    
                                    maxGauge = shownValue + (Math.random() * 25) + 10;
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
                        
                        //Non cancellare - Da recuperare quando riabilitiamo e aggiorniamo il blink in caso d'allarme
                        /*if((threshold === null) || (thresholdEval === null))
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
                        }*/

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
                        
                       yObj =  {
                           /*stops: [
                             [0.8, 'red'],
                             [1.0, 'green']
                           ],*/
                           lineWidth: 0,
                           tickInterval: 10,
                           minorTickInterval: 2.5,
                           tickWidth: 0,
                           minorTickWidth: 0,
                           title: null,
                           labels: {
                             y: 12,
                             distance: -12
                           },
                           min: minGauge,
                           max: maxGauge,
                           plotBands: null,
                           title: null
                      };
                      
                        var labelFontSize = 12 * (widgetHeight / 7);
                           
                        //Costruzione degli stop e delle plot lines delle soglie
                        if((thresholdObject !== null) && (thresholdObject !== 'undefined'))
                        {
                           plotBands = [];
                           
                           labelsObj = {
                             items: []
                           };
                           
                           var op, op1, op2 = null;
                           
                           var labelsDiv = $('<div ></div>'); 
                           labelsObj.style = {
                              top: "0px"
                           };
                           
                           for(var i = 0; i < thresholdObject.length; i++) 
                           {
                              labelObj = {};
                              var labelDiv = null;
                              plotBandObj = null;//Va resettato ad ogni iterazione, per evitare di inserire bande colorate fuori scala e rovinare il widget
                              
                              //Semiretta sinistra
                              if(((thresholdObject[i].op === "less")||(thresholdObject[i].op === "lessEqual"))&&(parseFloat(thresholdObject[i].thr1) >= minGauge))
                              {
                                 if(thresholdObject[i].op === "less")
                                 {
                                    op = "<";
                                 }
                                 else
                                 {
                                    op = "<=";
                                 }
                                 
                                 if(thresholdObject[i].thr1 >= maxGauge)
                                 {
                                    plotBandObj  = {
                                       color: thresholdObject[i].color, 
                                       from: minGauge, 
                                       to: maxGauge, 
                                       innerRadius: "100%",
                                       outerRadius: "105%"
                                   };
                                 }
                                 else
                                 {
                                    plotBandObj  = {
                                       color: thresholdObject[i].color, 
                                       from: minGauge, 
                                       to: thresholdObject[i].thr1, 
                                       innerRadius: "100%",
                                       outerRadius: "105%"
                                   };
                                 }
                                 
                                 labelDiv = $('<div style="font-size: ' + labelFontSize + 'px; color: ' + thresholdObject[i].color + '">' + thresholdObject[i].desc + " " + op + " " + thresholdObject[i].thr1 + '</div><br>');
                              }
                              
                              //Semiretta destra
                              if(((thresholdObject[i].op === "greater")||(thresholdObject[i].op === "greaterEqual"))&&(parseFloat(thresholdObject[i].thr1) <= maxGauge))
                              {
                                 if(thresholdObject[i].op === "greater")
                                 {
                                    op = ">";
                                 }
                                 else
                                 {
                                    op = ">=";
                                 }
                                 
                                 if(thresholdObject[i].thr1 <= minGauge)
                                 {
                                    plotBandObj  = {
                                       color: thresholdObject[i].color, 
                                       from: minGauge, 
                                       to: maxGauge, 
                                       innerRadius: "100%",
                                       outerRadius: "105%"
                                    };
                                 }
                                 else
                                 {
                                    plotBandObj  = {
                                       color: thresholdObject[i].color, 
                                       from: thresholdObject[i].thr1, 
                                       to: maxGauge, 
                                       innerRadius: "100%",
                                       outerRadius: "105%"
                                   };
                                 }
                                 
                                 labelDiv = $('<div style="font-size: ' + labelFontSize + 'px; color: ' + thresholdObject[i].color + '">' + thresholdObject[i].desc + " " + op + " " + thresholdObject[i].thr1 + '</div><br>');
                              }
                              
                              if((thresholdObject[i].op === "equal")&&(parseFloat(thresholdObject[i].thr1) <= maxGauge)&&(parseFloat(thresholdObject[i].thr1) >= minGauge))
                              {
                                 op = "=";
                                 
                                 var minLine = parseFloat(thresholdObject[i].thr1) - Math.abs(maxGauge - minGauge)*0.0025;
                                 var maxLine = parseFloat(thresholdObject[i].thr1) + Math.abs(maxGauge - minGauge)*0.0025;       
                                 
                                 plotBandObj  = {
                                    color: thresholdObject[i].color, 
                                    from: minLine, 
                                    to: maxLine, 
                                    innerRadius: "100%",
                                    outerRadius: "105%"
                                 };
                                 
                                 labelDiv = $('<div style="font-size: ' + labelFontSize + 'px; color: ' + thresholdObject[i].color + '">' + thresholdObject[i].desc + " " + op + " " + thresholdObject[i].thr1 + '</div><br>');
                              }
                              
                              if((thresholdObject[i].op === "notEqual")&&(parseFloat(thresholdObject[i].thr1) <= maxGauge)&&(parseFloat(thresholdObject[i].thr1) >= minGauge))
                              {
                                 op = "!=";
                                 
                                 var minLine = parseFloat(thresholdObject[i].thr1) - Math.abs(maxGauge - minGauge)*0.0025;
                                 var maxLine = parseFloat(thresholdObject[i].thr1) + Math.abs(maxGauge - minGauge)*0.0025;       
                                 
                                 plotBandObj  = {
                                    color: thresholdObject[i].color, 
                                    from: minLine, 
                                    to: maxLine, 
                                    innerRadius: "100%",
                                    outerRadius: "105%"
                                 };
                                 
                                 labelDiv = $('<div style="font-size: ' + labelFontSize + 'px; color: ' + thresholdObject[i].color + '">' + thresholdObject[i].desc + " " + op + " " + thresholdObject[i].thr1 + '</div><br>');
                              }
                              
                              //Intervallo bi-limitato
                              if(((thresholdObject[i].op === "intervalOpen")||(thresholdObject[i].op === "intervalClosed")||(thresholdObject[i].op === "intervalLeftOpen")||(thresholdObject[i].op === "intervalRightOpen"))&&(!((parseFloat(thresholdObject[i].thr1) <= minGauge)&&(parseFloat(thresholdObject[i].thr2) <= minGauge)))&&(!((parseFloat(thresholdObject[i].thr1) >= maxGauge)&&(parseFloat(thresholdObject[i].thr2) >= maxGauge))))
                              {
                                 switch(thresholdObject[i].op)
                                 {
                                    case "intervalOpen":
                                       op1 = "<"; //Alla rovescia rispetto al normale per mostrarlo correttamente nelle label
                                       op2 = "<";
                                       break;

                                    case "intervalClosed":
                                       op1 = "<="; //Alla rovescia rispetto al normale per mostrarlo correttamente nelle label
                                       op2 = "<=";
                                       break;

                                    case "intervalLeftOpen":
                                       op1 = "<"; //Alla rovescia rispetto al normale per mostrarlo correttamente nelle label
                                       op2 = "<=";
                                       break;

                                    case "intervalRightOpen":
                                       op1 = "<="; //Alla rovescia rispetto al normale per mostrarlo correttamente nelle label
                                       op2 = "<";
                                       break;   
                                 }
                                 
                                 
                                 //Sforamento inferiore
                                 if((parseFloat(thresholdObject[i].thr1) <= minGauge)&&(parseFloat(thresholdObject[i].max) < maxGauge))
                                 {
                                    plotBandObj  = {
                                       color: thresholdObject[i].color, 
                                       from: minGauge, 
                                       to: thresholdObject[i].thr2, 
                                       innerRadius: "100%",
                                       outerRadius: "105%"
                                   };
                                 }
                                 
                                 //Sforamento superiore
                                 if((parseFloat(thresholdObject[i].thr2) >= maxGauge)&&(parseFloat(thresholdObject[i].thr1) >= minGauge))
                                 {
                                    plotBandObj  = {
                                       color: thresholdObject[i].color, 
                                       from: thresholdObject[i].thr1, 
                                       to: maxGauge, 
                                       innerRadius: "100%",
                                       outerRadius: "105%"
                                   };
                                 }
                                 
                                 //Sforamento da ambo i lati
                                 if((parseFloat(thresholdObject[i].thr2) >= maxGauge)&&(parseFloat(thresholdObject[i].thr1) <= minGauge))
                                 {
                                    plotBandObj  = {
                                       color: thresholdObject[i].color, 
                                       from: minGauge, 
                                       to: maxGauge, 
                                       innerRadius: "100%",
                                       outerRadius: "105%"
                                   };
                                 }
                                 
                                 //Nessun sforamento
                                 if((parseFloat(thresholdObject[i].thr2) < maxGauge)&&(parseFloat(thresholdObject[i].thr1) > minGauge))
                                 {
                                    plotBandObj  = {
                                       color: thresholdObject[i].color, 
                                       from: thresholdObject[i].thr1, 
                                       to: thresholdObject[i].thr2, 
                                       innerRadius: "100%",
                                       outerRadius: "105%"
                                   };
                                 }
                                 
                                 labelDiv = $('<div style="font-size: ' + labelFontSize + 'px; color: ' + thresholdObject[i].color + '">' + thresholdObject[i].thr1 + " " + op1 + " " + thresholdObject[i].desc + " " + op2 + " " + thresholdObject[i].thr2 + '</div><br>');
                                 //In tutti gli altri casi (cioé con la banda interamente sopra il massimo o interamente sotto il minimo) non disegnamo banda colorata.
                              }
                              
                              if(plotBandObj !== null)
                              {
                                 plotBands.push(plotBandObj);
                              }
                              
                              labelsDiv.append(labelDiv);
                           }
                           
                           labelObj.html = labelsDiv.html();
                           labelsObj.items.push(labelObj);
                           yObj.plotBands = plotBands;
                        }
                        else
                        {
                           labelsObj = {
                             items: null
                           };
                        }
                        
                        var dataLabelFontSize; 
                        
                        if(sizeRows > sizeCols)
                        {
                           dataLabelFontSize = 13 * (sizeCols / 5);
                        }
                        else
                        {
                           dataLabelFontSize = 13 * (sizeRows / 5);
                        }
                        
                        if(dataLabelFontSize < 13)
                        {
                           dataLabelFontSize = 13;
                        }
                        
                        var dataLabelUdmFontSize = dataLabelFontSize - 3;
                        
                        solidGaugeObj = {
                           dataLabels: {
                               y: 18,
                               borderWidth: 0,
                               useHTML: false
                           }
                        };
                            
                        seriesObj = [{
                            data: [shownValue],
                            dataLabels: {
                                format: '<div style="text-align:center"><span style="font-size:' + dataLabelFontSize + 'px;color:' +
                                    ((Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black') + '">{y}</span>' +
                                       '<span style="font-size:' + dataLabelUdmFontSize + 'px;color:black; display:inline"> ' + udm + '</span></div>'
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
                            },
                            labels: labelsObj        
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

