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
    var colors = {
      GREEN: '#008800',
      ORANGE: '#FF9933',
      LOW_YELLOW: '#ffffcc',
      RED: '#FF0000',
    };
    var parameters = {};
    var valColori1 = {};
    var valColori2 = {};

    $(document).ready(function <?= $_GET['name'] ?>(firstLoad)
    {
        $('#<?= $_GET['name'] ?>_desc').width('70%');
        $('#<?= $_GET['name'] ?>_desc').html('<span><a id ="info_modal" href="#" class="info_source"><img id="source_<?= $_GET['name'] ?>" src="../management/img/info.png" class="source_button"></a><div id="<?= $_GET['name'] ?>_desc_text" class="desc_text" title="<?= preg_replace('/_/', ' ', $_GET['title']) ?>"><?= preg_replace('/_/', ' ', $_GET['title']) ?></div></span>');
        $('#<?= $_GET['name'] ?>_desc_text').css("width", "80%");
        $('#<?= $_GET['name'] ?>_desc_text').css("height", "100%");
        $("#<?= $_GET['name'] ?>_loading").css("background-color", '<?= $_GET['color'] ?>');
        var loadingFontDim = 13;
        var loadingIconDim = 20;
        var height = parseInt($("#<?= $_GET['name'] ?>_div").prop("offsetHeight") - 25);
        $("#<?= $_GET['name'] ?>_content").css("height", height);
        $('#<?= $_GET['name'] ?>_loading').css("height", height+"px");
        $('#<?= $_GET['name'] ?>_loading p').css("font-size", loadingFontDim+"px");
        $('#<?= $_GET['name'] ?>_loading i').css("font-size", loadingIconDim+"px");
        if(firstLoad != false)
        {
            $('#<?= $_GET['name'] ?>_loading').css("display", "block");
        }
        
        //colore finestra
        var colore_frame = "<?= $_GET['frame_color'] ?>";
        var nome_wid = "<?= $_GET['name'] ?>_div";
        $("#<?= $_GET['name'] ?>_div").css({'background-color':colore_frame});
        
        var fontSize = "<?= $_GET['fontSize'] ?>";
        var fontColor = "<?= $_GET['fontColor'] ?>";
        
        var link_w = "<?= $_GET['link_w'] ?>";
        var divChartContainer = $('#<?= $_GET['name'] ?>_content');
        var linkElement = $('#<?= $_GET['name'] ?>_link_w');
        var alarmSet = false;
        var $div2blink = null;
        var blinkInterval = null;
        
        //Estrazione dei parametri del widget
        $.ajax({
            url: "../widgets/getParametersWidgets.php",
            type: "GET",
            data: {"nomeWidget": ["<?= $_GET['name'] ?>"]},
            async: true,
            dataType: 'json',
            success: function (msg) {
                var parametri = msg.param.parameters;
                var contenuto = jQuery.parseJSON(parametri);
                var sizeRowsWidget = parseInt(msg.param.size_rows);
                var rangeMin = null;
                var rangeMax = null;
                
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
                    
                    if((parameters["<?= $_GET['name'] ?>"].color1 != null) && (parameters["<?= $_GET['name'] ?>"].color1 != "") && (typeof parameters["<?= $_GET['name'] ?>"].color1 !== "undefined")) 
                    {
                        valColori1["<?= $_GET['name'] ?>"] = parameters["<?= $_GET['name'] ?>"].color1;
                    }
                    else
                    {
                        valColori1["<?= $_GET['name'] ?>"] = colors.GREEN; 
                    }
                     
                    if((parameters["<?= $_GET['name'] ?>"].color2 != null) && (parameters["<?= $_GET['name'] ?>"].color2 != "") && (typeof parameters["<?= $_GET['name'] ?>"].color2 !== "undefined")) 
                    {
                        valColori2["<?= $_GET['name'] ?>"] = parameters["<?= $_GET['name'] ?>"].color2;
                    }
                    else
                    {
                        valColori2["<?= $_GET['name'] ?>"] = colors.LOW_YELLOW;
                    }
                } 
                else 
                {
                    valColori1["<?= $_GET['name'] ?>"] = colors.GREEN;
                    valColori2["<?= $_GET['name'] ?>"] = colors.LOW_YELLOW;
                }

                $.ajax({
                    url: "../widgets/getDataMetrics.php",
                    data: {"IdMisura": ["<?= $_GET['metric'] ?>"]},
                    type: "GET",
                    async: true,
                    dataType: 'json',
                    success: function (msg) {
                        var pattern = /Percentuale\//;
                        var metricType = msg.data[0].commit.author.metricType;
                        var seriesMainData = [];
                        var seriesComplData = [];
                        var mainColor = valColori1["<?= $_GET['name'] ?>"];
                        var complColor = valColori2["<?= $_GET['name'] ?>"];
                        var minGauge = null;
                        var maxGauge = null;
                        var threshold = parseInt(msg.data[0].commit.author.threshold);
                        var thresholdEval = msg.data[0].commit.author.thresholdEval;
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
                        var desc = msg.data[0].commit.author.descrip;
                        var yAxisObj = null;
                        
                        if(pattern.test(metricType))
                        {
                            minGauge = 0;
                            
                            if(msg.data[0].commit.author.value_perc1 != null)
                            {
                                maxGauge = 100;
                                udm = "%";
                                shownValue = parseFloat(parseFloat(msg.data[0].commit.author.value_perc1).toFixed(1));
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
                                var part = parseFloat(parseFloat(msg.data[0].commit.author.quant_perc1).toFixed(1));
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
                                    if((rangeMin != null) && (rangeMax != null))
                                    {
                                        minGauge = parseInt(rangeMin);
                                        maxGauge = parseInt(rangeMax);
                                    }

                                    if(msg.data[0].commit.author.value_num != null)
                                    {
                                        shownValue = parseInt(msg.data[0].commit.author.value_num);
                                        shownValueCompl = maxGauge - shownValue;
                                        shownValueCompl = parseInt(shownValueCompl);
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

                                case "Float":
                                    if((rangeMin != null) && (rangeMax != null))
                                    {
                                        minGauge = parseInt(rangeMin);
                                        maxGauge = parseInt(rangeMax);    
                                    }

                                    if(msg.data[0].commit.author.value_num != null)
                                    {
                                        shownValue = parseFloat(parseFloat(msg.data[0].commit.author.value_num).toFixed(1));
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
                                    if(msg.data[0].commit.author.value_perc1 != null)
                                    {
                                        udm = "%";
                                        shownValue = parseFloat(parseFloat(msg.data[0].commit.author.value_perc1).toFixed(1));
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
                        
                        if((shownValue != null) && (minGauge != null) && (maxGauge != null))
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
                                               complColor = colors.RED;
                                               alarmSet = true;
                                            }
                                            break;

                                        //Allarme attivo se il valore attuale è sopra la soglia
                                        case '>':
                                            if(shownValue > threshold)
                                            {
                                               //Allarme
                                               complColor = colors.RED;
                                               alarmSet = true;
                                            }
                                            break;

                                        //Allarme attivo se il valore attuale è uguale alla soglia (errore sui float = 0.1%)
                                        case '=':
                                            if(delta <= 0.1)
                                            {
                                                //Allarme
                                                complColor = colors.RED;
                                                alarmSet = true;
                                            }
                                            break;    

                                        //Non gestiamo altri operatori 
                                        default:
                                            break;
                                     }
                            }    
                                
                            seriesMainData.push(['Green', shownValue]);
                            seriesComplData.push(['Red', shownValueCompl]);
                            
                            if (link_w.trim()) 
                            {
                                if(linkElement.length === 0)
                                {
                                   linkElement = $("<a id='<?= $_GET['name'] ?>_link_w' href='<?= $_GET['link_w'] ?>' target='_blank' class='elementLink2'>");
                                   divChartContainer.wrap(linkElement); 
                                }
                            }
                            
                            if(firstLoad != false)
                            {
                                $('#<?= $_GET['name'] ?>_loading').css("display", "none");
                                $('#<?= $_GET['name'] ?>_content').css("display", "block");
                            }
                            
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
                                    spacingTop: 10,
                                },
                                title: {
                                    text: ''
                                },
                                xAxis: {
                                    visible: false,
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
                                                textOutline: "0px 0px contrast",
                                                "text-shadow": "1px 1px 1px rgba(0,0,0,0.3)"
                                            }
                                        }
                                    }
                                },
                                series: [
                                    {
                                        showInLegend: false,
                                        name: '<?= $_GET['metric'] ?>',
                                        color: complColor,
                                        data: seriesComplData,
                                        pointWidth: 600
                                    },
                                    {
                                        showInLegend: false,
                                        name: '<?= $_GET['metric'] ?>',
                                        color: mainColor,
                                        data: seriesMainData,
                                        pointWidth: 600
                                    }
                                    ]
                            }); //FINE HIGHCHARTS
                        }
                        else
                        {
                            $('#<?= $_GET['name'] ?>_content').html("<p style='text-align: center; font-size: 18px'>Nessun dato disponibile</p>");
                        }
                        $('#source_<?= $_GET['name'] ?>').on('click', function () {
                            $('#dialog_<?= $_GET['name'] ?>').show();
                        });
                        $('#close_<?= $_GET['name'] ?>').on('click', function () {
                            $('#dialog_<?= $_GET['name'] ?>').hide();
                        });
                        
                        $div2blink = $('#<?= $_GET['name'] ?>_desc_text');
                        blinkInterval = null;
                        if(alarmSet)
                        {
                            blinkInterval = setInterval(function(){
                                $div2blink.toggleClass("desc_text_alr");
                            },1000);
                        }
                        
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
        <div id="<?= $_GET['name'] ?>_content" class="content colunm_content"></div>
    </div>	
</div>