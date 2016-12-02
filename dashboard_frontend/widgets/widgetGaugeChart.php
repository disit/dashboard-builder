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
<script type="text/javascript">
    var colors = {
      GREEN: '#008800',
      ORANGE: '#FF9933',
      LOW_YELLOW: '#ffffcc',
      RED: '#FF0000',
    };
    var parameters = {};
    var valColori1 = {};
    var valColori2 = {};
    var valColori3 = {};

    $(document).ready(function <?= $_GET['name'] ?>(firstLoad) 
    {
        var alarmSet = false;
        var $div2blink = null;
        var blinkInterval = null;
        var loadingFontDim = 13; 
        var loadingIconDim = 20;
        
        var colore_frame = "<?= $_GET['frame_color'] ?>";
        var nome_wid = "<?= $_GET['name'] ?>_div";
        $("#<?= $_GET['name'] ?>_div").css({'background-color':colore_frame});
        
        var fontSize = "<?= $_GET['fontSize'] ?>";
        var fontColor = "<?= $_GET['fontColor'] ?>";
        
        var link_w = "<?= $_GET['link_w'] ?>";
        var divChartContainer = $('#<?= $_GET['name'] ?>_content');
        var linkElement = $('#<?= $_GET['name'] ?>_link_w');
        
        $('#<?= $_GET['name'] ?>_desc').width('70%');
        $('#<?= $_GET['name'] ?>_desc').html('<span><a id ="info_modal" href="#" class="info_source"><img id="source_<?= $_GET['name'] ?>" src="../management/img/info.png" class="source_button"></a><div id="<?= $_GET['name'] ?>_desc_text" class="desc_text" title="<?= preg_replace('/_/', ' ', $_GET['title']) ?>"><?= preg_replace('/_/', ' ', $_GET['title']) ?></div></span>');
        $('#<?= $_GET['name'] ?>_desc_text').css("width", "80%");
        $('#<?= $_GET['name'] ?>_desc_text').css("height", "100%");
        $("#<?= $_GET['name'] ?>_loading").css("background-color", '<?= $_GET['color'] ?>');        
        var height = parseInt($("#<?= $_GET['name'] ?>_div").prop("offsetHeight") - 25);
        $("#<?= $_GET['name'] ?>_content").css("height", height);
        $('#<?= $_GET['name'] ?>_loading').css("height", height+"px");
        $('#<?= $_GET['name'] ?>_loading p').css("font-size", loadingFontDim+"px");
        $('#<?= $_GET['name'] ?>_loading i').css("font-size", loadingIconDim+"px");
        if(firstLoad != false)
        {
            $('#<?= $_GET['name'] ?>_loading').css("display", "block");
        }
        
        $.ajax({
            url: "../widgets/getParametersWidgets.php",
            data: {"nomeWidget": ["<?= $_GET['name'] ?>"]},
            type: "GET",
            async: true,
            dataType: 'json',
            success: function (msg) {
                var parametri = msg.param.parameters;
                var contenuto = jQuery.parseJSON(parametri);
                var sizeRowsWidget = parseInt(msg.param.size_rows);
                var rangeMin = null;
                var rangeMax = null;
                
                if (contenuto !== null) {
                    parameters["<?= $_GET['name'] ?>"] = contenuto;
                    
                    if((parameters["<?= $_GET['name'] ?>"].rangeMin != null) && (parameters["<?= $_GET['name'] ?>"].rangeMin != "") && (typeof parameters["<?= $_GET['name'] ?>"].rangeMin != "undefined"))
                    {
                        rangeMin = parameters["<?= $_GET['name'] ?>"].rangeMin;
                    }

                    if((parameters["<?= $_GET['name'] ?>"].rangeMax != null) && (parameters["<?= $_GET['name'] ?>"].rangeMax != "") && (typeof parameters["<?= $_GET['name'] ?>"].rangeMax != "undefined"))
                    {
                        rangeMax = parameters["<?= $_GET['name'] ?>"].rangeMax;
                    }
                    
                    if((parameters["<?= $_GET['name'] ?>"].color3 != null) && (parameters["<?= $_GET['name'] ?>"].color3 != "") && (typeof parameters["<?= $_GET['name'] ?>"].color3 !== "undefined")) 
                    {
                        valColori3["<?= $_GET['name'] ?>"] = parameters["<?= $_GET['name'] ?>"].color3;
                    }
                    else
                    {
                        valColori3["<?= $_GET['name'] ?>"] = colors.RED; 
                    }
                    
                    if((parameters["<?= $_GET['name'] ?>"].color2 != null) && (parameters["<?= $_GET['name'] ?>"].color2 != "") && (typeof parameters["<?= $_GET['name'] ?>"].color2 !== "undefined")) 
                    {
                        valColori2["<?= $_GET['name'] ?>"] = parameters["<?= $_GET['name'] ?>"].color2;
                    }
                    else
                    {
                        valColori2["<?= $_GET['name'] ?>"] = colors.ORANGE;
                    }
                    
                    
                    if((parameters["<?= $_GET['name'] ?>"].color1 != null) && (parameters["<?= $_GET['name'] ?>"].color1 != "") && (typeof parameters["<?= $_GET['name'] ?>"].color1 !== "undefined")) 
                    {
                        valColori1["<?= $_GET['name'] ?>"] = parameters["<?= $_GET['name'] ?>"].color1;
                    }
                    else
                    {
                        valColori1["<?= $_GET['name'] ?>"] = colors.GREEN;
                    }
                } 
                else 
                {
                    valColori1["<?= $_GET['name'] ?>"] = colors.GREEN; 
                    valColori2["<?= $_GET['name'] ?>"] = colors.ORANGE; 
                    valColori3["<?= $_GET['name'] ?>"] = colors.RED; 
                }

                $.ajax({ 
                    url: "../widgets/getDataMetrics.php",
                    data: {"IdMisura": ["<?= $_GET['metric'] ?>"]},
                    type: "GET",
                    async: true,
                    dataType: 'json',
                    success: function (msg) {
                        var seriesDat = [];
                        var metricType = msg.data[0].commit.author.metricType;     
                        var minGauge = null;
                        var maxGauge = null;
                        var udm = "";
                        var shownValue = null;
                        var pattern = /Percentuale\//;
                        var threshold = null;
                        var thresholdEval = null;
                        var stopsArray;
                        var delta = null;
                        var deltaPerc = null;
                        var plotBandSet = null;
                        var paneObj = null;
                        var yObj = null;
                        var solidGaugeObj = null;
                        var seriesObj = null;
                        
                        threshold = msg.data[0].commit.author.threshold;
                        thresholdEval = msg.data[0].commit.author.thresholdEval;
                        
                        if(pattern.test(metricType))
                        {
                            minGauge = 0;
                            maxGauge = parseInt(metricType.substring(12));
                            if(msg.data[0].commit.author.quant_perc1 != null)
                            {
                                shownValue = parseFloat(parseFloat(msg.data[0].commit.author.quant_perc1).toFixed(1));
                            }
                        }
                        else
                        {
                            switch(metricType)
                            {
                                case "Intero":
                                    if((rangeMin != null) && (rangeMax != null))
                                    {
                                        minGauge = parseInt(rangeMin);
                                        maxGauge = parseInt(rangeMax);
                                    }
                                    
                                    if(msg.data[0].commit.author.value_num != null)
                                    {
                                        shownValue = parseInt(msg.data[0].commit.author.value_num);
                                    }
                                    
                                    break;

                                case "Float":
                                    if((rangeMin != null) && (rangeMax != null))
                                    {
                                        minGauge = parseInt(rangeMin);
                                        maxGauge = parseInt(rangeMax);
                                    }
                                    if(msg.data[0].commit.author.value_num != null)
                                    {
                                        shownValue = parseFloat(parseFloat(msg.data[0].commit.author.value_num).toFixed(1));
                                    }
                                    
                                    break;

                                case "Percentuale":
                                    minGauge = 0;
                                    maxGauge = 100;
                                    udm = "%";
                                    if(msg.data[0].commit.author.value_perc1 != null)
                                    {
                                        shownValue = parseFloat(parseFloat(msg.data[0].commit.author.value_perc1).toFixed(1));
                                        if(shownValue > 100)
                                        {
                                            shownValue = 100;
                                        }
                                    }
                                    break;
                            }
                        }
                        
                        if((shownValue != null) && (minGauge != null) && (maxGauge != null))
                        {   
                            if((threshold === null) || (thresholdEval === null))
                            {
                                //In questo caso non mostriamo soglia d'allarme.
                                threshold = minGauge;
                                stopsArray = [
                                    [0.0, valColori1["<?= $_GET['name'] ?>"]]  
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
                                               [0.0, valColori3["<?= $_GET['name'] ?>"]]
                                          ];
                                          alarmSet = true;
                                       }
                                       else
                                       {
                                            //Green
                                            stopsArray = [
                                                [0.0, valColori1["<?= $_GET['name'] ?>"]]
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
                                               [0.0, valColori3["<?= $_GET['name'] ?>"]]
                                          ];
                                          alarmSet = true;
                                       }
                                       else
                                       {
                                            stopsArray = [
                                                [0.0, valColori1["<?= $_GET['name'] ?>"]]
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
                                                   [0.0, valColori3["<?= $_GET['name'] ?>"]]
                                               ];   
                                       }
                                       else
                                       {
                                          stopsArray = [
                                                   [0.0, valColori1["<?= $_GET['name'] ?>"]]
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
                                                   [0.0, valColori1["<?= $_GET['name'] ?>"]]
                                               ];
                                       break;
                                }
                            }
                            
                            switch(sizeRowsWidget)
                            {
                                case 4:
                                    paneObj = {
                                        center: ['50%', '85%'],
                                        size: '125%',
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
                                            distance: -12,
                                            style: {
                                                "text-shadow": "1px 1px 1px rgba(0,0,0,0.35)"
                                            }
                                        },
                                        plotBands: plotBandSet
                                    };
                                    solidGaugeObj = {
                                            dataLabels: {
                                                y: 10,
                                                borderWidth: 0,
                                                useHTML: true
                                            }
                                        };
                                    seriesObj = [{
                                        data: [shownValue],
                                        dataLabels: {
                                            format: '<div style="text-align:center"><span style="font-family: Verdana; text-shadow: 1px 1px 1px rgba(0,0,0,0.35); font-size:16px; color:' + fontColor + '">{y}</span>' +
                                                    '<span style="font-family: Verdana; font-size:12px; text-shadow: 1px 1px 1px rgba(0,0,0,0.35); color:' + fontColor + '; display:inline"> ' + udm + '</span></div>'
                                        }
                                    }];    
                                    break;

                                case 5:
                                    paneObj = {
                                        center: ['50%', '85%'],
                                        size: '140%',
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
                                            y: 16,
                                            distance: -15,
                                            style: {
                                               fontSize: 12         
                                            }
                                        },
                                        plotBands: plotBandSet
                                    };
                                    solidGaugeObj = {
                                            dataLabels: {
                                                y: 10,
                                                borderWidth: 0,
                                                useHTML: true
                                            }
                                        };
                                    seriesObj = [{
                                        data: [shownValue],
                                        dataLabels: {
                                            /*format: '<div style="text-align:center"><span style="font-size:22px;color:' +
                                                ((Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black') + '">{y}</span>' +
                                                   '<span style="font-size:12px;color:black; display:inline"> ' + udm + '</span></div>'*/
                                            format: '<div style="text-align:center"><span style="font-family: Verdana; font-size:22px; color:' + fontColor + '">{y}</span>' +
                                                    '<span style="font-family: Verdana; font-size:12px;color:' + fontColor + '; display:inline"> ' + udm + '</span></div>'       
                                        }
                                    }]; 
                                    break;    
                                 
                                case 6:
                                    paneObj = {
                                        center: ['50%', '85%'],
                                        size: '150%',
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
                                            y: 18,
                                            distance: -22,
                                            style: {
                                               fontSize: 16        
                                            }
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
                                            /*format: '<div style="text-align:center"><span style="font-size:28px;color:' +
                                                ((Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black') + '">{y}</span>' +
                                                   '<span style="font-size:12px;color:black; display:inline"> ' + udm + '</span></div>'*/
                                            format: '<div style="text-align:center"><span style="font-family: Verdana; font-size:28px;color:' + fontColor + '">{y}</span>' +
                                                    '<span style="font-family: Verdana; font-size:12px;color:' + fontColor + '; display:inline"> ' + udm + '</span></div>'       
                                        }
                                    }]; 
                                    break;

                                default:
                                    paneObj = {
                                        center: ['50%', '85%'],
                                        size: '133%',
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
                                    break;
                            }
                            
                            

                            /**
                             * Creazione oggetto gaugeOptions per settare l'aspetto del diagramma.
                             */
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

                            if(firstLoad != false)
                            {
                                $('#<?= $_GET['name'] ?>_loading').css("display", "none");
                                $('#<?= $_GET['name'] ?>_content').css("display", "block");
                            } 
                            
                            /**
                             * Instanziazione del diagramma.
                             */ 
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
                            
                            if (link_w.trim()) 
                            {
                                if(linkElement.length === 0)
                                {
                                   linkElement = $("<a id='<?= $_GET['name'] ?>_link_w' href='<?= $_GET['link_w'] ?>' target='_blank' class='elementLink2'>");
                                   divChartContainer.wrap(linkElement); 
                                }
                            }
                        }
                        else
                        {
                            $('#<?= $_GET['name'] ?>_content').html("<p style='text-align: center; font-size: 18px'>Nessun dato disponibile</p>");
                        }
                        
                        var div2blink = $('#<?= $_GET['name'] ?>_desc_text');
                            blinkInterval = null;
                            if(alarmSet)
                            {
                                blinkInterval = setInterval(function(){
                                    div2blink.toggleClass("desc_text_alr");
                                },1000);
                            }
                        
                        
                        $('#source_<?= $_GET['name'] ?>').on('click', function () {
                            $('#dialog_<?= $_GET['name'] ?>').show();
                        });
                        $('#close_popup_<?= $_GET['name'] ?>').on('click', function () {

                            $('#dialog_<?= $_GET['name'] ?>').hide();
                        });

                        
                    }});

                var counter = <?= $_GET['freq'] ?>;
                var countdown = setInterval(function () {
                    counter--;
                    if (counter > 60) {
                        $("#countdown_<?= $_GET['name'] ?>").text(Math.floor(counter / 60) + "m");
                    } else {
                        $("#countdown_<?= $_GET['name'] ?>").text(counter + "s");
                    }
                    if (counter === 0) {
                        $("#countdown_<?= $_GET['name'] ?>").text(counter + "s");
                        clearInterval(countdown);
                        if(alarmSet)
                        {
                            clearInterval(blinkInterval);
                        }
                        setTimeout(<?= $_GET['name'] ?>(false), 1000);
                    }
                }, 1000);
            }
        });
    });
</script>

<div class="widget" id="<?= $_GET['name'] ?>_div">
    <div class='ui-widget-content'>
        <div id="<?= $_GET['name'] ?>_desc" class='desc'></div><div class="icons-modify-widget"><a class="icon-cfg-widget" href="#"><span class="glyphicon glyphicon-cog glyphicon-modify-widget" aria-hidden="true"></span></a><a class="icon-remove-widget" href="#"><span class="glyphicon glyphicon-remove glyphicon-modify-widget" aria-hidden="true"></span></a></div><div id="countdown_<?= $_GET['name'] ?>" class="countdown"></div>
        <div id="<?= $_GET['name'] ?>_loading" class="loadingDiv">
            <div class="loadingTextDiv">
                <p>Loading data, please wait</p>
            </div>
            <div class ="loadingIconDiv">
                <i class='fa fa-spinner fa-spin'></i>
            </div>
        </div>
        <div id="<?= $_GET['name'] ?>_content" class="content"></div>
    </div>	
</div>

