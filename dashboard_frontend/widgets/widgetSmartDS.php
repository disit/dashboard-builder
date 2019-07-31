<?php
/* Dashboard Builder.
   Copyright (C) 2018 DISIT Lab https://www.disit.org - University of Florence

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

<script type='text/javascript'>
    var colors = {
      GREEN: '#008800',
      ORANGE: '#FF9933',
      LOW_YELLOW: '#ffffcc',
      RED: '#FF0000'
    };
    $(document).ready(function <?= $_REQUEST['name_w'] ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef) 
    {
         <?php
            $titlePatterns = array();
            $titlePatterns[0] = '/_/';
            $titlePatterns[1] = '/\'/';
            $replacements = array();
            $replacements[0] = ' ';
            $replacements[1] = '&apos;';
            $title = $_REQUEST['title_w'];
            $link = mysqli_connect($host, $username, $password);
            if (checkWidgetNameInDashboard($link, $_REQUEST['name_w'], $_REQUEST['id_dashboard']) === false) {
                eventLog("Returned the following ERROR in widgetSmartDS.php for the widget ".escapeForHTML($_REQUEST['name_w'])." is not instantiated or allowed in this dashboard.");
                exit();
            }
        ?> 
                
        var hostFile = "<?= $_REQUEST['hostFile'] ?>";
        var widgetName = "<?= $_REQUEST['name_w'] ?>";
        var showTitle, widgetTitle, showHeader, hasTimer, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, fontSize, fontColor, timeToReload, widgetPropertiesString, widgetProperties, thresholdObject, infoJson, styleParameters, metricType, metricData, pattern, totValues, shownValues, 
            chartRef, threshold, thresholdEval, stopsArray, delta, deltaPerc,
            widgetParameters, countdownRef, value1, value2, value3, valueGreen, valueRed, valueWhite, desc, object, sizeRowsWidget, alarmSet = null;
        var metricId = "<?= $_REQUEST['id_metric'] ?>";
        var elToEmpty = $("#<?= $_REQUEST['name_w'] ?>_chartContainer");
        var url = "<?= $_REQUEST['link_w'] ?>";
        var embedWidget = <?= $_REQUEST['embedWidget'] ?>;
        var embedWidgetPolicy = '<?= $_REQUEST['embedWidgetPolicy'] ?>';	
        var headerHeight = 25;
        
        //Definizioni di funzione specifiche del widget
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
        
        function resizeWidget()
        {
            setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
        }
		
        $(document).off('resizeHighchart_' + widgetName);
        $(document).on('resizeHighchart_' + widgetName, function(event) 
        {
            showHeader = event.showHeader;
            $('#<?= $_REQUEST['name_w'] ?>_content').highcharts().reflow();
        });
        
        $.ajax({
            url: "../controllers/getWidgetParams.php",
            type: "GET",
            data: {
                widgetName: "<?= $_REQUEST['name_w'] ?>"
            },
            async: true,
            dataType: 'json',
            success: function(widgetData) 
            {
                showTitle = widgetData.params.showTitle;
                widgetContentColor = widgetData.params.color_w;
                fontSize = widgetData.params.fontSize;
                timeToReload = widgetData.params.frequency_w;
                hasTimer = widgetData.params.hasTimer;
                widgetTitle = widgetData.params.title_w;
                widgetHeaderColor = widgetData.params.frame_color_w;
                widgetHeaderFontColor = widgetData.params.headerFontColor;
                
                if(((embedWidget === true)&&(embedWidgetPolicy === 'auto'))||((embedWidget === true)&&(embedWidgetPolicy === 'manual')&&(showTitle === "no"))||((embedWidget === false)&&(showTitle === "no")))
                {
                    showHeader = false;
                }
                else
                {
                    showHeader = true;
                }
                
                setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
        
                $('#<?= $_REQUEST['name_w'] ?>_div').parents('li.gs_w').off('resizeWidgets');
                $('#<?= $_REQUEST['name_w'] ?>_div').parents('li.gs_w').on('resizeWidgets', resizeWidget);

                if(firstLoad === false)
                {
                    showWidgetContent(widgetName);
                }
                else
                {
                    setupLoadingPanel(widgetName, widgetContentColor, firstLoad);
                }

                widgetProperties = getWidgetProperties(widgetName);

                if((widgetProperties !== null) && (widgetProperties !== ''))
                {
                    //Inizio eventuale codice ad hoc basato sulle proprietà del widget
                    styleParameters = getStyleParameters();//Restituisce null finché non si usa il campo per questo widget
                    widgetParameters = widgetProperties.param.parameters;
                    var seriesDataGreen = [];
                    var seriesDataRed = [];
                    var seriesDataWhite = [];

                    sizeRowsWidget = parseInt(widgetProperties.param.size_rows);

                    metricData = getMetricData(metricId);
                    if(metricData !== null)
                    {
                        if(metricData.data[0] !== 'undefined')
                        {
                            if(metricData.data.length > 0)
                            {
                                /*Inizio eventuale codice ad hoc basato sui dati della metrica*/
                                metricType = metricData.data[0].commit.author.metricType;
                                threshold = parseInt(metricData.data[0].commit.author.threshold);
                                thresholdEval = metricData.data[0].commit.author.thresholdEval;
                                value1 = (metricData.data[0].commit.author.value_perc1) * 100;
                                valueGreen = parseFloat(parseFloat(value1).toFixed(2));
                                value2 = (metricData.data[0].commit.author.value_perc2) * 100;
                                valueRed = parseFloat(parseFloat(value2).toFixed(2));
                                value3 = (metricData.data[0].commit.author.value_perc3) * 100;
                                valueWhite = parseFloat(parseFloat(value3).toFixed(2));
                                desc = metricData.data[0].commit.author.descrip;
                                object = metricData.data[0].commit.author.value_text;
                                seriesDataGreen.push(['Green', valueGreen]);
                                seriesDataRed.push(['Red', valueRed]);
                                seriesDataWhite.push(['White', valueWhite]);

                                delta = Math.abs(metricData.data[0].commit.author.value_perc1 - threshold);
                                switch(thresholdEval)
                                {
                                    //Allarme attivo se il valore 1 attuale è sotto la soglia
                                    case '<':
                                        if(metricData.data[0].commit.author.value_perc1 < threshold)
                                        {
                                           //Allarme
                                           //alarmSet = true;
                                        }
                                        break;

                                    //Allarme attivo se il valore 1 attuale è sopra la soglia
                                    case '>':
                                        if(metricData.data[0].commit.author.value_perc1 > threshold)
                                        {
                                           //Allarme
                                           //alarmSet = true;
                                        }
                                        break;

                                    //Allarme attivo se il valore 1 attuale è uguale alla soglia (errore sui float = 0.1%)
                                    case '=':
                                        if(delta <= 0.1)
                                        {
                                            //Allarme
                                            //alarmSet = true;
                                        }
                                        break;    

                                    //Non gestiamo altri operatori 
                                    default:
                                        break;
                                }

                                /*if(alarmSet)
                                {
                                    $("#<?= $_REQUEST['name_w'] ?>_alarmDiv").removeClass("alarmDiv");
                                    $("#<?= $_REQUEST['name_w'] ?>_alarmDiv").addClass("alarmDivActive");
                                }*/

                                if(firstLoad !== false)
                                {
                                    showWidgetContent(widgetName);
                                }
                                else
                                {
                                    elToEmpty.empty();
                                }

                                chartRef = Highcharts.chart('<?= $_REQUEST['name_w'] ?>_chartContainer', {
                                    credits: {
                                        enabled: false
                                    },
                                    exporting: {
                                        enabled: false
                                    },
                                    chart: {
                                        type: 'bar',
                                        backgroundColor: 'transparent',
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
                                                    "textOutline": "1px 1px contrast",
                                                    "text-shadow": "1px 1px 1px rgba(0,0,0,0.2)"
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
                            }
                            else
                            {
                                showWidgetContent(widgetName);
                                $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                            }
                        }
                        else
                        {
                            showWidgetContent(widgetName);
                            $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                        }
                        /*Fine eventuale codice ad hoc basato sui dati della metrica*/
                    }
                    else
                    {
                        showWidgetContent(widgetName);
                        $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                    }
                }    
                else
                {
                    console.log("Errore in caricamento proprietà widget");
                }

                $("#<?= $_REQUEST['name_w'] ?>").on('customResizeEvent', function(event){
                    resizeWidget();
                    $('#<?= $_REQUEST['name_w'] ?>_content').highcharts().reflow();
                });

                $("#<?= $_REQUEST['name_w'] ?>").off('updateFrequency');
                $("#<?= $_REQUEST['name_w'] ?>").on('updateFrequency', function(event){
                        clearInterval(countdownRef);
                        timeToReload = event.newTimeToReload;
                        countdownRef = startCountdown(widgetName, timeToReload, <?= $_REQUEST['name_w'] ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, fromGisMarker, fromGisMapRef);
                });

                countdownRef = startCountdown(widgetName, timeToReload, <?= $_REQUEST['name_w'] ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, fromGisMarker, fromGisMapRef);
                
            },
            error: function(errorData)
            {
        
            }
        });
});//Fine document ready
</script>

<div class="widget" id="<?= $_REQUEST['name_w'] ?>_div">
    <div class='ui-widget-content'>
        <?php include '../widgets/widgetHeader.php'; ?>
        <?php include '../widgets/widgetCtxMenu.php'; ?>
        <?php include '../widgets/commonModules/widgetDimControls.php'; ?>
        <div id="<?= $_REQUEST['name_w'] ?>_loading" class="loadingDiv">
            <div class="loadingTextDiv">
                <p>Loading data, please wait</p>
            </div>
            <div class ="loadingIconDiv">
                <i class='fa fa-spinner fa-spin'></i>
            </div>
        </div>
        
        <div id="<?= $_REQUEST['name_w'] ?>_content" class="content">
            
            <p id="<?= $_REQUEST['name_w'] ?>_noDataAlert" style='text-align: center; font-size: 18px; display:none'>Nessun dato disponibile</p>
            <div id="<?= $_REQUEST['name_w'] ?>_chartContainer" class="chartContainer"></div>
        </div>
    </div>	
</div> 