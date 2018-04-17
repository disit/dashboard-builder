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
        var linkElement = $('#<?= $_REQUEST['name_w'] ?>_link_w');
        var fontSize = "<?= $_REQUEST['fontSize'] ?>";
        var fontColor = "<?= $_REQUEST['fontColor'] ?>";
        var timeToReload = <?= $_REQUEST['frequency_w'] ?>;
        var widgetProperties, infoJson, styleParameters, showHeader, orientation, widgetParameters, countdownRef, serviceUri, city,
            updateDateTimeInterval, language, todayDescAndIcon, otherDaysQt, otherDaysCellWidth, otherDayCell, otherDayDateContainer, 
            otherDayIconContainer, otherDayTempContainer, otherDayDate, otherDayDescAndIcon, todayDim,
            dateContainerSize, otherDaysCellHeight, otherDayDescContainer, timeToClearScroll, backgroundMode, sizeRows, iconSet, sizeColumns = null;
        var hasTimer = "<?= $_REQUEST['hasTimer'] ?>";
        var url = '<?= $_REQUEST['link_w'] ?>';
        var embedWidget = '<?= $_REQUEST['embedWidget'] ?>';
        var embedWidgetPolicy = '<?= $_REQUEST['embedWidgetPolicy'] ?>';	
        var headerHeight = 25;
        var showTitle = '<?= $_REQUEST['showTitle'] ?>';
        
        var monthsNames = [
            {
               "italian": "Gen",
               "english": "Jan",
               "italianExt": "Gennaio",
               "englishExt": "January"
            },
            {
               "italian": "Feb",
               "english": "Feb",
               "italianExt": "Febbraio",
               "englishExt": "February"
            },
            {
               "italian": "Mar",
               "english": "Mar",
               "italianExt": "Marzo",
               "englishExt": "March"
            },
            {
               "italian": "Apr",
               "english": "Apr",
               "italianExt": "Aprile",
               "englishExt": "April"
            },
            {
               "italian": "Mag",
               "english": "May",
               "italianExt": "Maggio",
               "englishExt": "May"
            },
            {
               "italian": "Giu",
               "english": "Jun",
               "italianExt": "Giugno",
               "englishExt": "June"
            },
            {
               "italian": "Lug",
               "english": "Jul",
               "italianExt": "Luglio",
               "englishExt": "July"
            },
            {
               "italian": "Ago",
               "english": "Ago",
               "italianExt": "Agosto",
               "englishExt": "August"
            },
            {
               "italian": "Set",
               "english": "Set",
               "italianExt": "Settembre",
               "englishExt": "September"
            },
            {
               "italian": "Ott",
               "english": "Oct",
               "italianExt": "Ottobre",
               "englishExt": "October"
            },
            {
               "italian": "Nov",
               "english": "Nov",
               "italianExt": "Novembre",
               "englishExt": "November"
            },
            {
               "italian": "Dic",
               "english": "Dec",
               "italianExt": "Dicembre",
               "englishExt": "December"
            }
        ];
        
        var daysOfWeek = [
            {
                "italian": "Dom",
                "english": "Sun",
                "italianExt": "Domenica",
                "englishExt": "Sunday"
            },
            {
                "italian": "Lun",
                "english": "Mon",
                "italianExt": "Lunedì",
                "englishExt": "Monday"
            },
            {
                "italian": "Mar",
                "english": "Tue",
                "italianExt": "Martedì",
                "englishExt": "Tuesday"
            },
            {
                "italian": "Mer",
                "english": "Wed",
                "italianExt": "Mercoledì",
                "englishExt": "Wednesday"
            },
            {
                "italian": "Gio",
                "english": "Thu",
                "italianExt": "Giovedì",
                "englishExt": "Thursday"
            },
            {
                "italian": "Ven",
                "english": "Fri",
                "italianExt": "Venerdì",
                "englishExt": "Friday"
            },
            {
                "italian": "Sab",
                "english": "Sat",
                "italianExt": "Sabato",
                "englishExt": "Saturday"
            }
        ];
        
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
        function setAutoBackground(originalDesc)
        {
            //OK
            if(originalDesc.includes("sereno"))
            {
                return "../img/meteoIcons/Backgrounds/sereno.jpg";
            }
            
            //OK
            if(originalDesc.includes("poco nuvoloso"))
            {
                return "../img/meteoIcons/Backgrounds/poco-nuvoloso.jpg";
            }
            
            //OK
            if(originalDesc.includes("velato"))
            {
                return "../img/meteoIcons/Backgrounds/foschia.jpg";
            }
            
            //OK
            if(originalDesc.includes("pioggia debole e schiarite"))
            {
                return "../img/meteoIcons/Backgrounds/pioggia-debole-e-schiarite.jpg";
            }
            //OK
            if(originalDesc.includes("nuvoloso")&&(!originalDesc.includes("poco")))
            {
                return "../img/meteoIcons/Backgrounds/nuvoloso.jpg";
            }
            
            //OK
            if(originalDesc.includes("pioggia debole")&&(!originalDesc.includes("schiarite")))
            {
                return "../img/meteoIcons/Backgrounds/pioggia-debole.jpg";
            }
            
            //OK
            if(originalDesc.includes("coperto"))
            {
                return "../img/meteoIcons/Backgrounds/nuvoloso.jpg";
            }
            
            //OK
            if(originalDesc.includes("pioggia e schiarite"))
            {
                return "../img/meteoIcons/Backgrounds/pioggia-debole-e-schiarite.jpg";
            }
            
            //OK
            if(originalDesc.includes("pioggia moderata-forte"))
            {
                return "../img/meteoIcons/Backgrounds/pioggia-moderata-forte.jpg";
            }
            
            //OK
            if(originalDesc.includes("foschia"))
            {
                return "../img/meteoIcons/Backgrounds/foschia.jpg";
            }
            
            //OK
            if(originalDesc.includes("temporale")&&(!originalDesc.includes("schiarite")))
            {
                return "../img/meteoIcons/Backgrounds/temporale.jpg";
            }
            
            //OK
            if(originalDesc.includes("neve debole e schiarite"))
            {
                return "../img/meteoIcons/Backgrounds/neve-e-schiarite.jpg";
            }
            
            //OK
            if(originalDesc.includes("temporale e schiarite"))
            {
                return "../img/meteoIcons/Backgrounds/temporale-e-schiarite.jpg";
            }
            
            //OK
            if(originalDesc.includes("neve moderata-forte"))
            {
                return "../img/meteoIcons/Backgrounds/neve-moderata-e-forte.jpg";
            }
            
            //OK
            if(originalDesc.includes("neve debole")&&(!originalDesc.includes("schiarite")))
            {
                return "../img/meteoIcons/Backgrounds/neve-e-schiarite.jpg";
            }
            
            //OK
            if(originalDesc.includes("neve e schiarite"))
            {
                return "../img/meteoIcons/Backgrounds/neve-e-schiarite.jpg";
            }
            
            //OK
            if(originalDesc.includes("neve debole"))
            {
                return "../img/meteoIcons/Backgrounds/neve-debole.jpg";
            }
            
            //OK
            if(originalDesc.includes("pioggia neve"))
            {
                return "../img/meteoIcons/Backgrounds/pioggia-e-neve.jpg";
            }
            
            //OK
            if(originalDesc.includes("nebbia"))
            {
                return "../img/meteoIcons/Backgrounds/nebbia.jpg";
            }
        }
        
        function setAutoFontSize(container)
        {
            fontSize = 120;
            var containerH = container.outerHeight();
            var containerW = container.outerWidth();
            var ourText = container.find('span');
            var textW = ourText.outerWidth();
            var textH = ourText.outerHeight();
            
            do {
                fontSize = fontSize - 1;
                ourText.css("font-size", fontSize + "px");
                textW = ourText.outerWidth();
                textH = ourText.outerHeight();
            }while((textH > containerH)||(textW > containerW));
            
            return fontSize;
        }
        
        function getDescAndIcon(originalDesc)
        {
            var descAndIcon = {
                desc: null,
                icon: null
            };
            
            if(originalDesc.includes("sereno"))
            {
                switch(iconSet)
                {
                    case 'multiColor':
                        descAndIcon.icon = "../img/meteoIcons/sereno.png";
                        break;
                        
                    case 'singleColor':
                        //descAndIcon.icon = "../img/meteoIcons/singleColor/sereno.svg";
                        descAndIcon.icon = "wi-day-sunny";
                        break;    
                }
                
                switch(language)
                {
                    case "italian":
                        descAndIcon.desc = "Sereno";
                        break;    

                    case "english": default:
                        descAndIcon.desc = "Cloudless";    
                        break;    
                }
            }
            
            if(originalDesc.includes("poco nuvoloso"))
            {
                switch(iconSet)
                {
                    case 'multiColor':
                        descAndIcon.icon = "../img/meteoIcons/poco-nuvoloso.png";
                        break;
                        
                    case 'singleColor':
                        descAndIcon.icon = "../img/meteoIcons/singleColor/nuvoloso.svg";
                        descAndIcon.icon = "wi-cloudy";
                        break;    
                }
                
                switch(language)
                {
                    case "italian":
                        descAndIcon.desc = "Poco nuvoloso";
                        break;    

                    case "english": default:
                        descAndIcon.desc = "Bit cloudy";    
                        break;       
                }
            }
            
            if(originalDesc.includes("velato"))
            {
                switch(iconSet)
                {
                    case 'multiColor':
                        descAndIcon.icon = "../img/meteoIcons/foschia.png";
                        break;
                        
                    case 'singleColor':
                        //descAndIcon.icon = "../img/meteoIcons/singleColor/foschia.png";
                        descAndIcon.icon = "wi-fog";
                        break;    
                }
                
                switch(language)
                {
                    case "italian":
                        descAndIcon.desc = "Velato";
                        break;    

                    case "english": default:
                        descAndIcon.desc = "Bleary";    
                        break;
                }
            }
            
            if(originalDesc.includes("pioggia debole e schiarite"))
            {
                switch(iconSet)
                {
                    case 'multiColor':
                        descAndIcon.icon = "../img/meteoIcons/pioggia-sole.png";
                        break;
                        
                    case 'singleColor':
                        //descAndIcon.icon = "../img/meteoIcons/singleColor/pioggia-sole.png";
                        descAndIcon.icon = "wi-rain-mix";
                        break;    
                }
                
                switch(language)
                {
                    case "italian":
                        descAndIcon.desc = "Pioggia debole e schiarite";
                        break;    

                    case "english": default:
                        descAndIcon.desc = "Light rain and sunny intervals";    
                        break;
                }
            }
            
            if(originalDesc.includes("nuvoloso")&&(!originalDesc.includes("poco")))
            {
                switch(iconSet)
                {
                    case 'multiColor':
                        descAndIcon.icon = "../img/meteoIcons/nuvoloso.png";
                        break;
                        
                    case 'singleColor':
                        //descAndIcon.icon = "../img/meteoIcons/singleColor/nuvoloso.png";
                        descAndIcon.icon = "wi-cloudy";
                        break;    
                }
                
                switch(language)
                {
                    case "italian":
                        descAndIcon.desc = "Nuvoloso";
                        break;    

                    case "english": default:
                        descAndIcon.desc = "Cloudy";    
                        break;    
                }
            }
            
            if(originalDesc.includes("pioggia debole")&&(!originalDesc.includes("schiarite")))
            {
                switch(iconSet)
                {
                    case 'multiColor':
                        descAndIcon.icon = "../img/meteoIcons/pioggia.png";
                        break;
                        
                    case 'singleColor':
                        //descAndIcon.icon = "../img/meteoIcons/singleColor/pioggia.png";
                        descAndIcon.icon = "wi-rain";
                        break;    
                }
                
                switch(language)
                {
                    case "italian":
                        descAndIcon.desc = "Pioggia debole";
                        break;    

                    case "english": default:
                        descAndIcon.desc = "Light rain";    
                        break;    
                }
            }
            
            if(originalDesc.includes("coperto"))
            {
                switch(iconSet)
                {
                    case 'multiColor':
                        descAndIcon.icon = "../img/meteoIcons/coperto.png";
                        break;
                        
                    case 'singleColor':
                        //descAndIcon.icon = "../img/meteoIcons/singleColor/coperto.png";
                        descAndIcon.icon = "wi-cloudy";
                        break;    
                }
                
                switch(language)
                {
                    case "italian":
                        descAndIcon.desc = "Coperto";
                        break;    

                    case "english": default:
                        descAndIcon.desc = "Overcast";    
                        break;   
                }
            }
            
            if(originalDesc.includes("pioggia e schiarite")&&(!originalDesc.includes("debole")))
            {
                switch(iconSet)
                {
                    case 'multiColor':
                        descAndIcon.icon = "../img/meteoIcons/pioggia-sole.png";
                        break;
                        
                    case 'singleColor':
                        //descAndIcon.icon = "../img/meteoIcons/singleColor/pioggia-sole.png";
                        descAndIcon.icon = "wi-day-rain";
                        break;    
                }
                
                switch(language)
                {
                   case "italian":
                        descAndIcon.desc = "Pioggia e schiarite";
                        break;    

                    case "english": default:
                        descAndIcon.desc = "Rain and sunny intervals";    
                        break;   
                }
            }
            
            if(originalDesc.includes("pioggia moderata-forte"))
            {
                switch(iconSet)
                {
                    case 'multiColor':
                        descAndIcon.icon = "../img/meteoIcons/pioggia.png";
                        break;
                        
                    case 'singleColor':
                        //descAndIcon.icon = "../img/meteoIcons/singleColor/pioggia.png";
                        descAndIcon.icon = "wi-showers";
                        break;    
                }
                
                switch(language)
                {
                    case "italian":
                        descAndIcon.desc = "Pioggia moderata o forte";
                        break;    

                    case "english": default:
                        descAndIcon.desc = "Moderate or strong rain";    
                        break;   
                }
            }
            
            if(originalDesc.includes("foschia"))
            {
                switch(iconSet)
                {
                    case 'multiColor':
                        descAndIcon.icon = "../img/meteoIcons/foschia.png";
                        break;
                        
                    case 'singleColor':
                        //descAndIcon.icon = "../img/meteoIcons/singleColor/foschia.png";
                        descAndIcon.icon = "wi-fog";
                        break;    
                }
                
                switch(language)
                {
                    case "italian":
                        descAndIcon.desc = "Foschia";
                        break;    

                    case "english": default:
                        descAndIcon.desc = "Mist";    
                        break;   
                }
            }
            
            if(originalDesc.includes("temporale")&&(!originalDesc.includes("schiarite")))
            {
                switch(iconSet)
                {
                    case 'multiColor':
                        descAndIcon.icon = "../img/meteoIcons/temporale.png";
                        break;
                        
                    case 'singleColor':
                        //descAndIcon.icon = "../img/meteoIcons/singleColor/temporale.png";
                        descAndIcon.icon = "wi-thunderstorm";
                        break;    
                }
                
                switch(language)
                {
                    case "italian":
                        descAndIcon.desc = "Temporali";
                        break;    

                    case "english": default:
                        descAndIcon.desc = "Rainstorms";    
                        break;   
                }
            }
            
            if(originalDesc.includes("neve debole e schiarite"))
            {
                switch(iconSet)
                {
                    case 'multiColor':
                        descAndIcon.icon = "../img/meteoIcons/neve-schiarite.png";
                        break;
                        
                    case 'singleColor':
                        //descAndIcon.icon = "../img/meteoIcons/singleColor/neve-schiarite.png";
                        descAndIcon.icon = "wi-day-snow";
                        break;    
                }
                
                switch(language)
                {
                    case "italian":
                        descAndIcon.desc = "Neve debole e schiarite";
                        break;    

                    case "english": default:
                        descAndIcon.desc = "Light snow and sunny intervals";    
                        break;  
                }
            }
            
            if(originalDesc.includes("temporale e schiarite"))
            {
                switch(iconSet)
                {
                    case 'multiColor':
                        descAndIcon.icon = "../img/meteoIcons/temporale.png";
                        break;
                        
                    case 'singleColor':
                        //descAndIcon.icon = "../img/meteoIcons/singleColor/temporale.png";
                        descAndIcon.icon = "wi-day-sleet-storm";
                        break;    
                }
                
                switch(language)
                {
                    case "italian":
                        descAndIcon.desc = "Temporali e schiarite";
                        break;    

                    case "english": default:
                        descAndIcon.desc = "Rainstorms and sunny intervals";    
                        break;   
                }
            }
            
            if(originalDesc.includes("neve moderata-forte"))
            {
                switch(iconSet)
                {
                    case 'multiColor':
                        descAndIcon.icon = "../img/meteoIcons/neve.png";
                        break;
                        
                    case 'singleColor':
                        //descAndIcon.icon = "../img/meteoIcons/singleColor/neve.png";
                        descAndIcon.icon = "wi-snow";
                        break;    
                }
                
                switch(language)
                {
                    case "italian":
                        descAndIcon.desc = "Neve moderata o forte";
                        break;    

                    case "english": default:
                        descAndIcon.desc = "Moderate or strong snow";    
                        break;   
                }
            }
            
            if(originalDesc.includes("neve e schiarite")&&(!originalDesc.includes("debole")))
            {
                switch(iconSet)
                {
                    case 'multiColor':
                        descAndIcon.icon = "../img/meteoIcons/neve-schiarite.png";
                        break;
                        
                    case 'singleColor':
                        //descAndIcon.icon = "../img/meteoIcons/singleColor/neve-schiarite.png";
                        descAndIcon.icon = "wi-day-snow";
                        break;    
                }
                
                switch(language)
                {
                    case "italian":
                        descAndIcon.desc = "Neve e schiarite";
                        break;    

                    case "english": default:
                        descAndIcon.desc = "Snow and sunny intervals";    
                        break;    
                }
            }
            
            if(originalDesc.includes("neve debole")&&(!originalDesc.includes("schiarite")))
            {
                switch(iconSet)
                {
                    case 'multiColor':
                        descAndIcon.icon = "../img/meteoIcons/neve.png";
                        break;
                        
                    case 'singleColor':
                        //descAndIcon.icon = "../img/meteoIcons/singleColor/neve.png";
                        descAndIcon.icon = "wi-snow";
                        break;    
                }
                
                switch(language)
                {
                    case "italian":
                        descAndIcon.desc = "Neve debole";
                        break;    

                    case "english": default:
                        descAndIcon.desc = "Light snow";    
                        break;   
                }
            }
            
            if(originalDesc.includes("pioggia neve"))
            {
                switch(iconSet)
                {
                    case 'multiColor':
                        descAndIcon.icon = "../img/meteoIcons/pioggia-neve.png";
                        break;
                        
                    case 'singleColor':
                        //descAndIcon.icon = "../img/meteoIcons/singleColor/pioggia-neve.png";
                        descAndIcon.icon = "wi-rain-mix";
                        break;    
                }
                
                switch(language)
                {
                    case "italian":
                        descAndIcon.desc = "Pioggia e neve";
                        break;    

                    case "english": default:
                        descAndIcon.desc = "Rain and snow";    
                        break;    
                }
            }
            
            if(originalDesc.includes("nebbia"))
            {
                switch(iconSet)
                {
                    case 'multiColor':
                        descAndIcon.icon = "../img/meteoIcons/nebbia.png";
                        break;
                        
                    case 'singleColor':
                        //descAndIcon.icon = "../img/meteoIcons/singleColor/nebbia.png";
                        descAndIcon.icon = "wi-fog";
                        break;    
                }
                
                switch(language)
                {
                    case "italian":
                        descAndIcon.desc = "Nebbia";
                        break;    

                    case "english": default:
                        descAndIcon.desc = "Fog";    
                        break;   
                }
            }
            
            return descAndIcon;
        }
        
        //Restituisce il JSON delle info se presente, altrimenti NULL
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
        
        function resizeWidget()
        {
            $('#<?= $_REQUEST['name_w'] ?>_div').parents('li.gs_w').off('resizeWidgets');
            $('#<?= $_REQUEST['name_w'] ?>_div').parents('li.gs_w').on('resizeWidgets', resizeWidget);
        }
        //Fine definizioni di funzione 
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
        
        
        $.ajax({
            url: getParametersWidgetUrl,
            type: "GET",
            data: {"nomeWidget": [widgetName]},
            async: true,
            dataType: 'json',
            success: function(data) 
            {
                widgetProperties = data;
                
                if((widgetProperties !== null) && (widgetProperties !== undefined))
                {
                    styleParameters = getStyleParameters();
                    widgetParameters = widgetProperties.param.parameters;
                    sizeRows = parseInt(widgetProperties.param.size_rows);
                    sizeColumns = parseInt(widgetProperties.param.size_columns);
                    city = widgetProperties.param.municipality_w;
                    
                    $.ajax({
                        url: '<?=$serviceMapUrlPrefix?>sparql?query=select+distinct+%3Fs+%7B+%3Fs+a+km4c%3AMunicipality.%3Fs+foaf%3Aname+"' + city + '".%7D&format=json',
                        type: "GET",
                        async: true,
                        dataType: 'json',
                        success: function(cityData) 
                        {
                            if((!cityData.hasOwnProperty("ERROR"))&&(cityData.hasOwnProperty("results")))
                            {
                                if(cityData.results.hasOwnProperty("bindings"))
                                {
                                    if(cityData.results.bindings.length > 0)
                                    {
                                        if(cityData.results.bindings[0].s.value !== "")
                                        {
                                            serviceUri = cityData.results.bindings[0].s.value;
                                        }
                                    } 
                                }
                            }
                            
                            if(serviceUri !== null)
                            {
                                $.ajax({
                                    url: "../management/iframeProxy.php",
                                    data: {
                                        action: "getMeteoForecast",
                                        cityServiceUri: serviceUri
                                    },
                                    type: "GET",
                                    async: true,
                                    success: function(meteoData) 
                                    {
                                        console.log(meteoData);
                                        meteoData = JSON.parse(meteoData);
                                        orientation = styleParameters.orientation;
                                        language = styleParameters.language;
                                        todayDim = styleParameters.todayDim;
                                        backgroundMode = styleParameters.backgroundMode;
                                        iconSet = styleParameters.iconSet;
                                        
                                        if(meteoData.hasOwnProperty("ERROR"))
                                        {
                                            showWidgetContent(widgetName);
                                            $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                                        }
                                        else
                                        {
                                            if(meteoData.results.bindings.length === 0)
                                            {
                                                showWidgetContent(widgetName);
                                                $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                                            }
                                            else
                                            {   
                                                if(firstLoad !== false)
                                                {
                                                    showWidgetContent(widgetName);
                                                }

                                                $('#<?= $_REQUEST['name_w'] ?>_chartContainer').css("color", fontColor);
                                                $('#<?= $_REQUEST['name_w'] ?>_cityContainer').html('<span style="display:block;">' + '<?= $_REQUEST['municipality_w'] ?>'.charAt(0) + '<?= $_REQUEST['municipality_w'] ?>'.slice(1).toLowerCase() + '</span>');

                                                todayDescAndIcon = getDescAndIcon(meteoData.results.bindings[0].description.value);
                                                $('#<?= $_REQUEST['name_w'] ?>_descContainer').html('<span style="display:block;">' + todayDescAndIcon.desc + '</span>');

                                                if(backgroundMode === 'auto')
                                                {
                                                    $('#<?= $_REQUEST['name_w'] ?>_chartContainer').css("background", "-moz-linear-gradient(to bottom, rgba(0,0,0,0.2) 0%,rgba(0,0,0,0.2) 100%), url(" + setAutoBackground(meteoData.results.bindings[0].description.value) + ")");
                                                    $('#<?= $_REQUEST['name_w'] ?>_chartContainer').css("background", "-webkit-gradient(top, rgba(0,0,0,0.2) 0%,rgba(0,0,0,0.2) 100%), url(" + setAutoBackground(meteoData.results.bindings[0].description.value) + ")");
                                                    $('#<?= $_REQUEST['name_w'] ?>_chartContainer').css("background", "-o-gradient(top, rgba(0,0,0,0.2) 0%,rgba(0,0,0,0.19) 100%), url(" + setAutoBackground(meteoData.results.bindings[0].description.value) + ")");
                                                    $('#<?= $_REQUEST['name_w'] ?>_chartContainer').css("background", "-ms-gradient(top, rgba(0,0,0,0.2) 0%,rgba(0,0,0,0.2) 100%), url(" + setAutoBackground(meteoData.results.bindings[0].description.value) + ")");      
                                                    $('#<?= $_REQUEST['name_w'] ?>_chartContainer').css("background", "linear-gradient(to bottom, rgba(0,0,0,0.2) 0%,rgba(0,0,0,0.2) 100%), url(" + setAutoBackground(meteoData.results.bindings[0].description.value) + ")");
                                                    $('#<?= $_REQUEST['name_w'] ?>_chartContainer').css("background-size", "cover");
                                                    $('#<?= $_REQUEST['name_w'] ?>_chartContainer').css("background-repeat", "no-repeat");
                                                    $('#<?= $_REQUEST['name_w'] ?>_chartContainer').css("background-position", "center center"); 
                                                }
                                                
                                                $('#<?= $_REQUEST['name_w'] ?>_tempContainer').html('<span style="display:block;">' + meteoData.results.bindings[0].minTemp.value + "°C / " + meteoData.results.bindings[0].maxTemp.value + " °C</span>");

                                                otherDaysQt = meteoData.results.bindings.length - 1;

                                                switch(orientation)
                                                { 
                                                    case "horizontal":
                                                       $('#<?= $_REQUEST['name_w'] ?>_todayContainer').css("width", "100%");    
                                                       $('#<?= $_REQUEST['name_w'] ?>_todayContainer').css("height", todayDim + "%");
                                                       $('#<?= $_REQUEST['name_w'] ?>_todayLeftContainer').css("width", "65%");    
                                                       $('#<?= $_REQUEST['name_w'] ?>_todayLeftContainer').css("height", "100%");
                                                       $('#<?= $_REQUEST['name_w'] ?>_todayRightContainer').css("width", "35%");    
                                                       $('#<?= $_REQUEST['name_w'] ?>_todayRightContainer').css("height", "100%");
                                                       $('#<?= $_REQUEST['name_w'] ?>_dateContainer').css("width", "100%");    
                                                       $('#<?= $_REQUEST['name_w'] ?>_dateContainer').css("height", "25%");
                                                       $('#<?= $_REQUEST['name_w'] ?>_cityContainer').css("width", "100%");    
                                                       $('#<?= $_REQUEST['name_w'] ?>_cityContainer').css("height", "47.5%");
                                                       $('#<?= $_REQUEST['name_w'] ?>_descContainer').css("width", "100%");    
                                                       $('#<?= $_REQUEST['name_w'] ?>_descContainer').css("height", "27.5%");
                                                       $('#<?= $_REQUEST['name_w'] ?>_lammaContainer').css("width", "100%");    
                                                       $('#<?= $_REQUEST['name_w'] ?>_iconContainer').css("width", "100%");    
                                                       $('#<?= $_REQUEST['name_w'] ?>_tempContainer').css("width", "100%");    
                                                       
                                                       if(sizeRows > 5)
                                                       {
                                                           $('#<?= $_REQUEST['name_w'] ?>_lammaContainer').css("height", "10%");
                                                           $('#<?= $_REQUEST['name_w'] ?>_iconContainer').css("height", "60%");
                                                           $('#<?= $_REQUEST['name_w'] ?>_tempContainer').css("height", "30%");
                                                       }
                                                       else
                                                       {
                                                           $('#<?= $_REQUEST['name_w'] ?>_lammaContainer').css("height", "25%");
                                                           $('#<?= $_REQUEST['name_w'] ?>_iconContainer').css("height", "50%");
                                                           $('#<?= $_REQUEST['name_w'] ?>_tempContainer').css("height", "25%");
                                                       }
                                                       
                                                       $('#<?= $_REQUEST['name_w'] ?>_otherDaysContainer').css("width", "100%");
                                                       $('#<?= $_REQUEST['name_w'] ?>_otherDaysContainer').css("height", parseInt(100 - todayDim) + "%"); 

                                                       otherDaysCellWidth = 100 / otherDaysQt;
                                                       for(var i = 1; i <= otherDaysQt; i++)
                                                       {
                                                          if(backgroundMode === 'auto')
                                                          {
                                                              otherDayCell = $('<div class="meteoOtherDaysCellDarkerH"></div>');
                                                          }
                                                          else
                                                          {
                                                              otherDayCell = $('<div class="meteoOtherDaysCell"></div>');
                                                          } 
                                                          otherDayCell.css("width", otherDaysCellWidth + "%");
                                                          otherDayCell.css("height", "100%");
                                                          otherDayCell.css("float", "left");
                                                          //otherDayCell.css("border", "1px solid red");

                                                          otherDayDateContainer = $('<div class="otherDayDateContainer"></div>');
                                                          otherDayCell.append(otherDayDateContainer);
                                                          otherDayDateContainer.css("width", "100%");
                                                          if(sizeRows > 3)
                                                          {
                                                              otherDayDateContainer.css("height", "20%");
                                                          }
                                                          else
                                                          {
                                                              otherDayDateContainer.css("height", "25%");
                                                          }

                                                          otherDayDateContainer.css("float", "left");
                                                          //otherDayDateContainer.css("border", "1px solid red");
                                                          otherDayDate = new Date();
                                                          otherDayDate.setDate(otherDayDate.getDate() + i);

                                                          if(language === 'english')
                                                          {
                                                             otherDayDateContainer.html('<span style="display:block;">' + daysOfWeek[otherDayDate.getDay()].english + ' ' + otherDayDate.getDate() + ' ' + monthsNames[otherDayDate.getMonth()].english + '</span>');
                                                          }
                                                          else
                                                          {
                                                             if(language === 'italian')
                                                             {
                                                                otherDayDateContainer.html('<span style="display:block;">' + daysOfWeek[otherDayDate.getDay()].italian + ' ' + otherDayDate.getDate() + ' ' + monthsNames[otherDayDate.getMonth()].italian + '</span>');
                                                             } 
                                                          }

                                                          if(sizeRows > 3)
                                                          {
                                                            otherDayTempContainer = $('<div class="otherDayTempContainer"></div>');
                                                            otherDayTempContainer.css("width", "100%");
                                                            otherDayTempContainer.css("height", "15%");
                                                            otherDayTempContainer.css("float", "left");
                                                            //otherDayTempContainer.css("border", "1px solid red");
                                                            otherDayCell.append(otherDayTempContainer);
                                                            if((meteoData.results.bindings[i].minTemp.value !== "")&&(meteoData.results.bindings[i].maxTemp.value !== ""))
                                                            {
                                                                otherDayTempContainer.html('<span style="display:block;">' + meteoData.results.bindings[i].minTemp.value + '°C / ' + meteoData.results.bindings[i].maxTemp.value + '°C</span>');
                                                            }
                                                            else
                                                            {
                                                                otherDayTempContainer.html('<span style="display:block;">Temp N/A</span>');
                                                            }
                                                          }

                                                          otherDayDescAndIcon = getDescAndIcon(meteoData.results.bindings[i].description.value);
                                                          otherDayIconContainer = $('<div data-toggle="tooltip" data-placement="top" title="' + otherDayDescAndIcon.desc + '"></div>');
                                                          otherDayIconContainer.css("width", "100%");

                                                          if(sizeRows > 3)
                                                          {
                                                              otherDayIconContainer.css("height", "40%");
                                                          }
                                                          else
                                                          {
                                                              otherDayIconContainer.css("height", "50%");
                                                          }

                                                          otherDayIconContainer.css("float", "left");
                                                          //otherDayIconContainer.css("border", "1px solid red");
                                                          otherDayCell.append(otherDayIconContainer);
                                                          otherDayIconContainer.tooltip(); 

                                                          otherDayIconContainer.css("background-image", "url(" + otherDayDescAndIcon.icon + ")");
                                                          otherDayIconContainer.css("background-size", "contain");
                                                          otherDayIconContainer.css("background-repeat", "no-repeat");
                                                          otherDayIconContainer.css("background-position", "center center");

                                                          otherDayDescContainer = $('<div class="otherDayDescContainer"></div>');
                                                          otherDayDescContainer.css("width", "100%");
                                                          otherDayDescContainer.css("height", "25%");

                                                          otherDayDescContainer.css("float", "left");
                                                          //otherDayDescContainer.css("border", "1px solid red");
                                                          otherDayCell.append(otherDayDescContainer);
                                                          otherDayDescContainer.html('<span style="display:block;">' + otherDayDescAndIcon.desc + '</span>');

                                                          $('#<?= $_REQUEST['name_w'] ?>_otherDaysContainer').append(otherDayCell);
                                                          setAutoFontSize(otherDayDateContainer);
                                                          if(sizeRows > 3)
                                                          {
                                                              setAutoFontSize(otherDayTempContainer);
                                                          }
                                                       }
                                                       break;

                                                    case "vertical":
                                                       $('#<?= $_REQUEST['name_w'] ?>_todayContainer').css("width", "100%");    
                                                       $('#<?= $_REQUEST['name_w'] ?>_todayContainer').css("height", todayDim + "%");
                                                       $('#<?= $_REQUEST['name_w'] ?>_todayLeftContainer').hide();
                                                       $('#<?= $_REQUEST['name_w'] ?>_todayRightContainer').hide();
                                                       $('#<?= $_REQUEST['name_w'] ?>_todayContainer').append($('#<?= $_REQUEST['name_w'] ?>_dateContainer'));
                                                       $('#<?= $_REQUEST['name_w'] ?>_dateContainer').css("width", "100%");    
                                                       $('#<?= $_REQUEST['name_w'] ?>_dateContainer').removeClass("meteoDateContainer");
                                                       $('#<?= $_REQUEST['name_w'] ?>_dateContainer').addClass("meteoDateContainerCentered");
                                                       $('#<?= $_REQUEST['name_w'] ?>_todayContainer').append($('#<?= $_REQUEST['name_w'] ?>_cityContainer'));
                                                       $('#<?= $_REQUEST['name_w'] ?>_cityContainer').css("width", "100%"); 
                                                       $('#<?= $_REQUEST['name_w'] ?>_iconContainer').css("width", "100%");    
                                                       $('#<?= $_REQUEST['name_w'] ?>_descContainer').css("width", "100%");    
                                                       $('#<?= $_REQUEST['name_w'] ?>_tempContainer').css("width", "100%");    
                                                       $('#<?= $_REQUEST['name_w'] ?>_lammaContainer').css("width", "100%");    
                                                       if(sizeRows > 6)
                                                       {
                                                           $('#<?= $_REQUEST['name_w'] ?>_dateContainer').css("height", "12.5%");
                                                           $('#<?= $_REQUEST['name_w'] ?>_cityContainer').css("height", "17.5%");
                                                           $('#<?= $_REQUEST['name_w'] ?>_iconContainer').css("height", "32.5%");
                                                           $('#<?= $_REQUEST['name_w'] ?>_descContainer').css("height", "20%");
                                                           $('#<?= $_REQUEST['name_w'] ?>_tempContainer').css("height", "10%");
                                                           $('#<?= $_REQUEST['name_w'] ?>_lammaContainer').css("height", "7.5%");
                                                       }
                                                       else
                                                       {
                                                           $('#<?= $_REQUEST['name_w'] ?>_dateContainer').css("height", "12.5%");
                                                           $('#<?= $_REQUEST['name_w'] ?>_cityContainer').css("height", "15%");
                                                           $('#<?= $_REQUEST['name_w'] ?>_iconContainer').css("height", "30%");
                                                           $('#<?= $_REQUEST['name_w'] ?>_descContainer').css("height", "17.5%");
                                                           $('#<?= $_REQUEST['name_w'] ?>_tempContainer').css("height", "15%");
                                                           $('#<?= $_REQUEST['name_w'] ?>_lammaContainer').css("height", "10%");
                                                       }
                                                       
                                                       $('#<?= $_REQUEST['name_w'] ?>_cityContainer').removeClass("meteoCityContainer");
                                                       $('#<?= $_REQUEST['name_w'] ?>_cityContainer').addClass("meteoCityContainerCentered");
                                                       $('#<?= $_REQUEST['name_w'] ?>_todayContainer').append($('#<?= $_REQUEST['name_w'] ?>_iconContainer'));
                                                       $('#<?= $_REQUEST['name_w'] ?>_todayContainer').append($('#<?= $_REQUEST['name_w'] ?>_descContainer'));
                                                       $('#<?= $_REQUEST['name_w'] ?>_descContainer').removeClass("meteoDescContainer");
                                                       $('#<?= $_REQUEST['name_w'] ?>_descContainer').addClass("meteoDescContainerCentered");
                                                       $('#<?= $_REQUEST['name_w'] ?>_todayContainer').append($('#<?= $_REQUEST['name_w'] ?>_tempContainer'));
                                                       $('#<?= $_REQUEST['name_w'] ?>_tempContainer').removeClass("meteoTempContainer");
                                                       $('#<?= $_REQUEST['name_w'] ?>_tempContainer').addClass("meteoTempContainerCentered");
                                                       $('#<?= $_REQUEST['name_w'] ?>_todayContainer').append($('#<?= $_REQUEST['name_w'] ?>_lammaContainer'));
                                                       $('#<?= $_REQUEST['name_w'] ?>_otherDaysContainer').css("width", "100%");
                                                       $('#<?= $_REQUEST['name_w'] ?>_otherDaysContainer').css("height", parseInt(100 - todayDim) + "%");

                                                       otherDaysCellHeight = 100 / otherDaysQt;
                                                       for(var i = 1; i <= otherDaysQt; i++)
                                                       {
                                                          if(backgroundMode === 'auto')
                                                          {
                                                              otherDayCell = $('<div class="meteoOtherDaysCellDarkerV"></div>');
                                                          }
                                                          else
                                                          {
                                                              otherDayCell = $('<div class="meteoOtherDaysCell"></div>');
                                                          }

                                                          otherDayCell.css("width", "100%");
                                                          otherDayCell.css("height", otherDaysCellHeight + "%");
                                                          otherDayCell.css("float", "left");
                                                          //otherDayCell.css("border", "1px solid red");

                                                          otherDayDateContainer = $('<div class="otherDayDateContainer"></div>');
                                                          otherDayCell.append(otherDayDateContainer);
                                                          otherDayDateContainer.css("width", "40%");
                                                          otherDayDateContainer.css("height", "60%");
                                                          otherDayDateContainer.css("float", "left");
                                                          //otherDayDateContainer.css("border", "1px solid red");
                                                          otherDayDate = new Date();
                                                          otherDayDate.setDate(otherDayDate.getDate() + i);

                                                          if(language === 'english')
                                                          {
                                                             otherDayDateContainer.html('<span style="display:block;">' + daysOfWeek[otherDayDate.getDay()].english + ' ' + otherDayDate.getDate() + ' ' + monthsNames[otherDayDate.getMonth()].english + '</span>');
                                                          }
                                                          else
                                                          {
                                                             if(language === 'italian')
                                                             {
                                                                otherDayDateContainer.html('<span style="display:block;">' + daysOfWeek[otherDayDate.getDay()].italian + ' ' + otherDayDate.getDate() + ' ' + monthsNames[otherDayDate.getMonth()].italian + '</span>');
                                                             } 
                                                          }

                                                          otherDayDescAndIcon = getDescAndIcon(meteoData.results.bindings[i].description.value);
                                                          otherDayIconContainer = $('<div data-toggle="tooltip" data-placement="top" title="' + otherDayDescAndIcon.desc + '"></div>');
                                                          otherDayIconContainer.css("width", "60%");
                                                          otherDayIconContainer.css("height", "60%");
                                                          otherDayIconContainer.css("float", "left");
                                                          //otherDayIconContainer.css("border", "1px solid red");
                                                          otherDayCell.append(otherDayIconContainer);
                                                          otherDayIconContainer.tooltip(); 

                                                          otherDayIconContainer.css("background-image", "url(" + otherDayDescAndIcon.icon + ")");
                                                          otherDayIconContainer.css("background-size", "contain");
                                                          otherDayIconContainer.css("background-repeat", "no-repeat");
                                                          otherDayIconContainer.css("background-position", "center center");

                                                          otherDayTempContainer = $('<div class="otherDayTempContainer"></div>');
                                                          otherDayTempContainer.css("width", "40%");
                                                          otherDayTempContainer.css("height", "40%");
                                                          otherDayTempContainer.css("float", "left");
                                                          //otherDayTempContainer.css("border", "1px solid red");
                                                          otherDayCell.append(otherDayTempContainer);
                                                          if((meteoData.results.bindings[i].minTemp.value !== "")&&(meteoData.results.bindings[i].maxTemp.value !== ""))
                                                          {
                                                              otherDayTempContainer.html('<span style="display:block;">' + meteoData.results.bindings[i].minTemp.value + '°C / ' + meteoData.results.bindings[i].maxTemp.value + '°C</span>');
                                                          }
                                                          else
                                                          {
                                                              otherDayTempContainer.html('<span style="display:block;">Temp N/A</span>');
                                                          }

                                                          otherDayDescContainer = $('<div class="otherDayDescContainer"></div>');
                                                          otherDayDescContainer.css("width", "60%");
                                                          otherDayDescContainer.css("height", "40%");
                                                          otherDayDescContainer.css("float", "left");
                                                          //otherDayDescContainer.css("border", "1px solid red");
                                                          otherDayCell.append(otherDayDescContainer);
                                                          otherDayDescContainer.html('<span style="display:block;">' + otherDayDescAndIcon.desc + '</span>');

                                                          $('#<?= $_REQUEST['name_w'] ?>_otherDaysContainer').append(otherDayCell);
                                                          setAutoFontSize(otherDayDateContainer);
                                                          setAutoFontSize(otherDayTempContainer);
                                                       }

                                                        break;
                                                        
                                                    case "verticalCompact":
                                                       var rowHeight =  100 / (otherDaysQt + 1); 
                                                       $('#<?= $_REQUEST['name_w'] ?>_todayContainer').css("width", "100%");    
                                                       $('#<?= $_REQUEST['name_w'] ?>_todayContainer').css("height", rowHeight + "%");
                                                       
                                                       if(sizeRows < 8)
                                                       {
                                                           $('#<?= $_REQUEST['name_w'] ?>_todayContainer').css("padding-top", "0.2%");
                                                           $('#<?= $_REQUEST['name_w'] ?>_todayContainer').css("padding-bottom", "0.2%");
                                                       }
                                                       else
                                                       {
                                                            if((sizeRows >= 8) && (sizeRows <= 12))
                                                            {
                                                                $('#<?= $_REQUEST['name_w'] ?>_todayContainer').css("padding-top", "0.4%");
                                                                $('#<?= $_REQUEST['name_w'] ?>_todayContainer').css("padding-bottom", "0.4%");
                                                            }
                                                            else
                                                            {
                                                                if(sizeRows > 12)
                                                                {
                                                                   $('#<?= $_REQUEST['name_w'] ?>_todayContainer').css("padding-top", "0.8%");
                                                                   $('#<?= $_REQUEST['name_w'] ?>_todayContainer').css("padding-bottom", "0.8%"); 
                                                                }
                                                            }
                                                       }
                                                       
                                                       $('#<?= $_REQUEST['name_w'] ?>_todayContainer').css("border-bottom", "1px solid #dddddd");
                                                       $('#<?= $_REQUEST['name_w'] ?>_todayLeftContainer').css("width", "65%");    
                                                       $('#<?= $_REQUEST['name_w'] ?>_todayLeftContainer').css("height", "100%");
                                                       $('#<?= $_REQUEST['name_w'] ?>_todayRightContainer').css("width", "35%");    
                                                       $('#<?= $_REQUEST['name_w'] ?>_todayRightContainer').css("height", "100%");
                                                       $('#<?= $_REQUEST['name_w'] ?>_dateContainer').hide();
                                                       $('#<?= $_REQUEST['name_w'] ?>_cityContainer').css("width", "100%");    
                                                       $('#<?= $_REQUEST['name_w'] ?>_cityContainer').css("height", "33.333333%");
                                                       $('#<?= $_REQUEST['name_w'] ?>_cityContainer').css("font-family", "Roboto-Medium");
                                                       $('#<?= $_REQUEST['name_w'] ?>_cityContainer').css("font-weight", "bold");
                                                       $('#<?= $_REQUEST['name_w'] ?>_descContainer').css("width", "100%");    
                                                       $('#<?= $_REQUEST['name_w'] ?>_descContainer').css("height", "33.333333%");
                                                       
                                                       $('#<?= $_REQUEST['name_w'] ?>_todayLeftContainer').append($('#<?= $_REQUEST['name_w'] ?>_tempContainer'));
                                                       $('#<?= $_REQUEST['name_w'] ?>_tempContainer').css("justify-content", "flex-start");
                                                       
                                                       $('#<?= $_REQUEST['name_w'] ?>_lammaContainer').css("width", "100%");    
                                                       $('#<?= $_REQUEST['name_w'] ?>_iconContainer').css("width", "100%");    
                                                       $('#<?= $_REQUEST['name_w'] ?>_tempContainer').css("width", "100%");
                                                       $('#<?= $_REQUEST['name_w'] ?>_tempContainer').css("height", "33.333333%");
                                                       
                                                        $('#<?= $_REQUEST['name_w'] ?>_lammaContainer').css("height", "22%");
                                                        $('#<?= $_REQUEST['name_w'] ?>_iconContainer').css("height", "78%");
                                                       
                                                       $('#<?= $_REQUEST['name_w'] ?>_otherDaysContainer').css("width", "100%");
                                                       $('#<?= $_REQUEST['name_w'] ?>_otherDaysContainer').css("height", parseInt(otherDaysQt*rowHeight) + "%");

                                                       otherDaysCellHeight = 100 / otherDaysQt;
                                                       for(var i = 1; i <= otherDaysQt; i++)
                                                       {
                                                          if(backgroundMode === 'auto')
                                                          {
                                                              otherDayCell = $('<div class="meteoOtherDaysCellDarkerV"></div>');
                                                          }
                                                          else
                                                          {
                                                              otherDayCell = $('<div class="meteoOtherDaysCellNoShadow"></div>');
                                                          }

                                                          otherDayCell.css("width", "100%");
                                                          otherDayCell.css("height", otherDaysCellHeight + "%");
                                                          otherDayCell.css("float", "left");
                                                          
                                                          if(sizeRows < 8)
                                                          {
                                                              otherDayCell.css("padding-top", "1.4%");
                                                              otherDayCell.css("padding-bottom", "1.4%");
                                                          }
                                                          else
                                                          {
                                                            if((sizeRows >= 8)&&(sizeRows <= 12))
                                                            {
                                                              otherDayCell.css("padding-top", "2.8%");
                                                              otherDayCell.css("padding-bottom", "2.8%");
                                                            }
                                                            else
                                                            {
                                                                if(sizeRows > 12)
                                                                {
                                                                   otherDayCell.css("padding-top", "5.6%");
                                                                   otherDayCell.css("padding-bottom", "5.6%"); 
                                                                }
                                                            }
                                                          }
                                                          
                                                          if(i < otherDaysQt)
                                                          {
                                                             otherDayCell.css("border-bottom", "1px solid #dddddd");
                                                          }
                                                          
                                                          var leftContainer = $('<div></div>');
                                                          leftContainer.css("width", "70%");
                                                          leftContainer.css("height", "100%");
                                                          leftContainer.css("float", "left");
                                                          otherDayCell.append(leftContainer);

                                                          otherDayDateContainer = $('<div class="otherDayDateContainer"></div>');
                                                          leftContainer.append(otherDayDateContainer);
                                                          otherDayDateContainer.css("width", "100%");
                                                          otherDayDateContainer.css("height", "33.333333%");
                                                          otherDayDateContainer.css("float", "left");
                                                          otherDayDateContainer.css("justify-content", "flex-start");
                                                          //otherDayDateContainer.css("border", "1px solid red");
                                                          otherDayDate = new Date();
                                                          otherDayDate.setDate(otherDayDate.getDate() + i);

                                                          if(language === 'english')
                                                          {
                                                             otherDayDateContainer.html('<span style="display:block;">' + daysOfWeek[otherDayDate.getDay()].englishExt + ' ' + otherDayDate.getDate() + ' ' + monthsNames[otherDayDate.getMonth()].englishExt + '</span>');
                                                          }
                                                          else
                                                          {
                                                             if(language === 'italian')
                                                             {
                                                                otherDayDateContainer.html('<span style="display:block;">' + daysOfWeek[otherDayDate.getDay()].italianExt + ' ' + otherDayDate.getDate() + ' ' + monthsNames[otherDayDate.getMonth()].italianExt + '</span>');
                                                             } 
                                                          }
                                                          otherDayDateContainer.css("font-weight", "bold");
                                                          otherDayDescAndIcon = getDescAndIcon(meteoData.results.bindings[i].description.value);
                                                          
                                                          otherDayDescContainer = $('<div class="otherDayDescContainer"></div>');
                                                          otherDayDescContainer.css("width", "100%");
                                                          otherDayDescContainer.css("height", "33.333333%");
                                                          otherDayDescContainer.css("float", "left");
                                                          otherDayDescContainer.css("justify-content", "flex-start");
                                                          //otherDayDescContainer.css("border", "1px solid red");
                                                          leftContainer.append(otherDayDescContainer);
                                                          otherDayDescContainer.html('<span style="display:block;">' + otherDayDescAndIcon.desc + '</span>');
                                                          
                                                          otherDayTempContainer = $('<div class="otherDayTempContainer"></div>');
                                                          otherDayTempContainer.css("width", "100%");
                                                          otherDayTempContainer.css("height", "33.333333%");
                                                          otherDayTempContainer.css("float", "left");
                                                          otherDayTempContainer.css("justify-content", "flex-start");
                                                          otherDayDescContainer.css("font-family", otherDayTempContainer.css("font-family"));
                                                          //otherDayTempContainer.css("border", "1px solid red");
                                                          leftContainer.append(otherDayTempContainer);
                                                          if((meteoData.results.bindings[i].minTemp.value !== "")&&(meteoData.results.bindings[i].maxTemp.value !== ""))
                                                          {
                                                              otherDayTempContainer.html('<span style="display:block;">' + meteoData.results.bindings[i].minTemp.value + '°C / ' + meteoData.results.bindings[i].maxTemp.value + '°C</span>');
                                                          }
                                                          else
                                                          {
                                                              otherDayTempContainer.html('<span style="display:block;">Temp N/A</span>');
                                                          }
                                                          
                                                          otherDayIconContainer = $('<div data-toggle="tooltip" data-placement="top" title="' + otherDayDescAndIcon.desc + '"></div>');
                                                          otherDayIconContainer.css("width", "30%");
                                                          otherDayIconContainer.css("height", "100%");
                                                          otherDayIconContainer.css("float", "left");
                                                          //otherDayIconContainer.css("border", "1px solid red");
                                                          otherDayCell.append(otherDayIconContainer);
                                                          //otherDayIconContainer.tooltip(); 
                                                          
                                                          $('#<?= $_REQUEST['name_w'] ?>_otherDaysContainer').append(otherDayCell);
                                                          setAutoFontSize(otherDayDateContainer);
                                                          setAutoFontSize(otherDayDescContainer);
                                                          setAutoFontSize(otherDayTempContainer);
                                                          if(iconSet === 'multiColor')
                                                          {
                                                            otherDayIconContainer.css("background-image", "url(" + otherDayDescAndIcon.icon + ")");
                                                            otherDayIconContainer.css("background-size", "contain");
                                                            otherDayIconContainer.css("background-repeat", "no-repeat");
                                                            otherDayIconContainer.css("background-position", "center center");
                                                          }
                                                          else
                                                          {
                                                            otherDayIconContainer.css("background-color", "transparent");
                                                            otherDayIconContainer.addClass("centerWithFlex");
                                                            otherDayIconContainer.html('<span style="display:block; color:' + fontColor + '"><i class="wi ' + otherDayDescAndIcon.icon + '"></i></span>');
                                                            setAutoFontSize(otherDayIconContainer);
                                                          }
                                                       }

                                                        break;    

                                                    case "today": default:
                                                       $('#<?= $_REQUEST['name_w'] ?>_todayContainer').css("width", "100%");    
                                                       $('#<?= $_REQUEST['name_w'] ?>_todayContainer').css("height", "100%");
                                                       $('#<?= $_REQUEST['name_w'] ?>_todayLeftContainer').hide();
                                                       $('#<?= $_REQUEST['name_w'] ?>_todayRightContainer').hide();
                                                       $('#<?= $_REQUEST['name_w'] ?>_todayContainer').append($('#<?= $_REQUEST['name_w'] ?>_dateContainer'));
                                                       $('#<?= $_REQUEST['name_w'] ?>_dateContainer').css("width", "100%");   
                                                       $('#<?= $_REQUEST['name_w'] ?>_dateContainer').removeClass("meteoDateContainer");
                                                       $('#<?= $_REQUEST['name_w'] ?>_dateContainer').addClass("meteoDateContainerCentered");
                                                       $('#<?= $_REQUEST['name_w'] ?>_todayContainer').append($('#<?= $_REQUEST['name_w'] ?>_cityContainer'));
                                                       $('#<?= $_REQUEST['name_w'] ?>_cityContainer').css("width", "100%");    
                                                       $('#<?= $_REQUEST['name_w'] ?>_cityContainer').removeClass("meteoCityContainer");
                                                       $('#<?= $_REQUEST['name_w'] ?>_cityContainer').addClass("meteoCityContainerCentered");
                                                       $('#<?= $_REQUEST['name_w'] ?>_todayContainer').append($('#<?= $_REQUEST['name_w'] ?>_iconContainer'));
                                                       $('#<?= $_REQUEST['name_w'] ?>_iconContainer').css("width", "100%");    
                                                       $('#<?= $_REQUEST['name_w'] ?>_todayContainer').append($('#<?= $_REQUEST['name_w'] ?>_descContainer'));
                                                       $('#<?= $_REQUEST['name_w'] ?>_descContainer').css("width", "100%");   
                                                       $('#<?= $_REQUEST['name_w'] ?>_descContainer').removeClass("meteoDescContainer");
                                                       $('#<?= $_REQUEST['name_w'] ?>_descContainer').addClass("meteoDescContainerCentered");
                                                       $('#<?= $_REQUEST['name_w'] ?>_todayContainer').append($('#<?= $_REQUEST['name_w'] ?>_tempContainer'));
                                                       $('#<?= $_REQUEST['name_w'] ?>_tempContainer').css("width", "100%");   
                                                       $('#<?= $_REQUEST['name_w'] ?>_tempContainer').removeClass("meteoTempContainer");
                                                       $('#<?= $_REQUEST['name_w'] ?>_tempContainer').addClass("meteoTempContainerCentered");
                                                       $('#<?= $_REQUEST['name_w'] ?>_todayContainer').append($('#<?= $_REQUEST['name_w'] ?>_lammaContainer'));
                                                       
                                                       if(sizeRows > 6)
                                                       {
                                                           $('#<?= $_REQUEST['name_w'] ?>_dateContainer').css("height", "12.5%");
                                                           $('#<?= $_REQUEST['name_w'] ?>_cityContainer').css("height", "17.5%");
                                                           $('#<?= $_REQUEST['name_w'] ?>_iconContainer').css("height", "32.5%");
                                                           $('#<?= $_REQUEST['name_w'] ?>_descContainer').css("height", "20%");
                                                           $('#<?= $_REQUEST['name_w'] ?>_tempContainer').css("height", "10%");
                                                           $('#<?= $_REQUEST['name_w'] ?>_lammaContainer').css("height", "7.5%");
                                                       }
                                                       else
                                                       {
                                                           $('#<?= $_REQUEST['name_w'] ?>_dateContainer').css("height", "12.5%");
                                                           $('#<?= $_REQUEST['name_w'] ?>_cityContainer').css("height", "15%");
                                                           $('#<?= $_REQUEST['name_w'] ?>_iconContainer').css("height", "30%");
                                                           $('#<?= $_REQUEST['name_w'] ?>_descContainer').css("height", "17.5%");
                                                           $('#<?= $_REQUEST['name_w'] ?>_tempContainer').css("height", "15%");
                                                           $('#<?= $_REQUEST['name_w'] ?>_lammaContainer').css("height", "10%");
                                                       }

                                                       $('#<?= $_REQUEST['name_w'] ?>_otherDaysContainer').hide(); 
                                                        break;
                                                }
                                                
                                                $('#<?= $_REQUEST['name_w'] ?>_lammaContainer a.lammaLink').css("color", fontColor);
                                                

                                                var dateTime = new Date();
                                                if(language === 'english')
                                                {
                                                    $('#<?= $_REQUEST['name_w'] ?>_dateContainer').html('<span style="display:block;">' + daysOfWeek[dateTime.getDay()].english + ' ' + dateTime.getDate() + ' ' + monthsNames[dateTime.getMonth()].english + '</span>');
                                                }
                                                else
                                                {
                                                    if(language === 'italian')
                                                    {
                                                        $('#<?= $_REQUEST['name_w'] ?>_dateContainer').html('<span style="display:block;">' + daysOfWeek[dateTime.getDay()].italian + ' ' + dateTime.getDate() + ' ' + monthsNames[dateTime.getMonth()].italian + '</span>');
                                                    } 
                                                }

                                                dateContainerSize = setAutoFontSize($('#<?= $_REQUEST['name_w'] ?>_dateContainer'));

                                                updateDateTimeInterval = setInterval(function(){
                                                    dateTime = new Date();
                                                    if(language === 'english')
                                                    {
                                                        $('#<?= $_REQUEST['name_w'] ?>_dateContainer').html('<span style="display:block;">' + daysOfWeek[dateTime.getDay()].english + ' ' + dateTime.getDate() + ' ' + monthsNames[dateTime.getMonth()].english + '</span>');
                                                    }
                                                    else
                                                    {
                                                        if(language === 'italian')
                                                        {
                                                            $('#<?= $_REQUEST['name_w'] ?>_dateContainer').html('<span style="display:block;">' + daysOfWeek[dateTime.getDay()].italian + ' ' + dateTime.getDate() + ' ' + monthsNames[dateTime.getMonth()].italian + '</span>');
                                                        } 
                                                    }
                                                    //$('#<?= $_REQUEST['name_w'] ?>_dateContainer').html('<span style="display:block;">' + dateTime.date + '</span>');
                                                    dateContainerSize = setAutoFontSize($('#<?= $_REQUEST['name_w'] ?>_dateContainer'));
                                                }, 600000);

                                                setAutoFontSize($('#<?= $_REQUEST['name_w'] ?>_cityContainer'));
                                                setAutoFontSize($('#<?= $_REQUEST['name_w'] ?>_descContainer'));
                                                setAutoFontSize($('#<?= $_REQUEST['name_w'] ?>_lammaContainer'));
                                                setAutoFontSize($('#<?= $_REQUEST['name_w'] ?>_tempContainer'));
                                                if(iconSet === 'multiColor')
                                                {
                                                    $('#<?= $_REQUEST['name_w'] ?>_iconContainer').css("background-image", "url(" + todayDescAndIcon.icon + ")");
                                                    $('#<?= $_REQUEST['name_w'] ?>_iconContainer').css("background-size", "contain");
                                                    $('#<?= $_REQUEST['name_w'] ?>_iconContainer').css("background-repeat", "no-repeat");
                                                    $('#<?= $_REQUEST['name_w'] ?>_iconContainer').css("background-position", "center center");
                                                }
                                                else
                                                {
                                                    $('#<?= $_REQUEST['name_w'] ?>_iconContainer').css("background-color", "transparent");
                                                    $('#<?= $_REQUEST['name_w'] ?>_iconContainer').addClass("centerWithFlex");
                                                    $('#<?= $_REQUEST['name_w'] ?>_iconContainer').html('<span style="display:block; color:' + fontColor + '"><i class="wi ' + todayDescAndIcon.icon + '"></i></span>');
                                                    setAutoFontSize($('#<?= $_REQUEST['name_w'] ?>_iconContainer'));
                                                }

                                                timeToClearScroll = (timeToReload - 0.5) * 1000;

                                                setTimeout(function()
                                                {
                                                    clearInterval(updateDateTimeInterval);
                                                }, timeToClearScroll);
                                            }
                                        }                                     
                                    },
                                    error: function(errorData)
                                    {
                                       console.log("KO"); 
                                       console.log(JSON.stringify(errorData));
                                       showWidgetContent(widgetName);
                                       if(firstLoad !== false)
                                       {
                                          $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                                          $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                                       }
                                    }
                                });
                            }
                            else
                            {
                                $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                                $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                            }
                        },
                        error: function(errorData)
                        {
                           console.log("KO"); 
                           console.log(JSON.stringify(errorData));
                           showWidgetContent(widgetName);
                           if(firstLoad !== false)
                           {
                              $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                              $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                           }
                        }  
                     });
                }
            },
            error: function(errorData)
            {
               console.log(JSON.stringify(errorData));
               showWidgetContent(widgetName);
               if(firstLoad !== false)
               {
                  $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                  $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
               }
            },
            complete: function()
            {
                countdownRef = startCountdown(widgetName, timeToReload, <?= $_REQUEST['name_w'] ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef);
            }
        });
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
            <div id="<?= $_REQUEST['name_w'] ?>_noDataAlert" class="noDataAlert">
                <div id="<?= $_REQUEST['name_w'] ?>_noDataAlertText" class="noDataAlertText">
                    No data available
                </div>
                <div id="<?= $_REQUEST['name_w'] ?>_noDataAlertIcon" class="noDataAlertIcon">
                    <i class="fa fa-times"></i>
                </div>
            </div>
            <div id="<?= $_REQUEST['name_w'] ?>_chartContainer" class="chartContainer">
                <div id="<?= $_REQUEST['name_w'] ?>_todayContainer" class="meteoTodayContainer">
                    <div id="<?= $_REQUEST['name_w'] ?>_todayLeftContainer" class="meteoTodayLeftContainer">
                        <div id="<?= $_REQUEST['name_w'] ?>_dateContainer" class="meteoDateContainer"></div>
                        <div id="<?= $_REQUEST['name_w'] ?>_cityContainer" class="meteoCityContainer"></div>
                        <div id="<?= $_REQUEST['name_w'] ?>_descContainer" class="meteoDescContainer"></div>
                    </div>
                    <div id="<?= $_REQUEST['name_w'] ?>_todayRightContainer" class="meteoTodayRightContainer">
                        <div id="<?= $_REQUEST['name_w'] ?>_lammaContainer" class="meteoLammaContainer"><a class="lammaLink" href="http://www.lamma.rete.toscana.it" target="_blank"><span style="display:block;">Powered by LaMMA</span></a></div>
                        <div id="<?= $_REQUEST['name_w'] ?>_iconContainer" class="meteoIconContainer"></div>
                        <div id="<?= $_REQUEST['name_w'] ?>_tempContainer" class="meteoTempContainer"></div>
                    </div>
                </div>
                <div id="<?= $_REQUEST['name_w'] ?>_otherDaysContainer" class="meteoOtherDaysContainer"></div>
            </div>
        </div>
    </div>	
</div>