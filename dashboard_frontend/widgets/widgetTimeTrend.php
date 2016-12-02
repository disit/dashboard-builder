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
        $('#<?= $_GET['name'] ?>_splane_content_desc').width('77%');
        $('#<?= $_GET['name'] ?>_splane_content_desc').html('<span><a id ="info_modal" href="#" class="info_source"><img id="source_<?= $_GET['name'] ?>" src="../management/img/info.png" class="source_button"></a><div id="<?= $_GET['name'] ?>_desc_text" class="desc_text" title="<?= preg_replace('/_/', ' ', $_GET['title']) ?>"><?= preg_replace('/_/', ' ', $_GET['title']) ?></div></span>');
        $("#<?= $_GET['name'] ?>_loading").css("background-color", '<?= $_GET['color'] ?>');            
        var height = parseInt($("#<?= $_GET['name'] ?>_div").prop("offsetHeight") - 25);
        var loadingFontDim = 13;
        var loadingIconDim = 20;
        $('#<?= $_GET['name'] ?>_loading').css("height", height+"px");
        $('#<?= $_GET['name'] ?>_loading p').css("font-size", loadingFontDim+"px");
        $('#<?= $_GET['name'] ?>_loading i').css("font-size", loadingIconDim+"px");
        if(firstLoad != false)
        {
            $('#<?= $_GET['name'] ?>_loading').css("display", "block");
        }
        $("#<?= $_GET['name'] ?>_splane_content").css("height", height);  
        
        var threshold = null;
        var thresholdEval = null;
        var alarmSet = false;
        var $div2blink = null;
        var blinkInterval = null;
        var flagNumeric = false;
        var colore_frame = "<?= $_GET['frame_color'] ?>";
        var nome_wid = "<?= $_GET['name'] ?>_div";
        $("#<?= $_GET['name'] ?>_div").css({'background-color':colore_frame});
        
        var fontSize = "<?= $_GET['fontSize'] ?>";
        var fontColor = "<?= $_GET['fontColor'] ?>";
        
        //Estrazione dei parametri del widget
        $.ajax({
            url: "../widgets/getParametersWidgets.php",
            type: "GET",
            data: {"nomeWidget": ["<?= $_GET['name'] ?>"]},
            async: true,
            dataType: 'json',
            success: function (msg) 
            {
                var parametri = msg.param.parameters;
                var contenuto = jQuery.parseJSON(parametri);
                var sizeRowsWidget = parseInt(msg.param.size_rows);
                
                $.ajax({
                url: "../widgets/getDataMetricsForTimeTrend.php",
                data: {"IdMisura": ["<?= $_GET['metric'] ?>"], "time": "<?= $_GET['tmprange'] ?>", "compare":0},
                type: "GET",
                async: true,
                dataType: 'json',
                success: function (msg) 
                {        
                    
                    if(msg.data.length==0)
                    {
                        if(firstLoad != false)
                        {
                            $('#<?= $_GET['name'] ?>_loading').css("display", "none");
                            $('#<?= $_GET['name'] ?>_splane_content').css("display", "");
                        }
            
                        $('#<?= $_GET['name'] ?>_splane_content').html("<p style='text-align: center; font-size: 18px'>Nessun dato disponibile</p>");
                    }
                    else
                    { 
                        
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


                    var range="<?= $_GET['tmprange'] ?>"; 
                    var seriesData = [];
                    var valuesData = [];
                    var desc = msg.data[0].commit.author.descrip;
                    thresholdEval = msg.data[0].commit.author.thresholdEval;
                    threshold = msg.data[0].commit.author.threshold;
                    var plotArray = null;
                    var value = null

                    for (var i = 0; i < msg.data.length; i++) 
                    {
                        var day = msg.data[i].commit.author.computationDate;
                        if ((msg.data[i].commit.author.value !== null) && (msg.data[i].commit.author.value !== "")) 
                        {
                            value = parseFloat(parseFloat(msg.data[i].commit.author.value).toFixed(1));
                            flagNumeric = true;
                        } else if ((msg.data[i].commit.author.value_perc1 !== null) && (msg.data[i].commit.author.value_perc1 !== "")) {
                            if (value === 100.0) {
                                value = parseFloat(parseFloat(msg.data[i].commit.author.value_perc1).toFixed(0));
                            } else {
                                value = parseFloat(parseFloat(msg.data[i].commit.author.value_perc1).toFixed(1));
                            }
                            flagNumeric = true;
                        }

                        var day_parts = day.substring(0, day.indexOf(' ')).split('-');

                        if('<?= $_GET['tmprange']=='1/DAY' || explode("/",$_GET['tmprange'])[1] =='HOUR' ?>'=='1') {
                          var time_parts = day.substr(day.indexOf(' ') + 1, 5).split(':');
                          var date=Date.UTC(day_parts[0], day_parts[1]-1, day_parts[2], time_parts[0], time_parts[1]);
                        }
                        else 
                          var date = Date.UTC(day_parts[0], day_parts[1] - 1, day_parts[2]);
                        seriesData.push([date, value]);
                        valuesData.push(value);
                     }

                     var maxValue = Math.max.apply(Math, valuesData);
                     var nInterval = parseFloat((maxValue / 4).toFixed(1));

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
                    }
                    
                    if(firstLoad != false)
                    {
                        $('#<?= $_GET['name'] ?>_loading').css("display", "none");
                        $('#<?= $_GET['name'] ?>_splane_content').css("display", "block");
                    }

                    $('#<?= $_GET['name'] ?>_splane_content').highcharts({
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
                            text: '',
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
                                    "text-shadow": "1px 1px 1px rgba(0,0,0,0.35)"
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
                                    "text-shadow": "1px 1px 1px rgba(0,0,0,0.35)"
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
                     
                    var link_w = "<?= $_GET['link_w'] ?>";
                    var divChartContainer = $('#<?= $_GET['name'] ?>_splane_content');
                    var linkElement = $('#<?= $_GET['name'] ?>_link_w');
                    if (link_w.trim()) 
                    {
                       if(linkElement.length === 0)
                       {
                          linkElement = $("<a id='<?= $_GET['name'] ?>_link_w' href='<?= $_GET['link_w'] ?>' target='_blank' class='elementLink2'>");
                          divChartContainer.wrap(linkElement); 
                       }
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
                        $("#countdown_<?= $_GET['name'] ?>").text(counter);
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
                },
                error: function (jqXHR, textStatus, errorThrown) 
                {
                    console.log('jqXHR:');
                    console.log(jqXHR);
                    console.log('textStatus:');
                    console.log(textStatus);
                    console.log('errorThrown:');
                    console.log(errorThrown);
                }
           }); 
            }//Chiusura success    
        });//Chiusura AJAX getParametersWigdets.
    });  
    
</script>

<div class="widget" id="<?= $_GET['name'] ?>_div">
    <div class='ui-widget-content'>
        <div id="<?= $_GET['name'] ?>_splane_content_desc" class='desc'></div><div class="icons-modify-widget"><a class="icon-cfg-widget" href="#"><span class="glyphicon glyphicon-cog glyphicon-modify-widget" aria-hidden="true"></span></a><a class="icon-remove-widget" href="#"><span class="glyphicon glyphicon-remove glyphicon-modify-widget" aria-hidden="true"></span></a></div><div id="countdown_<?= $_GET['name'] ?>" class="countdown"></div>
            <div id="<?= $_GET['name'] ?>_loading" class="loadingDiv">
                <div class="loadingTextDiv">
                    <p>Loading data, please wait</p>
                </div>
                <div class ="loadingIconDiv">
                    <i class='fa fa-spinner fa-spin'></i>
                </div>
            </div>
            <div id="<?= $_GET['name'] ?>_splane_content" class="content">
            </div>
    </div>	
</div> 