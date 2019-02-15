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
    var gradients = {
      GREEN: "linear-gradient(to right, #E2FF8C, #99CC00)",
      ORANGE: "linear-gradient(to right, #FFD382, #FFA500)",
      YELLOW: "linear-gradient(to right, #FAFAAF, #FFFF00)",
      RED: "linear-gradient(to right, #FF7878, #FF0000)"
    };
    
    $(document).ready(function <?= $_REQUEST['name_w'] ?>(firstLoad)  
    {
        var content, permalink, idWidget, idDash, idraulicoSrc, idraulicoLoc, temporaliSrc, temporaliLoc, idrogeologicoSrc,
        idrogeologicoLoc, neveSrc, neveLoc, ghiaccioSrc, ghiaccioLoc, ventoSrc, ventoLoc, mareSrc, mareLoc, maxAlarmDeg, descW, 
        sizeRowsWidget, styleParameters, genTabFontSize, genTabFontColor, meteoTabFontSize, descWPerc, iconDim, rowHeightPerc,
        height, genTabFontSizeFactor, meteoLegendaFontSizeFactor, showHeader, countdown = null;
		headerHeight = 25;
        var hostFile = "<?= $_REQUEST['hostFile'] ?>";
		var widgetName = "<?= $_REQUEST['name_w'] ?>";
		var widgetContentColor = "<?= $_REQUEST['color_w'] ?>";
		var widgetHeaderColor = "<?= $_REQUEST['frame_color_w'] ?>";
		var widgetHeaderFontColor = "<?= $_REQUEST['headerFontColor'] ?>";
        var embedWidget = <?= $_REQUEST['embedWidget'] ?>;
        var embedWidgetPolicy = '<?= $_REQUEST['embedWidgetPolicy'] ?>';
        var showTitle = "<?= $_REQUEST['showTitle'] ?>";
        if(((embedWidget === true)&&(embedWidgetPolicy === 'auto'))||((embedWidget === true)&&(embedWidgetPolicy === 'manual')&&(showTitle === "no"))||((embedWidget === false)&&(showTitle === "no")))
        {
            height = parseInt($("#<?= $_REQUEST['name_w'] ?>_div").prop("offsetHeight"));
            $('#<?= $_REQUEST['name_w'] ?>_logo').hide();
            showHeader = false;
        }
        else
        {
            $('#<?= $_REQUEST['name_w'] ?>_logo').show();
            height = parseInt($("#<?= $_REQUEST['name_w'] ?>_div").height() - $("#<?= $_REQUEST['name_w'] ?>_logo").height());
            showHeader = true;
        }
        var hasTimer = "<?= $_REQUEST['hasTimer'] ?>";
        var percHeight = Math.floor(height / $("#<?= $_REQUEST['name_w'] ?>_div").height() * 100);
        var carouselHeight = parseInt(height - 20);
        var carouselPercHeight = Math.floor(carouselHeight / height * 100);
        var loadingFontDim = 13; 
        var loadingIconDim = 20;
        var alarmDegs = new Array();
        var defaultTab = parseInt("<?= $_REQUEST['defaultTab'] ?>");
        var name = "<?= $_REQUEST['name_w'] ?>";
        
        var widgetProperties = getWidgetProperties(name);
        if(jQuery.parseJSON(widgetProperties.param.styleParameters !== null))
        {
            styleParameters = jQuery.parseJSON(widgetProperties.param.styleParameters);
            genTabFontSize = styleParameters.genTabFontSize;
            meteoTabFontSize = styleParameters.meteoTabFontSize;
            genTabFontColor = styleParameters.genTabFontColor;
        }
        
        $("#<?= $_REQUEST['name_w'] ?>_logo").css("background-color", '<?= $_REQUEST['frame_color_w'] ?>');
        $('#<?= $_REQUEST['name_w'] ?>_loading').css("height", percHeight + "%");
        $('#<?= $_REQUEST['name_w'] ?>_loading p').css("font-size", loadingFontDim + "px");
        $('#<?= $_REQUEST['name_w'] ?>_loading i').css("font-size", loadingIconDim + "px");
        $("#<?= $_REQUEST['name_w'] ?>_loading").css("background-color", '<?= $_REQUEST['color_w'] ?>');
        $("#<?= $_REQUEST['name_w'] ?>_content").css("background-color", '<?= $_REQUEST['color_w'] ?>');
        
        if(firstLoad !== false)
        {
            $('#<?= $_REQUEST['name_w'] ?>_loading').css("display", "block");
        }
        
        $("#<?= $_REQUEST['name_w'] ?>_content").css("height", percHeight + "%");
        $("#<?= $_REQUEST['name_w'] ?>_carousel").css("height", carouselPercHeight + "%");
		
        $('#<?= $_REQUEST['name_w'] ?>_titleDiv .pcPhoto').css("display", "block");
        
        //Legge empirica di applicazione responsive del font size del tab general
        genTabFontSizeFactor = genTabFontSize / $("#<?= $_REQUEST['name_w'] ?>_div").width();
        genTabFontSize = genTabFontSizeFactor * $("#<?= $_REQUEST['name_w'] ?>_div").width();
        
        //Definizioni di funzione specifiche del widget
        function getNumPriority(color)
        {
            numPriority = null;
            switch(color)
            {
                case gradients.GREEN:
                    numPriority = 1;
                    break;
                    
                case gradients.YELLOW:
                    numPriority = 2;
                    break;
                    
                case gradients.ORANGE:
                    numPriority = 3;
                    break;
                    
                case gradients.RED:
                    numPriority = 4;
                    break;    
            }
            return parseInt(numPriority);
        }
        
        function getColPriority(numeric)
        {
            colPriority = null;
            switch(numeric)
            {
                case 1:
                    colPriority = gradients.GREEN;
                    break;
                    
                case 2:
                    colPriority = gradients.YELLOW;
                    break;
                    
                case 3:
                    colPriority = gradients.ORANGE;
                    break;
                    
                case 4:
                    colPriority = gradients.RED;
                    break;    
            }
            
            return colPriority;
        }
        
        function getMaxAlarmGrade()
        {
            var max = alarmDegs[0];
            var maxNumeric = 1;
            var numeric = null;
            alarmDegs.forEach(function(element) {
                numeric = getNumPriority(element);
                if(numeric > maxNumeric)
                {
                    maxNumeric = numeric;
                }
            });
            max = getColPriority(maxNumeric);
            return max;
        }
        
        function getGradient(imgSrc)
        {
            if(imgSrc.indexOf("nessuno") >= 0)
            {
                return gradients.GREEN;
            }
            
            if(imgSrc.indexOf("basso") >= 0)
            {
                return gradients.YELLOW;
            }
            
            if(imgSrc.indexOf("medio") >= 0)
            {
                return gradients.ORANGE;
            }
            
            if(imgSrc.indexOf("alto") >= 0)
            {
                return gradients.RED;
            }
        }
        
        function resizeWidget()
        {
            if(showHeader === true)
            {
                height = parseInt($("#<?= $_REQUEST['name_w'] ?>_div").prop("offsetHeight"));
            }
            else
            {
                height = parseInt($("#<?= $_REQUEST['name_w'] ?>_div").height() - $("#<?= $_REQUEST['name_w'] ?>_logo").height());
            }
            
            percHeight = Math.floor(height / $("#<?= $_REQUEST['name_w'] ?>_div").height() * 100);
            carouselHeight = parseInt(height - 20);
            carouselPercHeight = Math.floor(carouselHeight / height * 100);
            
            $('#<?= $_REQUEST['name_w'] ?>_loading').css("height", percHeight + "%");
            $("#<?= $_REQUEST['name_w'] ?>_content").css("height", percHeight + "%");
            $("#<?= $_REQUEST['name_w'] ?>_carousel").css("height", carouselPercHeight + "%");
            
            //Legge empirica di applicazione responsive del font size del tab general
            genTabFontSizeFactor = genTabFontSize / $("#<?= $_REQUEST['name_w'] ?>_div").width();
            genTabFontSize = genTabFontSizeFactor * $("#<?= $_REQUEST['name_w'] ?>_div").width();
            
            setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
            
            rowHeightPerc = 12.5;
            descW = parseInt($('#<?= $_REQUEST['name_w'] ?>_div').width() - Math.floor(parseInt(carouselHeight) / 8));
            descWPerc = Math.floor(descW * 100 / $('#<?= $_REQUEST['name_w'] ?>_div').width());
            iconDim = 100 - descWPerc;
            
            $("#<?= $_REQUEST['name_w'] ?>_meteo .meteoPcRow").css("height", rowHeightPerc + "%");
            $("#<?= $_REQUEST['name_w'] ?>_meteo .meteoPcDesc").css("width", descWPerc + "%");
            $("#<?= $_REQUEST['name_w'] ?>_meteo .meteoPcIcon").css("width", iconDim + "%");
            
            $('#<?= $_REQUEST['name_w'] ?>_meteo .pcLegendaElement').textfill({
                maxFontPixels: -20
            });

            var minLegendaFontSize = 40;
            var meteoLegendaFontSize = parseInt($('#<?= $_REQUEST['name_w'] ?>_meteo .pcLegendaElement').eq(0).find('span').css('font-size').replace('px', ''));

            for(var k = 0; k < $('#<?= $_REQUEST['name_w'] ?>_meteo .pcLegendaElement').length; k++)
            {
                if(parseInt($('#<?= $_REQUEST['name_w'] ?>_meteo .pcLegendaElement').eq(k).find('span').css('font-size').replace('px', '')) < minLegendaFontSize)
                {
                    minLegendaFontSize = parseInt($('#<?= $_REQUEST['name_w'] ?>_meteo .pcLegendaElement').eq(k).find('span').css('font-size').replace('px', ''));
                }
            }

            if(minLegendaFontSize > meteoLegendaFontSize)
            {
                minLegendaFontSize = meteoLegendaFontSize;
            }

            $('#<?= $_REQUEST['name_w'] ?>_meteo .pcLegendaElement span').css("font-size", minLegendaFontSize*0.9 + "px");

            $('#<?= $_REQUEST['name_w'] ?>_meteo .meteoPcDesc').textfill({
                maxFontPixels: meteoTabFontSize
            });

            var minMeteoFontSize = parseInt($('#<?= $_REQUEST['name_w'] ?>_meteo .meteoPcDesc').eq(2).find('span').css('font-size').replace('px', ''));
            $("#<?= $_REQUEST['name_w'] ?>_meteo .meteoPcDesc span").css("font-size", minMeteoFontSize + "px");
        }
        //Fine definizioni di funzione
        
	setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
        $('#<?= $_REQUEST['name_w'] ?>_div').parents('li.gs_w').off('resizeWidgets');
        $('#<?= $_REQUEST['name_w'] ?>_div').parents('li.gs_w').on('resizeWidgets', resizeWidget);
        
        $('#<?= $_REQUEST['name_w'] ?>_content').on('slid.bs.carousel', function (ev) 
        {
            var id = ev.relatedTarget.id;
            switch(id)
            {
                case "<?= $_REQUEST['name_w'] ?>_general":
                    $("#<?= $_REQUEST['name_w'] ?>_generalLi").attr("class", "active");
                    $("#<?= $_REQUEST['name_w'] ?>_meteoLi").attr("class", "");       
                    break;

                case "<?= $_REQUEST['name_w'] ?>_meteo":
                    $("#<?= $_REQUEST['name_w'] ?>_generalLi").attr("class", "");
                    $("#<?= $_REQUEST['name_w'] ?>_meteoLi").attr("class", "active"); 
                    
                    $('#<?= $_REQUEST['name_w'] ?>_meteo div.pcLegendaNessuno span').html("nullo");
                    $('#<?= $_REQUEST['name_w'] ?>_meteo div.pcLegendaBasso span').html("basso");
                    $('#<?= $_REQUEST['name_w'] ?>_meteo div.pcLegendaMedio span').html("medio");
                    $('#<?= $_REQUEST['name_w'] ?>_meteo div.pcLegendaAlto span').html("alto");

                    $('#<?= $_REQUEST['name_w'] ?>_meteo .pcLegendaElement').textfill({
                        maxFontPixels: -20
                    });

                    var minLegendaFontSize = 40;
                    var meteoLegendaFontSize = parseInt($('#<?= $_REQUEST['name_w'] ?>_meteo .pcLegendaElement').eq(0).find('span').css('font-size').replace('px', ''));

                    for(var k = 0; k < $('#<?= $_REQUEST['name_w'] ?>_meteo .pcLegendaElement').length; k++)
                    {
                        if(parseInt($('#<?= $_REQUEST['name_w'] ?>_meteo .pcLegendaElement').eq(k).find('span').css('font-size').replace('px', '')) < minLegendaFontSize)
                        {
                            minLegendaFontSize = parseInt($('#<?= $_REQUEST['name_w'] ?>_meteo .pcLegendaElement').eq(k).find('span').css('font-size').replace('px', ''));
                        }
                    }

                    if(minLegendaFontSize > meteoLegendaFontSize)
                    {
                        minLegendaFontSize = meteoLegendaFontSize;
                    }

                    $('#<?= $_REQUEST['name_w'] ?>_meteo .pcLegendaElement span').css("font-size", minLegendaFontSize*0.9 + "px");
                    
                    $('#<?= $_REQUEST['name_w'] ?>_meteo .meteoPcDesc').textfill({
                        maxFontPixels: meteoTabFontSize
                    });

                    var minMeteoFontSize = parseInt($('#<?= $_REQUEST['name_w'] ?>_meteo .meteoPcDesc').eq(2).find('span').css('font-size').replace('px', ''));
                    $("#<?= $_REQUEST['name_w'] ?>_meteo .meteoPcDesc span").css("font-size", minMeteoFontSize + "px");
                    break;    
            }
        });

        $("#<?= $_REQUEST['name_w'] ?>_generalLi").click(function() 
        {
            $("#<?= $_REQUEST['name_w'] ?>_generalLi").attr("class", "active");
            $("#<?= $_REQUEST['name_w'] ?>_meteoLi").attr("class", "");
            $("#<?= $_REQUEST['name_w'] ?>_content").carousel(0);
        });

        $("#<?= $_REQUEST['name_w'] ?>_meteoLi").click(function() 
        {
            $("#<?= $_REQUEST['name_w'] ?>_generalLi").attr("class", "");
            $("#<?= $_REQUEST['name_w'] ?>_meteoLi").attr("class", "active");
            $("#<?= $_REQUEST['name_w'] ?>_content").carousel(1);
        });
        
        rowHeightPerc = 12.5;
        descW = parseInt($('#<?= $_REQUEST['name_w'] ?>_div').width() - Math.floor(parseInt(carouselHeight) / 8));
        descWPerc = Math.floor(descW * 100 / $('#<?= $_REQUEST['name_w'] ?>_div').width());
        iconDim = 100 - descWPerc;
        
        $("#<?= $_REQUEST['name_w'] ?>_meteo .meteoPcRow").css("height", rowHeightPerc + "%");
        $("#<?= $_REQUEST['name_w'] ?>_meteo .meteoPcDesc").css("width", descWPerc + "%");
        
        $("#<?= $_REQUEST['name_w'] ?>_meteo .meteoPcIcon").css("width", iconDim + "%");
        
        $('#source_<?= $_REQUEST['name_w'] ?>').on('click', function () 
        {
            $('#dialog_<?= $_REQUEST['name_w'] ?>').show();
        });

        $('#close_popup_<?= $_REQUEST['name_w'] ?>').on('click', function () 
        {
            $('#dialog_<?= $_REQUEST['name_w'] ?>').hide();
        });
        
        var counter = <?= $_REQUEST['frequency_w'] ?>;
        
        $("#<?= $_REQUEST['name_w'] ?>").on('customResizeEvent', function(event){
            resizeWidget();
        });
        
        $(document).off('resizeHighchart_' + widgetName);
        $(document).on('resizeHighchart_' + widgetName, function(event)
        {
            showHeader = event.showHeader;
        });
        
        $("#<?= $_REQUEST['name_w'] ?>").off('updateFrequency');
        $("#<?= $_REQUEST['name_w'] ?>").on('updateFrequency', function(event){
                clearInterval(countdown);
                counter = event.newTimeToReload;
                
                countdown = setInterval(function () 
                { 
                   var ref = "#ProtezioneCivile_" + idDash + "_widgetProtezioneCivile" + idWidget + "_countdownDiv"; 
                    $(ref).text(counter);
                    counter--;
                    if (counter > 60) 
                    {
                        $(ref).text(Math.floor(counter / 60) + "m");
                    } 
                    else 
                    {
                        $(ref).text(counter + "s");
                    }
                    if(counter === 0) 
                    {
                        $(ref).text(counter + "s");
                        $("#<?= $_REQUEST['name_w'] ?>").off('customResizeEvent');
                        $("#<?= $_REQUEST['name_w'] ?>_content").off();
                        $("#<?= $_REQUEST['name_w'] ?>_meteo .meteoPcIcon").html("");
                        switch(maxAlarmDeg)
                        {
                            case gradients.YELLOW:
                                $("#<?= $_REQUEST['name_w'] ?>_alarmDivPc").removeClass("alarmDivPcActiveYellow");
                                $("#<?= $_REQUEST['name_w'] ?>_alarmDivPc").addClass("alarmDivPc");
                                break;

                            case gradients.ORANGE:
                                $("#<?= $_REQUEST['name_w'] ?>_alarmDivPc").removeClass("alarmDivPcActiveOrange");
                                $("#<?= $_REQUEST['name_w'] ?>_alarmDivPc").addClass("alarmDivPc");
                                break;

                            case gradients.RED:
                                $("#<?= $_REQUEST['name_w'] ?>_alarmDivPc").removeClass("alarmDivPcActiveRed");
                                $("#<?= $_REQUEST['name_w'] ?>_alarmDivPc").addClass("alarmDivPc");
                                break;

                            default:
                                break;
                        }
                        $("#<?= $_REQUEST['name_w'] ?>_general").html("");

                        clearInterval(countdown);
                        setTimeout(<?= $_REQUEST['name_w'] ?>(false), 1000);
                    }
                }, 1000);
                
                countdownRef = startCountdown(widgetName, timeToReload, <?= $_REQUEST['name_w'] ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef);
        });
        
        countdown = setInterval(function () 
        { 
	   var ref = "#ProtezioneCivile_" + idDash + "_widgetProtezioneCivile" + idWidget + "_countdownDiv"; 
            $(ref).text(counter);
            counter--;
            if (counter > 60) 
            {
                $(ref).text(Math.floor(counter / 60) + "m");
            } 
            else 
            {
                $(ref).text(counter + "s");
            }
            if(counter === 0) 
            {
                $(ref).text(counter + "s");
                $("#<?= $_REQUEST['name_w'] ?>").off('customResizeEvent');
                $("#<?= $_REQUEST['name_w'] ?>_content").off();
                $("#<?= $_REQUEST['name_w'] ?>_meteo .meteoPcIcon").html("");
                switch(maxAlarmDeg)
                {
                    case gradients.YELLOW:
                        $("#<?= $_REQUEST['name_w'] ?>_alarmDivPc").removeClass("alarmDivPcActiveYellow");
                        $("#<?= $_REQUEST['name_w'] ?>_alarmDivPc").addClass("alarmDivPc");
                        break;

                    case gradients.ORANGE:
                        $("#<?= $_REQUEST['name_w'] ?>_alarmDivPc").removeClass("alarmDivPcActiveOrange");
                        $("#<?= $_REQUEST['name_w'] ?>_alarmDivPc").addClass("alarmDivPc");
                        break;

                    case gradients.RED:
                        $("#<?= $_REQUEST['name_w'] ?>_alarmDivPc").removeClass("alarmDivPcActiveRed");
                        $("#<?= $_REQUEST['name_w'] ?>_alarmDivPc").addClass("alarmDivPc");
                        break;

                    default:
                        break;
                }
                $("#<?= $_REQUEST['name_w'] ?>_general").html("");
                
                clearInterval(countdown);
                setTimeout(<?= $_REQUEST['name_w'] ?>(false), 1000);
            }
        }, 1000);
        
         $.ajax({//Inizio AJAX getParametersWidgets.php
            url: "../widgets/getParametersWidgets.php",
            type: "GET",
            data: {"nomeWidget": ["<?= $_REQUEST['name_w'] ?>"]},
            async: true,
            dataType: 'json',
            success: function (msg) 
            {
                idWidget = null;
                idDash = null;
                name = null;
                
                if(msg !== null)
                {
                    udm = msg.param.udm;
                    sizeRowsWidget = parseInt(msg.param.size_rows);
                    idWidget = msg.param.Id;
                    idDash = msg.param.id_dashboard;
                }
                
                $.ajax({
                    url: "../management/iframeProxy.php",
                    type: "GET",
                    data: {
                        action: "getPcMeteoInfo"
                    },
                    async: true,
                    //dataType: 'jsonp',
                    success: function (msg) 
                    {
                        idraulicoSrc = $(msg).find("img[name='idraulico']").attr("src");
                        temporaliSrc = $(msg).find("img[name='temporali']").attr("src");
                        idrogeologicoSrc = $(msg).find("img[name='idrogeologico']").attr("src");
                        neveSrc = $(msg).find("img[name='neve']").attr("src");
                        ghiaccioSrc = $(msg).find("img[name='ghiaccio']").attr("src");
                        ventoSrc = $(msg).find("img[name='vento']").attr("src");
                        mareSrc = $(msg).find("img[name='mare']").attr("src");
                        
                        var index = idraulicoSrc.indexOf("idraulico");
                        idraulicoLoc = "../img/meteoPc/" + idraulicoSrc.substring(index);
                        var newImg = $("<img style='width: 100%; height: 100%'>");
                        var grad = getGradient(idraulicoLoc);
                        alarmDegs.push(grad);
                        newImg.attr("src", idraulicoLoc);
                        $("#<?= $_REQUEST['name_w'] ?>_idraulico .meteoPcDesc").css("background", grad);
                        $("#<?= $_REQUEST['name_w'] ?>_idraulico .meteoPcIcon").append(newImg);
                        
                        index = temporaliSrc.indexOf("temporali");
                        temporaliLoc = "../img/meteoPc/" + temporaliSrc.substring(index);
                        newImg = $("<img style='width: 100%; height: 100%'>");
                        grad = getGradient(temporaliLoc);
                        alarmDegs.push(grad);
                        newImg.attr("src", temporaliLoc);
                        $("#<?= $_REQUEST['name_w'] ?>_temporali .meteoPcDesc").css("background", grad);
                        $("#<?= $_REQUEST['name_w'] ?>_temporali .meteoPcIcon").append(newImg);
                        
                        index = idrogeologicoSrc.indexOf("idrogeologico");
                        idrogeologicoLoc = "../img/meteoPc/" + idrogeologicoSrc.substring(index);
                        newImg = $("<img style='width: 100%; height: 100%'>");
                        grad = getGradient(idrogeologicoLoc);
                        alarmDegs.push(grad);
                        newImg.attr("src", idrogeologicoLoc);
                        $("#<?= $_REQUEST['name_w'] ?>_idrogeologico .meteoPcDesc").css("background", grad);
                        $("#<?= $_REQUEST['name_w'] ?>_idrogeologico .meteoPcIcon").append(newImg);
                        
                        index = neveSrc.indexOf("neve");
                        neveLoc = "../img/meteoPc/" + neveSrc.substring(index);
                        newImg = $("<img style='width: 100%; height: 100%'>");
                        grad = getGradient(neveLoc);
                        alarmDegs.push(grad);
                        newImg.attr("src", neveLoc);
                        $("#<?= $_REQUEST['name_w'] ?>_neve .meteoPcDesc").css("background", grad);
                        $("#<?= $_REQUEST['name_w'] ?>_neve .meteoPcIcon").append(newImg);
                        
                        index = ghiaccioSrc.indexOf("ghiaccio");
                        ghiaccioLoc = "../img/meteoPc/" + ghiaccioSrc.substring(index);
                        newImg = $("<img style='width: 100%; height: 100%'>");
                        grad = getGradient(ghiaccioLoc);
                        alarmDegs.push(grad);
                        newImg.attr("src", ghiaccioLoc);
                        $("#<?= $_REQUEST['name_w'] ?>_ghiaccio .meteoPcDesc").css("background", grad);
                        $("#<?= $_REQUEST['name_w'] ?>_ghiaccio .meteoPcIcon").append(newImg);
                        
                        index = ventoSrc.indexOf("vento");
                        ventoLoc = "../img/meteoPc/" + ventoSrc.substring(index);
                        newImg = $("<img style='width: 100%; height: 100%'>");
                        grad = getGradient(ventoLoc);
                        alarmDegs.push(grad);
                        newImg.attr("src", ventoLoc);
                        $("#<?= $_REQUEST['name_w'] ?>_vento .meteoPcDesc").css("background", grad);
                        $("#<?= $_REQUEST['name_w'] ?>_vento .meteoPcIcon").append(newImg);
                        
                        index = mareSrc.indexOf("mare");
                        mareLoc = "../img/meteoPc/" + mareSrc.substring(index);
                        newImg = $("<img style='width: 100%; height: 100%'>");
                        grad = getGradient(mareLoc);
                        alarmDegs.push(grad);
                        newImg.attr("src", mareLoc);
                        $("#<?= $_REQUEST['name_w'] ?>_mare .meteoPcDesc").css("background", grad);
                        $("#<?= $_REQUEST['name_w'] ?>_mare .meteoPcIcon").append(newImg);
                        
                        $("<?= $_REQUEST['name_w'] ?>_meteo").add("<img src='" + idraulicoSrc + "'/>");
                        $("<?= $_REQUEST['name_w'] ?>_meteo").add("<img src='" + temporaliSrc + "'/>");
                        $("<?= $_REQUEST['name_w'] ?>_meteo").add("<img src='" + idrogeologicoSrc + "'/>");
                        $("<?= $_REQUEST['name_w'] ?>_meteo").add("<img src='" + neveSrc + "'/>");
                        $("<?= $_REQUEST['name_w'] ?>_meteo").add("<img src='" + ghiaccioSrc + "'/>");
                        $("<?= $_REQUEST['name_w'] ?>_meteo").add("<img src='" + ventoSrc + "'/>");
                        $("<?= $_REQUEST['name_w'] ?>_meteo").add("<img src='" + mareSrc + "'/>");
                        
                        maxAlarmDeg = getMaxAlarmGrade();
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log("Errore in caricamento proprietà widget");
                        $("#<?= $_REQUEST['name_w'] ?>_general").html("No data available");
                    }
                });
                
                $.ajax({
                    url: "../management/iframeProxy.php",
                    type: "GET",
                    data: {
                        action: "getPcGeneralInfo"
                    },
                    async: true,
                    dataType: 'json',
                    success: function (msg) 
                    {
                        if(msg === null)
                        {
                            if(firstLoad !== false)
                            {
                                $('#<?= $_REQUEST['name_w'] ?>_loading').css("display", "none");
                                $('#<?= $_REQUEST['name_w'] ?>_content').css("display", "block");
                            }
                            $('#<?= $_REQUEST['name_w'] ?>_content').html("<p style='text-align: center;'>No data available</p>");
                        }
                        else
                        {
                            permalink = msg[0].permalink;
                            if((permalink === "null") || (permalink === null) || (permalink === ""))
                            {
                                permalink = null;
                            }

                            content = $(msg[0].content);
                            
                            $("#<?= $_REQUEST['name_w'] ?>_general").html(content);
                            $("#<?= $_REQUEST['name_w'] ?>_general").css("color", genTabFontColor);
                            $("#<?= $_REQUEST['name_w'] ?>_general").css("font-size", genTabFontSize + "px");
                            $("#<?= $_REQUEST['name_w'] ?>_general *").css("color", genTabFontColor);
                            $("#<?= $_REQUEST['name_w'] ?>_general *").css("font-size", genTabFontSize + "px");

                            $("#<?= $_REQUEST['name_w'] ?>_general").find("img").eq(0).remove();
                            $("#<?= $_REQUEST['name_w'] ?>_general").find("iframe").remove();
                            
                            if($("#<?= $_REQUEST['name_w'] ?>_general").html().toUpperCase().includes("Niente da Segnalare".toUpperCase()))
                            {
                                $("#<?= $_REQUEST['name_w'] ?>_general").html("Niente da segnalare");
                            }

                            switch(maxAlarmDeg)
                            {
                                case gradients.YELLOW:
                                    $("#<?= $_REQUEST['name_w'] ?>_alarmDivPc").removeClass("alarmDivPc");
                                    $("#<?= $_REQUEST['name_w'] ?>_alarmDivPc").addClass("alarmDivPcActiveYellow");
                                    break;

                                case gradients.ORANGE:
                                    $("#<?= $_REQUEST['name_w'] ?>_alarmDivPc").removeClass("alarmDivPc");
                                    $("#<?= $_REQUEST['name_w'] ?>_alarmDivPc").addClass("alarmDivPcActiveOrange");
                                    break;

                                case gradients.RED:
                                    $("#<?= $_REQUEST['name_w'] ?>_alarmDivPc").removeClass("alarmDivPc");
                                    $("#<?= $_REQUEST['name_w'] ?>_alarmDivPc").addClass("alarmDivPcActiveRed");
                                    break;

                                default:
                                    break;
                            }
                        }  
                    },
                    error:function(dataError)
                    {
                        console.log("Error getting bullettin from Civil Protection");
                        console.log(JSON.stringify(dataError));
                        $("#<?= $_REQUEST['name_w'] ?>_general").html("No data available");
                    }
                });
                
                if(firstLoad !== false)
                {
                    $('#<?= $_REQUEST['name_w'] ?>_loading').css("display", "none");
                    $("#<?= $_REQUEST['name_w'] ?>_content").css("display", "block");
                }
                
                if(defaultTab !== -1)
                {
                    $("#<?= $_REQUEST['name_w'] ?>_content").carousel(defaultTab);
                    $('#<?= $_REQUEST['name_w'] ?>_content').addClass('slide');
                }
                else
                {
                    $('#<?= $_REQUEST['name_w'] ?>_content').addClass('slide');
                    $('#<?= $_REQUEST['name_w'] ?>_content').attr('data-interval', 4000);
                    $('#<?= $_REQUEST['name_w'] ?>_content').carousel('cycle');
                }
            }
        });   
});//Fine document ready
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
        
        <div id='<?= $_REQUEST['name_w'] ?>_content' class="content pcContainer carousel" data-interval="false" data-pause="hover">
            <?php include '../widgets/commonModules/widgetDimControls.php'; ?>	
            <ul id="<?= $_REQUEST['name_w'] ?>_nav_ul" class="nav nav-tabs nav_ul">
                <li role="navigation" id="<?= $_REQUEST['name_w'] ?>_generalLi" class="active"><a disabled="true" class="atafTab">general</a></li>
                <li role="navigation" id="<?= $_REQUEST['name_w'] ?>_meteoLi"><a disabled="true" class="atafTab">meteo</a></li>
            </ul>
            <div id="<?= $_REQUEST['name_w'] ?>_carousel" class="carousel-inner" role="listbox">
                <div id="<?= $_REQUEST['name_w'] ?>_general" class="item active pcGeneralDiv"></div>
                <div id="<?= $_REQUEST['name_w'] ?>_meteo" class="item pcMeteoDiv">
                    <div id="<?= $_REQUEST['name_w'] ?>_legendaRow" class="meteoPcLegendaRow">
                        <div id="<?= $_REQUEST['name_w'] ?>_legendaContainer" class="pcLegendaContainer">
                            <div class="pcLegendaElement pcLegendaElementMarginRight">
                                <div class="pcLegendaNessuno"><span></span></div>    
                            </div>
                            <div class="pcLegendaElement pcLegendaElementMarginRight">
                                <div class="pcLegendaBasso"><span></span></div>    
                            </div>
                            <div class="pcLegendaElement pcLegendaElementMarginRight">
                                <div class="pcLegendaMedio"><span></span></div>     
                            </div>
                            <div class="pcLegendaElement">
                                <div class="pcLegendaAlto"><span></span></div>     
                            </div>      
                        </div>
                    </div>
                    <div id="<?= $_REQUEST['name_w'] ?>_idraulico" class="meteoPcRow">
                        <a href="http://www.regione.toscana.it/allerta-meteo-rischio-idraulico" class="eventLink" target="_blank">
                            <div class="meteoPcDesc"><span>rischio idraulico</span></div>
                        </a>
                        <div class="meteoPcIcon"></div>
                    </div>
                    <div id="<?= $_REQUEST['name_w'] ?>_temporali" class="meteoPcRow">
                        <a href="http://www.regione.toscana.it/allerta-meteo-rischio-temporali" class="eventLink" target="_blank">
                            <div class="meteoPcDesc"><span>rischio temporali</span></div>
                        </a>    
                        <div class="meteoPcIcon"></div>
                    </div>
                    <div id="<?= $_REQUEST['name_w'] ?>_idrogeologico" class="meteoPcRow">
                        <a href="http://www.regione.toscana.it/allerta-meteo-rischio-idrogeologico" class="eventLink" target="_blank">
                            <div class="meteoPcDesc"><span>rischio idrogeologico</span></div>
                        </a>
                        <div class="meteoPcIcon"></div>
                    </div>
                    <div id="<?= $_REQUEST['name_w'] ?>_neve" class="meteoPcRow">
                        <a href="http://www.regione.toscana.it/allerta-meteo-rischio-neve" class="eventLink" target="_blank">
                            <div class="meteoPcDesc"><span>rischio neve</span></div>
                        </a>    
                        <div class="meteoPcIcon"></div>
                    </div>
                    <div id="<?= $_REQUEST['name_w'] ?>_ghiaccio" class="meteoPcRow">
                        <a href="http://www.regione.toscana.it/allerta-meteo-rischio-ghiaccio" class="eventLink" target="_blank">
                            <div class="meteoPcDesc"><span>rischio ghiaccio</span></div>
                        </a>
                        <div class="meteoPcIcon"></div>
                    </div>
                    <div id="<?= $_REQUEST['name_w'] ?>_vento" class="meteoPcRow">
                        <a href="http://www.regione.toscana.it/allerta-meteo-rischio-vento" class="eventLink" target="_blank">
                            <div class="meteoPcDesc"><span>rischio vento</span></div>
                        </a>
                        <div class="meteoPcIcon"></div>
                    </div>
                    <div id="<?= $_REQUEST['name_w'] ?>_mare" class="meteoPcRow">
                        <a href="http://www.regione.toscana.it/allerta-meteo-rischio-mareggiate" class="eventLink" target="_blank">
                            <div class="meteoPcDesc"><span>rischio mareggiate</span></div>
                        </a>
                        <div class="meteoPcIcon"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>	
</div> 
