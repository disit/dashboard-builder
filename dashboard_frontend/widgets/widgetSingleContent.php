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
        $('#<?= $_GET['name'] ?>_desc').width('70%');
        $('#<?= $_GET['name'] ?>_desc').html('<span><a id ="info_modal" href="#" class="info_source"><img id="source_<?= $_GET['name'] ?>" src="../management/img/info.png" class="source_button"></a><div id="<?= $_GET['name'] ?>_desc_text" class="desc_text" title="<?= preg_replace('/_/', ' ', $_GET['title']) ?>"><?= preg_replace('/_/', ' ', $_GET['title']) ?></div></span>');
        $('#<?= $_GET['name'] ?>_desc_text').css("width", "80%");
        $('#<?= $_GET['name'] ?>_desc_text').css("height", "100%");
        $("#<?= $_GET['name'] ?>_loading").css("background-color", '<?= $_GET['color'] ?>');
        height = parseInt($("#<?= $_GET['name'] ?>_div").prop("offsetHeight") - 25);
        
        var loadingFontDim = 13; 
        var loadingIconDim = 20;
        $('#<?= $_GET['name'] ?>_loading').css("height", height+"px");
        $('#<?= $_GET['name'] ?>_loading p').css("font-size", loadingFontDim+"px");
        $('#<?= $_GET['name'] ?>_loading i').css("font-size", loadingIconDim+"px");
        if(firstLoad != false)
        {
            $('#<?= $_GET['name'] ?>_loading').css("display", "block");
        }
        
        $("#<?= $_GET['name'] ?>_utility").css("height", height);
        
        var value = null;
        var threshold = null;
        var thresholdEval = null;
        var flagNumeric = false;
        var alarmSet = false;
        var alarmInterval = null;
        var udm = "";
        var pattern = /Percentuale\//;
        var typeMetric = null;
        var height = null;
        var sizeRowsWidget = null;
        var fontRatio = null;
        var fontRatioSmall = null;
        
        var colore_frame = "<?= $_GET['frame_color'] ?>";
        var nome_wid = "<?= $_GET['name'] ?>_div";
        $("#<?= $_GET['name'] ?>_div").css({'background-color':colore_frame});
             
        var link_w = "<?= $_GET['link_w'] ?>";
        var divChartContainer = $('#<?= $_GET['name'] ?>_value');
        var linkElement = $('#<?= $_GET['name'] ?>_link_w');
        
        var fontColor = "<?= $_GET['fontColor'] ?>";
        $('#<?= $_GET['name'] ?>_value').css("color", fontColor);
        $('#<?= $_GET['name'] ?>_udm').css("color", fontColor);
        
         $.ajax({//Inizio AJAX getParametersWidgets.php
            url: "../widgets/getParametersWidgets.php",
            type: "GET",
            data: {"nomeWidget": ["<?= $_GET['name'] ?>"]},
            async: true,
            dataType: 'json',
            success: function (msg) {
                if (msg != null)
                {
                    udm = msg.param.udm;
                    sizeRowsWidget = parseInt(msg.param.size_rows);
                }
                
                $.ajax({
                    url: "../widgets/getDataMetrics.php",
                    data: {"IdMisura": ["<?= $_GET['metric'] ?>"]},
                    type: "GET",
                    async: true,
                    dataType: 'json',
                    success: function (msg) 
                    {
                        if((msg == null) || (msg.data[0] == null))
                        {
                            if(firstLoad != false)
                            {
                                $('#<?= $_GET['name'] ?>_loading').css("display", "none");
                                $("#<?= $_GET['name'] ?>_utility").css("display", "");
                            }
            
                            $('#<?= $_GET['name'] ?>_utility').html("<p style='text-align: center; font-size: 18px;'>Nessun dato disponibile</p>");
                        }
                        else
                        {
                            typeMetric = "<?= $_GET['type_metric'] ?>";

                            threshold = msg.data[0].commit.author.threshold;
                            thresholdEval = msg.data[0].commit.author.thresholdEval;

                            if((typeMetric === "Percentuale") || (pattern.test(typeMetric)))
                            {
                                udm = "%";
                                if((msg.data[0].commit.author.value_perc1 != null) && (msg.data[0].commit.author.value_perc1 != "") && (typeof msg.data[0].commit.author.value_perc1 != "undefined"))
                                {
                                    value = parseFloat(parseFloat(msg.data[0].commit.author.value_perc1).toFixed(1));
                                    if(value > 100)
                                    {
                                        value = 100;
                                    }
                                }
                                flagNumeric = true;
                            }
                            else
                            {
                                switch(typeMetric)
                                {
                                    case "Intero":
                                        if((msg.data[0].commit.author.value_num != null) && (msg.data[0].commit.author.value_num != "") && (typeof msg.data[0].commit.author.value_num != "undefined"))
                                        {
                                            value = parseInt(msg.data[0].commit.author.value_num);
                                        }
                                        flagNumeric = true;
                                        break;

                                    case "Float":
                                        if((msg.data[0].commit.author.value_num != null) && (msg.data[0].commit.author.value_num != "") && (typeof msg.data[0].commit.author.value_num != "undefined"))
                                        {
                                           value = parseFloat(parseFloat(msg.data[0].commit.author.value_num).toFixed(1)); 
                                        }
                                        flagNumeric = true;
                                        break;

                                    case "Testuale":
                                        value = msg.data[0].commit.author.value_text;
                                        break;
                                }
                            }

                            //Fattore di ingrandimento font calcolato sull'altezza in righe, base 4.
                            fontRatio = parseInt((sizeRowsWidget / 4)*65);
                            fontRatioSmall = parseInt((fontRatio / 100)*30);
                            fontRatio = fontRatio.toString() + "%";
                            fontRatioSmall = fontRatioSmall.toString() + "%";
                            $("#<?= $_GET['name'] ?>_value").css("font-size", fontRatio);
                            $("#<?= $_GET['name'] ?>_udm").css("font-size", fontRatioSmall);
                            
                            $("#<?= $_GET['name'] ?>_value").css({backgroundColor: '<?= $_GET['color'] ?>'});
                            $("#<?= $_GET['name'] ?>_udm").css({backgroundColor: '<?= $_GET['color'] ?>'});

                            if((udm != null))
                            {
                                if(udm.length <= 2)
                                {
                                    $("#<?= $_GET['name'] ?>_value").css("height", "100%");             
                                    $("#<?= $_GET['name'] ?>_value").css("alignItems", "center");
                                    if(((value != null) && (value != "") && (typeof value != "undefined") && (value != "NaN")) || (value == 0))
                                    {
                                        if(firstLoad != false)
                                        {
                                            $('#<?= $_GET['name'] ?>_loading').css("display", "none");
                                            $("#<?= $_GET['name'] ?>_utility").css("display", "block");
                                        }
                                        $("#<?= $_GET['name'] ?>_value").html(value + udm);
                                        //$("#<?= $_GET['name'] ?>_value_small_udm").css("font-size", fontRatioSmall);
                                    }
                                    else
                                    {
                                        if(firstLoad != false)
                                        {
                                            $('#<?= $_GET['name'] ?>_loading').css("display", "none");
                                            $("#<?= $_GET['name'] ?>_utility").css("display", "block");
                                        }
                                        $('#<?= $_GET['name'] ?>_utility').html("<p style='text-align: center; font-size: 18px;'>Nessun dato disponibile</p>");
                                    }
                                }
                                else
                                {
                                    var valueHeight = parseInt(height*0.7);
                                    var udmHeight = parseInt(height*0.3);
                                    $("#<?= $_GET['name'] ?>_value").css("height", valueHeight);
                                    $("#<?= $_GET['name'] ?>_value").css("alignItems", "flex-end");
                                    $("#<?= $_GET['name'] ?>_udm").css("height", udmHeight);
                                    if(((value != null) && (value != "") && (typeof value != "undefined") && (value != "NaN")) || (value == 0))
                                    {
                                        if(firstLoad != false)
                                        {
                                            $('#<?= $_GET['name'] ?>_loading').css("display", "none");
                                            $("#<?= $_GET['name'] ?>_utility").css("display", "block");
                                        }
                                        $("#<?= $_GET['name'] ?>_value").html(value);
                                        $("#<?= $_GET['name'] ?>_udm").html(udm);
                                        //$("#<?= $_GET['name'] ?>_value_small_udm").css("font-size", fontRatioSmall);
                                    }
                                    else
                                    {
                                        if(firstLoad != false)
                                        {
                                            $('#<?= $_GET['name'] ?>_loading').css("display", "none");
                                            $("#<?= $_GET['name'] ?>_utility").css("display", "block");
                                        } 
                                        $('#<?= $_GET['name'] ?>_utility').html("<p style='text-align: center; font-size: 18px;'>Nessun dato disponibile</p>");
                                    }
                                }
                            }
                            else
                            {
                                if(((value != null) && (value != "") && (typeof value != "undefined") && (value != "NaN")) || (value == 0))
                                {
                                    if(firstLoad != false)
                                    {
                                        $('#<?= $_GET['name'] ?>_loading').css("display", "none");
                                        $("#<?= $_GET['name'] ?>_utility").css("display", "block");
                                    }
                                    $("#<?= $_GET['name'] ?>_udm").css("display", "none");
                                    $("#<?= $_GET['name'] ?>_value").css("height", "100%");
                                    $("#<?= $_GET['name'] ?>_value").html(value);
                                    //$("#<?= $_GET['name'] ?>_value_small_udm").css("font-size", fontRatioSmall);
                                }
                                else
                                {
                                    if(firstLoad != false)
                                    {
                                        $('#<?= $_GET['name'] ?>_loading').css("display", "none");
                                        $("#<?= $_GET['name'] ?>_utility").css("display", "block");
                                    }
                                    $('#<?= $_GET['name'] ?>_utility').html("<p style='text-align: center; font-size: 18px;'>Nessun dato disponibile</p>");
                                }
                            }
                            
                            if (link_w.trim()) 
                            {
                                if(linkElement.length === 0)
                                {
                                   linkElement = $("<a id='<?= $_GET['name'] ?>_link_w' href='<?= $_GET['link_w'] ?>' target='_blank' class='elementLink2'>"); 
                                   divChartContainer.wrap(linkElement);
                                   linkElement.css("height", $("#<?= $_GET['name'] ?>_utility").css("height"));
                                }
                            }

                            if(flagNumeric && (threshold !== null) && (thresholdEval !== null))
                            {
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
        <div id='<?= $_GET['name'] ?>_utility' class="singleContentUtility">
            <div id='<?= $_GET['name'] ?>_value' class="singleContentValue"></div>
            <div id='<?= $_GET['name'] ?>_udm' class="singleContentUdm"></div>
        </div>
    </div>	
</div> 