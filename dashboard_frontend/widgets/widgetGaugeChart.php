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

<script type="text/javascript">
    var colors = {
      GREEN: '#008800',
      ORANGE: '#FF9933',
      LOW_YELLOW: '#ffffcc',
      RED: '#FF0000'
    };

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
            plotBandObj, paneObj, yObj, solidGaugeObj, alarmSet, labelsObj, labelObj, sizeRows, sizeCols, hasNegativeValues, metricName, widgetTitle, countdownRef, urlToCall = null;
        var metricName = "<?= $_GET['metric'] ?>";
        var elToEmpty = $("#<?= $_GET['name'] ?>_chartContainer");
        var url = "<?= $_GET['link_w'] ?>";
        var areaColors = new Array();
        var pattern = /Percentuale\//;
        var thresholdObject = null;
        var embedWidget = <?= $_GET['embedWidget'] ?>;
        var embedWidgetPolicy = '<?= $_GET['embedWidgetPolicy'] ?>';	
        var headerHeight = 25;
        var showTitle = "<?= $_GET['showTitle'] ?>";
	var showHeader = null;
        
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
        
        if((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null))
        {
            metricName = "<?= $_GET['metric'] ?>";
            widgetTitle = "<?= preg_replace($titlePatterns, $replacements, $title) ?>";
            widgetHeaderColor = "<?= $_GET['frame_color'] ?>";
            widgetHeaderFontColor = "<?= $_GET['headerFontColor'] ?>";
        }
        else
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
        $(document).on('changeMetricFromButton_' + widgetName, function(event) 
        {
            if((event.targetWidget === widgetName) && (event.newMetricName !== "noMetricChange"))
            {
                clearInterval(countdownRef); 
                $("#<?= $_GET['name'] ?>_content").hide();
                <?= $_GET['name'] ?>(true, event.newMetricName, event.newTargetTitle, event.newHeaderAndBorderColor, event.newHeaderFontColor, false, null, null, /*null,*/ null, null, null);
            }
        });
        
        $(document).off('mouseOverLastDataFromExternalContentGis_' + widgetName);
        $(document).on('mouseOverLastDataFromExternalContentGis_' + widgetName, function(event) 
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
        $(document).on('mouseOutLastDataFromExternalContentGis_' + widgetName, function(event) 
        {
            $("#<?= $_GET['name'] ?>_titleDiv").html(widgetTitle);
            $("#" + widgetName).css("border-color", widgetOriginalBorderColor);
            $("#<?= $_GET['name'] ?>_header").css("background", widgetHeaderColor);
            $("#<?= $_GET['name'] ?>_header").css("color", widgetHeaderFontColor);
        });
        
        $(document).off('showLastDataFromExternalContentGis_' + widgetName);
        $(document).on('showLastDataFromExternalContentGis_' + widgetName, function(event) 
        {
            if(event.targetWidget === widgetName)
            {
                clearInterval(countdownRef); 
                $("#<?= $_GET['name'] ?>_content").hide();
                <?= $_GET['name'] ?>(true, metricName, event.widgetTitle, event.color1, "black", true, event.serviceUri, event.field, null, /*event.randomSingleGeoJsonIndex,*/ event.marker, event.mapRef, event.fakeId);
            }
        });
        
        $(document).off('restoreOriginalLastDataFromExternalContentGis_' + widgetName);
        $(document).on('restoreOriginalLastDataFromExternalContentGis_' + widgetName, function(event) 
        {
            if(event.targetWidget === widgetName)
            {
                clearInterval(countdownRef); 
                $("#<?= $_GET['name'] ?>_content").hide();
                <?= $_GET['name'] ?>(true, metricName, "<?= preg_replace($titlePatterns, $replacements, $title) ?>", "<?= $_GET['frame_color'] ?>", "<?= $_GET['headerFontColor'] ?>", false, /*null,*/ null, null, null);
            }
        });
        
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
        
        function populateWidget()
        {
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
                                    
                                    if(fromGisExternalContent)
                                    {
                                        if(shownValue >= 0)
                                        {
                                           minGauge = 0;
                                        }
                                        else
                                        {
                                           minGauge = Math.floor(shownValue*1.4);
                                        }

                                        maxGauge = Math.floor(Math.abs(shownValue*1.7));
                                    }
                                    else
                                    {
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
                                    }
                                    udm = "";
                                    break;

                                case "Float":
                                    if(metricData.data[0].commit.author.value_num !== null)
                                    {
                                        shownValue = parseFloat(parseFloat(metricData.data[0].commit.author.value_num).toFixed(1));
                                    }
                                    
                                    if(fromGisExternalContent)
                                    {
                                        if(shownValue >= 0)
                                        {
                                           minGauge = 0;
                                        }
                                        else
                                        {
                                           minGauge = Math.floor(shownValue*1.4);
                                        }

                                        maxGauge = Math.floor(Math.abs(shownValue*1.7));
                                    }
                                    else
                                    {
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
                                    
                                default:
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
                        
                        if(metricType === "Testuale")
                        {
                            $("#<?= $_GET['name'] ?>_chartContainer").hide();
                            $('#<?= $_GET['name'] ?>_noDataAlert').show();
                        }
                        else
                        {
                            $('#<?= $_GET['name'] ?>_noDataAlert').hide();
                            $("#<?= $_GET['name'] ?>_chartContainer").show();
                            
                            //Disegno del diagramma
                            $('#<?= $_GET['name'] ?>_chartContainer').highcharts(Highcharts.merge(gaugeOptions, {
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
                        
                    }
                    else
                    {
                        showWidgetContent(widgetName);
                        if(firstLoad !== false)
                        {
                           $("#<?= $_GET['name'] ?>_chartContainer").hide();
                           $('#<?= $_GET['name'] ?>_noDataAlert').show();
                        }
                    }
                }
                else
                {   
                    showWidgetContent(widgetName);
                    if(firstLoad !== false)
                    {
                       $("#<?= $_GET['name'] ?>_chartContainer").hide();
                       $('#<?= $_GET['name'] ?>_noDataAlert').show();
                    }
                }
                //Fine eventuale codice ad hoc basato sui dati della metrica
            }
            else
            {
                showWidgetContent(widgetName);
                if(firstLoad !== false)
                {
                   $("#<?= $_GET['name'] ?>_chartContainer").hide();
                   $('#<?= $_GET['name'] ?>_noDataAlert').show();
                }
            } 
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
                    if(fromGisExternalContent)
                    { 
                        if((fromGisFakeId !== null) && (fromGisFakeId !== 'null') && (fromGisFakeId !== undefined))
                        {
                            urlToCall = "../serviceMapFake.php?getSingleGeoJson=true&singleGeoJsonId=" + fromGisFakeId;
                        }
                        else
                        {
                            urlToCall = "<?php echo $serviceMapUrlPrefix; ?>api/v1/?serviceUri=" + fromGisExternalContentServiceUri + "&format=json";
                        }
                
                        $.ajax({
                            url: urlToCall,
                            type: "GET",
                            data: {},
                            async: true,
                            dataType: 'json',
                            success: function(geoJsonServiceData) 
                            {
                                $('#<?= $_GET['name'] ?>_infoButtonDiv a.info_source').hide();
                                $('#<?= $_GET['name'] ?>_infoButtonDiv i.gisDriverPin').show();

                                $('#<?= $_GET['name'] ?>_infoButtonDiv i.gisDriverPin').off('click');
                                $('#<?= $_GET['name'] ?>_infoButtonDiv i.gisDriverPin').click(function(){
                                    if($(this).attr('data-onMap') === 'false')
                                    {
                                        if(fromGisMapRef.hasLayer(fromGisMarker))
                                        {
                                            fromGisMarker.fire('click');
                                        }
                                        else
                                        {
                                            fromGisMapRef.addLayer(fromGisMarker);
                                            fromGisMarker.fire('click');
                                        } 
                                        $(this).attr('data-onMap', 'true');
                                        $(this).html('near_me');
                                        $(this).css('color', 'white');
                                        $(this).css('text-shadow', '2px 2px 4px black');
                                    }
                                    else
                                    {
                                        fromGisMapRef.removeLayer(fromGisMarker);
                                        $(this).attr('data-onMap', 'false');
                                        $(this).html('navigation');
                                        $(this).css('color', '#337ab7');
                                        $(this).css('text-shadow', 'none');
                                    }
                                });

                                metricData = {  
                                    "data":[  
                                       {  
                                          "commit":{  
                                             "author":{  
                                                "IdMetric_data": fromGisExternalContentField,
                                                "computationDate": null,
                                                "value_num":null,
                                                "value_perc1": null,
                                                "value_perc2": null,
                                                "value_perc3": null,
                                                "value_text": null,
                                                "quant_perc1": null,
                                                "quant_perc2": null,
                                                "quant_perc3": null,
                                                "tot_perc1": null,
                                                "tot_perc2": null,
                                                "tot_perc3": null,
                                                "series": null,
                                                "descrip": fromGisExternalContentField,
                                                "metricType": null,
                                                "threshold":null,
                                                "thresholdEval":null,
                                                "field1Desc": null,
                                                "field2Desc": null,
                                                "field3Desc": null,
                                                "hasNegativeValues": "1"
                                             }
                                          }
                                       }
                                    ]
                                };

                                var fatherNode = null;
                                if(geoJsonServiceData.hasOwnProperty("BusStop"))
                                {
                                    fatherNode = geoJsonServiceData.BusStop;
                                }
                                else
                                {
                                    if(geoJsonServiceData.hasOwnProperty("Sensor"))
                                    {
                                        fatherNode = geoJsonServiceData.Sensor;
                                    }
                                    else
                                    {
                                        //Prevedi anche la gestione del caso in cui non c'è nessuna di queste tre, sennò il widget rimane appeso.
                                        fatherNode = geoJsonServiceData.Service;
                                    }
                                }

                                var serviceProperties = fatherNode.features[0].properties;
                                var underscoreIndex = serviceProperties.serviceType.indexOf("_");
                                var serviceClass = serviceProperties.serviceType.substr(0, underscoreIndex);
                                var serviceSubclass = serviceProperties.serviceType.substr(underscoreIndex);
                                serviceSubclass = serviceSubclass.replace(/_/g, " ");

                                var numberPattern = /^-?\d*\.?\d+$/;
                                var integerPattern = /^[+\-]?\d+$/;
                                
                                if(numberPattern.test(geoJsonServiceData.realtime.results.bindings[0][fromGisExternalContentField].value))
                                {
                                    if(integerPattern.test(geoJsonServiceData.realtime.results.bindings[0][fromGisExternalContentField].value))
                                    {
                                        metricData.data[0].commit.author.value_num = geoJsonServiceData.realtime.results.bindings[0][fromGisExternalContentField].value;
                                        metricData.data[0].commit.author.metricType = "Intero"; 
                                    }
                                    else
                                    {
                                        metricData.data[0].commit.author.value_num = geoJsonServiceData.realtime.results.bindings[0][fromGisExternalContentField].value;
                                        metricData.data[0].commit.author.metricType = "Float"; 
                                    }
                                }
                                else
                                {
                                    metricData.data[0].commit.author.value_text = geoJsonServiceData.realtime.results.bindings[0][fromGisExternalContentField].value;
                                    metricData.data[0].commit.author.metricType = "Testuale";
                                }
                            },
                            error: function(errorData)
                            {
                                console.log("Error in data retrieval");
                                console.log(JSON.stringify(errorData));
                            },
                            complete: function()
                            {
                                populateWidget(); 
                            }
                        });
                    }
                    else
                    {
                        $('#<?= $_GET['name'] ?>_infoButtonDiv i.gisDriverPin').hide();
                        $('#<?= $_GET['name'] ?>_infoButtonDiv a.info_source').show();
                        manageInfoButtonVisibility(widgetProperties.param.infoMessage_w, $('#<?= $_GET['name'] ?>_header'));
                        metricData = getMetricData(metricName);
                        populateWidget(); 
                    }
                }
                else
                {
                    console.log("Errore in caricamento proprietà widget");
                    showWidgetContent(widgetName);
                    if(firstLoad !== false)
                    {
                       $("#<?= $_GET['name'] ?>_chartContainer").hide();
                       $('#<?= $_GET['name'] ?>_noDataAlert').show();
                    }
                }
            },
            error: function(errorData)
            {
               console.log("Errore in caricamento proprietà widget");
               console.log(JSON.stringify(errorData));
               showWidgetContent(widgetName);
               if(firstLoad !== false)
               {
                  $("#<?= $_GET['name'] ?>_chartContainer").hide();
                  $('#<?= $_GET['name'] ?>_noDataAlert').show();
               }
            },
            complete: function()
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
                <!--<a id ="info_modal" href="#" class="info_source"><img id="source_<?= $_GET['name'] ?>" src="../management/img/info.png" class="source_button"></a>-->
               <a id ="info_modal" href="#" class="info_source"><i id="source_<?= $_GET['name'] ?>" class="source_button fa fa-info-circle" style="font-size: 22px"></i></a>
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
            <div id="<?= $_GET['name'] ?>_chartContainer" class="chartContainer"></div>
        </div>
    </div>	
</div> 

