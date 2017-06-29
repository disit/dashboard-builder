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
    var gradients = {
      GREEN: "linear-gradient(to right, #E2FF8C, #99CC00)",
      ORANGE: "linear-gradient(to right, #FFD382, #FFA500)",
      YELLOW: "linear-gradient(to right, #FAFAAF, #FFFF00)",
      RED: "linear-gradient(to right, #FF7878, #FF0000)"
    };
    
    $(document).ready(function <?= $_GET['name'] ?>(firstLoad)  
    {
        var contentHeight, permalink, idWidget, idDash, idraulicoSrc, idraulicoLoc, temporaliSrc, temporaliLoc, idrogeologicoSrc,
        idrogeologicoLoc, neveSrc, neveLoc, ghiaccioSrc, ghiaccioLoc, ventoSrc, ventoLoc, mareSrc, mareLoc, maxAlarmDeg, descW, 
        sizeRowsWidget, styleParameters, genTabFontSize, genTabFontColor, meteoTabFontSize = null;
        var height = parseInt($("#<?= $_GET['name'] ?>_div").prop("offsetHeight") - 75);
        var loadingFontDim = 13; 
        var loadingIconDim = 20;
        var alarmDegs = new Array();
        var defaultTab = parseInt("<?= $_GET['defaultTab'] ?>");
        var name = "<?= $_REQUEST['name'] ?>";
        var divLinkContainer = $('#<?= $_GET['name'] ?>_logoPc');
        var linkElement = $('#<?= $_GET['name'] ?>_link_w');
        
        var widgetProperties = getWidgetProperties(name);
        if(jQuery.parseJSON(widgetProperties.param.styleParameters !== null))
        {
            styleParameters = jQuery.parseJSON(widgetProperties.param.styleParameters);
            genTabFontSize = styleParameters.genTabFontSize;
            meteoTabFontSize = styleParameters.meteoTabFontSize;
            genTabFontColor = styleParameters.genTabFontColor;
        }
        
        $("#<?= $_GET['name'] ?>_logo").css("background-color", '<?= $_GET['frame_color'] ?>');
        $('#<?= $_GET['name'] ?>_loading').css("height", height+"px");
        $('#<?= $_GET['name'] ?>_loading p').css("font-size", loadingFontDim + "px");
        $('#<?= $_GET['name'] ?>_loading i').css("font-size", loadingIconDim + "px");
        $("#<?= $_GET['name'] ?>_loading").css("background-color", '<?= $_GET['color'] ?>');
        $("#<?= $_GET['name'] ?>_content").css("background-color", '<?= $_GET['color'] ?>');
        
        if(firstLoad !== false)
        {
            $('#<?= $_GET['name'] ?>_loading').css("display", "block");
        }
        
        $("#<?= $_GET['name'] ?>_content").css("height", height + "px");
        var contentHeight = parseInt($('#<?= $_GET['name'] ?>_div').prop("offsetHeight") - 75 - 20);
        $("#<?= $_GET['name'] ?>_general").css("height", contentHeight + "px");
        $("#<?= $_GET['name'] ?>_meteo").css("height", contentHeight + "px");
        
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
        //Fine definizioni di funzione
        
        
        $('#<?= $_GET['name'] ?>_content').on('slid.bs.carousel', function (ev) 
        {
            var id = ev.relatedTarget.id;
            switch(id)
            {
                case "<?= $_GET['name'] ?>_general":
                        $("#<?= $_GET['name'] ?>_generalLi").attr("class", "active");
                        $("#<?= $_GET['name'] ?>_meteoLi").attr("class", "");       
                        break;

                case "<?= $_GET['name'] ?>_meteo":
                        $("#<?= $_GET['name'] ?>_generalLi").attr("class", "");
                        $("#<?= $_GET['name'] ?>_meteoLi").attr("class", "active");       
                        break;    
            }
        });

        $("#<?= $_GET['name'] ?>_generalLi").click(function() 
        {
            $("#<?= $_GET['name'] ?>_generalLi").attr("class", "active");
            $("#<?= $_GET['name'] ?>_meteoLi").attr("class", "");
            $("#<?= $_GET['name'] ?>_content").carousel(0);
        });

        $("#<?= $_GET['name'] ?>_meteoLi").click(function() 
        {
            $("#<?= $_GET['name'] ?>_generalLi").attr("class", "");
            $("#<?= $_GET['name'] ?>_meteoLi").attr("class", "active");
            $("#<?= $_GET['name'] ?>_content").carousel(1);
        });
        
        var contentH = $("#<?= $_GET['name'] ?>_div").prop("offsetHeight") - 75 - 20 - 32;
        
        var rowHPx = Math.floor(parseInt(contentH - 6) / 7); 
        var iconDim = rowHPx;
        descW = parseInt($('#<?= $_GET['name'] ?>_div').width() - iconDim - 4);
        var descWPerc = Math.floor(descW * 100 / $('#<?= $_GET['name'] ?>_div').width());
        
        $("#<?= $_GET['name'] ?>_meteo .meteoPcRow").css("height", rowHPx + "px");
        $("#<?= $_GET['name'] ?>_meteo .meteoPcRow").css("margin-bottom", 1 + "px");
        $("#<?= $_GET['name'] ?>_meteo .meteoPcRowLast").css("height", rowHPx + "px");
        $("#<?= $_GET['name'] ?>_meteo .meteoPcRowLast").css("margin-bottom", 0 + "px");
        $("#<?= $_GET['name'] ?>_meteo .meteoPcRow").css("margin-bottom", 1 + "px");
        $("#<?= $_GET['name'] ?>_meteo .meteoPcDesc").css("width", descWPerc + "%");
        $("#<?= $_GET['name'] ?>_meteo .meteoPcDesc").css("margin-right", 1 + "px");
        $("#<?= $_GET['name'] ?>_meteo .meteoPcDesc").css("font-size", meteoTabFontSize + "px");
        $("#<?= $_GET['name'] ?>_meteo .meteoPcIcon").css("width", iconDim + "px");
        
        $('#<?= $_GET['name'] ?>_meteo').css("overflow", "auto");
        
        addLink("<?= $_GET['name'] ?>", permalink, linkElement, divLinkContainer);

        $('#source_<?= $_GET['name'] ?>').on('click', function () 
        {
            $('#dialog_<?= $_GET['name'] ?>').show();
        });

        $('#close_popup_<?= $_GET['name'] ?>').on('click', function () 
        {
            $('#dialog_<?= $_GET['name'] ?>').hide();
        });
        
        var counter = <?= $_GET['freq'] ?>;
        var countdown = setInterval(function () 
        {
            var ref = "#ProtezioneCivile_" + idDash + "_widgetProtezioneCivile" + idWidget + "_div .pcCountdown"; 
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
            if (counter === 0) 
            {
                $(ref).text(counter + "s");
                $("#<?= $_GET['name'] ?>_content").off();
                $("#<?= $_GET['name'] ?>_meteo .meteoPcIcon").html("");
                switch(maxAlarmDeg)
                {
                    case gradients.YELLOW:
                        $("#<?= $_GET['name'] ?>_alarmDivPc").removeClass("alarmDivPcActiveYellow");
                        $("#<?= $_GET['name'] ?>_alarmDivPc").addClass("alarmDivPc");
                        break;

                    case gradients.ORANGE:
                        $("#<?= $_GET['name'] ?>_alarmDivPc").removeClass("alarmDivPcActiveOrange");
                        $("#<?= $_GET['name'] ?>_alarmDivPc").addClass("alarmDivPc");
                        break;

                    case gradients.RED:
                        $("#<?= $_GET['name'] ?>_alarmDivPc").removeClass("alarmDivPcActiveRed");
                        $("#<?= $_GET['name'] ?>_alarmDivPc").addClass("alarmDivPc");
                        break;

                    default:
                        break;
                }
                $("#<?= $_GET['name'] ?>_general").html("");
                
                clearInterval(countdown);
                setTimeout(<?= $_GET['name'] ?>(false), 1000);
            }
        }, 1000);
        
         $.ajax({//Inizio AJAX getParametersWidgets.php
            url: "../widgets/getParametersWidgets.php",
            type: "GET",
            data: {"nomeWidget": ["<?= $_GET['name'] ?>"]},
            async: false,
            dataType: 'json',
            success: function (msg) 
            {
                var moveDownRef = null;
                var contentSel = null; 
                idWidget = null;
                idDash = null;
                name = null;
                
                var counter = <?= $_GET['freq'] ?>;
                
                if (msg !== null)
                {
                    udm = msg.param.udm;
                    sizeRowsWidget = parseInt(msg.param.size_rows);
                    idWidget = msg.param.Id;
                    idDash = msg.param.id_dashboard;
                }
                
                $.ajax({
                    url: "../management/iframeProxy.php",
                    type: "GET",
                    async: false,
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
                        $("#<?= $_GET['name'] ?>_idraulico .meteoPcDesc").css("background", grad);
                        $("#<?= $_GET['name'] ?>_idraulico .meteoPcIcon").append(newImg);
                        
                        index = temporaliSrc.indexOf("temporali");
                        temporaliLoc = "../img/meteoPc/" + temporaliSrc.substring(index);
                        newImg = $("<img style='width: 100%; height: 100%'>");
                        grad = getGradient(temporaliLoc);
                        alarmDegs.push(grad);
                        newImg.attr("src", temporaliLoc);
                        $("#<?= $_GET['name'] ?>_temporali .meteoPcDesc").css("background", grad);
                        $("#<?= $_GET['name'] ?>_temporali .meteoPcIcon").append(newImg);
                        
                        index = idrogeologicoSrc.indexOf("idrogeologico");
                        idrogeologicoLoc = "../img/meteoPc/" + idrogeologicoSrc.substring(index);
                        newImg = $("<img style='width: 100%; height: 100%'>");
                        grad = getGradient(idrogeologicoLoc);
                        alarmDegs.push(grad);
                        newImg.attr("src", idrogeologicoLoc);
                        $("#<?= $_GET['name'] ?>_idrogeologico .meteoPcDesc").css("background", grad);
                        $("#<?= $_GET['name'] ?>_idrogeologico .meteoPcIcon").append(newImg);
                        
                        index = neveSrc.indexOf("neve");
                        neveLoc = "../img/meteoPc/" + neveSrc.substring(index);
                        newImg = $("<img style='width: 100%; height: 100%'>");
                        grad = getGradient(neveLoc);
                        alarmDegs.push(grad);
                        newImg.attr("src", neveLoc);
                        $("#<?= $_GET['name'] ?>_neve .meteoPcDesc").css("background", grad);
                        $("#<?= $_GET['name'] ?>_neve .meteoPcIcon").append(newImg);
                        
                        index = ghiaccioSrc.indexOf("ghiaccio");
                        ghiaccioLoc = "../img/meteoPc/" + ghiaccioSrc.substring(index);
                        newImg = $("<img style='width: 100%; height: 100%'>");
                        grad = getGradient(ghiaccioLoc);
                        alarmDegs.push(grad);
                        newImg.attr("src", ghiaccioLoc);
                        $("#<?= $_GET['name'] ?>_ghiaccio .meteoPcDesc").css("background", grad);
                        $("#<?= $_GET['name'] ?>_ghiaccio .meteoPcIcon").append(newImg);
                        
                        index = ventoSrc.indexOf("vento");
                        ventoLoc = "../img/meteoPc/" + ventoSrc.substring(index);
                        newImg = $("<img style='width: 100%; height: 100%'>");
                        grad = getGradient(ventoLoc);
                        alarmDegs.push(grad);
                        newImg.attr("src", ventoLoc);
                        $("#<?= $_GET['name'] ?>_vento .meteoPcDesc").css("background", grad);
                        $("#<?= $_GET['name'] ?>_vento .meteoPcIcon").append(newImg);
                        
                        index = mareSrc.indexOf("mare");
                        mareLoc = "../img/meteoPc/" + mareSrc.substring(index);
                        newImg = $("<img style='width: 100%; height: 100%'>");
                        grad = getGradient(mareLoc);
                        alarmDegs.push(grad);
                        newImg.attr("src", mareLoc);
                        $("#<?= $_GET['name'] ?>_mare .meteoPcDesc").css("background", grad);
                        $("#<?= $_GET['name'] ?>_mare .meteoPcIcon").append(newImg);
                        
                        $("<?= $_GET['name'] ?>_meteo").add("<img src='" + idraulicoSrc + "'/>");
                        $("<?= $_GET['name'] ?>_meteo").add("<img src='" + temporaliSrc + "'/>");
                        $("<?= $_GET['name'] ?>_meteo").add("<img src='" + idrogeologicoSrc + "'/>");
                        $("<?= $_GET['name'] ?>_meteo").add("<img src='" + neveSrc + "'/>");
                        $("<?= $_GET['name'] ?>_meteo").add("<img src='" + ghiaccioSrc + "'/>");
                        $("<?= $_GET['name'] ?>_meteo").add("<img src='" + ventoSrc + "'/>");
                        $("<?= $_GET['name'] ?>_meteo").add("<img src='" + mareSrc + "'/>");
                        
                        maxAlarmDeg = getMaxAlarmGrade();
                        
                        $.ajax({
                            url: "http://protezionecivile.comune.fi.it/?cat=5&feed=json",
                            type: "GET",
                            async: false,
                            dataType: 'jsonp',
                            success: function (msg) 
                            {
                                if(msg === null)
                                {
                                    if(firstLoad !== false)
                                    {
                                        $('#<?= $_GET['name'] ?>_loading').css("display", "none");
                                        $('#<?= $_GET['name'] ?>_content').css("display", "block");
                                    }
                                    $('#<?= $_GET['name'] ?>_content').html("<p style='text-align: center;'>Nessun dato disponibile</p>");
                                }
                                else
                                {
                                    permalink = msg[0].permalink;
                                    if((permalink === "null") || (permalink === null) || (permalink === ""))
                                    {
                                        permalink = null;
                                    }
                                    var content = $(msg[0].content);
                                    
                                    $("#<?= $_GET['name'] ?>_permalink").attr("href", permalink);
                                    
                                    $("#<?= $_GET['name'] ?>_general").html(content);
                                    $("#<?= $_GET['name'] ?>_general").css("color", genTabFontColor);
                                    $("#<?= $_GET['name'] ?>_general").css("font-size", genTabFontSize + "px");
                                    $("#<?= $_GET['name'] ?>_general *").css("color", genTabFontColor);
                                    $("#<?= $_GET['name'] ?>_general *").css("font-size", genTabFontSize + "px");
                                    
                                    $("#<?= $_GET['name'] ?>_general").find("img").eq(0).remove();
                                    $("#<?= $_GET['name'] ?>_general").find("iframe").remove();
                                    
                                    switch(maxAlarmDeg)
                                    {
                                        case gradients.YELLOW:
                                            $("#<?= $_GET['name'] ?>_alarmDivPc").removeClass("alarmDivPc");
                                            $("#<?= $_GET['name'] ?>_alarmDivPc").addClass("alarmDivPcActiveYellow");
                                            break;
                                            
                                        case gradients.ORANGE:
                                            $("#<?= $_GET['name'] ?>_alarmDivPc").removeClass("alarmDivPc");
                                            $("#<?= $_GET['name'] ?>_alarmDivPc").addClass("alarmDivPcActiveOrange");
                                            break;
                                            
                                        case gradients.RED:
                                            $("#<?= $_GET['name'] ?>_alarmDivPc").removeClass("alarmDivPc");
                                            $("#<?= $_GET['name'] ?>_alarmDivPc").addClass("alarmDivPcActiveRed");
                                            break;
                                            
                                        default:
                                            break;
                                    }
                                }  
                            },
                            error: function()
                            {
                                $("#<?= $_GET['name'] ?>_general").html("Nessun dato disponibile");
                            }
                        });
                        
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        alert('An error occurred... Look at the console (F12 or Ctrl+Shift+I, Console tab) for more information!');

                        $('#page-wrapper').html('<p>status code: ' + jqXHR.status + '</p><p>errorThrown: ' + errorThrown + '</p><p>jqXHR.responseText:</p><div>' + jqXHR.responseText + '</div>');
                         console.log('jqXHR:');
                         console.log(jqXHR);
                         console.log('textStatus:');
                         console.log(textStatus);
                         console.log('errorThrown:');
                         console.log(errorThrown);
                    }
                });
                if(firstLoad !== false)
                {
                    $('#<?= $_GET['name'] ?>_loading').css("display", "none");
                    $("#<?= $_GET['name'] ?>_content").css("display", "block");
                }
                
                if(defaultTab !== -1)
                {
                    $("#<?= $_GET['name'] ?>_content").carousel(defaultTab);
                    $('#<?= $_GET['name'] ?>_content').addClass('slide');
                }
                else
                {
                    $('#<?= $_GET['name'] ?>_content').addClass('slide');
                    $('#<?= $_GET['name'] ?>_content').attr('data-interval', 4000);
                    $('#<?= $_GET['name'] ?>_content').carousel('cycle');
                }
            }
        });   
});//Fine document ready
</script>

<div class="widget" id="<?= $_GET['name'] ?>_div">
    <div class='ui-widget-content'>
        <div id='<?= $_GET['name'] ?>_logo' class="pcLogosContainer">
            <div id='<?= $_GET['name'] ?>_alarmDivPc' class="alarmDivPc">
                <div id="<?= $_GET['name'] ?>_info" class="pcInfoContainer">
                <!--<a id ="info_modal" href="#" class="info_source">
                    <img id="source_<?= $_GET['name'] ?>" src="../management/img/info.png" class="source_button">
                </a>-->
                  <a id ="info_modal" href="#" class="info_source"><i id="source_<?= $_GET['name'] ?>" class="source_button fa fa-info-circle" style="font-size: 22px"></i></a>
                </div>
                <div id="<?= $_GET['name'] ?>_logoPc" class="logoPc">
                    <a id="<?= $_GET['name'] ?>_permalink" href="about:blank" target="_blank"><img src="../img/protezioneCivile.png"></a>
                </div>

                <div id="<?= $_GET['name'] ?>_iconsModifyWidget" class="iconsModifyPcWidget">
                    <a class="icon-cfg-widget" href="#">
                        <span class="glyphicon glyphicon-cog glyphicon-modify-widget" aria-hidden="true"></span>
                    </a>
                    <a class="icon-remove-widget" href="#">
                        <span class="glyphicon glyphicon-remove glyphicon-modify-widget" aria-hidden="true"></span>
                    </a>
                    <div id="countdown_<?= $_GET['name'] ?>" class="pcCountdown"></div>
                </div>
                <div id="<?= $_GET['name'] ?>_pcCountdownContainer" class="pcCountdownContainer">
                    <div id="countdown_<?= $_GET['name'] ?>" class="pcCountdown"></div>
                </div>
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
        
        <div id='<?= $_GET['name'] ?>_content' class="content pcContainer carousel" data-interval="false" data-pause="hover">
            <ul id="<?= $_GET['name'] ?>_nav_ul" class="nav nav-tabs nav_ul">
                <li role="navigation" id="<?= $_GET['name'] ?>_generalLi" class="active"><a disabled="true" class="atafTab">general</a></li>
                <li role="navigation" id="<?= $_GET['name'] ?>_meteoLi"><a disabled="true" class="atafTab">meteo</a></li>
            </ul>
            <div id="<?= $_GET['name'] ?>_carousel" class="carousel-inner" role="listbox">
                <div id="<?= $_GET['name'] ?>_general" class="item active pcGeneralDiv">
                </div>
                <div id="<?= $_GET['name'] ?>_meteo" class="item">
                    <div id="<?= $_GET['name'] ?>_legendaRow" class="meteoPcLegendaRow">
                        <div id="<?= $_GET['name'] ?>_legendaContainer" class="pcLegendaContainer">
                            <div class="pcLegendaElement pcLegendaElementMarginRight">
                                <div class="pcLegendaNessuno">nullo</div>    
                            </div>
                            <div class="pcLegendaElement pcLegendaElementMarginRight">
                                <div class="pcLegendaBasso">basso</div>    
                            </div>
                            <div class="pcLegendaElement pcLegendaElementMarginRight">
                                <div class="pcLegendaMedio">medio</div>     
                            </div>
                            <div class="pcLegendaElement">
                                <div class="pcLegendaAlto">alto</div>     
                            </div>      
                        </div>
                        
                    </div>
                    <div id="<?= $_GET['name'] ?>_idraulico" class="meteoPcRow">
                        <a href="http://www.regione.toscana.it/allerta-meteo-rischio-idraulico" class="eventLink" target="_blank">
                            <div class="meteoPcDesc">rischio idraulico</div>
                        </a>
                        <div class="meteoPcIcon"></div>
                    </div>
                    <div id="<?= $_GET['name'] ?>_temporali" class="meteoPcRow">
                        <a href="http://www.regione.toscana.it/allerta-meteo-rischio-temporali" class="eventLink" target="_blank">
                            <div class="meteoPcDesc">rischio temporali</div>
                        </a>    
                        <div class="meteoPcIcon"></div>
                    </div>
                    <div id="<?= $_GET['name'] ?>_idrogeologico" class="meteoPcRow">
                        <a href="http://www.regione.toscana.it/allerta-meteo-rischio-idrogeologico" class="eventLink" target="_blank">
                            <div class="meteoPcDesc">rischio idrogeologico</div>
                        </a>
                        <div class="meteoPcIcon"></div>
                    </div>
                    <div id="<?= $_GET['name'] ?>_neve" class="meteoPcRow">
                        <a href="http://www.regione.toscana.it/allerta-meteo-rischio-neve" class="eventLink" target="_blank">
                            <div class="meteoPcDesc">rischio neve</div>
                        </a>    
                        <div class="meteoPcIcon"></div>
                    </div>
                    <div id="<?= $_GET['name'] ?>_ghiaccio" class="meteoPcRow">
                        <a href="http://www.regione.toscana.it/allerta-meteo-rischio-ghiaccio" class="eventLink" target="_blank">
                            <div class="meteoPcDesc">rischio ghiaccio</div>
                        </a>
                        <div class="meteoPcIcon"></div>
                    </div>
                    <div id="<?= $_GET['name'] ?>_vento" class="meteoPcRow">
                        <a href="http://www.regione.toscana.it/allerta-meteo-rischio-vento" class="eventLink" target="_blank">
                            <div class="meteoPcDesc">rischio vento</div>
                        </a>
                        <div class="meteoPcIcon"></div>
                    </div>
                    <div id="<?= $_GET['name'] ?>_mare" class="meteoPcRowLast">
                        <a href="http://www.regione.toscana.it/allerta-meteo-rischio-mareggiate" class="eventLink" target="_blank">
                            <div class="meteoPcDesc">rischio mareggiate</div>
                        </a>
                        <div class="meteoPcIcon"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>	
</div> 
