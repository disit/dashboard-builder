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
    $(document).ready(function <?= $_GET['name'] ?>(firstLoad) 
    {
        $('#<?= $_GET['name'] ?>_desc').width('74%');
        $('#<?= $_GET['name'] ?>_desc').html('<span><a id ="info_modal" href="#" class="info_source"><img id="source_<?= $_GET['name'] ?>" src="../management/img/info.png" class="source_button"></a><div id="<?= $_GET['name'] ?>_desc_text" class="desc_text" title="<?= preg_replace('/_/', ' ', $_GET['title']) ?>"><?= preg_replace('/_/', ' ', $_GET['title']) ?></div></span>');
        $('#<?= $_GET['name'] ?>_desc_text').css("width", "90%");
        $('#<?= $_GET['name'] ?>_desc_text').css("height", "100%");
        $("#<?= $_GET['name'] ?>_loading").css("background-color", '<?= $_GET['color'] ?>');
        height = parseInt($("#<?= $_GET['name'] ?>_div").prop("offsetHeight") - 25);
        $('#<?= $_GET['name'] ?>_loading').css("height", height+"px");
        $("#<?= $_GET['name'] ?>_content").css("height", height);
        
        var fontSize = "<?= $_GET['fontSize'] ?>";
        var fontColor = "<?= $_GET['fontColor'] ?>";
        
        var colore_frame = "<?= $_GET['frame_color'] ?>";
        var nome_wid = "<?= $_GET['name'] ?>_div";
        var height = null;
        var sizeRowsWidget = null;
        var loadingFontDim = 13;
        var loadingIconDim = 20;
        if(firstLoad != false)
        {
            $('#<?= $_GET['name'] ?>_loading').css("display", "block");
        }
        
        $('#<?= $_GET['name'] ?>_loading p').css("font-size", loadingFontDim+"px");
        $('#<?= $_GET['name'] ?>_loading i').css("font-size", loadingIconDim+"px");
        
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
            success: function (msg) {
                if (msg != null)
                {
                    sizeRowsWidget = parseInt(msg.param.size_rows);
                }
                
                $.ajax({
                url: "../widgets/getDataMetrics.php",
                data: {"IdMisura": ["<?= $_GET['metric'] ?>"]},
                type: "GET",
                async: true,
                dataType: 'json',
                success: function (msg) {
                    var seriesDataGreen = [];
                    var seriesDataRed = [];
                    var seriesDataWhite = [];
                    var value1 = (msg.data[0].commit.author.value_perc1) * 100;
                    valueGreen = parseFloat(parseFloat(value1).toFixed(2))
                    var value2 = (msg.data[0].commit.author.value_perc2) * 100;
                    valueRed = parseFloat(parseFloat(value2).toFixed(2));
                    var value3 = (msg.data[0].commit.author.value_perc3) * 100;
                    valueWhite = parseFloat(parseFloat(value3).toFixed(2));
                    var desc = msg.data[0].commit.author.descrip;
                    var object = msg.data[0].commit.author.value_text;
                    seriesDataGreen.push(['Green', valueGreen]);
                    seriesDataRed.push(['Red', valueRed]);
                    seriesDataWhite.push(['White', valueWhite]);

                    if(firstLoad != false)
                    {
                        $('#<?= $_GET['name'] ?>_loading').css("display", "none");
                        $('#<?= $_GET['name'] ?>_content').css("display", "block");
                    }
                    
                    $('#<?= $_GET['name'] ?>_process').html("Processo: " + object);

                    $('#<?= $_GET['name'] ?>_content').highcharts({
                        credits: {
                            enabled: false
                        },
                        exporting: {
                            enabled: false
                        },
                        chart: {
                            type: 'bar',
                            backgroundColor: '<?= $_GET['color'] ?>',
                            spacingBottom: 10,
                            spacingTop: 10
                        },
                        title: {
                            text: ''

                        },
                        xAxis: {
                            visible: false
                        },
                        yAxis: {
                            visible: false,
                            min: 0,
                            max: 100,
                            title: {
                                text: ''
                            }
                        },
                        tooltip: {
                            enabled: false,
                            pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br/>',
                            shared: true,
                        },
                        plotOptions: {
                            bar: {
                                stacking: 'normal',
                                dataLabels: {
                                    formatter: function () {
                                        var value;
                                        if (this.y === 100.0) {
                                            value = Highcharts.numberFormat(this.y, 0);
                                        }
                                        else {
                                            value = Highcharts.numberFormat(this.y, 1);
                                        }

                                        return value + '%';

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
                        series: [{
                                showInLegend: false,
                                name: 'red',
                                color: 'red',
                                data: seriesDataRed,
                                pointWidth: 100
                            }, {
                                showInLegend: false,
                                name: 'white',
                                color: 'white',
                                data: seriesDataWhite,
                                pointWidth: 100
                            }, {
                                showInLegend: false,
                                name: 'green',
                                color: 'green',
                                data: seriesDataGreen,
                                pointWidth: 100
                            }]
                    });
                    
                    if (link_w.trim()) 
                    {
                        if(linkElement.length == 0)
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

                    var counter = <?= $_GET['freq'] ?>;
                    var countdown = setInterval(function () {
                        counter--;
                        if (counter > 60) 
                        {
                            $("#countdown_<?= $_GET['name'] ?>").text(Math.floor(counter / 60) + "m");
                        } 
                        else 
                        {
                            $("#countdown_<?= $_GET['name'] ?>").text(counter + "s");
                        }
                        if (counter === 0) 
                        {
                            $("#countdown_<?= $_GET['name'] ?>").text(counter + "s");
                            clearInterval(countdown);
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
        <div id='<?= $_GET['name'] ?>_desc' class="desc"></div><div class="icons-modify-widget"><a class="icon-cfg-widget" href="#"><span class="glyphicon glyphicon-cog glyphicon-modify-widget" aria-hidden="true"></span></a><a class="icon-remove-widget" href="#"><span class="glyphicon glyphicon-remove glyphicon-modify-widget" aria-hidden="true"></span></a></div><div id="countdown_<?= $_GET['name'] ?>" class="countdown"></div>
        <div id="<?= $_GET['name'] ?>_loading" class="loadingDiv">
            <div class="loadingTextDiv">
                <p>Loading data, please wait</p>
            </div>
            <div class ="loadingIconDiv">
                <i class='fa fa-spinner fa-spin'></i>
            </div>
        </div>
        <div id="<?= $_GET['name'] ?>_content" class="content smartDS">   
        </div>
    </div>	
</div> 