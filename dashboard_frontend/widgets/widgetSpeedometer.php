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
        var timeToReload = <?= $_GET['freq'] ?>;
        var widgetProperties, thresholdObject, infoJson, styleParameters, metricType, metricData, pattern, udm,
            widgetParameters, paneObj, minGauge, maxGauge, shownValue, thicknessVal, plotOptionsObj, sizeRowsWidget, alarmSet, 
            plotBandObj, plotBandSet, hasNegativeValues, metricName, widgetTitle, countdownRef = null;
        var metricName = "<?= $_GET['metric'] ?>";
        var elToEmpty = $("#<?= $_GET['name'] ?>_chartContainer");
        var url = "<?= $_GET['link_w'] ?>";
        var hasThresholds = false;
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
                <?= $_GET['name'] ?>(true, metricName, "<?= preg_replace($titlePatterns, $replacements, $title) ?>", "<?= $_GET['frame_color'] ?>", "<?= $_GET['headerFontColor'] ?>", false, null, null, /*null,*/ null, null, null);
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
                        pattern = /Percentuale\//;
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
                                    /*if(metricData.data[0].commit.author.value_num !== null)
                                    {
                                       shownValue = parseInt(metricData.data[0].commit.author.value_num);
                                    }
                                    else
                                    {
                                       shownValue = 0;
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
                                    maxGauge = Math.floor(shownValue + (Math.random() * 25) + 10);*/
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
                                    break;

                                case "Float":
                                    /*if(metricData.data[0].commit.author.value_num !== null)
                                    {
                                        shownValue = parseFloat(parseFloat(metricData.data[0].commit.author.value_num).toFixed(1));
                                    }
                                    else
                                    {
                                       shownValue = 0;
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
                                    
                                    maxGauge = Math.floor(shownValue + (Math.random() * 25) + 10);*/
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
                                    break;

                                case "Percentuale":
                                    minGauge = 0;
                                    maxGauge = 100;
                                    if(metricData.data[0].commit.author.value_perc1 !== null)
                                    {
                                        udm = "%";
                                        shownValue = parseFloat(parseFloat(metricData.data[0].commit.author.value_perc1).toFixed(1));
                                    }
                                    break;

                                default:
                                    break;
                            }
                        }
                        
                        var innerRadius = "92%";
                        var outerRadius = "100%";

                        //Controllo tipo metrica non compatibile col widget
                        if((shownValue !== null) && (minGauge !== null) && (maxGauge !== null))
                        {
                           if(hasThresholds)
                           {
                              //Applicazione soglie
                              plotBandSet = [];
                              plotBandObj = null;//Va resettato ad ogni iterazione, per evitare di inserire bande colorate fuori scala e rovinare il widget
                              
                              var op, op1, op2 = null;
                              
                              for(var i = 0; i < thresholdObject.length; i++) 
                              {
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
                                          innerRadius: innerRadius,
                                          outerRadius: outerRadius
                                      };
                                    }
                                    else
                                    {
                                       plotBandObj  = {
                                          color: thresholdObject[i].color, 
                                          from: minGauge, 
                                          to: thresholdObject[i].thr1, 
                                          innerRadius: innerRadius,
                                          outerRadius: outerRadius
                                      };
                                    }
                                 }
                                 
                                 //Semiretta destra
                                 if(((thresholdObject[i].op === "greater")||(thresholdObject[i].op === "greaterEqual"))&&(parseFloat(thresholdObject[i].thr1) <= maxGauge))
                                 {
                                    if(thresholdObject[i].thr1 <= minGauge)
                                    {
                                       plotBandObj  = {
                                          color: thresholdObject[i].color, 
                                          from: minGauge, 
                                          to: maxGauge, 
                                          innerRadius: innerRadius,
                                          outerRadius: outerRadius
                                       };
                                    }
                                    else
                                    {
                                       plotBandObj  = {
                                          color: thresholdObject[i].color, 
                                          from: thresholdObject[i].thr1, 
                                          to: maxGauge, 
                                          innerRadius: innerRadius,
                                          outerRadius: outerRadius
                                      };
                                    }
                                 }
                                 
                                 //Uguale a
                                 if((thresholdObject[i].op === "equal")&&(parseFloat(thresholdObject[i].thr1) <= maxGauge)&&(parseFloat(thresholdObject[i].thr1) >= minGauge))
                                 {
                                    var minLine = parseFloat(thresholdObject[i].thr1) - Math.abs(maxGauge - minGauge)*0.0025;
                                    var maxLine = parseFloat(thresholdObject[i].thr1) + Math.abs(maxGauge - minGauge)*0.0025;       

                                    plotBandObj  = {
                                       color: thresholdObject[i].color, 
                                       from: minLine, 
                                       to: maxLine, 
                                       innerRadius: innerRadius,
                                       outerRadius: outerRadius
                                    };
                                 }
                                 
                                 //Diverso da
                                 if((thresholdObject[i].op === "notEqual")&&(parseFloat(thresholdObject[i].thr1) <= maxGauge)&&(parseFloat(thresholdObject[i].thr1) >= minGauge))
                                 {
                                    var minLine = parseFloat(thresholdObject[i].thr1) - Math.abs(maxGauge - minGauge)*0.0025;
                                    var maxLine = parseFloat(thresholdObject[i].thr1) + Math.abs(maxGauge - minGauge)*0.0025;       

                                    plotBandObj  = {
                                       color: thresholdObject[i].color, 
                                       from: minLine, 
                                       to: maxLine, 
                                       innerRadius: innerRadius,
                                       outerRadius: outerRadius
                                    };                                  
                                 }
                                 
                                 //Intervallo bi-limitato
                                 if(((thresholdObject[i].op === "intervalOpen")||(thresholdObject[i].op === "intervalClosed")||(thresholdObject[i].op === "intervalLeftOpen")||(thresholdObject[i].op === "intervalRightOpen"))&&(!((parseFloat(thresholdObject[i].thr1) <= minGauge)&&(parseFloat(thresholdObject[i].thr2) <= minGauge)))&&(!((parseFloat(thresholdObject[i].thr1) >= maxGauge)&&(parseFloat(thresholdObject[i].thr2) >= maxGauge))))
                                 {
                                    //Sforamento inferiore
                                    if((parseFloat(thresholdObject[i].thr1) <= minGauge)&&(parseFloat(thresholdObject[i].max) < maxGauge))
                                    {
                                       plotBandObj  = {
                                          color: thresholdObject[i].color, 
                                          from: minGauge, 
                                          to: thresholdObject[i].thr2, 
                                          innerRadius: innerRadius,
                                          outerRadius: outerRadius
                                      };
                                    }

                                    //Sforamento superiore
                                    if((parseFloat(thresholdObject[i].thr2) >= maxGauge)&&(parseFloat(thresholdObject[i].thr1) >= minGauge))
                                    {
                                       plotBandObj  = {
                                          color: thresholdObject[i].color, 
                                          from: thresholdObject[i].thr1, 
                                          to: maxGauge, 
                                          innerRadius: innerRadius,
                                          outerRadius: outerRadius
                                      };
                                    }

                                    //Sforamento da ambo i lati
                                    if((parseFloat(thresholdObject[i].thr2) >= maxGauge)&&(parseFloat(thresholdObject[i].thr1) <= minGauge))
                                    {
                                       plotBandObj  = {
                                          color: thresholdObject[i].color, 
                                          from: minGauge, 
                                          to: maxGauge, 
                                          innerRadius: innerRadius,
                                          outerRadius: outerRadius
                                      };
                                    }

                                    //Nessun sforamento
                                    if((parseFloat(thresholdObject[i].thr2) < maxGauge)&&(parseFloat(thresholdObject[i].thr1) > minGauge))
                                    {
                                       plotBandObj  = {
                                          color: thresholdObject[i].color, 
                                          from: thresholdObject[i].thr1, 
                                          to: thresholdObject[i].thr2, 
                                          innerRadius: innerRadius,
                                          outerRadius: outerRadius
                                      };
                                    }
                                    //In tutti gli altri casi (cioé con la banda interamente sopra il massimo o interamente sotto il minimo) non disegnamo banda colorata.
                                 }
                                 
                                 if(plotBandObj !== null)
                                 {
                                    plotBandSet.push(plotBandObj);
                                 }
                              }
                           }
                           else
                           {
                              //Nessuna soglia, usiamo un colore di default
                              plotBandSet = [{
                                 from: minGauge,
                                 to: maxGauge,
                                 color: colors.GREEN,
                                 innerRadius: innerRadius,
                                 outerRadius: outerRadius
                              }];
                           }
                           
                           //NON CANCELLARE - Da riusare quando ripristiniamo il blink ad allarme attivo.
                            /*if((threshold === null) || (thresholdEval === null))
                            {
                                //In questo caso non mostriamo soglia d'allarme.
                                threshold = 0;

                                //Per qualsiasi combinazione non prevista impostiamo un unico colore (verde)
                                plotBandSet = [{
                                            from: minGauge,
                                            to: maxGauge,
                                            color: chartColors[0],
                                            thickness: thicknessVal
                                        }];

                                if((limSup1 === null) && (limSup2 === null))
                                {
                                    plotBandSet = [{
                                            from: minGauge,
                                            to: maxGauge,
                                            color: chartColors[0],
                                            thickness: thicknessVal
                                        }];
                                }
                                else
                                {
                                    if(limSup1 !== null)
                                    {
                                        plotBandSet = [
                                        {  
                                            from: minGauge,
                                            to: limSup1,
                                            color: chartColors[0],
                                            thickness: thicknessVal
                                        },
                                        {  
                                            from: limSup1,
                                            to: maxGauge,
                                            color: chartColors[1],
                                            thickness: thicknessVal
                                        }
                                        ];
                                    }
                                    if((limSup1 !== null) && (limSup2 !== null))
                                    {
                                        plotBandSet = [
                                        {  
                                            from: minGauge,
                                            to: limSup1,
                                            color: chartColors[0],
                                            thickness: thicknessVal
                                        },
                                        {  
                                            from: limSup1,
                                            to: limSup2,
                                            color: chartColors[1],
                                            thickness: thicknessVal
                                        },
                                        {  
                                            from: limSup2,
                                            to: maxGauge,
                                            color: chartColors[2],
                                            thickness: thicknessVal
                                        }        
                                        ];
                                    }
                                }
                            }
                            else
                            {
                                //Distinguiamo in base all'operatore di confronto
                                switch(thresholdEval)
                                {
                                    //Allarme attivo se il valore attuale è sotto la soglia
                                    case '<':
                                        if(shownValue < threshold)
                                        {
                                           //Allarme
                                           //alarmSet = true;
                                        }

                                        plotBandSet = [
                                            {
                                                from: minGauge,
                                                to: threshold,
                                                color: chartColors[2],
                                                thickness: thicknessVal
                                            },        
                                            {
                                                from: threshold,
                                                to: maxGauge,
                                                color: chartColors[0],
                                                thickness: thicknessVal
                                            }
                                        ];
                                        break;

                                    //Allarme attivo se il valore attuale è sopra la soglia
                                    case '>':
                                        if(shownValue > threshold)
                                        {
                                           //Allarme
                                           //alarmSet = true;
                                        }

                                        plotBandSet = [
                                            {
                                                from: minGauge,
                                                to: threshold,
                                                color: chartColors[0],
                                                thickness: thicknessVal
                                            }, 
                                            {
                                                from: threshold,
                                                to: maxGauge,
                                                color: chartColors[2],
                                                thickness: thicknessVal
                                            }
                                        ];
                                        break;

                                    //Allarme attivo se il valore attuale è uguale alla soglia (errore sui float = 0.01%)
                                    case '=':
                                        delta = Math.abs(shownValue - threshold);
                                        deltaPerc = ((delta / threshold)*100);

                                        if(deltaPerc <= 0.01)
                                        {
                                            //Allarme
                                            //alarmSet = true;
                                        }

                                        var incAlr = parseInt(threshold*0.05);
                                        var infAlr = threshold-incAlr;
                                        var supAlr = parseInt(threshold) + incAlr;

                                        plotBandSet = [
                                            {
                                                from: minGauge,
                                                to: infAlr,
                                                color: chartColors[0],
                                                thickness: thicknessVal
                                            },
                                            {
                                                from: infAlr,
                                                to: supAlr,
                                                color: chartColors[2],
                                                thickness: thicknessVal
                                            },
                                            {
                                                from: supAlr,
                                                to: maxGauge,
                                                color: chartColors[0],
                                                thickness: thicknessVal
                                            }
                                        ];
                                        break;    

                                    //Non gestiamo altri operatori 
                                    default:
                                        threshold = 0;
                                        plotBandSet = [{
                                            from: minGauge,
                                            to: maxGauge,
                                            color: chartColors[0],
                                            thickness: thicknessVal
                                        }];
                                        break;
                                 }
                            }*/

                            if(sizeRowsWidget <= 4)
                            {
                                //Speedo piccolo (anche in caso di valore errato su DB)
                                plotOptionsObj = {
                                    gauge : {
                                        dial : {
                                            baseWidth: 2,
                                            topWidth: 1
                                        }
                                    }
                                };

                                paneObj = [{
                                    startAngle: -135,
                                    endAngle: 135,
                                    size: '112%',
                                    center: ['50%', '52%'],
                                    background: [{
                                            backgroundColor: {
                                                linearGradient: {x1: 0, y1: 0, x2: 0, y2: 1},
                                                stops: [
                                                    [0, '#FFF'],
                                                    [1, '#333']
                                                ]
                                            },
                                            borderWidth: 0,
                                            outerRadius: '100%'
                                        }, {
                                            backgroundColor: {
                                                linearGradient: {x1: 0, y1: 0, x2: 0, y2: 1},
                                                stops: [
                                                    [0, '#333'],
                                                    [1, '#FFF']
                                                ]
                                            },
                                            borderWidth: 0,
                                            outerRadius: '100%'
                                        }, {
                                            // default background
                                        }, {
                                            backgroundColor: '#DDD',
                                            borderWidth: 0,
                                            outerRadius: '100%',
                                            innerRadius: '100%'
                                        }]
                                }];

                                dataLabelsObj = {
                                    enabled: true,
                                    style: {
                                        fontWeight: 'bold',
                                        fontSize: '12px',
                                        "text-shadow": "1px 1px 1px rgba(0,0,0,0.20)",
                                        "textOutline": "1px 1px contrast",
                                        fontFamily: 'Verdana'
                                    },
                                    padding: 1,
                                    borderWidth: 0,
                                    y: 60,
                                    formatter: function () {
                                        var val = this.y;
                                        return val;
                                    }
                                };

                                udmObj = {
                                        text: udm, 
                                        y: 60,
                                        style: {
                                            fontWeight:'normal',
                                            fontSize: '15px',
                                            color: 'black',
                                            "text-shadow": "1px 1px 1px rgba(0,0,0,0.20)",
                                            "textOutline": "1px 1px contrast",
                                            fontFamily: 'Verdana'
                                        }
                                    };

                                yAxisObj = {
                                    min: minGauge,
                                    max: maxGauge,
                                    minorTickInterval: 'auto',
                                    minorTickWidth: 1,
                                    minorTickLength: 5,
                                    minorTickPosition: 'inside',
                                    minorTickColor: '#666',
                                    tickPixelInterval: 30,
                                    tickWidth: 2,
                                    tickPosition: 'inside',
                                    tickLength: 7,
                                    //tickInterval: 2,
                                    tickColor: '#666',
                                    labels: {
                                        step: 2,
                                        rotation: 'auto',
                                        distance: -20,
                                        style: {
                                            "text-shadow": "1px 1px 1px rgba(0,0,0,0.20)",
                                            "textOutline": "1px 1px contrast",
                                            fontFamily: 'Verdana'
                                        }
                                    },
                                    title: udmObj,
                                    plotBands: plotBandSet 
                                }; 
                            }
                            else
                            {
                                //Speedo grande (anche in caso di valore errato su DB)
                                plotOptionsObj = {
                                    gauge : {
                                        dial : {
                                            baseWidth: 3,
                                            topWidth: 1
                                        }
                                    }
                                };

                                paneObj = [{
                                    startAngle: -135,
                                    endAngle: 135,
                                    size: '97%',
                                    center: ['50%', '51%'],
                                    background: [{
                                            backgroundColor: {
                                                linearGradient: {x1: 0, y1: 0, x2: 0, y2: 1},
                                                stops: [
                                                    [0, '#FFF'],
                                                    [1, '#333']
                                                ]
                                            },
                                            borderWidth: 0,
                                            outerRadius: '100%'
                                        }, {
                                            backgroundColor: {
                                                linearGradient: {x1: 0, y1: 0, x2: 0, y2: 1},
                                                stops: [
                                                    [0, '#333'],
                                                    [1, '#FFF']
                                                ]
                                            },
                                            borderWidth: 0,
                                            outerRadius: '100%'
                                        }, {
                                            // default background
                                        }, {
                                            backgroundColor: '#DDD',
                                            borderWidth: 0,
                                            outerRadius: '100%',
                                            innerRadius: '100%'
                                        }]
                                }];

                                dataLabelsObj = {
                                    enabled: true,
                                    style: {
                                        fontSize: '20px',
                                        "text-shadow": "1px 1px 1px rgba(0,0,0,0.20)",
                                        "textOutline": "1px 1px contrast",
                                        fontFamily: 'Verdana'
                                    },
                                    y: 85,
                                    borderWidth: 0,
                                    formatter: function () {
                                        var val = this.y;
                                        return val;
                                    }
                                };

                                udmObj = {
                                        text: udm, 
                                        y: 120,
                                        style: {
                                            fontWeight:'bold',
                                            fontSize: '18px',
                                            color: 'black',
                                            "text-shadow": "1px 1px 1px rgba(0,0,0,0.20)",
                                            "textOutline": "1px 1px contrast",
                                            fontFamily: 'Verdana'
                                        }
                                    };

                                yAxisObj = {
                                    min: minGauge,
                                    max: maxGauge,
                                    minorTickInterval: 'auto',
                                    minorTickWidth: 1,
                                    minorTickLength: 6,
                                    minorTickPosition: 'inside',
                                    minorTickColor: '#666',
                                    tickPixelInterval: 30,
                                    tickWidth: 2,
                                    tickPosition: 'inside',
                                    tickLength: 8,
                                    //tickInterval: 2,
                                    tickColor: '#666',
                                    labels: {
                                        step: 2,
                                        rotation: 'auto',
                                        style: {
                                            "text-shadow": "1px 1px 1px rgba(0,0,0,0.20)",
                                            "textOutline": "1px 1px contrast",
                                            fontFamily: 'Verdana'
                                        }
                                    },
                                    title: udmObj,
                                    plotBands: plotBandSet 
                                }; 
                            }

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
                                $('#<?= $_GET['name'] ?>_chartContainer').highcharts({
                                    credits: {
                                        enabled: false
                                    },
                                    chart: {
                                        type: 'gauge',
                                        backgroundColor: '<?= $_GET['color'] ?>',
                                        plotBackgroundColor: null,
                                        plotBackgroundImage: null,
                                        plotBorderWidth: 0,
                                        plotShadow: false
                                    },
                                    //NON RIMUOVERE        
                                    title: {
                                        text: ''
                                    },
                                    pane: paneObj,
                                    plotOptions: plotOptionsObj,
                                    yAxis: yAxisObj,
                                    series: [{
                                            data: [shownValue],
                                            tooltip: {
                                                enabled: false
                                            },
                                            dataLabels: dataLabelsObj
                                        }],
                                    exporting: {
                                        enabled: false
                                    }
                                });
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
                if((widgetProperties !== null) && (widgetProperties !== ''))
                {
                    //Inizio eventuale codice ad hoc basato sulle proprietà del widget
                    styleParameters = getStyleParameters();//Restituisce null finché non si usa il campo per questo widget
                    widgetParameters = widgetProperties.param.parameters;
                    udm = widgetProperties.param.udm;
                    sizeRowsWidget = parseInt(widgetProperties.param.size_rows);

                    if(widgetParameters !== null)
                    {
                       widgetParameters = JSON.parse(widgetProperties.param.parameters);

                       if(widgetParameters.hasOwnProperty("thresholdObject"))
                       {
                          thresholdObject = widgetParameters.thresholdObject; 
                          hasThresholds = true;
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
    });//Fine document.ready            
</script>
<div class="widget" id="<?= $_GET['name'] ?>_div">
    <div class='ui-widget-content'>
        <div id='<?= $_GET['name'] ?>_header' class="widgetHeader">
            <div id="<?= $_GET['name'] ?>_infoButtonDiv" class="infoButtonContainer">
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
        