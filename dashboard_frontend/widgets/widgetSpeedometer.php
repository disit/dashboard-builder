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


    $(document).ready(function <?= $_GET['name'] ?>(firstLoad) 
    {
        $('#<?= $_GET['name'] ?>_desc').width('70%');
        $('#<?= $_GET['name'] ?>_desc_text').css("width", "80%");
        $('#<?= $_GET['name'] ?>_desc_text').css("height", "100%");
        $('#<?= $_GET['name'] ?>_desc').html('<span><a id ="info_modal" href="#" class="info_source"><img id="source_<?= $_GET['name'] ?>" src="../management/img/info.png" class="source_button"></a><div id="<?= $_GET['name'] ?>_desc_text" class="desc_text" title="<?= preg_replace('/_/', ' ', $_GET['title']) ?>"><?= preg_replace('/_/', ' ', $_GET['title']) ?></div></span>');
        $("#<?= $_GET['name'] ?>_loading").css("background-color", '<?= $_GET['color'] ?>');
        var loadingFontDim = 13;
        var loadingIconDim = 20;
        height = parseInt($("#<?= $_GET['name'] ?>_div").prop("offsetHeight") - 25);
        $('#<?= $_GET['name'] ?>_loading').css("height", height+"px");
        $('#<?= $_GET['name'] ?>_loading p').css("font-size", loadingFontDim+"px");
        $('#<?= $_GET['name'] ?>_loading i').css("font-size", loadingIconDim+"px");
        if(firstLoad != false)
        {
            $('#<?= $_GET['name'] ?>_loading').css("display", "block");
        }
        $("#<?= $_GET['name'] ?>_content").css("height", height);
        
        var alarmSet = false;
        var $div2blink = null;
        var blinkInterval = null;
        var colore_frame = "<?= $_GET['frame_color'] ?>";
        var nome_wid = "<?= $_GET['name'] ?>_div";
        $("#<?= $_GET['name'] ?>_div").css({'background-color':colore_frame});
        
        
        var link_w = "<?= $_GET['link_w'] ?>";
        var divChartContainer = $('#<?= $_GET['name'] ?>_content');
        var linkElement = $('#<?= $_GET['name'] ?>_link_w');
        
        $.ajax({//Inizio AJAX getParametersWidgets.php
            url: "../widgets/getParametersWidgets.php",
            type: "GET",
            data: {"nomeWidget": ["<?= $_GET['name'] ?>"]},
            async: true,
            dataType: 'json',
            success: function (msg) 
            {
                var parameters = {};
                var valColori1 = {};
                var valColori2 = {};
                var valColori3 = {};
                var valLimit1 = {};
                var valLimit2 = {};
                var valLimit3 = {};
                var parametri = msg.param.parameters;
                var contenuto = jQuery.parseJSON(parametri);
                var sizeColumnsWidget = parseInt(msg.param.size_columns);
                var sizeRowsWidget = parseInt(msg.param.size_rows);
                var paneObj = null;
                var rangeMin = null;
                var rangeMax = null;
                var udm = msg.param.udm;
                
                if (contenuto !== null) 
                {
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
                    
                    if((parameters["<?= $_GET['name'] ?>"].limitSup1 != null) && (parameters["<?= $_GET['name'] ?>"].limitSup1 != "") && (typeof parameters["<?= $_GET['name'] ?>"].limitSup1 !== "undefined")) 
                    {
                        valLimit1["<?= $_GET['name'] ?>"] = parameters["<?= $_GET['name'] ?>"].limitSup1;
                    }
                    else
                    {
                        valLimit1["<?= $_GET['name'] ?>"] = null;
                    }
                    
                    if((parameters["<?= $_GET['name'] ?>"].limitSup2 != null) && (parameters["<?= $_GET['name'] ?>"].limitSup2 != "") && (typeof parameters["<?= $_GET['name'] ?>"].limitSup2 !== "undefined")) 
                    {
                        valLimit2["<?= $_GET['name'] ?>"] = parameters["<?= $_GET['name'] ?>"].limitSup2;
                    }
                    else
                    {
                        valLimit2["<?= $_GET['name'] ?>"] = null;
                    }
                    
                    /*if((parameters["<?= $_GET['name'] ?>"].limitSup3 != null) && (parameters["<?= $_GET['name'] ?>"].limitSup3 != "") && (typeof parameters["<?= $_GET['name'] ?>"].limitSup3 !== "undefined")) 
                    {
                        valLimit3["<?= $_GET['name'] ?>"] = parameters["<?= $_GET['name'] ?>"].limitSup3;
                    }
                    else
                    {
                        valLimit3["<?= $_GET['name'] ?>"] = 0;
                    }*/
                } 
                else 
                {
                    valColori1["<?= $_GET['name'] ?>"] = colors.GREEN; 
                    valColori2["<?= $_GET['name'] ?>"] = colors.ORANGE; 
                    valColori3["<?= $_GET['name'] ?>"] = colors.RED; 
                    valLimit1["<?= $_GET['name'] ?>"] = null;
                    valLimit2["<?= $_GET['name'] ?>"] = null;
                    //valLimit3["<?= $_GET['name'] ?>"] = 0;
                }

                $.ajax({//Inizio AJAX getDataMetrics.php
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
                        var shownValue = null;
                        var pattern = /Percentuale\//;
                        var threshold = null;
                        var thresholdEval = null;
                        var thicknessVal = null;
                        var plotOptionsObj = null;
                        
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
                                    if(msg.data[0].commit.author.value_perc1 != null)
                                    {
                                        udm = "%";
                                        shownValue = parseFloat(parseFloat(msg.data[0].commit.author.value_perc1).toFixed(1));
                                    }
                                    break;

                                default:
                                    break;
                            }
                        }
                        
                        if(shownValue > maxGauge)
                        {
                            maxGauge = shownValue; 
                        }
                        if(shownValue < minGauge)
                        {
                            minGauge = shownValue; 
                        }
                        
                        if(sizeRowsWidget <= 4)
                        {
                            thicknessVal = 7;
                        }
                        else
                        {
                            thicknessVal = 10;
                        }
                        
                        //Controllo tipo metrica non compatibile col widget
                        if((shownValue != null) && (minGauge != null) && (maxGauge != null))
                        {
                            if((threshold === null) || (thresholdEval === null))
                            {
                                //In questo caso non mostriamo soglia d'allarme.
                                threshold = 0;
                                
                                //Per qualsiasi combinazione non prevista impostiamo un unico colore (verde)
                                plotBandSet = [{
                                            from: minGauge,
                                            to: maxGauge,
                                            color: valColori1["<?= $_GET['name'] ?>"],
                                            thickness: thicknessVal
                                        }];
                                
                                if((valLimit1["<?= $_GET['name'] ?>"] == null) && (valLimit2["<?= $_GET['name'] ?>"] == null))
                                {
                                    plotBandSet = [{
                                            from: minGauge,
                                            to: maxGauge,
                                            color: valColori1["<?= $_GET['name'] ?>"],
                                            thickness: thicknessVal
                                        }];
                                }
                                else
                                {
                                    if(valLimit1["<?= $_GET['name'] ?>"] != null)
                                    {
                                        plotBandSet = [
                                        {  
                                            from: minGauge,
                                            to: valLimit1["<?= $_GET['name'] ?>"],
                                            color: valColori1["<?= $_GET['name'] ?>"],
                                            thickness: thicknessVal
                                        },
                                        {  
                                            from: valLimit1["<?= $_GET['name'] ?>"],
                                            to: maxGauge,
                                            color: valColori2["<?= $_GET['name'] ?>"],
                                            thickness: thicknessVal
                                        }
                                        ];
                                    }
                                    if((valLimit1["<?= $_GET['name'] ?>"] != null) && (valLimit2["<?= $_GET['name'] ?>"] != null))
                                    {
                                        plotBandSet = [
                                        {  
                                            from: minGauge,
                                            to: valLimit1["<?= $_GET['name'] ?>"],
                                            color: valColori1["<?= $_GET['name'] ?>"],
                                            thickness: thicknessVal
                                        },
                                        {  
                                            from: valLimit1["<?= $_GET['name'] ?>"],
                                            to: valLimit2["<?= $_GET['name'] ?>"],
                                            color: valColori2["<?= $_GET['name'] ?>"],
                                            thickness: thicknessVal
                                        },
                                        {  
                                            from: valLimit2["<?= $_GET['name'] ?>"],
                                            to: maxGauge,
                                            color: valColori3["<?= $_GET['name'] ?>"],
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
                                           alarmSet = true;
                                        }

                                        plotBandSet = [
                                            {
                                                from: minGauge,
                                                to: threshold,
                                                color: valColori3["<?= $_GET['name'] ?>"],
                                                thickness: thicknessVal
                                            },        
                                            {
                                                from: threshold,
                                                to: maxGauge,
                                                color: valColori1["<?= $_GET['name'] ?>"],
                                                thickness: thicknessVal
                                            }, 
                                        ];
                                        break;

                                    //Allarme attivo se il valore attuale è sopra la soglia
                                    case '>':
                                        if(shownValue > threshold)
                                        {
                                           //Allarme
                                           alarmSet = true;
                                        }

                                        plotBandSet = [
                                            {
                                                from: minGauge,
                                                to: threshold,
                                                color: valColori1["<?= $_GET['name'] ?>"],
                                                thickness: thicknessVal
                                            }, 
                                            {
                                                from: threshold,
                                                to: maxGauge,
                                                color: valColori3["<?= $_GET['name'] ?>"],
                                                thickness: thicknessVal
                                            }, 
                                        ];
                                        break;

                                    //Allarme attivo se il valore attuale è uguale alla soglia (errore sui float = 0.01%)
                                    case '=':
                                        delta = Math.abs(shownValue - threshold);
                                        deltaPerc = ((delta / threshold)*100);

                                        if(deltaPerc <= 0.01)
                                        {
                                            //Allarme
                                            alarmSet = true;
                                        }

                                        var incAlr = parseInt(threshold*0.05);
                                        var infAlr = threshold-incAlr;
                                        var supAlr = parseInt(threshold) + incAlr;

                                        plotBandSet = [
                                            {
                                                from: minGauge,
                                                to: infAlr,
                                                color: valColori1["<?= $_GET['name'] ?>"],
                                                thickness: thicknessVal
                                            },
                                            {
                                                from: infAlr,
                                                to: supAlr,
                                                color: valColori3["<?= $_GET['name'] ?>"],
                                                thickness: thicknessVal
                                            },
                                            {
                                                from: supAlr,
                                                to: maxGauge,
                                                color: valColori1["<?= $_GET['name'] ?>"],
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
                                            color: valColori1["<?= $_GET['name'] ?>"],
                                            thickness: thicknessVal
                                        }];
                                        break;
                                 }
                            }

                            var div2blink = $('#<?= $_GET['name'] ?>_desc_text');
                            blinkInterval = null;
                            if(alarmSet)
                            {
                                blinkInterval = setInterval(function(){
                                    div2blink.toggleClass("desc_text_alr");
                                },1000);
                            }

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
                                        "text-shadow": "1px 1px 1px rgba(0,0,0,0.35)",
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
                                            "text-shadow": "1px 1px 1px rgba(0,0,0,0.35)",
                                            fontFamily: 'Verdana'
                                        },
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
                                            "text-shadow": "1px 1px 1px rgba(0,0,0,0.30)",
                                            fontFamily: 'Verdana'
                                        }
                                    },
                                    title: udmObj,
                                    plotBands: plotBandSet 
                                }; 

                                $("#<?= $_GET['name'] ?>_content").attr("class", "container-speedomenter-x1");
                            }
                            else
                            {
                                //Speedo grande (anche in caso di valore errato su DB)
                                $('#<?= $_GET['name'] ?>_desc').width('74%');
                                $('#<?= $_GET['name'] ?>_desc_text').css("width", "90%");
                                $('#<?= $_GET['name'] ?>_desc_text').css("height", "100%");
                                
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
                                    size: '100%',
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
                                        "text-shadow": "1px 1px 1px rgba(0,0,0,0.35)",
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
                                            "text-shadow": "1px 1px 1px rgba(0,0,0,0.35)",
                                            fontFamily: 'Verdana'
                                        },
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
                                            "text-shadow": "1px 1px 1px rgba(0,0,0,0.30)",
                                            fontFamily: 'Verdana'
                                        }
                                    },
                                    title: udmObj,
                                    plotBands: plotBandSet 
                                }; 

                                $("#<?= $_GET['name'] ?>_content").attr("class", "container-speedomenter-x2");
                            }

                            if(firstLoad != false)
                            {
                                $('#<?= $_GET['name'] ?>_loading').css("display", "none");
                                $('#<?= $_GET['name'] ?>_content').css("display", "block");
                            }
                            
                            /**
                             * Creazione diagramma
                             */
                            var chart = $('#<?= $_GET['name'] ?>_content').highcharts({
                                credits: {
                                    enabled: false
                                },
                                chart: {
                                    type: 'gauge',
                                    backgroundColor: '<?= $_GET['color'] ?>',
                                    plotBackgroundColor: null,
                                    plotBackgroundImage: null,
                                    plotBorderWidth: 0,
                                    plotShadow: false,
                                },
                                //NON RIMUOVERE        
                                title: {
                                    text: '',
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
                        else
                        {
                            $('#<?= $_GET['name'] ?>_content').html("<p style='text-align: center; font-size: 18px'>Nessun dato disponibile</p>");
                        }
                        
                        if (link_w.trim()) 
                        {
                            if(linkElement.length === 0)
                            {
                               linkElement = $("<a id='<?= $_GET['name'] ?>_link_w' href='<?= $_GET['link_w'] ?>' target='_blank' class='elementLink2'>");
                               divChartContainer.wrap(linkElement); 
                            }
                        }
                        
                        $('#source_<?= $_GET['name'] ?>').on('click', function () {
                            $('#dialog_<?= $_GET['name'] ?>').show();
                        });
                        $('#close_popup_<?= $_GET['name'] ?>').on('click', function () {
                            $('#dialog_<?= $_GET['name'] ?>').hide();
                        });

                    }
                });//Fine AJAX getDataMetrics.php

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
        });//Fine AJAX getParametersWidgets.php

    });//Fine document.ready

</script>
<div class="widget" id="<?= $_GET['name'] ?>_div">
    <div class='ui-widget-content'>
        <div id='<?= $_GET['name'] ?>_desc' class="desc"></div><div class="icons-modify-widget"><a class="icon-cfg-widget" href="#"><span class="glyphicon glyphicon-cog glyphicon-modify-widget" aria-hidden="true"></span></a><a class="icon-remove-widget" href="#"><span class="glyphicon glyphicon-remove glyphicon-modify-widget" aria-hidden="true"></span></a></div>
        <div id="countdown_<?= $_GET['name'] ?>" class="countdown"></div>
        <div id="<?= $_GET['name'] ?>_loading" class="loadingDiv">
            <div class="loadingTextDiv">
                <p>Loading data, please wait</p>
            </div>
            <div class ="loadingIconDiv">
                <i class='fa fa-spinner fa-spin'></i>
            </div>
        </div>
        <div id="<?= $_GET['name'] ?>_content" class="content"></div><!--  style="border-color: red; border-style: solid; border-width: 1px; width: 100%" -->
    </div>
</div>
        