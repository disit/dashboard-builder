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
    var colore_frame = "<?= $_GET['frame_color'] ?>";
    var nome_wid = "<?= $_GET['name'] ?>_div";
    $("#<?= $_GET['name'] ?>_div").css({'background-color':colore_frame});
    var fontSize = "<?= $_GET['fontSize'] ?>";
    var fontColor = "<?= $_GET['fontColor'] ?>";
    
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
    var threshold = null;
    var thresholdEval = null;
    var alarmSet = false;
    var $div2blink = null;
    var blinkInterval = null;
    var flagNumeric = false;
    
    function drawDiagram (id, dataObj, udm, pieObj){
        var formatObj = null;
        if(udm == "%")
        {
            formatObj = '{point.percentage:.1f} %';
        }
        else
        {
            formatObj = '{point.y}' + udm;
        }
        
        $(id).highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie',
                backgroundColor: '<?= $_GET['color'] ?>',
                options3d: {
                    enabled: false,
                    alpha: 45,
                    beta: 0
                }
            },
                    
            title: {
                text: ''
            },
            tooltip: {
                pointFormat: formatObj
            },
            plotOptions: {
                pie: pieObj
            },
            series: [{
                    data: dataObj
                }],
            exporting: {
                enabled: false
            },
            credits: {
                enabled: false
            }
        });
    };
    
    $(document).ready(function <?= $_GET['name'] ?>(firstLoad) 
    {
        $('#<?= $_GET['name'] ?>_desc').width('71%');
        $('#<?= $_GET['name'] ?>_desc').html('<span><a id ="info_modal" href="#" class="info_source"><img id="source_<?= $_GET['name'] ?>" src="../management/img/info.png" class="source_button"></a><div id="<?= $_GET['name'] ?>_desc_text" class="desc_text" title="<?= preg_replace('/_/', ' ', $_GET['title']) ?>"><?= preg_replace('/_/', ' ', $_GET['title']) ?></div></span>');
        $('#<?= $_GET['name'] ?>_desc_text').css("height", "100%");
        $("#<?= $_GET['name'] ?>_loading").css("background-color", '<?= $_GET['color'] ?>');
        var loadingFontDim = 13; 
        var loadingIconDim = 20;
        var height = parseInt($("#<?= $_GET['name'] ?>_div").prop("offsetHeight") - 25);
        $('#<?= $_GET['name'] ?>_loading').css("height", height+"px");
        $('#<?= $_GET['name'] ?>_loading p').css("font-size", loadingFontDim+"px");
        $('#<?= $_GET['name'] ?>_loading i').css("font-size", loadingIconDim+"px");
        if(firstLoad != false)
        {
            $('#<?= $_GET['name'] ?>_loading').css("display", "block");
        }
        
        var link_w = "<?= $_GET['link_w'] ?>";
        var divChartContainer = $('#<?= $_GET['name'] ?>_content');
        var linkElement = $('#<?= $_GET['name'] ?>_link_w');
        
        $("#<?= $_GET['name'] ?>_content").css("height", height);
        
        if (link_w.trim()) 
        {
            if(linkElement.length === 0)
            {
               linkElement = $("<a id='<?= $_GET['name'] ?>_link_w' href='<?= $_GET['link_w'] ?>' target='_blank' class='elementLink2'>");
               divChartContainer.wrap(linkElement); 
            }
        }
        
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
                var formatObj = null;
                
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
                        valColori2["<?= $_GET['name'] ?>"] = colors.RED;
                    }
                    
                    if((parameters["<?= $_GET['name'] ?>"].color3 != null) && (parameters["<?= $_GET['name'] ?>"].color3 != "") && (typeof parameters["<?= $_GET['name'] ?>"].color3 !== "undefined")) 
                    {
                        valColori3["<?= $_GET['name'] ?>"] = parameters["<?= $_GET['name'] ?>"].color3;
                    }
                    else
                    {
                        valColori3["<?= $_GET['name'] ?>"] = colors.ORANGE;
                    }
                } 
                else 
                {
                    valColori1["<?= $_GET['name'] ?>"] = colors.GREEN;
                    valColori2["<?= $_GET['name'] ?>"] = colors.RED;
                    valColori3["<?= $_GET['name'] ?>"] = colors.ORANGE;
                }

                $.ajax({
                    //importazione dati
                    url: "../widgets/getDataMetrics.php",
                    data: {"IdMisura": ["<?= $_GET['metric'] ?>"]},
                    type: "GET",
                    async: true,
                    dataType: 'json',
                    success: function (msg) {
                        var metricType = msg.data[0].commit.author.metricType;     
                        var minGauge = null;
                        var maxGauge = null;
                        var udm = "%";
                        var shownValues = [];
                        var totValues = [];
                        var pattern = /Percentuale\//;
                        var threshold = null;
                        var thresholdEval = null;
                        var stopsArray;
                        var delta = null;
                        var deltaPerc = null;
                        var dataObj = null;
                        var pieObj = null;
                        
                        threshold = msg.data[0].commit.author.threshold;
                        thresholdEval = msg.data[0].commit.author.thresholdEval;
                        
                        if(pattern.test(metricType))
                        {
                            minGauge = 0;
                            maxGauge = parseInt(metricType.substring(12));
                            if(msg.data[0].commit.author.quant_perc1 != null)
                            {
                                shownValues[0] = parseFloat(parseFloat(msg.data[0].commit.author.quant_perc1).toFixed(1));
                            }
                            
                            if(msg.data[0].commit.author.tot_perc1 != null)
                            {
                                totValues[0] = parseFloat(parseFloat(msg.data[0].commit.author.tot_perc1).toFixed(1));
                            }
                            
                            if(msg.data[0].commit.author.quant_perc2 != null)
                            {
                                shownValues[1] = parseFloat(parseFloat(msg.data[0].commit.author.quant_perc2).toFixed(1));
                            }
                            
                            if(msg.data[0].commit.author.tot_perc2 != null)
                            {
                                totValues[1] = parseFloat(parseFloat(msg.data[0].commit.author.tot_perc2).toFixed(1));
                            }
                            
                            if(msg.data[0].commit.author.quant_perc3 != null)
                            {
                                shownValues[2] = parseFloat(parseFloat(msg.data[0].commit.author.quant_perc3).toFixed(1));
                            }
                            
                            if(msg.data[0].commit.author.tot_perc3 != null)
                            {
                                totValues[2] = parseFloat(parseFloat(msg.data[0].commit.author.tot_perc2).toFixed(1));
                            }
                        }
                        else
                        {
                            if(metricType == "Percentuale")
                            {
                                minGauge = 0;
                                maxGauge = 100;
                                if(msg.data[0].commit.author.value_perc1 != null)
                                {
                                    shownValues[0] = parseFloat(parseFloat(msg.data[0].commit.author.value_perc1).toFixed(1));
                                }

                                if(msg.data[0].commit.author.value_perc2 != null)
                                {
                                    shownValues[1] = parseFloat(parseFloat(msg.data[0].commit.author.value_perc2).toFixed(1));
                                }

                                if(msg.data[0].commit.author.value_perc3 != null)
                                {
                                    shownValues[2] = parseFloat(parseFloat(msg.data[0].commit.author.value_perc3).toFixed(1));
                                }
                            }
                        }
                        
                        if(udm == "%")
                        {
                            formatObj = '{point.percentage:.1f} %';
                        }
                        else
                        {
                            formatObj = '{point.y}' + udm;
                        }
                        
                        switch(sizeRowsWidget)
                        {
                            case 4:
                                $('#<?= $_GET['name'] ?>_desc_text').css("width", "70%");
                                
                                pieObj = {
                                    allowPointSelect: true,
                                    cursor: 'pointer',
                                    size: '120%',
                                    depth: 35,
                                    center: ["50%", "55%"],
                                    dataLabels: {
                                        enabled: true,
                                        format: formatObj,
                                        distance: -0.5,
                                        style: {
                                            color: fontColor,
                                            fontFamily: 'Verdana',
                                            fontWeight: 'bold',
                                            fontSize: fontSize + "px",
                                            textOutline: "0px 0px contrast",
                                            "text-shadow": "1px 1px 1px rgba(0,0,0,0.3)"
                                        }
                                    }
                                };
                                break;

                            case 6:
                                $('#<?= $_GET['name'] ?>_desc_text').css("width", "90%");
                                
                                pieObj = {
                                    allowPointSelect: true,
                                    cursor: 'pointer',
                                    size: '95%',
                                    depth: 35,
                                    center: ["50%", "52%"],
                                    dataLabels: {
                                        enabled: true,
                                        format: formatObj,
                                        distance: 0,
                                        style: {
                                            color: fontColor,
                                            fontFamily: 'Verdana',
                                            fontWeight: 'bold',
                                            fontSize: fontSize + "px",
                                            textOutline: "0px 0px contrast",
                                            "text-shadow": "1px 1px 1px rgba(0,0,0,0.3)"
                                        }
                                    }
                                };
                                break;

                            case 8:
                                $('#<?= $_GET['name'] ?>_desc_text').css("width", "90%");
                                pieObj = {
                                    allowPointSelect: true,
                                    cursor: 'pointer',
                                    size: '95%',
                                    depth: 35,
                                    center: ["50%", "52%"],
                                    dataLabels: {
                                        enabled: true,
                                        format: formatObj,
                                        distance: 0,
                                        style: {
                                            color: fontColor,
                                            fontFamily: 'Verdana',
                                            fontWeight: 'bold',
                                            fontSize: fontSize + "px",
                                            textOutline: "0px 0px contrast",
                                            "text-shadow": "1px 1px 1px rgba(0,0,0,0.3)"
                                        }
                                    }
                                };
                                break;

                            default:
                                pieObj = {
                                    allowPointSelect: true,
                                    cursor: 'pointer',
                                    size: '115%',
                                    depth: 35,
                                    dataLabels: {
                                        enabled: true,
                                        format: formatObj,
                                        distance: 0,
                                        style: {
                                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black',
                                            fontSize: '14px'
                                        }
                                    }
                                };
                                break;
                        }
                        
                        if(firstLoad != false)
                        {
                            $('#<?= $_GET['name'] ?>_loading').css("display", "none");
                            $('#<?= $_GET['name'] ?>_content').css("display", "block");
                        }
                        
                        if((minGauge != null) && (maxGauge != null))
                        {
                            //Casi percentuale classica valorizzata
                            if((metricType == "Percentuale") && (shownValues[0] != null))
                            {
                                if((shownValues[1] != null) && (shownValues[2] != null))
                                {
                                    //Caso 3 valori
                                    dataObj = [
                                            {name: '', color: valColori1["<?= $_GET['name'] ?>"], y: shownValues[0]},
                                            {name: '', color: valColori2["<?= $_GET['name'] ?>"], y: shownValues[1]},
                                            {name: '', color: valColori3["<?= $_GET['name'] ?>"], y: shownValues[2]}
                                       ];
                                    drawDiagram("#<?= $_GET['name'] ?>_content", dataObj, "%", pieObj);
                                }
                                else
                                {
                                    if(shownValues[1] != null)
                                    {
                                        //Caso 2 valori
                                       dataObj = [
                                            {name: '', color: valColori1["<?= $_GET['name'] ?>"], y: shownValues[0]},
                                            {name: '', color: valColori2["<?= $_GET['name'] ?>"], y: shownValues[1]}
                                       ];
                                       drawDiagram("#<?= $_GET['name'] ?>_content", dataObj, "%", pieObj);
                                    }
                                    else
                                    {
                                       //Caso 1 valore
                                       dataObj = [
                                            {name: '', color: valColori1["<?= $_GET['name'] ?>"], y: shownValues[0]},
                                            {name: '', color: valColori2["<?= $_GET['name'] ?>"], y: (100 - shownValues[0])}
                                       ];
                                       drawDiagram("#<?= $_GET['name'] ?>_content", dataObj, "%", pieObj);
                                    }
                                }
                            }
                            else
                            {
                                if((metricType == "Percentuale") && (shownValues[0] == null) && ((shownValues[1] != null) || (shownValues[2] != null)))
                                {
                                   //Percentuali 2 e 3 singole, non ammesso. 
                                   $('#<?= $_GET['name'] ?>_content').html("<p style='text-align: center; position: relative; top: 45%; font-size: 18px'>Nessun dato disponibile</p>");    
                                }
                                
                                //Casi percentuale con modulo valorizzata
                                if(pattern.test(metricType) && (shownValues[0] != null) && (totValues[0] != null))
                                { 
                                    if((shownValues[1] != null) && (shownValues[2] != null) /*&& (totValues[1] != null) && (totValues[2] != null)*/)
                                    {
                                        //Caso 3 valori con modulo
                                        dataObj = [
                                                    {name: '', color: valColori1["<?= $_GET['name'] ?>"], y: shownValues[0]},
                                                    {name: '', color: valColori2["<?= $_GET['name'] ?>"], y: shownValues[1]},
                                                    {name: '', color: valColori3["<?= $_GET['name'] ?>"], y: shownValues[2]},
                                               ];
                                        drawDiagram("#<?= $_GET['name'] ?>_content", dataObj, "", pieObj);
                                    }
                                    else
                                    {
                                        if((shownValues[1] != null) /*&& (totValues[1] != null)*/)
                                        {
                                            //Caso 2 valori con modulo
                                            dataObj = [
                                                    {name: '', color: valColori1["<?= $_GET['name'] ?>"], y: shownValues[0]},
                                                    {name: '', color: valColori2["<?= $_GET['name'] ?>"], y: shownValues[1]}
                                               ];  
                                            drawDiagram("#<?= $_GET['name'] ?>_content", dataObj, "", pieObj);
                                        }
                                        else
                                        {
                                            //Caso 1 valore con modulo
                                            dataObj = [
                                                    {name: '', color: valColori1["<?= $_GET['name'] ?>"], y: shownValues[0]},
                                                    {name: '', color: valColori2["<?= $_GET['name'] ?>"], y: (totValues[0] - shownValues[0])}
                                               ];  
                                            drawDiagram("#<?= $_GET['name'] ?>_content", dataObj, "", pieObj);
                                        }
                                    }
                                }
                                else
                                {
                                    $('#<?= $_GET['name'] ?>_content').html("<p style='text-align: center; font-size: 18px'>Nessun dato disponibile</p>");    
                                }
                            } 
                        }
                        else
                        {
                            $('#<?= $_GET['name'] ?>_content').html("<p style='text-align: center; font-size: 18px'>Nessun dato disponibile</p>");
                        }

                        if((threshold !== null) && (thresholdEval !== null) && (shownValues[0] !== null))
                        {
                            delta = Math.abs(shownValues[0] - threshold);
                            //Distinguiamo in base all'operatore di confronto
                            switch(thresholdEval)
                            {
                               //Allarme attivo se il valore attuale è sotto la soglia
                               case '<':
                                   if(shownValues[0] < threshold)
                                   {
                                      alarmSet = true;
                                   }
                                   break;

                               //Allarme attivo se il valore attuale è sopra la soglia
                               case '>':
                                   if(shownValues[0] > threshold)
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
                        }

                        $('#source_<?= $_GET['name'] ?>').on('click', function () {
                            $('#dialog_<?= $_GET['name'] ?>').show();
                        });
                        $('#close_popup_<?= $_GET['name'] ?>').on('click', function () {

                            $('#dialog_<?= $_GET['name'] ?>').hide();
                        });
                        
                        var div2blink = $('#<?= $_GET['name'] ?>_desc_text');
                        var blinkInterval = null;
                        if(alarmSet)
                        {
                            blinkInterval = setInterval(function(){
                                div2blink.toggleClass("desc_text_alr");
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
                                setTimeout(<?= $_GET['name'] ?>(false), 1000);
                            }
                        }, 1000);
                    }});
            }
        });
    });

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
        <div id="<?= $_GET['name'] ?>_content" class="content"></div>	
    </div>
</div>

