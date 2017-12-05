<?php
/* Dashboard Builder.
   Copyright (C) 2017 DISIT Lab https://www.disit.org - University of Florence

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
    $(document).ready(function <?= $_GET['name'] ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef) 
    {
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
        var linkElement = $('#<?= $_GET['name'] ?>_link_w');
        var color = '<?= $_GET['color'] ?>';
        var fontSize = "<?= $_GET['fontSize'] ?>";
        var fontColor = "<?= $_GET['fontColor'] ?>";
        var timeToReload = <?= $_GET['freq'] ?>;
        var widgetProperties = null;
        var elToEmpty = $("#<?= $_GET['name'] ?>_table");
        var url = "<?= $_GET['link_w'] ?>"; 
        var styleParameters, legendHeight, serviceUri, viewMode = null;
        var tableRows = [];
        var embedWidget = <?= $_GET['embedWidget'] ?>;
        var embedWidgetPolicy = '<?= $_GET['embedWidgetPolicy'] ?>';	
        var headerHeight = 25;
        var showTitle = "<?= $_GET['showTitle'] ?>";
	var showHeader = null;
        
        if(((embedWidget === true)&&(embedWidgetPolicy === 'auto'))||((embedWidget === true)&&(embedWidgetPolicy === 'manual')&&(showTitle === "no"))||((embedWidget === false)&&(showTitle === "no")&&(hostFile === "index")))
	{
            showHeader = false;
	}
	else
	{
            showHeader = true;
	}
        
        //Definizioni di funzione specifiche del widget
        
        //Funzione di calcolo ed applicazione dell'altezza della tabella
        function setTableHeight()
        {
            var height = null;
            if((embedWidget === true) && (embedWidgetPolicy === 'auto'))
            {
                height = parseInt($("#<?= $_GET['name'] ?>_div").prop("offsetHeight"));
            }
            else
            {
                //TBD - Vanno gestiti i futuri casi di policy manuale e show/hide header a scelta utente
                height = parseInt($("#<?= $_GET['name'] ?>_div").prop("offsetHeight") - headerHeight);
            }
            
            $("#<?= $_GET['name'] ?>_table").css("height", height);
        }
        
        //Salvataggio del JSON dei dati più recente su DB
        function updateLastSeries(seriesObj)
        {
           $.ajax({
                url: "../management/process-form.php",
                data: {
                   updatedLastSeries: JSON.stringify(seriesObj),
                   widgetName: widgetName
                },
                type: "POST",
                async: true,
                //dataType: 'json',
                success: function (data) 
                {
                   //Non facciamo niente di specifico
                },
                error: function (data)
                {
                   console.log("Save last series KO");
                   console.log(JSON.stringify(data));
                }
                
           });
        }
        
        //Funzione di popolamento della tabella
        function populateTable(serviceUri)
        {
            var series = null;
            var colsQt = null;
            var rowsQt = null;
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
            //var colsLabelsBckColor = styleParameters.colsLabelsBckColor;
            
            //Valori gestione bordi
            var tableBorders = styleParameters.tableBorders;
            var tableBordersColor = styleParameters.tableBordersColor;
            
            if(viewMode === "hospitalsOverview")
            {
               var widgetHospitalList = JSON.parse(widgetProperties.param.hospitalList);
               var hospitalsLoaded = 0;
               
               series = {  
                  "firstAxis":{  
                     "desc":"Priority",
                     "labels":[  
                        "Red code",
                        "Yellow code",
                        "Green code",
                        "Blue code",
                        "White code"
                     ]
                  },
                  "secondAxis":{  
                     "desc":"Hospitals",
                     "labels":[],
                     "series":[]
                  }
               };
               
               //Riga di intestazione
               newRow = $("<tr></tr>");
               
               for(var j = 0; j < 6; j++)
               {
                   if(j === 0)
                   {
                       //Cella (0,0)
                       if(showTableFirstCell === 'yes')
                       {
                           newCell = $("<td>" + series.firstAxis.desc  + "\\<br/>" + series.secondAxis.desc + "</td>");
                           newCell.css("font-size", tableFirstCellFontSize + "px");
                           newCell.css("color", tableFirstCellFontColor);
                           newCell.css("word-wrap", "break-word");
                       }
                       else
                       {
                           newCell = $("<td></td>");
                       }

                       newCell.css("background-color", "transparent");
                   }
                   else
                   {
                        //Celle labels
                        k = parseInt(parseInt(j) -1);
                        var colLabelBckColor = null;
                        switch(k)
                        {
                           case 0:
                              colLabelBckColor = "#ff0000";
                              break;

                           case 1:
                              colLabelBckColor = "#ffff00";
                              break;

                           case 2:
                              colLabelBckColor = "#66ff33";
                              break;

                           case 3:
                              colLabelBckColor = "#66ccff";
                              break;

                           case 4:
                              colLabelBckColor = "#ffffff";
                              break;   
                        }

                        newCell = $("<td><span>" + series.firstAxis.labels[k] + "</span></td>");
                        newCell.css("font-size", colsLabelsFontSize + "px");
                        newCell.css("font-weight", "bold");
                        newCell.css("color", colsLabelsFontColor);
                        newCell.css("background-color", colLabelBckColor);
                        //newCell.css("word-wrap", "break-word");
                   }
                   newRow.append(newCell);
               }
               tableRows.push(newRow);
               
               //Righe dei dati
               for(var i = 0; i < widgetHospitalList.length; i++)
               {
                  $.ajax({
                     url: "https://servicemap.km4city.org/WebAppGrafo/api/v1/?serviceUri=" + widgetHospitalList[i] + "&requestFrom=app&format=json&uid=96d8ecaedc0f2e33262b7c2abd8492d0bbd438a25bcebc392def069553a66a5b&lang=it",
                     type: "GET",
                     async: true,
                     dataType: 'json',
                     success: function (data) 
                     {
                        hospitalsLoaded++;
                        
                        newRow = $("<tr></tr>");
                        
                        if((data.realtime.results === 'undefined') || (typeof data.realtime.results === 'undefined'))
                        {
                           //Codice per dire che la riga non ha dati
                           var hospitalName = data.Service.features[0].properties.name;
                           hospitalName = hospitalName.replace("PRONTO SOCCORSO", "PS");
                           hospitalName = hospitalName.replace("PRIMO INTERVENTO", "PI");
                           hospitalName = hospitalName.replace("AZIENDA OSPEDALIERA", "AO");
                           hospitalName = hospitalName.replace("PRESIDIO OSPEDALIERO", "PO");
                           hospitalName = hospitalName.replace("ISTITUTO DI PUBBLICA ASSISTENZA", "IPA");
                           hospitalName = hospitalName.replace("ASSOCIAZIONE DI PUBBLICA ASSISTENZA", "APA");
                           hospitalName = hospitalName.replace("OSPEDALE DI", "");
                           hospitalName = hospitalName.replace("OSPEDALE DEL", "");
                           hospitalName = hospitalName.replace("OSPEDALE DELL'", "");
                           hospitalName = hospitalName.replace("OSPEDALE DELLA", "");
                           hospitalName = hospitalName.replace("DELL'OSPEDALE", "");
                           hospitalName = hospitalName.replace("OSPEDALE", "");
                           hospitalName = hospitalName.replace("ITALIANA", "");
                           hospitalName = hospitalName.replace("  ", " ");
                           series.secondAxis.labels.push(hospitalName);
                           
                           var dataSlot = [];
                           series.secondAxis.series.push(dataSlot);
                           
                           newCell = $("<td>" + hospitalName + "</td>");
                           newCell.css("font-size", rowsLabelsFontSize + "px");
                           newCell.css("font-weight", "bold");
                           newCell.css("color", rowsLabelsFontColor);
                           newCell.css("background-color", rowsLabelsBckColor);
                           newRow.append(newCell);
                           
                           newCell = $("<td colspan='5'>No data available for this hospital</td>");
                           newCell.css('font-size', fontSize + "px");
                           newCell.css('color', fontColor);
                           newRow.append(newCell);
                           tableRows.push(newRow);
                        }
                        else
                        {
                           var hospitalName = data.Service.features[0].properties.name;
                           hospitalName = hospitalName.replace("PRONTO SOCCORSO", "PS");
                           hospitalName = hospitalName.replace("PRIMO INTERVENTO", "PI");
                           hospitalName = hospitalName.replace("AZIENDA OSPEDALIERA", "AO");
                           hospitalName = hospitalName.replace("PRESIDIO OSPEDALIERO", "PO");
                           hospitalName = hospitalName.replace("ISTITUTO DI PUBBLICA ASSISTENZA", "IPA");
                           hospitalName = hospitalName.replace("ASSOCIAZIONE DI PUBBLICA ASSISTENZA", "APA");
                           hospitalName = hospitalName.replace("OSPEDALE DI", "");
                           hospitalName = hospitalName.replace("OSPEDALE DEL", "");
                           hospitalName = hospitalName.replace("OSPEDALE DELL'", "");
                           hospitalName = hospitalName.replace("OSPEDALE DELLA", "");
                           hospitalName = hospitalName.replace("DELL'OSPEDALE", "");
                           hospitalName = hospitalName.replace("OSPEDALE", "");
                           hospitalName = hospitalName.replace("ITALIANA", "");
                           hospitalName = hospitalName.replace("  ", " ");
                           series.secondAxis.labels.push(hospitalName);
   
                           var dataSlot = [];
                           var index = parseInt(data.realtime.results.bindings.length - 1);

                           dataSlot.push(data.realtime.results.bindings[index].redCode.value);
                           dataSlot.push(data.realtime.results.bindings[index].yellowCode.value);
                           dataSlot.push(data.realtime.results.bindings[index].greenCode.value);
                           dataSlot.push(data.realtime.results.bindings[index].blueCode.value);
                           dataSlot.push(data.realtime.results.bindings[index].whiteCode.value);

                           series.secondAxis.series.push(dataSlot);
                           
                           for(var j = 0; j < 6; j++)
                           {
                               k = parseInt(parseInt(j) -1);
                               if(j === 0)
                               {
                                   //Cella label
                                   newCell = $("<td>" + hospitalName + "</td>");
                                   newCell.css("font-size", rowsLabelsFontSize + "px");
                                   newCell.css("font-weight", "bold");
                                   newCell.css("color", rowsLabelsFontColor);
                                   newCell.css("background-color", rowsLabelsBckColor);
                               }
                               else
                               {
                                   //Celle dati
                                   newCell = $("<td>" + dataSlot[k] + "</td>");
                                   newCell.css('font-size', fontSize + "px");
                                   newCell.css('color', fontColor);
                               }
                               newRow.append(newCell);
                           }
                           tableRows.push(newRow);
                        }
                        
                        if(hospitalsLoaded === widgetHospitalList.length)
                        {
                            if(firstLoad === false)
                            {
                                $("#<?= $_GET['name'] ?>_table").empty();
                            }
                            
                           for(var z = 0; z < tableRows.length; z++)
                           {
                              $("#<?= $_GET['name'] ?>_table").append(tableRows[z]);
                           }
                           
                           switch(tableBorders)
                           {
                               case "no":
                                   $("#<?= $_GET['name'] ?>_table").css("border", "none");
                                   $("#<?= $_GET['name'] ?>_table tr").css("border", "none");
                                   $("#<?= $_GET['name'] ?>_table tr td").css("border", "none");
                                   break;

                               case "horizontal":
                                   $("#<?= $_GET['name'] ?>_table").css("border", "none");
                                   $("#<?= $_GET['name'] ?>_table tr").css("border", "none");
                                   $("#<?= $_GET['name'] ?>_table tr td").css("border", "none");
                                   $("#<?= $_GET['name'] ?>_table tr").css("border-bottom-width", "1px");
                                   $("#<?= $_GET['name'] ?>_table tr").css("border-bottom-style", "solid");
                                   $("#<?= $_GET['name'] ?>_table tr").css("border-bottom-color", tableBordersColor);
                                   $("#<?= $_GET['name'] ?>_table tr td").css("border-bottom-width", "1px");
                                   $("#<?= $_GET['name'] ?>_table tr td").css("border-bottom-style", "solid");
                                   $("#<?= $_GET['name'] ?>_table tr td").css("border-bottom-color", tableBordersColor);
                                   break;

                               case "all":
                                   $("#<?= $_GET['name'] ?>_table").css("border-width", "1px");
                                   $("#<?= $_GET['name'] ?>_table").css("border-style", "solid");
                                   $("#<?= $_GET['name'] ?>_table").css("border-color", tableBordersColor);
                                   $("#<?= $_GET['name'] ?>_table tr").css("border-width", "1px");
                                   $("#<?= $_GET['name'] ?>_table tr").css("border-style", "solid");
                                   $("#<?= $_GET['name'] ?>_table tr").css("border-color", tableBordersColor);
                                   $("#<?= $_GET['name'] ?>_table tr td").css("border-width", "1px");
                                   $("#<?= $_GET['name'] ?>_table tr td").css("border-style", "solid");
                                   $("#<?= $_GET['name'] ?>_table tr td").css("border-color", tableBordersColor);
                                   break;

                               default:
                                   $("#<?= $_GET['name'] ?>_table").css("border", "none");
                                   $("#<?= $_GET['name'] ?>_table tr").css("border", "none");
                                   $("#<?= $_GET['name'] ?>_table tr td").css("border", "none");
                                   break;    
                           }
                           $("#<?= $_GET['name'] ?>_table tr:last").css("border-bottom", "none");
                           $("#<?= $_GET['name'] ?>_table tr:last td").css("border-bottom", "none");

                           applyThresholdCodes(series);
                           setTableHeight();
                           var widgetHeight = parseInt($("#<?= $_GET['name'] ?>_table").height() + 25);
                           createLegends(series, widgetHeight);
                           updateLastSeries(series);
                           showWidgetContent(widgetName);
                           createInfoButtons();
                        }
                     },
                     error: function (data)
                     {
                        hospitalsLoaded++;
                        
                        newRow = $("<tr></tr>");
                        newCell = $('<td colspan="6">Error retrieving data of this hospital</td>');
                        newCell.css('font-size', fontSize + "px");
                        newCell.css('color', fontColor);
                        newRow.append(newCell);
                        tableRows.push(newRow);
                        
                        series.secondAxis.labels.push("Unknown" + hospitalsLoaded);
                           
                        var dataSlot = [];
                        series.secondAxis.series.push(dataSlot);
                        
                        if(hospitalsLoaded === widgetHospitalList.length)
                        {
                            if(firstLoad === false)
                            {
                                $("#<?= $_GET['name'] ?>_table").empty();
                            }
                            
                           for(var z = 0; z < tableRows.length; z++)
                           {
                              $("#<?= $_GET['name'] ?>_table").append(tableRows[z]);
                           }
                           
                           switch(tableBorders)
                           {
                               case "no":
                                   $("#<?= $_GET['name'] ?>_table").css("border", "none");
                                   $("#<?= $_GET['name'] ?>_table tr").css("border", "none");
                                   $("#<?= $_GET['name'] ?>_table tr td").css("border", "none");
                                   break;

                               case "horizontal":
                                   $("#<?= $_GET['name'] ?>_table").css("border", "none");
                                   $("#<?= $_GET['name'] ?>_table tr").css("border", "none");
                                   $("#<?= $_GET['name'] ?>_table tr td").css("border", "none");
                                   $("#<?= $_GET['name'] ?>_table tr").css("border-bottom-width", "1px");
                                   $("#<?= $_GET['name'] ?>_table tr").css("border-bottom-style", "solid");
                                   $("#<?= $_GET['name'] ?>_table tr").css("border-bottom-color", tableBordersColor);
                                   $("#<?= $_GET['name'] ?>_table tr td").css("border-bottom-width", "1px");
                                   $("#<?= $_GET['name'] ?>_table tr td").css("border-bottom-style", "solid");
                                   $("#<?= $_GET['name'] ?>_table tr td").css("border-bottom-color", tableBordersColor);
                                   break;

                               case "all":
                                   $("#<?= $_GET['name'] ?>_table").css("border-width", "1px");
                                   $("#<?= $_GET['name'] ?>_table").css("border-style", "solid");
                                   $("#<?= $_GET['name'] ?>_table").css("border-color", tableBordersColor);
                                   $("#<?= $_GET['name'] ?>_table tr").css("border-width", "1px");
                                   $("#<?= $_GET['name'] ?>_table tr").css("border-style", "solid");
                                   $("#<?= $_GET['name'] ?>_table tr").css("border-color", tableBordersColor);
                                   $("#<?= $_GET['name'] ?>_table tr td").css("border-width", "1px");
                                   $("#<?= $_GET['name'] ?>_table tr td").css("border-style", "solid");
                                   $("#<?= $_GET['name'] ?>_table tr td").css("border-color", tableBordersColor);
                                   break;

                               default:
                                   $("#<?= $_GET['name'] ?>_table").css("border", "none");
                                   $("#<?= $_GET['name'] ?>_table tr").css("border", "none");
                                   $("#<?= $_GET['name'] ?>_table tr td").css("border", "none");
                                   break;    
                           }
                           $("#<?= $_GET['name'] ?>_table tr:last").css("border-bottom", "none");
                           $("#<?= $_GET['name'] ?>_table tr:last td").css("border-bottom", "none");

                           applyThresholdCodes(series);
                           setTableHeight();
                           var widgetHeight = parseInt($("#<?= $_GET['name'] ?>_table").height() + 25);
                           createLegends(series, widgetHeight);
                           updateLastSeries(series);
                           showWidgetContent(widgetName);
                           createInfoButtons();
                        }
      
                        console.log("Ko");
                        console.log(JSON.stringify(data));
                     }
                  });
               }
            }
            else
            {
               $.ajax({
                  url: "https://servicemap.km4city.org/WebAppGrafo/api/v1/?serviceUri=" + serviceUri + "&requestFrom=app&format=json&uid=96d8ecaedc0f2e33262b7c2abd8492d0bbd438a25bcebc392def069553a66a5b&lang=it",
                  type: "GET",
                  async: true,
                  dataType: 'json',
                  success: function (data) 
                  {
                     if((data.realtime.results === 'undefined') || (typeof data.realtime.results === 'undefined'))
                     {
                       switch(viewMode)
                       {
                          case "singleSummary":
                             series = {  
                                "firstAxis":{  
                                   "desc":"Priority",
                                   "labels":[  
                                      "Red code",
                                      "Yellow code",
                                      "Green code",
                                      "Blue code",
                                      "White code"
                                   ]
                                },
                                "secondAxis":{  
                                   "desc":"Status",
                                   "labels":[  
                                      "Totals"
                                   ],
                                   "series":[]
                                }
                             };

                             var dataSlot = [];
                             series.secondAxis.series.push(dataSlot);
                             break;
                             
                           case "singleDetails":
                              series = {  
                                 "firstAxis":{  
                                    "desc":"Priority",
                                    "labels":[  
                                       "Red code",
                                       "Yellow code",
                                       "Green code",
                                       "Blue code",
                                       "White code"
                                    ]
                                 },
                                 "secondAxis":{  
                                    "desc":"Status",
                                    "labels":[  
                                       "Addressed",
                                       "Waiting",
                                       "In visit",
                                       "In observation",
                                       "Totals"
                                    ],
                                    "series":[]
                                 }
                              };
                                
                              var dataSlot = [];
                              series.secondAxis.series.push(dataSlot);  
                              break;
                        }
         
                       showWidgetContent(widgetName);
                       $("#<?= $_GET['name'] ?>_table").css("display", "none"); 
                       $("#<?= $_GET['name'] ?>_noDataAlert").css("display", "block");
                    }
                    else
                    {
                       switch(viewMode)
                       {
                          case "singleSummary":
                             series = {  
                                "firstAxis":{  
                                   "desc":"Priority",
                                   "labels":[  
                                      "Red code",
                                      "Yellow code",
                                      "Green code",
                                      "Blue code",
                                      "White code"
                                   ]
                                },
                                "secondAxis":{  
                                   "desc":"Status",
                                   "labels":[  
                                      "Totals"
                                   ],
                                   "series":[]
                                }
                             };

                             var dataSlot = [];
                             var index = parseInt(data.realtime.results.bindings.length - 1);

                             dataSlot.push(data.realtime.results.bindings[index].redCode.value);
                             dataSlot.push(data.realtime.results.bindings[index].yellowCode.value);
                             dataSlot.push(data.realtime.results.bindings[index].greenCode.value);
                             dataSlot.push(data.realtime.results.bindings[index].blueCode.value);
                             dataSlot.push(data.realtime.results.bindings[index].whiteCode.value);

                             series.secondAxis.series.push(dataSlot);

                             var colLabelBckColor, colDataBckColor = null;
                             var headerRow = $("<tr></tr>");
                             var dataRow = $("<tr></tr>");

                             //Celle labels
                             for(var i = 0; i < 5; i++)
                             {
                                switch(i)
                                {
                                   case 0:
                                      colLabelBckColor = "#ff0000";
                                      break;

                                   case 1:
                                      colLabelBckColor = "#ffff00";
                                      break;

                                   case 2:
                                      colLabelBckColor = "#66ff33";
                                      break;

                                   case 3:
                                      colLabelBckColor = "#66ccff";
                                      break;

                                   case 4:
                                      colLabelBckColor = "#ffffff";
                                      break;   
                                }

                                newCell = $("<td><span>" + series.firstAxis.labels[i] + "</span></td>");
                                newCell.css("font-size", colsLabelsFontSize + "px");
                                newCell.css("font-weight", "bold");
                                newCell.css("color", colsLabelsFontColor);
                                newCell.css("background-color", colLabelBckColor);
                                headerRow.append(newCell);

                                newCell = $("<td>" + dataSlot[i] + "</td>");
                                newCell.css('font-size', fontSize + "px");
                                newCell.css('color', fontColor);
                                dataRow.append(newCell);
                             }
                             
                            if(firstLoad === false)
                            {
                                $("#<?= $_GET['name'] ?>_table").empty();
                            }

                             $("#<?= $_GET['name'] ?>_table").append(headerRow);
                             $("#<?= $_GET['name'] ?>_table").append(dataRow);  
                             break;

                          case "singleDetails":
                              series = {  
                                 "firstAxis":{  
                                    "desc":"Priority",
                                    "labels":[  
                                       "Red code",
                                       "Yellow code",
                                       "Green code",
                                       "Blue code",
                                       "White code"
                                    ]
                                 },
                                 "secondAxis":{  
                                    "desc":"Status",
                                    "labels":[],
                                    "series":[]
                                 }
                              };

                             var dataSlot = null;
                             
                             for(var i = 0; i < data.realtime.results.bindings.length; i++)
                             {
                                 if(data.realtime.results.bindings[i].state.value.indexOf("estinazione") > 0)
                                 {
                                    series.secondAxis.labels.push("Addressed");
                                 }
                                 
                                 if(data.realtime.results.bindings[i].state.value.indexOf("ttesa") > 0)
                                 {
                                    series.secondAxis.labels.push("Waiting");
                                 }
                                 
                                 if(data.realtime.results.bindings[i].state.value.indexOf("isita") > 0)
                                 {
                                    series.secondAxis.labels.push("In visit");
                                 }
                                
                                 if(data.realtime.results.bindings[i].state.value.indexOf("emporanea") > 0)
                                 {
                                    series.secondAxis.labels.push("In observation");
                                 }
                                 
                                 if(data.realtime.results.bindings[i].state.value.indexOf("tali") > 0)
                                 {
                                    series.secondAxis.labels.push("Totals");
                                 }
      
                                dataSlot = [];
                                dataSlot.push(data.realtime.results.bindings[i].redCode.value);
                                dataSlot.push(data.realtime.results.bindings[i].yellowCode.value);
                                dataSlot.push(data.realtime.results.bindings[i].greenCode.value);
                                dataSlot.push(data.realtime.results.bindings[i].blueCode.value);
                                dataSlot.push(data.realtime.results.bindings[i].whiteCode.value);

                                series.secondAxis.series.push(dataSlot);
                             }

                             colsQt = parseInt(parseInt(series.firstAxis.labels.length) + 1);
                             rowsQt = parseInt(parseInt(series.secondAxis.labels.length) + 1);
                             
                            if(firstLoad === false)
                            {
                                $("#<?= $_GET['name'] ?>_table").empty();
                            }
                             
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
                                                 newCell = $("<td>" + series.firstAxis.desc  + "\\" + series.secondAxis.desc + "</td>");
                                                 newCell.css("font-size", tableFirstCellFontSize + "px");
                                                 newCell.css("color", tableFirstCellFontColor);
                                             }
                                             else
                                             {
                                                 newCell = $("<td></td>");
                                             }

                                             newCell.css("background-color", "transparent");
                                         }
                                         else
                                         {
                                             //Celle labels
                                             k = parseInt(parseInt(j) - 1);
                                             var colLabelBckColor = null;
                                             switch(k)
                                             {
                                                case 0:
                                                   colLabelBckColor = "#ff0000";
                                                   break;

                                                case 1:
                                                   colLabelBckColor = "#ffff00";
                                                   break;

                                                case 2:
                                                   colLabelBckColor = "#66ff33";
                                                   break;

                                                case 3:
                                                   colLabelBckColor = "#66ccff";
                                                   break;

                                                case 4:
                                                   colLabelBckColor = "#ffffff";
                                                   break;   
                                             }

                                             newCell = $("<td><span>" + series.firstAxis.labels[k] + "</span></td>");
                                             newCell.css("font-size", colsLabelsFontSize + "px");
                                             newCell.css("font-weight", "bold");
                                             newCell.css("color", colsLabelsFontColor);
                                             newCell.css("background-color", colLabelBckColor);
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
                                             newCell = $("<td>" + series.secondAxis.labels[z] + "</td>");
                                             newCell.css("font-size", rowsLabelsFontSize + "px");
                                             newCell.css("font-weight", "bold");
                                             newCell.css("color", rowsLabelsFontColor);
                                             newCell.css("background-color", rowsLabelsBckColor);
                                         }
                                         else
                                         {
                                             //Celle dati
                                             newCell = $("<td>" + series.secondAxis.series[z][k] + "</td>");
                                             newCell.css('font-size', fontSize + "px");
                                             newCell.css('color', fontColor);
                                             if(i === (rowsQt - 1))
                                             {
                                                newCell.css('font-weight', 'bold');
                                                switch(j)
                                                {
                                                   case 1:
                                                      newCell.css('background-color', '#ffb3b3');
                                                      break;

                                                   case 2:
                                                      newCell.css('background-color', '#ffff99');
                                                      break;

                                                   case 3:
                                                      newCell.css('background-color', '#d9ffcc');
                                                      break;

                                                   case 4:
                                                      newCell.css('background-color', '#cceeff');
                                                      break;

                                                   case 5:
                                                      newCell.css('background-color', 'white');
                                                      break;   
                                                }
                                             }
                                         }
                                         newRow.append(newCell);
                                     }
                                 }
                                 $("#<?= $_GET['name'] ?>_table").append(newRow);      
                             }
                             break;
                       }
                       
                       showWidgetContent(widgetName);

                       switch(tableBorders)
                       {
                           case "no":
                               $("#<?= $_GET['name'] ?>_table").css("border", "none");
                               $("#<?= $_GET['name'] ?>_table tr").css("border", "none");
                               $("#<?= $_GET['name'] ?>_table tr td").css("border", "none");
                               break;

                           case "horizontal":
                               $("#<?= $_GET['name'] ?>_table").css("border", "none");
                               $("#<?= $_GET['name'] ?>_table tr").css("border", "none");
                               $("#<?= $_GET['name'] ?>_table tr td").css("border", "none");
                               $("#<?= $_GET['name'] ?>_table tr").css("border-bottom-width", "1px");
                               $("#<?= $_GET['name'] ?>_table tr").css("border-bottom-style", "solid");
                               $("#<?= $_GET['name'] ?>_table tr").css("border-bottom-color", tableBordersColor);
                               $("#<?= $_GET['name'] ?>_table tr td").css("border-bottom-width", "1px");
                               $("#<?= $_GET['name'] ?>_table tr td").css("border-bottom-style", "solid");
                               $("#<?= $_GET['name'] ?>_table tr td").css("border-bottom-color", tableBordersColor);
                               break;

                           case "all":
                               $("#<?= $_GET['name'] ?>_table").css("border-width", "1px");
                               $("#<?= $_GET['name'] ?>_table").css("border-style", "solid");
                               $("#<?= $_GET['name'] ?>_table").css("border-color", tableBordersColor);
                               $("#<?= $_GET['name'] ?>_table tr").css("border-width", "1px");
                               $("#<?= $_GET['name'] ?>_table tr").css("border-style", "solid");
                               $("#<?= $_GET['name'] ?>_table tr").css("border-color", tableBordersColor);
                               $("#<?= $_GET['name'] ?>_table tr td").css("border-width", "1px");
                               $("#<?= $_GET['name'] ?>_table tr td").css("border-style", "solid");
                               $("#<?= $_GET['name'] ?>_table tr td").css("border-color", tableBordersColor);
                               break;

                           default:
                               $("#<?= $_GET['name'] ?>_table").css("border", "none");
                               $("#<?= $_GET['name'] ?>_table tr").css("border", "none");
                               $("#<?= $_GET['name'] ?>_table tr td").css("border", "none");
                               break;    
                       }
                       $("#<?= $_GET['name'] ?>_table tr:last").css("border-bottom", "none");
                       $("#<?= $_GET['name'] ?>_table tr:last td").css("border-bottom", "none");

                       applyThresholdCodes(series);
                       setTableHeight();
                       var widgetHeight = parseInt($("#<?= $_GET['name'] ?>_table").height() + 25);
                       createLegends(series, widgetHeight);
                       createInfoButtons();
                    }
                    updateLastSeries(series);
                  },
                  error: function (data)
                  {
                       switch(viewMode)
                       {
                          case "singleSummary":
                             series = {  
                                "firstAxis":{  
                                   "desc":"Priority",
                                   "labels":[  
                                      "Red code",
                                      "Yellow code",
                                      "Green code",
                                      "Blue code",
                                      "White code"
                                   ]
                                },
                                "secondAxis":{  
                                   "desc":"Status",
                                   "labels":[  
                                      "Totals"
                                   ],
                                   "series":[]
                                }
                             };

                             var dataSlot = [];
                             series.secondAxis.series.push(dataSlot);
                             break;
                             
                           case "singleDetails":
                              series = {  
                                 "firstAxis":{  
                                    "desc":"Priority",
                                    "labels":[  
                                       "Red code",
                                       "Yellow code",
                                       "Green code",
                                       "Blue code",
                                       "White code"
                                    ]
                                 },
                                 "secondAxis":{  
                                    "desc":"Status",
                                    "labels":[  
                                       "Addressed",
                                       "Waiting",
                                       "In visit",
                                       "In observation",
                                       "Totals"
                                    ],
                                    "series":[]
                                 }
                              };
                                
                              var dataSlot = [];
                              series.secondAxis.series.push(dataSlot);  
                              break;
                        }
      
                    showWidgetContent(widgetName);
                    $("#<?= $_GET['name'] ?>_table").css("display", "none"); 
                    $("#<?= $_GET['name'] ?>_noDataAlert").css("display", "block");
                    
                    updateLastSeries(series);
                    
                    console.log(JSON.stringify(data));
                  }
               });
            } 
        }
        
        /*Restituisce il JSON delle soglie se presente, altrimenti NULL*/
        function getThresholdsJson()
        {
            var thresholdsJson = jQuery.parseJSON(widgetProperties.param.parameters);
            return thresholdsJson;
        }
        
        /*Restituisce il JSON delle info se presente, altrimenti NULL*/
        function getInfoJson()
        {
            var infoJson = jQuery.parseJSON(widgetProperties.param.infoJson);
            return infoJson;
        }
        
        //Funzione di colorazione delle celle in base alle eventuali soglie stabilite
        function applyThresholdCodes(series2)
        {
            var thresholdsJson = getThresholdsJson();
            var target = null;
            
            if(thresholdsJson !== null)
            {
                target = thresholdsJson.thresholdObject.target;
                var fields, thrFields = null;
                
                switch(viewMode)
                {
                   case "singleSummary":
                        var tableLabels = new Array();
                        $('#<?= $_GET['name'] ?>_table tr').each(function (i, row) 
                        {
                            var row = $(row);
                            var cells = $(this).find('td');
                            var cellValue, cellLabel, thrSeries, min, max = null;
                            if(i === 0)
                            {
                               //Labels sulle colonne
                               cells.each(function (k)
                               {
                                   tableLabels.push(cells.eq(k).find('span').html());
                               });
                            }
                            else
                            {
                              cells.each(function (j)
                              {
                                 cellValue = parseFloat(cells.eq(j).html());
                                 fields = thresholdsJson.thresholdObject.firstAxis.fields;
                                 
                                 var mainColor, softColor = null;

                                 if(fields[parseInt(j)].fieldName === tableLabels[j])
                                 {
                                     thrSeries = fields[parseInt(j)].thrSeries;
                                     if(thrSeries.length > 0)
                                     {
                                         for(var a = 0; a < thrSeries.length; a++)
                                         {
                                             min = parseInt(thrSeries[a].min);
                                             max = parseInt(thrSeries[a].max);
                                             color = thrSeries[a].color;
                                             if((cellValue >= min) && (cellValue < max))
                                             {
                                                switch(j)
                                                {
                                                   case 0:
                                                      $('#<?= $_GET['name'] ?>_table tr').eq(0).find("td").eq(0).addClass("alarmFirstAidLblActiveRed");
                                                      cells.eq(j).addClass("alarmFirstAidCellActiveRed");
                                                      break;
                                                      
                                                   case 1:
                                                      $('#<?= $_GET['name'] ?>_table tr').eq(0).find("td").eq(1).addClass("alarmFirstAidLblActiveYellow");
                                                      cells.eq(j).addClass("alarmFirstAidCellActiveYellow");
                                                      break;   
                                                      
                                                   case 2:
                                                      $('#<?= $_GET['name'] ?>_table tr').eq(0).find("td").eq(2).addClass("alarmFirstAidLblActiveGreen");
                                                      cells.eq(j).addClass("alarmFirstAidCellActiveGreen");
                                                      break;
                                                      
                                                   case 3:
                                                      $('#<?= $_GET['name'] ?>_table tr').eq(0).find("td").eq(3).addClass("alarmFirstAidLblActiveBlue");
                                                      cells.eq(j).addClass("alarmFirstAidCellActiveBlue");
                                                      break;
                                                      
                                                   case 4:
                                                      $('#<?= $_GET['name'] ?>_table tr').eq(0).find("td").eq(4).addClass("alarmFirstAidLblActiveWhite");
                                                      cells.eq(j).addClass("alarmFirstAidCellActiveWhite");
                                                      break;   
                                                }
                                             }
                                         }
                                     }
                                 }
                              });
                           }        
                        });
                      break;
                      
                   case "singleDetails":
                      var tableLabels = new Array();
                      var rowsNumber = $('#<?= $_GET['name'] ?>_table tr').length;
                      var z = null;
                      
                        $('#<?= $_GET['name'] ?>_table tr').each(function (i, row) 
                        {
                            var row = $(row);
                            var cells = $(this).find('td');
                            var cellValue, cellLabel, thrSeries, min, max = null;
                            if(i === 0)
                            {
                               //Labels sulle colonne
                               cells.each(function (k)
                               {
                                   if(k > 0)
                                   {
                                      tableLabels.push(cells.eq(k).find('span').html());
                                   }
                               });
                            }
                            else
                            {
                              if(i === (rowsNumber - 1))
                              {
                                 cells.each(function (j)
                                 {
                                    if(j > 0)
                                    {
                                       z = j - 1;
                                       cellValue = parseFloat(cells.eq(j).html());
                                       
                                       fields = thresholdsJson.thresholdObject.firstAxis.fields;
                                       
                                       if(thresholdsJson.thresholdObject.firstAxis.fields[z].fieldName === tableLabels[z])
                                       {
                                          thrSeries = fields[z].thrSeries;
                                          
                                           if(thrSeries.length > 0)
                                           {
                                                for(var a = 0; a < thrSeries.length; a++)
                                                {
                                                   min = parseInt(thrSeries[a].min);
                                                   max = parseInt(thrSeries[a].max);
                                                   color = thrSeries[a].color;
                                                   if((cellValue >= min) && (cellValue < max))
                                                   {
                                                      switch(z)
                                                      {
                                                         case 0:
                                                            $('#<?= $_GET['name'] ?>_table tr').eq(0).find("td").eq(1).addClass("alarmFirstAidLblActiveRed");
                                                            cells.eq(j).addClass("alarmFirstAidCellActiveRed");
                                                            break;

                                                         case 1:
                                                            $('#<?= $_GET['name'] ?>_table tr').eq(0).find("td").eq(2).addClass("alarmFirstAidLblActiveYellow");
                                                            cells.eq(j).addClass("alarmFirstAidCellActiveYellow");
                                                            break;   

                                                         case 2:
                                                            $('#<?= $_GET['name'] ?>_table tr').eq(0).find("td").eq(3).addClass("alarmFirstAidLblActiveGreen");
                                                            cells.eq(j).addClass("alarmFirstAidCellActiveGreen");
                                                            break;

                                                         case 3:
                                                            $('#<?= $_GET['name'] ?>_table tr').eq(0).find("td").eq(4).addClass("alarmFirstAidLblActiveBlue");
                                                            cells.eq(j).addClass("alarmFirstAidCellActiveBlue");
                                                            break;

                                                         case 4:
                                                            $('#<?= $_GET['name'] ?>_table tr').eq(0).find("td").eq(5).addClass("alarmFirstAidLblActiveWhite");
                                                            cells.eq(j).addClass("alarmFirstAidCellActiveWhite");
                                                            break;   
                                                      }
                                                   }
                                               }
                                           }
                                       }
                                    }   
                                 });   
                              }
                           }        
                        });
                      break;
                  }
               }
        }
        
        function applyThresholdCodesOld(series2)
        {
            var thresholdsJson = getThresholdsJson();
            var target = null;
            
            if(thresholdsJson !== null)
            {
                target = thresholdsJson.thresholdObject.target;
                var fields = null;
                var thrFields = null;
                
                if(target === series2.firstAxis.desc)
                {
                    //Caso in cui le soglie sono definite sulle colonne
                    var tableLabels = new Array();
                    $('#<?= $_GET['name'] ?>_table tr').each(function (i, row) {
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
                                 switch(viewMode)
                                 {
                                    case "singleSummary":
                                       tableLabels.push(cells.eq(k).find('span').html());
                                       break;
                                       
                                    case "singleDetails":
                                       if(k !== 0)
                                       {
                                           tableLabels.push(cells.eq(k).find('span').html());
                                       }
                                       else
                                       {
                                           tableLabels.push("Pippo");
                                       }
                                       break;   
                                 }
                            });
                        }
                        else
                        {
                            switch(viewMode)
                            {
                              case "singleSummary":
                                 cells.each(function (j)
                                 {
                                    cellValue = parseFloat(cells.eq(j).html());
                                    fields = thresholdsJson.thresholdObject.firstAxis.fields;
                                    
                                    if(fields[parseInt(j)].fieldName === tableLabels[j])
                                    {
                                        thrSeries = fields[parseInt(j)].thrSeries;
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
                                 });
                                 break;
                                 
                              case "singleDetails":
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
                                 break;  
                           }        
                        }
                    });    
                }
                else if(target === series2.secondAxis.desc)
                {
                    var tableLabels = new Array();
                    var index = null;
                    
                    $('#<?= $_GET['name'] ?>_table tr').each(function (i, row) {
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
            var thresholdsJson = getThresholdsJson();
            var target = null;
            
            if(thresholdsJson !== null)
            {
                var thresholdObject = thresholdsJson.thresholdObject;
                target = thresholdObject.target;
                var thresholdObject = thresholdsJson.thresholdObject;
                var series2 = seriesString2;
                var dropdownLegend, dropDownElement, label, tableCell = null;
                var rowsLabelsFontSize = styleParameters.rowsLabelsFontSize;
                var rowsLabelsFontColor = styleParameters.rowsLabelsFontColor;
                var colsLabelsFontSize = styleParameters.colsLabelsFontSize;
                var colsLabelsFontColor = styleParameters.colsLabelsFontColor;
                var k, labelCellWidth, legendWidth, legendMargin, colsLabels = null;
                
                if(target === series2.firstAxis.desc)
                {
                    colsLabels = thresholdObject.firstAxis.fields.length;
            
                    switch(viewMode)
                    {
                       case "singleSummary":
                          $('#<?= $_GET['name'] ?>_table tr').first().find('td').each(function (i) 
                           {
                                   tableCell = $(this);
                                   labelCellWidth = tableCell.width();
                                   var base = i / colsLabels;
                                   var quadrato = (Math.pow(base, 3)).toFixed(2);
                                   legendMargin = quadrato*100;

                                   if(thresholdObject.firstAxis.fields[i].thrSeries.length > 0)
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

                                       thresholdObject.firstAxis.fields[i].thrSeries.forEach(function(range) 
                                       {
                                           dropDownElement = $('<li><a href="#">Alarm range: ' + range.min + ' <i class="fa fa-arrows-h"></i> ' + range.max + '</a></li>');
                                           dropDownElement.css("font", "bold 10px Verdana");
                                           dropDownElement.find("i").css("font-size", "12px");
                                           dropdownLegend.find("ul").append(dropDownElement);
                                       });
                                       dropdownLegend.find("ul").css("left", "-" + legendMargin + "%");
                                       $(this).html(dropdownLegend);
                                   }
                           });
                          break;
                          
                       case "singleDetails":
                          $('#<?= $_GET['name'] ?>_table tr').first().find('td').each(function (i) 
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
                                           dropDownElement = $('<li><a href="#">Alarm range: ' + range.min + ' <i class="fa fa-arrows-h"></i> ' + range.max + '</a></li>');
                                           dropDownElement.css("font", "bold 10px Verdana");
                                           dropDownElement.find("i").css("font-size", "12px");
                                           dropdownLegend.find("ul").append(dropDownElement);
                                       });
                                       dropdownLegend.find("ul").css("left", "-" + legendMargin + "%");
                                       $(this).html(dropdownLegend);
                                   }
                               }
                           });
                          break;  
                    }
                }
                else
                {
                    //LEGENDA SOGLIE SUL SECONDO ASSE
                    var firstRowH, firstRowTopBorder, firstRowBottomBorder, rowH, rowTopBorder, rowBottomBorder, labelHeight = null;
                    
                    $('#<?= $_GET['name'] ?>_table tr').each(function (i) 
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
        
        function createInfoButtons()
        {
            var infoJson = getInfoJson();
            var colsLabelsFontSize = styleParameters.colsLabelsFontSize;
            var colsLabelsFontColor = styleParameters.colsLabelsFontColor;
            var rowsLabelsFontSize = styleParameters.rowsLabelsFontSize;
            var rowsLabelsFontColor = styleParameters.rowsLabelsFontColor;
            var label, id, singleInfo, infoIcon, cell, cellContent, newCellContent = null;
            
            if(infoJson !== null)
            {
                //Aggiunta tasti alle labels sulle colonne
                $('#<?= $_GET['name'] ?>_table tr').first().find('td').each(function (i) 
                {
                    if((viewMode === 'singleSummary')||((viewMode !== 'singleSummary')&&(i > 0)))
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
                if(viewMode !== 'singleSummary')
                {
                  $('#<?= $_GET['name'] ?>_table tr').each(function (i) 
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

                              if(singleInfo !== '')
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

                              if((singleInfo !== ''))
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
        }
        
        function showModalFieldsInfoFirstAxis()
        {
            var infoJson = getInfoJson();
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
            var infoJson = getInfoJson();
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
        
        setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight);
        
        if(firstLoad !== false)
        {
            setupLoadingPanel(widgetName, widgetContentColor, firstLoad);
        }
        else
        {
            showWidgetContent(widgetName);
        }
        
        addLink(widgetName, url, linkElement, divContainer);
        $("#<?= $_GET['name'] ?>_titleDiv").html("<?= preg_replace($titlePatterns, $replacements, $title) ?>");
        widgetProperties = getWidgetProperties(widgetName);
        
        if(widgetProperties !== null)
        {
            //Inizio codice ad hoc basato sulle proprietà del widget
            manageInfoButtonVisibility(widgetProperties.param.infoMessage_w, $('#<?= $_GET['name'] ?>_header'));
            var parametersObj = JSON.parse(widgetProperties.param.parameters);
            serviceUri = widgetProperties.param.serviceUri;
            viewMode = widgetProperties.param.viewMode;
            var styleParametersString = widgetProperties.param.styleParameters;
            styleParameters = jQuery.parseJSON(styleParametersString);
            
            //Fine codice ad hoc basato sulle proprietà del widget
            populateTable(serviceUri);       
        }
        else
        {
            console.log("Errore in caricamento proprietà widget");
        }
        startCountdown(widgetName, timeToReload, <?= $_GET['name'] ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef);
        //Fine del codice core del widget
    });
</script>

<div class="widget" id="<?= $_GET['name'] ?>_div">
    <div class='ui-widget-content'>
        <div id='<?= $_GET['name'] ?>_header' class="widgetHeader">
            <div id="<?= $_GET['name'] ?>_infoButtonDiv" class="infoButtonContainer">
               <a id ="info_modal" href="#" class="info_source"><i id="source_<?= $_GET['name'] ?>" class="source_button fa fa-info-circle" style="font-size: 22px"></i></a>
            </div>
            <div id="<?= $_GET['name'] ?>_mapButtonDiv" class="mapButtonContainer">
               <i id="<?= $_GET['name'] ?>_mapButton" class="fa fa-map-marker mapButton" style="font-size: 22px"></i>
            </div>
            <div id="<?= $_GET['name'] ?>_titleDiv" class="titleDiv"></div>
            <div id="<?= $_GET['name'] ?>_buttonsDiv" class="buttonsContainer">
                <div class="singleBtnContainer"><a class="icon-cfg-widget" href="#"><span class="glyphicon glyphicon-cog glyphicon-modify-widget" aria-hidden="true"></span></a></div>
                <div class="singleBtnContainer"><a class="icon-remove-widget" href="#"><span class="glyphicon glyphicon-remove glyphicon-modify-widget" aria-hidden="true"></span></a></div>
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
            <!--<p id="<?= $_GET['name'] ?>_noDataAlert" style='text-align: center; font-size: 18px; display:none'>Nessun dato disponibile</p>-->
            <div id="<?= $_GET['name'] ?>_noDataAlert" class="noDataAlert">
                <div id="<?= $_GET['name'] ?>_noDataAlertText" class="noDataAlertText">
                    No data available
                </div>
                <div id="<?= $_GET['name'] ?>_noDataAlertIcon" class="noDataAlertIcon">
                    <i class="fa fa-times"></i>
                </div>
           </div>
            <table id="<?= $_GET['name'] ?>_table" class="psTable">
            </table>
        </div>
    </div>	
</div> 