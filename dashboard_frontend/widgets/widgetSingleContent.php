<?php
/* Dashboard Builder.
   Copyright (C) 2017 DISIT Lab http://www.disit.org - University of Florence

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
        //$("#<?= $_GET['name'] ?>_chartContainer").css("height", height); a cosa serviva?
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
        var widgetProperties, styleParameters, metricType, pattern, udm, udmPos, threshold, thresholdEval, 
            delta, deltaPerc, sizeRowsWidget, fontSize, value, metricType, height, fontRatio, fontRatioSmall = null;
        var metricId = "<?= $_GET['metric'] ?>";
        var elToEmpty = $("#<?= $_GET['name'] ?>_chartContainer");
        elToEmpty.css("font-family", "Verdana");
        var url = "<?= $_GET['link_w'] ?>";
        
        //Specifiche per questo widget
        var flagNumeric = false;
        var alarmSet = false;
        var udm = "";
        var pattern = /Percentuale\//;
        
        //Definizioni di funzione specifiche del widget
        /*Restituisce il JSON delle soglie se presente, altrimenti NULL*/
        function getThresholdsJson()
        {
            var thresholdsJson = null;
            if(jQuery.parseJSON(widgetProperties.param.parameters !== null))
            {
                thresholdsJson = widgetProperties.param.parameters; 
            }
            
            return thresholdsJson;
        }
        
        /*Restituisce il JSON delle info se presente, altrimenti NULL*/
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
        //Fine definizioni di funzione 
        
        setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor);
        if(firstLoad === false)
        {
            showWidgetContent(widgetName);
        }
        else
        {
            setupLoadingPanel(widgetName, widgetContentColor, firstLoad);
        }
        addLink(widgetName, url, linkElement, divContainer);
        $("#<?= $_GET['name'] ?>_titleDiv").html("<?= preg_replace($titlePatterns, $replacements, $title) ?>");
        widgetProperties = getWidgetProperties(widgetName);
        
        if((widgetProperties !== null) && (widgetProperties !== ''))
        {
            //Inizio eventuale codice ad hoc basato sulle proprietà del widget
            styleParameters = getStyleParameters();//Restituisce null finché non si usa il campo per questo widget
            udm = widgetProperties.param.udm;
            udmPos = widgetProperties.param.udmPos;
            sizeRowsWidget = parseInt(widgetProperties.param.size_rows);
            
            //Fine eventuale codice ad hoc basato sulle proprietà del widget
            metricData = getMetricData(metricId);
            if(metricData !== null)
            {
                if(metricData.data[0] !== 'undefined')
                {
                    if(metricData.data.length > 0)
                    {
                        //Inizio eventuale codice ad hoc basato sui dati della metrica
                        if(firstLoad !== false)
                        {
                            showWidgetContent(widgetName);
                        }
                        else
                        {
                            $("#" + widgetName + "_value").empty();
                            $("#" + widgetName + "_udm").empty();
                        }
                        metricType = metricData.data[0].commit.author.metricType;
                        threshold = metricData.data[0].commit.author.threshold;
                        thresholdEval = metricData.data[0].commit.author.thresholdEval;

                        if((metricType === "Percentuale") || (pattern.test(metricType)))
                        {
                            if((metricData.data[0].commit.author.value_perc1 !== null) && (metricData.data[0].commit.author.value_perc1 !== "") && (metricData.data[0].commit.author.value_perc1 !== "undefined"))
                            {
                                value = parseFloat(parseFloat(metricData.data[0].commit.author.value_perc1).toFixed(1));
                                if(value > 100)
                                {
                                    value = 100;
                                }
                            }
                            flagNumeric = true;
                        }
                        else
                        {
                            switch(metricType)
                            {
                                case "Intero":
                                    if((metricData.data[0].commit.author.value_num !== null) && (metricData.data[0].commit.author.value_num !== "") && (typeof metricData.data[0].commit.author.value_num !== "undefined"))
                                    {
                                        value = parseInt(metricData.data[0].commit.author.value_num);
                                    }
                                    flagNumeric = true;
                                    break;

                                case "Float":
                                    if((metricData.data[0].commit.author.value_num !== null) && (metricData.data[0].commit.author.value_num !== "") && (typeof metricData.data[0].commit.author.value_num !== "undefined"))
                                    {
                                       value = parseFloat(parseFloat(metricData.data[0].commit.author.value_num).toFixed(1)); 
                                    }
                                    flagNumeric = true;
                                    break;

                                case "Testuale":
                                    value = metricData.data[0].commit.author.value_text;
                                    break;
                            }
                        }
                        
                        var fontSizeUdm = parseInt(fontSize*0.6);
                        $("#<?= $_GET['name'] ?>_value").css("font-size", fontSize + "px");
                        $("#<?= $_GET['name'] ?>_udm").css("font-size", fontSizeUdm + "px");

                        if(udm !== null)
                        {
                           if(udmPos === 'next')
                           {   
                              if((value !== null) && (value !== "") && (value !== "undefined"))
                              {
                                 $("#<?= $_GET['name'] ?>_chartContainer").show();
                                 $("#<?= $_GET['name'] ?>_value").show();
                                 $("#<?= $_GET['name'] ?>_udm").hide();
                                 $("#<?= $_GET['name'] ?>_value").css("height", "100%");             
                                 $("#<?= $_GET['name'] ?>_value").css("alignItems", "center"); 
                                 $("#<?= $_GET['name'] ?>_value").html(value + udm);
                              }
                              else
                              {
                                 $("#<?= $_GET['name'] ?>_value").hide();
                                 $("#<?= $_GET['name'] ?>_udm").hide(); 
                                 $("#<?= $_GET['name'] ?>_chartContainer").hide();
                                 $('#<?= $_GET['name'] ?>_noDataAlert').show();
                              }
                           }
                           else
                           {
                              if((value !== null) && (value !== "") && (value !== "undefined"))
                              {
                                 $("#<?= $_GET['name'] ?>_chartContainer").show();
                                 $("#<?= $_GET['name'] ?>_value").show();
                                 $("#<?= $_GET['name'] ?>_udm").show();
                                 $("#<?= $_GET['name'] ?>_value").css("height", "60%");
                                 $("#<?= $_GET['name'] ?>_value").html(value);
                                 $("#<?= $_GET['name'] ?>_udm").css("height", "40%");
                                 $("#<?= $_GET['name'] ?>_udm").html(udm);
                              }
                              else
                              {
                                 $("#<?= $_GET['name'] ?>_value").hide();
                                 $("#<?= $_GET['name'] ?>_udm").hide();
                                 $("#<?= $_GET['name'] ?>_chartContainer").hide();
                                 $('#<?= $_GET['name'] ?>_noDataAlert').show();
                              }
                           }
                        }
                        else
                        {
                            if((value !== null) && (value !== "") && (value !== "undefined"))
                            {
                                $("#<?= $_GET['name'] ?>_udm").css("display", "none");
                                $("#<?= $_GET['name'] ?>_value").css("height", "100%");
                                $("#<?= $_GET['name'] ?>_value").html(value);
                            }
                            else
                            {
                                $('#<?= $_GET['name'] ?>_noDataAlert').show();
                            }
                        }
                        
                        $("#<?= $_GET['name'] ?>_value").css("color", fontColor);
                        $("#<?= $_GET['name'] ?>_udm").css("color", fontColor);

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
                    }
                    else
                    {
                        showWidgetContent(widgetName);
                        $('#<?= $_GET['name'] ?>_noDataAlert').show();
                    }
                }
                else
                {
                    showWidgetContent(widgetName);
                    $('#<?= $_GET['name'] ?>_noDataAlert').show();
                } 
            }
            else
            {
                showWidgetContent(widgetName);
                $('#<?= $_GET['name'] ?>_noDataAlert').show();
            } 
        }
        else
        {
            alert("Error while loading widget properties");
        }
        startCountdown(widgetName, timeToReload, <?= $_GET['name'] ?>, elToEmpty, "widgetSingleContent", null, null);
});//Fine document ready 
</script>

<div class="widget" id="<?= $_GET['name'] ?>_div">
    <div class='ui-widget-content'>
        <div id='<?= $_GET['name'] ?>_header' class="widgetHeader">
            <div id="<?= $_GET['name'] ?>_infoButtonDiv" class="infoButtonContainer">
                <!--<a id ="info_modal" href="#" class="info_source"><img id="source_<?= $_GET['name'] ?>" src="../management/img/info.png" class="source_button"></a>-->
               <a id ="info_modal" href="#" class="info_source"><i id="source_<?= $_GET['name'] ?>" class="source_button fa fa-info-circle" style="font-size: 22px"></i></a>
            </div>    
            <div id="<?= $_GET['name'] ?>_titleDiv" class="titleDiv"></div>
            <div id="<?= $_GET['name'] ?>_buttonsDiv" class="buttonsContainer">
                <a class="icon-cfg-widget" href="#"><span class="glyphicon glyphicon-cog glyphicon-modify-widget" aria-hidden="true"></span></a>
                <a class="icon-remove-widget" href="#"><span class="glyphicon glyphicon-remove glyphicon-modify-widget" aria-hidden="true"></span></a>
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
            <p id="<?= $_GET['name'] ?>_noDataAlert" style='text-align: center; font-size: 18px; display:none'>Nessun dato disponibile</p>
            <div id="<?= $_GET['name'] ?>_chartContainer" class="chartContainer">
                <div id='<?= $_GET['name'] ?>_value' class="singleContentValue"></div>
                <div id='<?= $_GET['name'] ?>_udm' class="singleContentUdm"></div>
            </div>
        </div>
    </div>	
</div> 