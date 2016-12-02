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
    function hexToR(h) {return parseInt((cutHex(h)).substring(0,2),16)}
    function hexToG(h) {return parseInt((cutHex(h)).substring(2,4),16)}
    function hexToB(h) {return parseInt((cutHex(h)).substring(4,6),16)}
    function cutHex(h) {return (h.charAt(0)=="#") ? h.substring(1,7):h}
    function hexToRgb(hex)
    {
        var r = hexToR(hex);
        var g = hexToG(hex);
        var b = hexToB(hex);
        return r + ", " + g + ", " + b;
    }
    
    $(document).ready(function <?= $_GET['name'] ?>(firstLoad) 
    {
        var list_metrics = "<?= $_GET['metric'] ?>";
        var metrics = list_metrics.split('+');
        var list_type_metrics = "<?= $_GET['type_metric'] ?>";
        var type_metrics = list_type_metrics.split(',');
        
        var colore_frame = "<?= $_GET['frame_color'] ?>";
        var nome_wid = "<?= $_GET['name'] ?>_div";
        $("#<?= $_GET['name'] ?>_div").css("background-color", colore_frame);
        $("#<?= $_GET['name'] ?>_headerContainer").css("width", "100%");
        $("#<?= $_GET['name'] ?>_headerContainer").css("height", "25px");
        $("#<?= $_GET['name'] ?>_headerContainer").css("background-color", colore_frame);
        
        var iconsWidth = $("#<?= $_GET['name'] ?>_icons").width();
        
        var url = window.location.pathname;
        if(url.indexOf("index") >= 0)
        {
            var descWidth = parseInt($("#<?= $_GET['name'] ?>_div").prop("offsetWidth")) - 28;
        }
        else
        {
            var descWidth = parseInt($("#<?= $_GET['name'] ?>_div").prop("offsetWidth")) - iconsWidth - 28;
        }
        
        $('#<?= $_GET['name'] ?>_desc').css("width", descWidth + "px");
        $('#<?= $_GET['name'] ?>_desc').html('<span><a id ="info_modal" href="#" class="info_source"><img id="source_<?= $_GET['name'] ?>" src="../management/img/info.png" class="source_button"></a><div id="<?= $_GET['name'] ?>_desc_text" class="desc_text" title="<?= preg_replace('/_/', ' ', $_GET['title']) ?>"><?= preg_replace('/_/', ' ', $_GET['title']) ?></div></span>');
        $('#<?= $_GET['name'] ?>_desc_text').css("width", "90%");
        $('#<?= $_GET['name'] ?>_desc_text').css("height", "100%");
        
        $("#<?= $_GET['name'] ?>_loading").css("background-color", '<?= $_GET['color'] ?>');
        var loadingFontDim = 13; 
        var loadingIconDim = 20;
        var height = parseInt($("#<?= $_GET['name'] ?>_div").prop("offsetHeight") - 25);
        $("#<?= $_GET['name'] ?>_content").css("height", height);
        $('#<?= $_GET['name'] ?>_loading').css("height", height + "px");
        $('#<?= $_GET['name'] ?>_loading p').css("font-size", loadingFontDim+"px");
        $('#<?= $_GET['name'] ?>_loading i').css("font-size", loadingIconDim+"px");
        if(firstLoad != false)
        {
            $('#<?= $_GET['name'] ?>_loading').css("display", "block");
        }
        
        var fontColor = "<?= $_GET['fontColor'] ?>";
        $('#<?= $_GET['name'] ?>_content').css("color", fontColor);
        
        var alarmCount = 0;
        var alarms = [];
        var alarmIntervals = [];
        var desc2Blink = [];
        var $div2blink = null;
        var blinkInterval = null;
        var blinkFunction = function(blinkTarget)
        {
            blinkTarget.toggleClass("desc_text_alr"); 
        };
        
        var link_w = "<?= $_GET['link_w'] ?>";
        var divChartContainer = $('#<?= $_GET['name'] ?>_content');
        var linkElement = $('#<?= $_GET['name'] ?>_link_w');
       
        
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
                var widgetColor = msg.param.color_w;
                
                if (link_w.trim()) 
                {
                    if(linkElement.length === 0)
                    {
                       linkElement = $("<a id='<?= $_GET['name'] ?>_link_w' href='<?= $_GET['link_w'] ?>' target='_blank' class='elementLink2'>");
                       divChartContainer.wrap(linkElement); 
                    }
                }
                
                $.ajax({
                url: "../widgets/getDataMetrics.php",
                data: {"IdMisura": metrics},
                type: "GET",
                async: true,
                dataType: 'json',
                success: function (msg) {
                    var threshold = [];
                    var thresholdEval = [];
                    var flagNumeric = [];
                    var fontSize = null;
                    var rowColor = null;
                    var rowColorRgb = null;
                    
                    for(var i = 0; i < msg.data.length; i++) 
                    {
                        var value = null;
                        flagNumeric[i] = false;
                        alarms[i] = false;
                        alarmIntervals[i] = null;
                        var udm = "";
                        var pattern = /Percentuale\//;
                        threshold[i] = msg.data[i].commit.author.threshold;
                        thresholdEval[i] = msg.data[i].commit.author.thresholdEval;

                        if((type_metrics[i] === "Percentuale") || (pattern.test(type_metrics[i])))
                        {
                            udm = "%";
                            value = parseFloat(parseFloat(msg.data[i].commit.author.value_perc1).toFixed(1));
                            if(value > 100)
                            {
                                value = 100;
                            }
                            flagNumeric[i] = true;
                        }
                        else
                        {
                            switch(type_metrics[i])
                            {
                                case "Intero":
                                    value = parseInt(msg.data[i].commit.author.value_num);
                                    flagNumeric[i] = true;
                                    break;

                                case "Float":
                                    value = parseFloat(parseFloat(msg.data[i].commit.author.value_num).toFixed(1));
                                    flagNumeric[i] = true;
                                    break;

                                case "Testuale":
                                    value = msg.data[i].commit.author.value_text;
                                    break;
                            }
                        }

                        if(flagNumeric[i] && (threshold[i] !== null) && (thresholdEval[i] !== null))
                        {
                            delta = Math.abs(value - threshold[i]);
                            //Distinguiamo in base all'operatore di confronto
                            switch(thresholdEval[i])
                            {
                               //Allarme attivo se il valore attuale è sotto la soglia
                               case '<':
                                   if(value < threshold[i])
                                   {
                                      alarms[i] = true;
                                      alarmCount++;
                                   }
                                   break;

                               //Allarme attivo se il valore attuale è sopra la soglia
                               case '>':
                                   if(value > threshold[i])
                                   {
                                      alarms[i] = true;
                                      alarmCount++;
                                   }
                                   break;

                               //Allarme attivo se il valore attuale è uguale alla soglia (errore sui float = 0.1% la distanza dalla soglia rispetto alla soglia stessa)
                               case '=':
                                   deltaPerc = (delta / threshold[i])*100;
                                   if(deltaPerc < 0.01)
                                   {
                                       alarms[i] = true;
                                       alarmCount++;
                                   }
                                   break;    

                               //Non gestiamo altri operatori 
                               default:
                                   break;
                            }
                        }

                        switch(sizeRowsWidget)
                        {
                            case 4:
                                fontSize = "14px";
                                break;

                            case 5:
                                fontSize = "18px";
                                break;

                            case 6:
                                fontSize = "22px";
                                break;

                            case 7:
                                fontSize = "24px";
                                break;

                            case 8:
                                fontSize = "28px";
                                break;

                            default:
                                fontSize = "14px";
                                break;
                        }
                        
                        rowColorHex = "<?= $_GET['color'] ?>";
                        rowColorRgb = hexToRgb(rowColorHex);
                        var rowColor = "linear-gradient(to right, rgba(" + rowColorRgb + ",0.6), rgba(" + rowColorRgb + ",1))";
                            
                        if((i == 0) && (msg.data.length == 1))
                        {
                            $("#<?= $_GET['name'] ?>_content").css("background", "linear-gradient(to bottom right, rgba(" + rowColorRgb + ",0.6), rgba(" + rowColorRgb + ",1))");
                        }
                        
                        if(firstLoad != false)
                        {
                            $('#<?= $_GET['name'] ?>_loading').css("display", "none");
                            $('#<?= $_GET['name'] ?>_content').css("display", "block");
                        }
                        var rowHeight = parseInt(($('#<?= $_GET['name'] ?>_content').height() / 3) - 5);
                        var newRow = $("<div class='row_data_line'></div>");
                        newRow.css("height", rowHeight + "px");
                        var marginBottom = Math.ceil(($('#<?= $_GET['name'] ?>_content').height() - 3 * rowHeight) / 2);
                        if(i != 2)
                        {
                            newRow.css("margin-bottom", marginBottom + "px");
                        }
                        else
                        {
                            newRow.css("margin-bottom", "0px");
                        }
                        var newDescContainer = $("<div class='row_data_desc'>" + msg.data[i].commit.author.descrip + "</div>");
                        newDescContainer.css("background", rowColor);
                        newDescContainer.css("line-height", rowHeight + "px")
                        newRow.append(newDescContainer);
                        
                        var newValueContainer = $("<div class='row_data_value'>" + value + " " + udm  + "</div>");
                        var valueContainerWidth = parseInt(($('#<?= $_GET['name'] ?>_content').width()*0.3) - 5);
                        newValueContainer.css("width", valueContainerWidth + "px");
                        newValueContainer.css("background-color", rowColorHex);
                        newValueContainer.css("line-height", rowHeight + "px");
                        newRow.append(newValueContainer);
                        newRow.attr("title", msg.data[i].commit.author.descrip);
                        newRow.css("fontSize", fontSize);
                        $("#<?= $_GET['name'] ?>_content").append(newRow);

                        desc2Blink[i] = newValueContainer;
                        desc2Blink[i].css({transition: "background 1.8s ease-in-out"});
                        desc2Blink[i].css({webkitTransition: "background 1.8s ease-in-out"});
                        desc2Blink[i].css({msTransition: "background 1.8s ease-in-out"});

                        if(alarms[i])
                        {
                            alarmIntervals[i] = setInterval(blinkFunction, 1000, desc2Blink[i]);
                        }
                    }
                    
                    /*if(i < 3)
                    {
                        for(var y = i; y <= 3; y++)
                        {
                            $("#<?= $_GET['name'] ?>_content").append("<div class='row_data_line' style='" + fontSize + rowColor +"' title='emptyRow" + y + "'><div class='row_data_desc'><p></p></div><div class='row_data_value'><p></p></div></div>");
                        }
                    }*/
                    
                    
                    $('#source_<?= $_GET['name'] ?>').on('click', function () {
                        $('#dialog_<?= $_GET['name'] ?>').show();

                    });
                    $('#close_popup_<?= $_GET['name'] ?>').on('click', function () {

                        $('#dialog_<?= $_GET['name'] ?>').hide();

                    });

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
                            if(alarmCount > 0)
                            {
                                clearInterval(blinkInterval);
                                var i = null;
                                for(i = 0; i < 3; i++)
                                {
                                    if(alarms[i])
                                    {
                                        clearInterval(alarmIntervals[i]);
                                    }
                                }
                            }
                            $("#<?= $_GET['name'] ?>_content").empty();
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
        <!-- Workaround temporaneo, rimuovere quando bonifichi il box model del titolo di ogni widget -->
        <div id="<?= $_GET['name'] ?>_headerContainer"> <!-- style="width: 100%; height: 25px; border-style: solid; border-color: red; border-width: 2px" -->
            <div id='<?= $_GET['name'] ?>_desc' class="genContentDesc"></div>
            <div id='<?= $_GET['name'] ?>_icons' class="modifyWidgetGenContent">
                <a class="icon-cfg-widget" href="#">
                    <span class="glyphicon glyphicon-cog glyphicon-modify-widget" aria-hidden="true"></span>
                </a>
                <a class="icon-remove-widget" href="#">
                    <span class="glyphicon glyphicon-remove glyphicon-modify-widget" aria-hidden="true"></span>
                </a>
            </div>
            <div id="countdown_<?= $_GET['name'] ?>" class="countdown"></div>
            <div id="<?= $_GET['name'] ?>_loading" class="loadingDiv">
                <div class="loadingTextDiv">
                    <p>Loading data, please wait</p>
                </div>
                <div class ="loadingIconDiv">
                    <i class='fa fa-spinner fa-spin'></i>
                </div>
            </div>
        </div>
        <div id='<?= $_GET['name'] ?>_content' class="content"></div>  
    </div>	
</div>