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
     
    //Inizio JQuery document ready handler
    $(document).ready(function <?= $_REQUEST['name_w'] ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef)   
    {
        <?php
            $link = mysqli_connect($host, $username, $password);
            if (checkWidgetNameInDashboard($link, $_REQUEST['name_w'], $_REQUEST['id_dashboard']) === false) {
                eventLog("Returned the following ERROR in widgetTable.php for the widget ".escapeForHTML($_REQUEST['name_w'])." is not instantiated or allowed in this dashboard.");
                exit();
            }
        ?>  
        var hostFile = "<?= escapeForJS($_REQUEST['hostFile']) ?>";
        var widgetName = "<?= $_REQUEST['name_w'] ?>";
        var divContainer = $("#<?= $_REQUEST['name_w'] ?>_content");
        var widgetContentColor = "<?= escapeForJS($_REQUEST['color_w']) ?>";
        var widgetHeaderColor = "<?= escapeForJS($_REQUEST['frame_color_w']) ?>";
        var widgetHeaderFontColor = "<?= escapeForJS($_REQUEST['headerFontColor']) ?>";
        var nome_wid = "<?= $_REQUEST['name_w'] ?>_div";
        var linkElement = $('#<?= $_REQUEST['name_w'] ?>_link_w');
        var color = '<?= escapeForJS($_REQUEST['color_w']) ?>';
        var fontSize = "<?= escapeForJS($_REQUEST['fontSize']) ?>";
        var fontColor = "<?= escapeForJS($_REQUEST['fontColor']) ?>";
        var timeToReload = <?= sanitizeInt('frequency_w') ?>;
        var widgetProperties = null;
        var metricName = "<?= escapeForJS($_REQUEST['id_metric']) ?>";
        var metricData = null;
        var elToEmpty = $("#<?= $_REQUEST['name_w'] ?>_table");
        var url = "<?= escapeForJS($_REQUEST['link_w']) ?>"; 
        var metricType = null;
        var series = null;
        var styleParameters = null;
        var legendHeight = null;
        var metricName, widgetTitle, countdownRef = null;
        var embedWidget = <?= $_REQUEST['embedWidget']=='true'?'true':'false' ?>;
        var embedWidgetPolicy = '<?= escapeForJS($_REQUEST['embedWidgetPolicy']) ?>';	
        var headerHeight = 25;
        var showTitle = "<?= escapeForJS($_REQUEST['showTitle']) ?>";
        var showHeader, widgetParameters, thresholdsJson, infoJson, rowParameters, xAxisTitle, smField, editLabels, groupByAttr, colorMaps, aggregationGetData, getDataFinishCount = null;
        var hasTimer = "<?= escapeForJS($_REQUEST['hasTimer']) ?>";
        var seriesDataArray = [];
        var serviceUri = "";

        if(((embedWidget === true)&&(embedWidgetPolicy === 'auto'))||((embedWidget === true)&&(embedWidgetPolicy === 'manual')&&(showTitle === "no"))||((embedWidget === false)&&(showTitle === "no")))
		{
			showHeader = false;
		}
		else
		{
			showHeader = true;
		}

		console.log("Entrato in widgetTable --> " + widgetName);

        //Definizioni di funzione specifiche del widget

        function serializeAndDisplay(rowParameters, seriesDataArray, editLabels, groupByAttr) {

            var deviceLabels = [];
            var metricLabels = [];
            var auxLabels = [];
            let mappedSeriesDataArray = [];

            metricLabels = getMetricLabelsForBarSeries(rowParameters);
            deviceLabels = getDeviceLabelsForBarSeries(rowParameters);
            if (groupByAttr != null) {
                //if (groupByAttr == "metrics") {
                if (groupByAttr == "value type") {
                    flipFlag = false;
                    //    } else if (groupByAttr == "device") {
                } else if (groupByAttr == "value name") {
                    flipFlag = true;
                }
            } else {
                flipFlag = false;
            }
            if (flipFlag !== true) {
                mappedSeriesDataArray = buildBarSeriesArrayMap(seriesDataArray);
            } else {
                mappedSeriesDataArray = buildBarSeriesArrayMap2(seriesDataArray);
                auxLabels = metricLabels;
                metricLabels = deviceLabels;
                deviceLabels = auxLabels;
            }
            series = serializeSensorDataForBarSeries(mappedSeriesDataArray, metricLabels, deviceLabels, flipFlag);

            xAxisCategories = metricLabels.slice();

            widgetHeight = parseInt($("#<?= $_REQUEST['name_w'] ?>_chartContainer").height() + 25);

            if(firstLoad !== false)
            {
                populateTable(JSON.stringify(series));
                showWidgetContent(widgetName);
                $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').hide();
                $("#<?= $_REQUEST['name_w'] ?>_chartContainer").show();
                $("#<?= $_REQUEST['name_w'] ?>_table").show();
            }
            else
            {
                elToEmpty.empty();
                populateTable(JSON.stringify(series));
                $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').hide();
                $("#<?= $_REQUEST['name_w'] ?>_chartContainer").show();
                $("#<?= $_REQUEST['name_w'] ?>_table").show();
            }
        //    populateTable(JSON.stringify(series));
            //   legendWidth = $("#<?= $_REQUEST['name_w'] ?>_content").width();
            //    xAxisCategories = getXAxisCategories(series, widgetHeight);
            applyThresholdCodes(series);
            setTableHeight();
            var widgetHeight = parseInt($("#WeatherStations_77_widgetTable783_table").height() + 25);
            // createLegends(series, widgetHeight);
            createLegendFromColorMap(series, widgetHeight);
            createInfoButtons();

            if (!serviceUri) {
                $.ajax({
                    url: "../widgets/updateBarSeriesParameters.php",
                    type: "GET",
                    data: {
                        widgetName: "<?= $_REQUEST['name_w'] ?>",
                        series: series
                    },
                    async: true,
                    dataType: 'json',
                    success: function (widgetData) {

                    },
                    error: function (errorData) {
                        metricData = null;
                        console.log("Error in updating widgetBarSeries: <?= $_REQUEST['name_w'] ?>");
                        console.log(JSON.stringify(errorData));
                    }
                });
            }
        //    drawDiagram();

        }

        //Funzione di calcolo ed applicazione dell'altezza della tabella
        function setTableHeight()
        {
            if(showHeader)
            {
                var height = parseInt($("#<?= $_REQUEST['name_w'] ?>_div").prop("offsetHeight") - 25);
            }
            else
            {
                var height = parseInt($("#<?= $_REQUEST['name_w'] ?>_div").prop("offsetHeight"));
            }
            
            $("#<?= $_REQUEST['name_w'] ?>_table").css("height", height);
        }

        //Funzione di popolamento della tabella
        function populateTable(seriesString)
        {
            var series = jQuery.parseJSON(seriesString);
            var colsQt = parseInt(parseInt(series.firstAxis.labels.length) + 1);
            var rowsQt = parseInt(parseInt(series.secondAxis.labels.length) + 1);
            var newRow = null;
            var newCell = null;
            var k = null;
            var z = null;
            
            //Stile prima cella
            var showTableFirstCell = styleParameters.showTableFirstCell;
            var tableFirstCellFontSize = styleParameters.tableFirstCellFontSize;
            var tableFirstCellFontColor = styleParameters.tableFirstCellFontColor;
            
            //Stile labels righe
            var rowsLabelsFontSize = styleParameters.rowsLabelsFontSize;
            var rowsLabelsFontColor = styleParameters.rowsLabelsFontColor;
            var rowsLabelsBckColor = styleParameters.rowsLabelsBckColor;
            
            //Stile labels colonne
            var colsLabelsFontSize = styleParameters.colsLabelsFontSize;
            var colsLabelsFontColor = styleParameters.colsLabelsFontColor;
            var colsLabelsBckColor = styleParameters.colsLabelsBckColor;
            
            //Valori gestione bordi
            var tableBorders = styleParameters.tableBorders;
            var tableBordersColor = styleParameters.tableBordersColor;

            for(var i = 0; i < rowsQt; i++)
            {
                newRow = $("<tr></tr>");
                z = parseInt(parseInt(i) -1);
                
                if(i === 0)
                {
                    //Riga di intestazione
                    for(var j = 0; j < colsQt; j++)
                    {
                        if(j === 0)
                        {
                            //Cella (0,0)
                            if(showTableFirstCell === 'yes')
                            {
                                newCell = $("<td>" + series.firstAxis.desc  + "<br/>/<br/>" + series.secondAxis.desc + "</td>");
                                newCell.css("font-size", tableFirstCellFontSize + "px");
                                newCell.css("color", tableFirstCellFontColor);
                                
                            }
                            else
                            {
                                newCell = $("<td></td>");
                            }
                            
                            newCell.css("background-color", "transparent");
                            newCell.css("font-style", "italic");
                        }
                        else
                        {
                            //Celle labels
                            k = parseInt(parseInt(j) -1);
                            newCell = $("<td><span>" + series.firstAxis.labels[k] + "</span></td>");
                            newCell.css("font-size", colsLabelsFontSize + "px");
                            newCell.css("font-style", "italic");
                            newCell.css("color", colsLabelsFontColor);
                            newCell.css("background-color", colsLabelsBckColor);
                         //   newCell.css("overflow", "hidden");
                         //   newCell.css("text-overflow", "ellipsis");
                        }
                        newRow.append(newCell);
                    }
                }
                else
                {
                    //Righe dati
                    for(var j = 0; j < colsQt; j++)
                    {
                        k = parseInt(parseInt(j) -1);
                        if(j === 0)
                        {
                            //Cella label
                            if (editLabels != null) {
                                if (editLabels[z] != null) {
                                    newCell = $("<td>" + editLabels[z] + "</td>");
                                } else {
                                    newCell = $("<td>" + series.secondAxis.labels[z] + "</td>");
                                }
                            } else {
                                newCell = $("<td>" + series.secondAxis.labels[z] + "</td>");
                            }
                            newCell.css("font-size", rowsLabelsFontSize + "px");
                            newCell.css("font-style", "italic");
                            newCell.css("color", rowsLabelsFontColor);
                            newCell.css("background-color", rowsLabelsBckColor);
                            newCell.css("overflow", "hidden");
                            newCell.css("text-overflow", "ellipsis");
                        }
                        else
                        {
                            //Celle dati
                            let hasColorMap = false;
                            let mapIdx = null;
                            let cellColor = null;
                            if (colorMaps != null) {
                                if (colorMaps != "" && colorMaps != "[]") {
                                    for (let h = 0; h < colorMaps.length; h++) {
                                        if (colorMaps[h].id == j - 1) {
                                            hasColorMap = true;
                                            mapIdx = h;
                                        }
                                    }
                                }
                            }
                            if (hasColorMap && series.secondAxis.series[z][k] != null && $.isNumeric(series.secondAxis.series[z][k])) {
                                for (let t = 0; t < colorMaps[mapIdx].colorMap.length; t++) {
                                    if (colorMaps[mapIdx].colorMap[t].min == null) {
                                        if (series.secondAxis.series[z][k] <= colorMaps[mapIdx].colorMap[t].max) {
                                            cellColor = colorMaps[mapIdx].colorMap[t].rgb;
                                            break;
                                        }
                                    } else if (colorMaps[mapIdx].colorMap[t].max == null) {
                                        if (series.secondAxis.series[z][k] > colorMaps[mapIdx].colorMap[t].min) {
                                            cellColor = colorMaps[mapIdx].colorMap[t].rgb;
                                            break;
                                        }
                                    } else {
                                        if (series.secondAxis.series[z][k] > colorMaps[mapIdx].colorMap[t].min && series.secondAxis.series[z][k] <= colorMaps[mapIdx].colorMap[t].max) {
                                            cellColor = colorMaps[mapIdx].colorMap[t].rgb;
                                            break;
                                        }
                                    }
                                }
                            }
                            newCell = $("<td>" + series.secondAxis.series[z][k] + "</td>");
                            if (cellColor != null) {
                                newCell.css('background-color', "rgb(" + cellColor.substring(1, cellColor.length-1) + ")");
                            }
                            newCell.css('font-size', fontSize + "px");
                            newCell.css('color', fontColor);
                            newCell.css("overflow", "hidden");
                            newCell.css("text-overflow", "ellipsis");
                        }
                        newRow.append(newCell);
                    }
                }
                $("#<?= $_REQUEST['name_w'] ?>_table").append(newRow);      
            }
            
            switch(tableBorders)
            {
                case "no":
                    $("#<?= $_REQUEST['name_w'] ?>_table").css("border", "none");
                    $("#<?= $_REQUEST['name_w'] ?>_table tr").css("border", "none");
                    $("#<?= $_REQUEST['name_w'] ?>_table tr td").css("border", "none");
                    break;
                    
                case "horizontal":
                    $("#<?= $_REQUEST['name_w'] ?>_table").css("border", "none");
                    $("#<?= $_REQUEST['name_w'] ?>_table tr").css("border", "none");
                    $("#<?= $_REQUEST['name_w'] ?>_table tr td").css("border", "none");
                    $("#<?= $_REQUEST['name_w'] ?>_table tr").css("border-bottom-width", "1px");
                    $("#<?= $_REQUEST['name_w'] ?>_table tr").css("border-bottom-style", "solid");
                    $("#<?= $_REQUEST['name_w'] ?>_table tr").css("border-bottom-color", tableBordersColor);
                    $("#<?= $_REQUEST['name_w'] ?>_table tr td").css("border-bottom-width", "1px");
                    $("#<?= $_REQUEST['name_w'] ?>_table tr td").css("border-bottom-style", "solid");
                    $("#<?= $_REQUEST['name_w'] ?>_table tr td").css("border-bottom-color", tableBordersColor);
                    break;
                    
                case "all":
                    $("#<?= $_REQUEST['name_w'] ?>_table").css("border-width", "1px");
                    $("#<?= $_REQUEST['name_w'] ?>_table").css("border-style", "solid");
                    $("#<?= $_REQUEST['name_w'] ?>_table").css("border-color", tableBordersColor);
                    $("#<?= $_REQUEST['name_w'] ?>_table tr").css("border-width", "1px");
                    $("#<?= $_REQUEST['name_w'] ?>_table tr").css("border-style", "solid");
                    $("#<?= $_REQUEST['name_w'] ?>_table tr").css("border-color", tableBordersColor);
                    $("#<?= $_REQUEST['name_w'] ?>_table tr td").css("border-width", "1px");
                    $("#<?= $_REQUEST['name_w'] ?>_table tr td").css("border-style", "solid");
                    $("#<?= $_REQUEST['name_w'] ?>_table tr td").css("border-color", tableBordersColor);
                    break;
                    
                default:
                    $("#<?= $_REQUEST['name_w'] ?>_table").css("border", "none");
                    $("#<?= $_REQUEST['name_w'] ?>_table tr").css("border", "none");
                    $("#<?= $_REQUEST['name_w'] ?>_table tr td").css("border", "none");
                    break;    
            }
            $("#<?= $_REQUEST['name_w'] ?>_table tr:last").css("border-bottom", "none");
            $("#<?= $_REQUEST['name_w'] ?>_table tr:last td").css("border-bottom", "none");
        }
        
        //Funzione di colorazione delle celle in base alle eventuali soglie stabilite
        function applyThresholdCodes(seriesString2)
        {
            var target = null;
            
            if((thresholdsJson !== null)&&(thresholdsJson !== undefined)&&(thresholdsJson !== 'undefined'))
            {
                target = thresholdsJson.thresholdObject.target;
                var series2 = jQuery.parseJSON(seriesString2);
                var fields = null;
                var thrFields = null;
                
                if(target === series2.firstAxis.desc)
                {
                    //Caso in cui le soglie sono definite sulle colonne
                    var tableLabels = new Array();
                    $('#<?= $_REQUEST['name_w'] ?>_table tr').each(function (i, row) {
                        var row = $(row);
                        var cells = $(this).find('td');
                        var cellValue = null;
                        var cellLabel = null;
                        var thrSeries = null;
                        var min = null;
                        var max = null;
                        var color = null;
                        if(i === 0)
                        {
                           //Labels sulle colonne
                            cells.each(function (k){
                                if(k !== 0)
                                {
                                    tableLabels.push(cells.eq(k).find('span').html());
                                }
                                else
                                {
                                    tableLabels.push("Pippo");
                                }
                            });
                        }
                        else
                        {
                            //Labels sulle righe
                            cells.each(function (j){
                                if(j !== 0)
                                {
                                    cellValue = parseFloat(cells.eq(j).html());
                                    fields = thresholdsJson.thresholdObject.firstAxis.fields;
                                    if(fields[parseInt(parseInt(j) - 1)].fieldName === tableLabels[j])
                                    {
                                        thrSeries = fields[parseInt(parseInt(j) - 1)].thrSeries;
                                        if(thrSeries.length > 0)
                                        {
                                            for(var a = 0; a < thrSeries.length; a++)
                                            {
                                                min = parseInt(thrSeries[a].min);
                                                max = parseInt(thrSeries[a].max);
                                                color = thrSeries[a].color;
                                                if((cellValue >= min) && (cellValue < max))
                                                {
                                                    cells.eq(j).css("background-color", color);
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        }
                    });    
                }
                else if(target === series2.secondAxis.desc)
                {
                    var tableLabels = new Array();
                    var index = null;
                    
                    $('#<?= $_REQUEST['name_w'] ?>_table tr').each(function (i, row) {
                        var row = $(row);
                        index = i;
                        
                        var cells = $(this).find('td');
                        var cellValue = null;
                        var cellLabel = null;
                        var thrSeries = null;
                        var min = null;
                        var max = null;
                        var color = null;
                        
                        if(i !== 0)
                        {
                            cells.each(function (j){
                                
                                if(j === 0)
                                {
                                    tableLabels.push(cells.eq(j).html());
                                }
                                else
                                {
                                    cellValue = parseFloat(cells.eq(j).html());
                                    fields = thresholdsJson.thresholdObject.secondAxis.fields;
                                    
                                    for(var z = 0; z < fields.length; z++)
                                    {
                                        if(fields[z].fieldName === tableLabels[parseInt(index-1)])
                                        {
                                            thrSeries = fields[z].thrSeries;
                                            for(var y = 0; y < thrSeries.length; y++)
                                            {
                                                min = parseInt(thrSeries[y].min);
                                                max = parseInt(thrSeries[y].max);
                                                color = thrSeries[y].color;
                                                if((cellValue >= min) && (cellValue < max))
                                                {
                                                    cells.eq(j).css("background-color", color);
                                                }
                                            }
                                        }
                                    }
                                }
                                
                            });
                        }
                    });   
                }
            }
        }

        function createLegends(seriesString2, widgetHeight)
        {
            var target = null;

            if((thresholdsJson !== null)&&(thresholdsJson !== undefined)&&(thresholdsJson !== 'undefined')&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
            {
                var thresholdObject = thresholdsJson.thresholdObject;
                target = thresholdObject.target;
                var thresholdObject = thresholdsJson.thresholdObject;
                var series2 = jQuery.parseJSON(seriesString2);
                var fields, thrFields, dropdownLegend, dropDownElement, label, tableCell = null;
                var rowsLabelsFontSize = styleParameters.rowsLabelsFontSize;
                var rowsLabelsFontColor = styleParameters.rowsLabelsFontColor;
                var colsLabelsFontSize = styleParameters.colsLabelsFontSize;
                var colsLabelsFontColor = styleParameters.colsLabelsFontColor;
                var k, labelCellWidth, legendWidth, legendMargin, colsLabels = null;

                if(target === series2.firstAxis.desc)
                {
                    colsLabels = thresholdObject.firstAxis.fields.length;
                    $('#<?= $_REQUEST['name_w'] ?>_table tr').first().find('td').each(function (i)
                    {
                        if(i !== 0)
                        {
                            tableCell = $(this);
                            labelCellWidth = tableCell.width();
                            var base = (i-1)/colsLabels;
                            var quadrato = (Math.pow(base, 3)).toFixed(2);
                            legendMargin = quadrato*100;

                            k = parseInt(i - 1);
                            if(thresholdObject.firstAxis.fields[k].thrSeries.length > 0)
                            {
                                label = $(this).find('span').html();

                                dropdownLegend = $('<div class="dropdown">' +
                                    '<a href="#" data-toggle="dropdown" class="dropdown-toggle"><span class="inline">' + label + '</span><b class="caret"></b></a>' +
                                    '<ul class="dropdown-menu">' +
                                    '</ul>' +
                                    '</div>');
                                dropdownLegend.find("a").css("text-decoration", "none");
                                dropdownLegend.find("a").css("color", colsLabelsFontColor);
                                dropdownLegend.find("a").css("font-size", colsLabelsFontSize);
                                dropdownLegend.find("ul").css("padding-left", "2px");
                                dropdownLegend.find("a:hover").css("text-decoration", "none");
                                dropdownLegend.find("a:link").css("text-decoration", "none");
                                dropdownLegend.find("a:visited").css("text-decoration", "none");
                                dropdownLegend.find("a:active").css("text-decoration", "none");

                                thresholdObject.firstAxis.fields[k].thrSeries.forEach(function(range)
                                {
                                    dropDownElement = $('<li><a href="#"><div style="width: 15px; height: 15px; border: none; float: left; background-color: ' + range.color + '"></div>&nbsp;&nbsp;&nbsp;' + range.min + ' <i class="fa fa-arrows-h"></i> ' + range.max + '</a></li>');
                                    /*if(range.desc !== '')
                                    {
                                        dropDownElement = $('<li><a href="#"><div style="width: 15px; height: 15px; border: none; float: left; background-color: ' + range.color + '"></div>&nbsp;' + range.min + ' <i class="fa fa-arrows-h"></i> ' + range.max + '&nbsp;&nbsp;<b>' + range.desc + '</b></a></li>');
                                    }
                                    else
                                    {
                                        dropDownElement = $('<li><a href="#"><div style="width: 15px; height: 15px; border: none; float: left; background-color: ' + range.color + '"></div>&nbsp;&nbsp;' + range.min + ' <i class="fa fa-arrows-h"></i> ' + range.max + '</a></li>');
                                    }*/
                                    dropDownElement.css("font", "bold 10px Verdana");
                                    dropDownElement.find("i").css("font-size", "12px");
                                    dropdownLegend.find("ul").append(dropDownElement);
                                });
                                dropdownLegend.find("ul").css("left", "-" + legendMargin + "%");
                                $(this).html(dropdownLegend);
                            }
                        }
                    });
                }
                else
                {
                    //LEGENDA SOGLIE SUL SECONDO ASSE
                    var firstRowH, firstRowTopBorder, firstRowBottomBorder, rowH, rowTopBorder, rowBottomBorder, labelHeight = null;

                    $('#<?= $_REQUEST['name_w'] ?>_table tr').each(function (i)
                    {
                        if(i > 0)
                        {
                            legendHeight = parseInt(thresholdObject.secondAxis.fields[i-1].thrSeries.length*20 + 10);
                            if(i === 1)
                            {
                                rowH = $(this).height();
                                rowTopBorder = parseInt($(this).css("border-top-width").replace('px', ''));
                                rowBottomBorder = parseInt($(this).css("border-bottom-width").replace('px', ''));
                                rowH = parseInt(rowH + rowTopBorder + rowBottomBorder);
                            }
                            var rowDistanceFromTop = 25 + firstRowH + parseInt((i - 1)*rowH);
                            var rowDistanceFromBottom = widgetHeight - rowDistanceFromTop - rowH;
                            var labelDistanceFromRow = parseInt((rowH - rowsLabelsFontSize)/2);
                            var availableHeight = labelDistanceFromRow + rowDistanceFromBottom;
                            var menuType = null;
                            if(availableHeight > legendHeight)
                            {
                                menuType = 'dropdown';
                            }
                            else
                            {
                                menuType = 'dropup';
                            }

                            tableCell = $(this).find('td').first();
                            labelCellWidth = tableCell.width();
                            legendWidth = 118;
                            legendMargin = parseInt((Math.abs(labelCellWidth - legendWidth))/2);


                            k = parseInt(i - 1);
                            if(thresholdObject.secondAxis.fields[k].thrSeries.length > 0)
                            {
                                label = tableCell.html();
                                dropdownLegend = $('<div class="' + menuType + '">' +
                                    '<a href="#" data-toggle="dropdown" class="dropdown-toggle"><span>' + label + '</span><b class="caret"></b></a>' +
                                    '<ul class="dropdown-menu">' +
                                    '</ul>' +
                                    '</div>');

                                dropdownLegend.find("a").css("color", rowsLabelsFontColor);
                                dropdownLegend.find("a").css("font-size", rowsLabelsFontSize);
                                dropdownLegend.find("ul").css("padding-left", "2px");
                                dropdownLegend.find("a").css("text-decoration", "none");
                                dropdownLegend.find("a:hover").css("text-decoration", "none");
                                dropdownLegend.find("a:link").css("text-decoration", "none");
                                dropdownLegend.find("a:visited").css("text-decoration", "none");
                                dropdownLegend.find("a:active").css("text-decoration", "none");

                                thresholdObject.secondAxis.fields[k].thrSeries.forEach(function(range)
                                {
                                    dropDownElement = $('<li><a href="#"><div style="width: 15px; height: 15px; border: none; float: left; background-color: ' + range.color + '"></div>&nbsp;&nbsp;&nbsp;' + range.min + ' <i class="fa fa-arrows-h"></i> ' + range.max + '</a></li>');
                                    /*if(range.desc !== '')
                                    {
                                        dropDownElement = $('<li><a href="#"><div style="width: 15px; height: 15px; border: none; float: left; background-color: ' + range.color + '"></div>&nbsp;' + range.min + ' <i class="fa fa-arrows-h"></i> ' + range.max + '&nbsp;&nbsp;<b>' + range.desc + '</b></a></li>');
                                    }
                                    else
                                    {
                                        dropDownElement = $('<li><a href="#"><div style="width: 15px; height: 15px; border: none; float: left; background-color: ' + range.color + '"></div>&nbsp;&nbsp;' + range.min + ' <i class="fa fa-arrows-h"></i> ' + range.max + '</a></li>');
                                    }*/
                                    dropDownElement.css("font", "bold 10px Verdana");
                                    dropDownElement.find("i").css("font-size", "12px");
                                    dropdownLegend.find("ul").append(dropDownElement);
                                    legendWidth = dropdownLegend.find('ul').width();
                                });

                                dropdownLegend.find("ul").css("left", legendMargin + "px");
                                tableCell.html(dropdownLegend);
                            }
                        }
                        else
                        {
                            firstRowH = $(this).height();
                            firstRowTopBorder = parseInt($(this).css("border-top-width").replace('px', ''));
                            firstRowBottomBorder = parseInt($(this).css("border-bottom-width").replace('px', ''));
                            firstRowH = parseInt(firstRowH + firstRowTopBorder + firstRowBottomBorder);
                        }

                    });
                }
            }
        }

        function createLegendFromColorMap(series2, widgetHeight)
        {
            var target = null;
            
            if((colorMaps !== null)&&(colorMaps !== undefined)&&(colorMaps !== 'undefined'))
            {
                var fields, thrFields, dropdownLegend, dropDownElement, label, tableCell = null;
                var rowsLabelsFontSize = styleParameters.rowsLabelsFontSize;
                var rowsLabelsFontColor = styleParameters.rowsLabelsFontColor;
                var colsLabelsFontSize = styleParameters.colsLabelsFontSize;
                var colsLabelsFontColor = styleParameters.colsLabelsFontColor;
                var k, labelCellWidth, legendWidth, legendMargin, colsLabels = null;

                colsLabels = series2.firstAxis.labels.length;
                $('#<?= $_REQUEST['name_w'] ?>_table tr').first().find('td').each(function (i)
                {
                    if(i !== 0)
                    {
                        tableCell = $(this);
                        labelCellWidth = tableCell.width();
                        var base = (i-1)/colsLabels;
                        var quadrato = (Math.pow(base, 3)).toFixed(2);
                        legendMargin = quadrato*100;

                        k = parseInt(i - 1);
                        colorMaps.forEach(function(cMap) {
                            if (cMap.colorMap.length > 0 && cMap.id == k) {
                                label = tableCell.find('span').html();

                                dropdownLegend = $('<div class="dropdown">' +
                                    '<a href="#" data-toggle="dropdown" class="dropdown-toggle"><span class="inline">' + label + '</span><b class="caret"></b></a>' +
                                    '<ul class="dropdown-menu">' +
                                    '</ul>' +
                                    '</div>');
                                dropdownLegend.find("a").css("text-decoration", "none");
                                dropdownLegend.find("a").css("color", colsLabelsFontColor);
                                dropdownLegend.find("a").css("font-size", colsLabelsFontSize);
                                dropdownLegend.find("ul").css("padding-left", "2px");
                                dropdownLegend.find("a:hover").css("text-decoration", "none");
                                dropdownLegend.find("a:link").css("text-decoration", "none");
                                dropdownLegend.find("a:visited").css("text-decoration", "none");
                                dropdownLegend.find("a:active").css("text-decoration", "none");

                                cMap.colorMap.forEach(function (range) {
                                    let minTh, maxTh = null;
                                    if (range.min == null) {
                                        dropDownElement = $('<li><a href="#"><div style="width: 15px; height: 15px; border: none; float: left; background-color: rgb(' + range.rgb.substring(1, range.rgb.length - 1) + ')"></div>&nbsp;&nbsp;&nbsp; < ' + range.max + '</a></li>');
                                    } else if (range.max == null) {
                                        dropDownElement = $('<li><a href="#"><div style="width: 15px; height: 15px; border: none; float: left; background-color: rgb(' + range.rgb.substring(1, range.rgb.length - 1) + ')"></div>&nbsp;&nbsp;&nbsp; > ' + range.min + '</a></li>');
                                    } else {
                                        dropDownElement = $('<li><a href="#"><div style="width: 15px; height: 15px; border: none; float: left; background-color: rgb(' + range.rgb.substring(1, range.rgb.length - 1) + ')"></div>&nbsp;&nbsp;&nbsp;' + range.min + ' <i class="fa fa-arrows-h"></i> ' + range.max + '</a></li>');
                                    }
                                //    dropDownElement = $('<li><a href="#"><div style="width: 15px; height: 15px; border: none; float: left; background-color: rgb(' + range.rgb.substring(1, range.rgb.length - 1) + ')"></div>&nbsp;&nbsp;&nbsp;' + minTh + ' <i class="fa fa-arrows-h"></i> ' + maxTh + '</a></li>');
                                    /*if(range.desc !== '')
                                    {
                                        dropDownElement = $('<li><a href="#"><div style="width: 15px; height: 15px; border: none; float: left; background-color: ' + range.color + '"></div>&nbsp;' + range.min + ' <i class="fa fa-arrows-h"></i> ' + range.max + '&nbsp;&nbsp;<b>' + range.desc + '</b></a></li>');
                                    }
                                    else
                                    {
                                        dropDownElement = $('<li><a href="#"><div style="width: 15px; height: 15px; border: none; float: left; background-color: ' + range.color + '"></div>&nbsp;&nbsp;' + range.min + ' <i class="fa fa-arrows-h"></i> ' + range.max + '</a></li>');
                                    }*/
                                    dropDownElement.css("font", "bold 10px Verdana");
                                    dropDownElement.find("i").css("font-size", "12px");
                                    dropdownLegend.find("ul").append(dropDownElement);
                                });
                                dropdownLegend.find("ul").css("left", "-" + legendMargin + "%");
                                tableCell.html(dropdownLegend);
                            }
                        });
                    }
                });

            }
        }
        
        function createInfoButtons()
        {
            var colsLabelsFontSize = styleParameters.colsLabelsFontSize;
            var colsLabelsFontColor = styleParameters.colsLabelsFontColor;
            var rowsLabelsFontSize = styleParameters.rowsLabelsFontSize;
            var rowsLabelsFontColor = styleParameters.rowsLabelsFontColor;
            var label, id, singleInfo, infoIcon, cell, cellContent, newCellContent = null;
            
            if((infoJson !== null)&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
            {
                //Aggiunta tasti alle labels sulle colonne
                $('#<?= $_REQUEST['name_w'] ?>_table tr').first().find('td').each(function (i) 
                {
                    if(i > 0)
                    {
                        cellContent = $($(this).html());
                        label = $(this).find('span').html();
                        id = label.replace(/\s/g, '_');
                        
                        singleInfo = infoJson.firstAxis[id];
                        
                        if(singleInfo !== '')
                        {
                            if(cellContent.find('a').length > 0)
                            {
                                //C'è la legenda sulla colonna
                                var infoIcon = $('<i class="fa fa-info-circle handPointer" style="font-size: ' + colsLabelsFontSize + 'px; color: ' + colsLabelsFontColor + '"></i><br/>');
                                infoIcon.insertBefore($(this).find('a.dropdown-toggle'));
                            }
                            else
                            {
                                //Non c'è la legenda sulla colonna
                                newCellContent = $('<i class="fa fa-info-circle handPointer" style="font-size: ' + colsLabelsFontSize + 'px; color: ' + colsLabelsFontColor + '"></i><br/>' +
                                        '<span>' + label + '</span>');
                                $(this).html(newCellContent);

                            }
                            $(this).find('i').on("click", showModalFieldsInfoFirstAxis);
                        }
                    }
                });
                
                //Aggiunta tasti alle labels sulle righe
                $('#<?= $_REQUEST['name_w'] ?>_table tr').each(function (i) 
                {
                    if(i > 0)//Si salta la prima riga
                    {
                        cell = $(this).find('td').eq(0);
                        cellContent = $(cell.html());
                            
                        if(cellContent.find('a').length > 0)
                        {
                            //C'è la legenda sulla riga
                            label = cellContent.find('span').html();
                            id = label.replace(/\s/g, '_');
                            singleInfo = infoJson.secondAxis[id];

                            if((singleInfo !== '')&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
                            {
                                var infoIcon = $('<i class="fa fa-info-circle handPointer" style="font-size: ' + rowsLabelsFontSize + 'px; color: ' + rowsLabelsFontColor + '"></i><br/>');
                                infoIcon.insertBefore(cell.find('a.dropdown-toggle'));
                            }
                        }
                        else
                        {
                            //Non c'è la legenda sulla riga
                            label = cell.html();
                            id = label.replace(/\s/g, '_');
                            singleInfo = infoJson.secondAxis[id];

                            if((singleInfo !== '')&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
                            {
                                newCellContent = $('<i class="fa fa-info-circle handPointer" style="font-size: ' + rowsLabelsFontSize + 'px; color: ' + rowsLabelsFontColor + '"></i><br/>' +
                                    '<span>' + label + '</span>');
                                cell.html(newCellContent);
                            }
                        }
                        $(this).find('i').on("click", showModalFieldsInfoSecondAxis);
                    }
                });
            }
        }
        
        function showModalFieldsInfoFirstAxis()
        {
            var label = $(this).parent().find('span').html();
            var id = label.replace(/\s/g, '_');
            var info = infoJson.firstAxis[id];
            
            $('#modalWidgetFieldsInfoTitle').html("Detailed info for field <b>" + label + "</b>");
            $('#modalWidgetFieldsInfoContent').html(info);
            
            $('#modalWidgetFieldsInfo').css({
                'vertical-align': 'middle',
                'position': 'absolute',
                'top': '10%'
            });
            $('#modalWidgetFieldsInfo').modal('show');
        }
        
        function showModalFieldsInfoSecondAxis()
        {
            var label = $(this).parent().find('span').html();
            var id = label.replace(/\s/g, '_');
            var info = infoJson.secondAxis[id];
            
            $('#modalWidgetFieldsInfoTitle').html("Detailed info for field <b>" + label + "</b>");
            $('#modalWidgetFieldsInfoContent').html(info);
            
            $('#modalWidgetFieldsInfo').css({
                'vertical-align': 'middle',
                'position': 'absolute',
                'top': '10%'
            });
            $('#modalWidgetFieldsInfo').modal('show');
        }
        
        //Fine definizioni di funzione  
        
        //Codice core del widget
        if(url === "null")
        {
            url = null;
        }
        
        if((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null))
        {
            metricName = "<?= escapeForJS($_REQUEST['id_metric']) ?>";
            widgetTitle = "<?= sanitizeTitle($_REQUEST['title_w']) ?>";
            widgetHeaderColor = "<?= escapeForJS($_REQUEST['frame_color_w']) ?>";
            widgetHeaderFontColor = "<?= escapeForJS($_REQUEST['headerFontColor']) ?>";
        }
        else
        {
            metricName = metricNameFromDriver;
            widgetTitleFromDriver.replace(/_/g, " ");
            widgetTitleFromDriver.replace(/\'/g, "&apos;");
            widgetTitle = widgetTitleFromDriver;
            $("#" + widgetName).css("border-color", widgetHeaderColorFromDriver);
            widgetHeaderColor = widgetHeaderColorFromDriver;
            widgetHeaderFontColor = widgetHeaderFontColorFromDriver;
        }
        
        $(document).off('changeMetricFromButton_' + widgetName);
        $(document).on('changeMetricFromButton_' + widgetName, function(event) 
        {
            if((event.targetWidget === widgetName) && (event.newMetricName !== "noMetricChange"))
            {
                $("#<?= $_REQUEST['name_w'] ?>_table").empty();
                clearInterval(countdownRef); 
                $("#<?= $_REQUEST['name_w'] ?>_content").hide();
                <?= $_REQUEST['name_w'] ?>(true, event.newMetricName, event.newTargetTitle, event.newHeaderAndBorderColor, event.newHeaderFontColor, false, null, null, /*null,*/ null, null);
            }
        });
	
        function resizeWidget()
        {
            setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
            setTableHeight();
        }     
            
        $(document).off('resizeHighchart_' + widgetName);
        $(document).on('resizeHighchart_' + widgetName, function(event){
            showHeader = event.showHeader;
            resizeWidget();
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
        //addLink(widgetName, url, linkElement, divContainer, null);
        
        //Nuova versione
        if(('<?= sanitizeJsonRelaxed2($_REQUEST['styleParameters']) ?>' !== "")&&('<?= sanitizeJsonRelaxed2($_REQUEST['styleParameters']) ?>' !== "null"))
        {
            styleParameters = JSON.parse('<?= sanitizeJsonRelaxed2($_REQUEST['styleParameters']) ?>');
        }
        
        if('<?= sanitizeJsonRelaxed2($_REQUEST['parameters']) ?>'.length > 0)
        {
            widgetParameters = JSON.parse('<?= sanitizeJsonRelaxed2($_REQUEST['parameters']) ?>');
            thresholdsJson = widgetParameters;
        }
        
        if(('<?= sanitizeJsonRelaxed2($_REQUEST['infoJson']) ?>' !== 'null')&&('<?= sanitizeJsonRelaxed2($_REQUEST['infoJson']) ?>' !== ''))
        {
            infoJson = "<?= sanitizeJsonRelaxed2($_REQUEST['infoJson']) ?>";
        }

        //Nuova versione // **************** NEW GP ********************************************************************
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
                fontColor = widgetData.params.fontColor;
                timeToReload = widgetData.params.frequency_w;
                hasTimer = widgetData.params.hasTimer;
                chartColor = widgetData.params.chartColor;
                dataLabelsFontSize = widgetData.params.dataLabelsFontSize;
                dataLabelsFontColor = widgetData.params.dataLabelsFontColor;
                chartLabelsFontSize = widgetData.params.chartLabelsFontSize;
                chartLabelsFontColor = widgetData.params.chartLabelsFontColor;
                appId = widgetData.params.appId;
                flowId = widgetData.params.flowId;
                nrMetricType = widgetData.params.nrMetricType;
                gridLineColor = widgetData.params.chartPlaneColor;
                chartAxesColor = widgetData.params.chartAxesColor;
                serviceUri = widgetData.params.serviceUri;

                if(((embedWidget === true)&&(embedWidgetPolicy === 'auto'))||((embedWidget === true)&&(embedWidgetPolicy === 'manual')&&(showTitle === "no"))||((embedWidget === false)&&(showTitle === "no")))
                {
                    showHeader = false;
                }
                else
                {
                    showHeader = true;
                }

                if((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null))
                {
                    metricName = "<?= escapeForJS($_REQUEST['id_metric']) ?>";
                    widgetTitle = "<?= sanitizeTitle($_REQUEST['title_w']) ?>";
                    widgetHeaderColor = "<?= escapeForJS($_REQUEST['frame_color_w']) ?>";
                    widgetHeaderFontColor = "<?= escapeForJS($_REQUEST['headerFontColor']) ?>";
                    rowParameters = widgetData.params.rowParameters;
                }
                else
                {
                    metricName = metricNameFromDriver;
                    widgetTitleFromDriver.replace(/_/g, " ");
                    widgetTitleFromDriver.replace(/\'/g, "&apos;");
                    widgetTitle = widgetTitleFromDriver;
                    $("#" + widgetName).css("border-color", widgetHeaderColorFromDriver);
                    widgetHeaderColor = widgetHeaderColorFromDriver;
                    widgetHeaderFontColor = widgetHeaderFontColorFromDriver;
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

                if((widgetData.params.styleParameters !== "")&&(widgetData.params.styleParameters !== "null"))
                {
                    styleParameters = JSON.parse(widgetData.params.styleParameters);
                    groupByAttr = styleParameters['groupByAttr'];
                    if (styleParameters.colorMaps != null) {
                        if (styleParameters.colorMaps != "" && styleParameters.colorMaps != "[]") {
                            colorMaps = JSON.parse(styleParameters.colorMaps);
                        }
                    }
                }

                if(widgetData.params.parameters !== null)
                {
                    if(widgetData.params.parameters.length > 0)
                    {
                        widgetParameters = JSON.parse(widgetData.params.parameters);
                        thresholdsJson = widgetParameters;
                    }
                }

                if((widgetData.params.infoJson !== 'null')&&(widgetData.params.infoJson !== ''))
                {
                    infoJson = JSON.parse(widgetData.params.infoJson);
                    //Patch per il resize, non mostriamo i pulsanti info per ora
                    infoJson = null;
                }

                chartType = styleParameters.chartType;
                lineWidth = styleParameters.lineWidth;

                /*     switch(chartType)
                     {
                         case 'lines':
                             stackingOption = null;
                             highchartsChartType = 'spline';
                             dataLabelsAlign = 'center';
                             dataLabelsVerticalAlign = 'middle';
                             dataLabelsY = 0;
                             break;

                         case 'area':
                             stackingOption = null;
                             highchartsChartType = 'areaspline';
                             dataLabelsAlign = 'center';
                             dataLabelsVerticalAlign = 'middle';
                             dataLabelsY = 0;
                             break;

                         case 'stacked':
                             stackingOption = 'normal';
                             highchartsChartType = 'areaspline';
                             dataLabelsAlign = 'center';
                             dataLabelsVerticalAlign = 'middle';
                             dataLabelsY = 0;
                             break;

                         default:
                             stackingOption = null;
                             highchartsChartType = 'spline';
                             dataLabelsAlign = 'center';
                             break;
                     }*/

                let aggregationFlag = false;
                if (JSON.parse(widgetData.params.rowParameters) != null) {
                    if (JSON.parse(widgetData.params.rowParameters)[0].metricHighLevelType == "Sensor" || JSON.parse(widgetData.params.rowParameters)[0].metricHighLevelType == "MyKPI") {
                        aggregationFlag = true;
                    }
                }

                if (widgetData.params.id_metric === 'AggregationSeries' || aggregationFlag === true || widgetData.params.id_metric.includes("NR_"))
                {
                    rowParameters = JSON.parse(rowParameters);
                    aggregationGetData = [];
                    getDataFinishCount = 0;
                    editLabels = (JSON.parse(widgetData.params.styleParameters)).editDeviceLabels;

                    for(var i = 0; i < rowParameters.length; i++)
                    {
                        aggregationGetData[i] = false;
                    }

                    for(var i = 0; i < rowParameters.length; i++)
                    {
                        let dataOrigin = rowParameters[i].metricHighLevelType;
                        switch(dataOrigin) {
                            case "KPI":
                                index = i;
                                $.ajax({
                                    url: "../controllers/aggregationSeriesProxy.php",
                                    type: "POST",
                                    data:
                                        {
                                            dataOrigin: JSON.stringify(rowParameters[i]),
                                            index: i
                                        },
                                    async: true,
                                    dataType: 'json',
                                    success: function (data) {
                                        aggregationGetData[data.index] = data;
                                        getDataFinishCount++;

                                        //Popoliamo il widget quando sono arrivati tutti i dati
                                        if (getDataFinishCount === rowParameters.length) {
                                            populateTable(JSON.stringify(series));
                                            //   legendWidth = $("#<?= $_REQUEST['name_w'] ?>_content").width();
                                            //    xAxisCategories = getXAxisCategories(series, widgetHeight);
                                            applyThresholdCodes(series);
                                            setTableHeight();
                                            var widgetHeight = parseInt($("#WeatherStations_77_widgetTable783_table").height() + 25);
                                            createLegends(series, widgetHeight);
                                            createInfoButtons();

                                            if (firstLoad !== false) {
                                                showWidgetContent(widgetName);
                                                $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').hide();
                                                $("#<?= $_REQUEST['name_w'] ?>_chartContainer").show();
                                                $("#<?= $_REQUEST['name_w'] ?>_table").show();
                                            } else {
                                                elToEmpty.empty();
                                                $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').hide();
                                                $("#<?= $_REQUEST['name_w'] ?>_chartContainer").show();
                                                $("#<?= $_REQUEST['name_w'] ?>_table").show();
                                            }

                                        //    drawDiagram();
                                        }
                                    },
                                    error: function (errorData) {
                                        metricData = null;
                                        console.log("Error in data retrieval");
                                        console.log(JSON.stringify(errorData));
                                        showWidgetContent(widgetName);
                                        $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                                        $("#<?= $_REQUEST['name_w'] ?>_table").hide();
                                        $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                                    }
                                });
                                break;

                            case "Sensor":
                                var timeRange = null;
                                var urlToCall = "";
                                var xlabels = [];
                                let smUrl = "";
                                if (rowParameters[i].metricId.split("serviceUri=").length > 1) {
                                    smUrl = "<?= $superServiceMapProxy ?>/api/v1/?serviceUri=" + rowParameters[i].metricId.split("serviceUri=")[1];
                                } else {
                                    smUrl = "<?= $superServiceMapProxy ?>/api/v1/?serviceUri=" + rowParameters[i].metricId;
                                }
                                //    metricType = "Float";

                                if("<?= $_REQUEST['timeRange']?>") {
                                    if("<?= $_REQUEST['timeRange'] ?>" != 'last' && "<?= $_REQUEST['timeRange'] ?>" != "") {
                                        /*  switch("<?= $_REQUEST['timeRange'] ?>") {
                                            case "4 Ore":
                                                timeRange = "fromTime=4-hour";
                                                break;

                                            case "12 Ore":
                                                timeRange = "fromTime=12-hour";
                                                break;

                                            case "Giornaliera":
                                                timeRange = "fromTime=1-day";
                                                break;

                                            case "Settimanale":
                                                timeRange = "fromTime=7-day";
                                                break;

                                            case "Mensile":
                                                timeRange = "fromTime=30-day";
                                                break;

                                            case "Annuale":
                                                timeRange = "fromTime=365-day";
                                                break;
                                        }   */

                                        urlToCall = smUrl + "&" + timeRange;
                                    } else {
                                        urlToCall = smUrl;
                                    }
                                } else {
                                    urlToCall = smUrl;
                                }

                                getSmartCitySensorValues(rowParameters, i, smUrl, null, true, function(extractedData) {

                                    if(extractedData) {
                                        seriesDataArray.push(extractedData);
                                    }
                                    else
                                    {
                                        console.log("Dati Smart City non presenti");
                                        seriesDataArray.push(undefined);
                                    }
                                    //if (endFlag === true) {
                                    // Alla fine quando si arriva all'ultimo record ottenuto dalle varie chiamate asincrone
                                    if (rowParameters.length === seriesDataArray.length) {
                                        // DO FINAL SERIALIZATION
                                        serializeAndDisplay(rowParameters, seriesDataArray, editLabels, groupByAttr);
                                    }

                                });
                                break;

                            case "Dynamic":
                                let extractedData = {};
                                extractedData.value = rowParameters[i].value;
                                extractedData.metricType = rowParameters[i].metricType;
                                extractedData.metricId = rowParameters[i].metricId;
                                extractedData.metricName = rowParameters[i].metricName;
                                extractedData.measuredTime = rowParameters[i].measuredTime;
                                extractedData.metricValueUnit = rowParameters[i].metricValueUnit;

                                seriesDataArray.push(extractedData);

                                if (rowParameters.length === seriesDataArray.length) {
                                    // DO FINAL SERIALIZATION
                                    serializeAndDisplay(rowParameters, seriesDataArray, editLabels, groupByAttr)
                                }

                                break;

                            case "MyKPI":

                                //    var convertedData = getMyKPIValues(rowParameters[i].metricId);
                                let aggregationCell = [];
                                var xlabels = [];
                                let kpiMetricName =  rowParameters[i].metricName;
                                let kpiMetricType =  rowParameters[i].metricType;
                                if (rowParameters[i].metricId.includes("datamanager/api/v1/poidata/")) {
                                    rowParameters[i].metricId = rowParameters[i].metricId.split("datamanager/api/v1/poidata/")[1];
                                }
                                getMyKPIValues(rowParameters, i, null, 1, function (extractedData) {

                                    if (extractedData) {
                                        seriesDataArray.push(extractedData);
                                    } else {
                                        console.log("Dati Smart City non presenti");
                                        seriesDataArray.push(undefined);
                                    }
                                    //if (endFlag === true) {
                                    // Alla fine quando si arriva all'ultimo record ottenuto dalle varie chiamate asincrone
                                    if (rowParameters.length === seriesDataArray.length) {
                                        // DO FINAL SERIALIZATION
                                        serializeAndDisplay(rowParameters, seriesDataArray, editLabels, groupByAttr)
                                    }

                                });
                                break;

                        }
                    }
                }
                else {

                    $.ajax({
                        url: getMetricDataUrl,
                        type: "GET",
                        data: {"IdMisura": ["<?= escapeForJS($_REQUEST['id_metric']) ?>"]},
                        async: true,
                        dataType: 'json',
                        success: function (data) {
                            metricData = data;
                            if (metricData.data.length !== 0) {
                                metricType = metricData.data[0].commit.author.metricType;
                                series = metricData.data[0].commit.author.series;
                                if (firstLoad !== false) {
                                    showWidgetContent(widgetName);
                                    $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').hide();
                                    $("#<?= $_REQUEST['name_w'] ?>_table").show();
                                } else {
                                    elToEmpty.empty();
                                    $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').hide();
                                    $("#<?= $_REQUEST['name_w'] ?>_table").show();
                                }
                                populateTable(series);
                                applyThresholdCodes(series);
                                setTableHeight();
                                var widgetHeight = parseInt($("#<?= $_REQUEST['name_w'] ?>_table").height() + 25);
                                createLegends(series, widgetHeight);
                                createInfoButtons();
                            } else {
                                showWidgetContent(widgetName);
                                $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                                $("#<?= $_REQUEST['name_w'] ?>_table").hide();
                            }
                        },
                        error: function (errorData) {
                            metricData = null;
                            console.log("Error in data retrieval");
                            console.log(JSON.stringify(errorData));
                            showWidgetContent(widgetName);
                            $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                            $("#<?= $_REQUEST['name_w'] ?>_table").hide();
                            $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                        }
                    });
                }
                // GP FINE SECONDA SOLUZIONE ------------------------------------------------------------
            },
            error: function(errorData)
            {
                console.log("Error in widget params retrieval");
                console.log(JSON.stringify(errorData));
                showWidgetContent(widgetName);
                $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                $("#<?= $_REQUEST['name_w'] ?>_table").hide();
                $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
            }
        });
        // ************* FINE NEW GP ***********************************************************************************
        
        $("#<?= $_REQUEST['name_w'] ?>").on('customResizeEvent', function(event){
            resizeWidget();
        });
        
        $("#<?= $_REQUEST['name_w'] ?>").off('updateFrequency');
        $("#<?= $_REQUEST['name_w'] ?>").on('updateFrequency', function(event){
                clearInterval(countdownRef);
                timeToReload = event.newTimeToReload;
                countdownRef = startCountdown(widgetName, timeToReload, <?= $_REQUEST['name_w'] ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef);
        });
        
        countdownRef = startCountdown(widgetName, timeToReload, <?= $_REQUEST['name_w'] ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef);
        //Fine del codice core del widget
    });
</script>

<div class="widget" id="<?= $_REQUEST['name_w'] ?>_div">
    <div class='ui-widget-content'>
        <?php include '../widgets/widgetHeader.php'; ?>
        <?php include '../widgets/widgetCtxMenu.php'; ?>
        
        <div id="<?= $_REQUEST['name_w'] ?>_loading" class="loadingDiv">
            <div class="loadingTextDiv">
                <p>Loading data, please wait</p>
            </div>
            <div class ="loadingIconDiv">
                <i class='fa fa-spinner fa-spin'></i>
            </div>
        </div>
        
        <div id="<?= $_REQUEST['name_w'] ?>_content" class="content">
            <?php include '../widgets/commonModules/widgetDimControls.php'; ?>
            <p id="<?= $_REQUEST['name_w'] ?>_noDataAlert" style='text-align: center; font-size: 18px; display:none'>Nessun dato disponibile</p>
            <table id="<?= $_REQUEST['name_w'] ?>_table" class="tableStyle tableBorder">
            </table>
        </div>
    </div>	
</div> 