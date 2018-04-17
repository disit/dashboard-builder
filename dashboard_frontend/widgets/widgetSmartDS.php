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
        ?> 
                
        var hostFile = "<?= $_REQUEST['hostFile'] ?>";
        var widgetName = "<?= $_REQUEST['name_w'] ?>";
        var divContainer = $("#<?= $_REQUEST['name_w'] ?>_content");
        var widgetContentColor = "<?= $_REQUEST['color_w'] ?>";
        var widgetHeaderColor = "<?= $_REQUEST['frame_color_w'] ?>";
        var widgetHeaderFontColor = "<?= $_REQUEST['headerFontColor'] ?>";
        var nome_wid = "<?= $_REQUEST['name_w'] ?>_div";
        var linkElement = $('#<?= $_REQUEST['name_w'] ?>_link_w');
        var color = '<?= $_REQUEST['color_w'] ?>';
        var fontSize = "<?= $_REQUEST['fontSize'] ?>";
        var fontColor = "<?= $_REQUEST['fontColor'] ?>";
        var timeToReload = <?= $_REQUEST['frequency_w'] ?>;
        var widgetPropertiesString, widgetProperties, thresholdObject, infoJson, styleParameters, metricType, metricData, pattern, totValues, shownValues, 
            descriptions, udm, threshold, thresholdEval, stopsArray, delta, deltaPerc, seriesObj, dataObj, pieObj, legendLength,
            rangeMin, rangeMax, widgetParameters, value1, value2, value3, valueGreen, valueRed, valueWhite, desc, object, sizeRowsWidget, alarmSet = null;
        var metricId = "<?= $_REQUEST['id_metric'] ?>";
        var elToEmpty = $("#<?= $_REQUEST['name_w'] ?>_chartContainer");
        var url = "<?= $_REQUEST['link_w'] ?>";
        var barColors = new Array();
        var embedWidget = <?= $_REQUEST['embedWidget'] ?>;
        var embedWidgetPolicy = '<?= $_REQUEST['embedWidgetPolicy'] ?>';	
        var headerHeight = 25;
        var showTitle = "<?= $_REQUEST['showTitle'] ?>";
		var showHeader = null;
        var hasTimer = "<?= $_REQUEST['hasTimer'] ?>";
        if(url === "null")
        {
            url = null;
        }
        
        if(((embedWidget === true)&&(embedWidgetPolicy === 'auto'))||((embedWidget === true)&&(embedWidgetPolicy === 'manual')&&(showTitle === "no"))||((embedWidget === false)&&(showTitle === "no")))
	{
		showHeader = false;
	}
	else
	{
		showHeader = true;
	}
        
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
        
        /*Restituisce il JSON delle info se presente, altrimenti NULL*/
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
			$('#<?= $_REQUEST['name_w'] ?>_content').highcharts().reflow();
		});
        
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
        addLink(widgetName, url, linkElement, divContainer);
        $("#<?= $_REQUEST['name_w'] ?>_titleDiv").html("<?= preg_replace($titlePatterns, $replacements, $title) ?>");
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
                        
                        $('#<?= $_REQUEST['name_w'] ?>_content').highcharts({
                            credits: {
                                enabled: false
                            },
                            exporting: {
                                enabled: false
                            },
                            chart: {
                                type: 'bar',
                                backgroundColor: '<?= $_REQUEST['color_w'] ?>',
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
        startCountdown(widgetName, timeToReload, <?= $_REQUEST['name_w'] ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef);
});//Fine document ready
</script>

<div class="widget" id="<?= $_REQUEST['name_w'] ?>_div">
    <div class='ui-widget-content'>
	    <?php include '../widgets/widgetHeader.php'; ?>
		<?php include '../widgets/widgetCtxMenu.php'; ?>
        <!--<div id='<?= $_REQUEST['name_w'] ?>_header' class="widgetHeader">
            <div id="<?= $_REQUEST['name_w'] ?>_infoButtonDiv" class="infoButtonContainer">
               <a id ="info_modal" href="#" class="info_source"><i id="source_<?= $_REQUEST['name_w'] ?>" class="source_button fa fa-info-circle" style="font-size: 22px"></i></a>
            </div>    
            <div id="<?= $_REQUEST['name_w'] ?>_titleDiv" class="titleDiv"></div>
            <div id="<?= $_REQUEST['name_w'] ?>_buttonsDiv" class="buttonsContainer">
                <div class="singleBtnContainer"><a class="icon-cfg-widget" href="#"><span class="glyphicon glyphicon-cog glyphicon-modify-widget" aria-hidden="true"></span></a></div>
                <div class="singleBtnContainer"><a class="icon-remove-widget" href="#"><span class="glyphicon glyphicon-remove glyphicon-modify-widget" aria-hidden="true"></span></a></div>
            </div>
            <div id="<?= $_REQUEST['name_w'] ?>_countdownContainerDiv" class="countdownContainer">
                <div id="<?= $_REQUEST['name_w'] ?>_countdownDiv" class="countdown"></div> 
            </div>   
        </div>-->
        
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