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
    $(document).ready(function <?= $_REQUEST['name_w'] ?>(firstLoad) 
    {
        var titleWidth = null;
        
        $('#<?= $_REQUEST['name_w'] ?>_desc').width('74%');
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
        var hasTimer = "<?= $_REQUEST['hasTimer'] ?>";
        var headerHeight = 25;
        var embedWidget = <?= $_REQUEST['embedWidget'] ?>;
        var embedWidgetPolicy = '<?= $_REQUEST['embedWidgetPolicy'] ?>';
        var showTitle = "<?= $_REQUEST['showTitle'] ?>";
        var showHeader, countdown = null;
        if(((embedWidget === true)&&(embedWidgetPolicy === 'auto'))||((embedWidget === true)&&(embedWidgetPolicy === 'manual')&&(showTitle === "no"))||((embedWidget === false)&&(showTitle === "no")))
        {
            var height = parseInt($("#<?= $_REQUEST['name_w'] ?>_div").prop("offsetHeight"));
            $('#<?= $_REQUEST['name_w'] ?>_header').hide();
        }
        else
        {
            //TBD - Vanno gestiti i futuri casi di policy manuale e show/hide header a scelta utente
            var height = parseInt($("#<?= $_REQUEST['name_w'] ?>_div").prop("offsetHeight") - headerHeight);
            $('#<?= $_REQUEST['name_w'] ?>_header').show();
        }
        
        if(hostFile === "config")
        {
            titleWidth = parseInt(parseInt($("#<?= $_REQUEST['name_w'] ?>_div").width() - 25 - 50 - 25 - 2));
        }
        else
        {
            $("#<?= $_REQUEST['name_w'] ?>_buttonsDiv").css("display", "none");
            titleWidth = parseInt(parseInt($("#<?= $_REQUEST['name_w'] ?>_div").width() - 25 - 25 - 2));
        }
        
        $("#<?= $_REQUEST['name_w'] ?>_titleDiv").css("width", titleWidth + "px");
        $("#<?= $_REQUEST['name_w'] ?>_titleDiv").css("color", "<?= $_REQUEST['headerFontColor'] ?>");
        //$("#<?= $_REQUEST['name_w'] ?>_titleDiv").html("<?= preg_replace($titlePatterns, $replacements, $title) ?>");   
        $("#<?= $_REQUEST['name_w'] ?>_countdownDiv").css("color", "<?= $_REQUEST['headerFontColor'] ?>");
        $("#<?= $_REQUEST['name_w'] ?>_loading").css("background-color", '<?= $_REQUEST['color_w'] ?>');
        
        var loadingFontDim = 13;
        var loadingIconDim = 20;
        
        $("#<?= $_REQUEST['name_w'] ?>_loading").css("background-color", '<?= $_REQUEST['color_w'] ?>');
        $('#<?= $_REQUEST['name_w'] ?>_loading').css("height", height+"px");
        $('#<?= $_REQUEST['name_w'] ?>_loading p').css("font-size", loadingFontDim+"px");
        $('#<?= $_REQUEST['name_w'] ?>_loading i').css("font-size", loadingIconDim+"px");
        
        if(firstLoad !== false)
        {
            $('#<?= $_REQUEST['name_w'] ?>_loading').css("display", "block");
        }
        
        $("#table_<?= $_REQUEST['name_w'] ?>").css("height", height);
        
        var circleHeight = null;
        var fontRatio = null;
        var fontRatioSmall = null;
        var fontRatioLines = null;
        var fontRatioTitle = null;
        var valueHeight = null;
        var descHeight = null;
        var paddingLines = null;
        var carHeight = null;
        var tabIndex = 0;
        var alarmSet = false;
        
        var colore_frame = "<?= $_REQUEST['frame_color_w'] ?>";
        var nome_wid = "<?= $_REQUEST['name_w'] ?>_div";
        var defaultTab = parseInt("<?= $_REQUEST['defaultTab'] ?>");
        $("#<?= $_REQUEST['name_w'] ?>_div").css({'background-color':colore_frame});
        
        var url = "<?= $_REQUEST['link_w'] ?>";
        if(url === "null")
        {
            url = null;
        }
        var divChartContainer = $('#table_<?= $_REQUEST['name_w'] ?>');
        var linkElement = $('#<?= $_REQUEST['name_w'] ?>_link_w');
        
        
        $.ajax({//Inizio AJAX getParametersWidgets.php
            url: "../widgets/getParametersWidgets.php",
            type: "GET",
            data: {"nomeWidget": ["<?= $_REQUEST['name_w'] ?>"]},
            async: true,
            dataType: 'json',
            success: function (msg) 
            {
                var sizeRowsWidget = parseInt(msg.param.size_rows);
                
                $.ajax({
                    url: "../widgets/getDataMetrics.php",
                    data: {"IdMisura": ["Bus_State_Lines"]},
                    type: "GET",
                    async: true,
                    dataType: 'json',
                    success: function (msg) 
                    {
                        carHeight = height - 23;
                        $("#<?= $_REQUEST['name_w'] ?>_carousel").css("height", carHeight);
                                
                        circleHeight = parseInt(carHeight*0.78);
                        $("#<?= $_REQUEST['name_w'] ?>_ataf_intime").css("width", circleHeight);
                        $("#<?= $_REQUEST['name_w'] ?>_ataf_intime").css("height", circleHeight);
                        $("#<?= $_REQUEST['name_w'] ?>_ataf_early").css("width", circleHeight);
                        $("#<?= $_REQUEST['name_w'] ?>_ataf_early").css("height", circleHeight);
                        $("#<?= $_REQUEST['name_w'] ?>_ataf_late").css("width", circleHeight);
                        $("#<?= $_REQUEST['name_w'] ?>_ataf_late").css("height", circleHeight);
                        
                        valueHeight = parseInt(circleHeight*0.42);
                        descHeight = parseInt(circleHeight*0.25);
                        $("#measure_<?= $_REQUEST['name_w'] ?>_ataf_intime_value_p").css("height", valueHeight);
                        $("#measure_<?= $_REQUEST['name_w'] ?>_ataf_intime_desc_p").css("height", descHeight);
                        $("#measure_<?= $_REQUEST['name_w'] ?>_ataf_early_value_p").css("height", valueHeight);
                        $("#measure_<?= $_REQUEST['name_w'] ?>_ataf_early_desc_p").css("height", descHeight);
                        $("#measure_<?= $_REQUEST['name_w'] ?>_ataf_late_value_p").css("height", valueHeight);
                        $("#measure_<?= $_REQUEST['name_w'] ?>_ataf_late_desc_p").css("height", descHeight);
                        
                        var threshold = parseInt(msg.data[0].commit.author.threshold);
                        var thresholdEval = msg.data[0].commit.author.thresholdEval;
                        
                        //Fattore di ingrandimento font calcolato sull'altezza in righe, base 4.
                        fontRatio = parseInt((sizeRowsWidget / 4)*90);
                        fontRatioSmall = parseInt((fontRatio / 100)*40);
                        fontRatioTitle = parseInt((fontRatio / 100)*60);
                        fontRatioUpdate = parseInt((sizeRowsWidget / 4)*45);
                        fontRatioStateIntro = parseInt((sizeRowsWidget / 4)*60);
                        fontRatio = fontRatio.toString() + "%";
                        fontRatioSmall = fontRatioSmall.toString() + "%";
                        fontRatioTitle = fontRatioTitle.toString() + "%";
                        fontRatioUpdate = fontRatioUpdate.toString() + "%";
                        fontRatioStateIntro = fontRatioStateIntro.toString() + "%";
                        $("#measure_<?= $_REQUEST['name_w'] ?>_ataf_intime_value_p").css("font-size", fontRatio);
                        $("#measure_<?= $_REQUEST['name_w'] ?>_ataf_early_value_p").css("font-size", fontRatio);
                        $("#measure_<?= $_REQUEST['name_w'] ?>_ataf_late_value_p").css("font-size", fontRatio);
                        $("#measure_<?= $_REQUEST['name_w'] ?>_ataf_intime_desc_p").css("font-size", fontRatioSmall);
                        $("#measure_<?= $_REQUEST['name_w'] ?>_ataf_early_desc_p").css("font-size", fontRatioSmall);
                        $("#measure_<?= $_REQUEST['name_w'] ?>_ataf_late_desc_p").css("font-size", fontRatioSmall);
                        $("#<?= $_REQUEST['name_w'] ?>_date_update").css("font-size", fontRatioUpdate);
                        
                        var fontRatioNav = "55%";
                        $("#<?= $_REQUEST['name_w'] ?>_nav_ul").css("font-size", fontRatioNav);
                        
                        $("#<?= $_REQUEST['name_w'] ?>_nav_ul a").on("click", function(event){
                            event.preventDefault();
                        });
                        
                        if(firstLoad !== false)
                        {
                            $('#<?= $_REQUEST['name_w'] ?>_loading').css("display", "none");
                            $('#table_<?= $_REQUEST['name_w'] ?>').css("display", "block");
                        }

                        var liHeight = $("#<?= $_REQUEST['name_w'] ?>_nav_service_state_li").height();
                        var paneHeight = height - liHeight;
                        var linesContentHeight = parseInt(paneHeight*0.8);
                        var linesFillerHeight = parseInt(paneHeight*0.1);
                        
                        var valPaneHeight = $("#table_<?= $_REQUEST['name_w'] ?>").height();
                        var valHeight = $("#measure_<?= $_REQUEST['name_w'] ?>_intime").height();
                        var valMargin = parseInt((valPaneHeight*0.25 - 23)/2);
                        var valueMargin = parseInt((sizeRowsWidget / 4)*20);
                        $("#measure_<?= $_REQUEST['name_w'] ?>_ataf_intime_value_div").css("margin-top", valueMargin);
                        $("#measure_<?= $_REQUEST['name_w'] ?>_ataf_late_value_div").css("margin-top", valueMargin);
                        $("#measure_<?= $_REQUEST['name_w'] ?>_ataf_early_value_div").css("margin-top", valueMargin);
                        var lastUpdateHeight = $("#table_<?= $_REQUEST['name_w'] ?>").height() - 23;
                        lastUpdateHeight = lastUpdateHeight + "px";
                        $("#<?= $_REQUEST['name_w'] ?>_date_update").css("height", lastUpdateHeight);
                        
                        var date_agg = msg.data[0].commit.author.computationDate;
                        $("#<?= $_REQUEST['name_w'] ?>_date_update_content").html("ULTIMO AGGIORNAMENTO:<br/>" + date_agg);
                        
                        
                        $("#<?= $_REQUEST['name_w'] ?>_lines_container").css("height", linesContentHeight + "px");
                        $("#<?= $_REQUEST['name_w'] ?>_lines_container").css("margin-top", linesFillerHeight + "px");
                        
                        
                        $("#<?= $_REQUEST['name_w'] ?>_nav_service_state_li").click(function() 
                        {
                            $("#<?= $_REQUEST['name_w'] ?>_nav_service_state_li").attr("class", "active ");
                            $("#<?= $_REQUEST['name_w'] ?>_nav_lines_li").attr("class", "");
                            $("#<?= $_REQUEST['name_w'] ?>_nav_data_li").attr("class", "");
                            $("#table_<?= $_REQUEST['name_w'] ?>").carousel(0);
                        });
                        
                        $("#<?= $_REQUEST['name_w'] ?>_nav_lines_li").click(function() 
                        {
                            $("#<?= $_REQUEST['name_w'] ?>_nav_service_state_li").attr("class", "");
                            $("#<?= $_REQUEST['name_w'] ?>_nav_lines_li").attr("class", "active");
                            $("#<?= $_REQUEST['name_w'] ?>_nav_data_li").attr("class", "");
                            $("#table_<?= $_REQUEST['name_w'] ?>").carousel(1);
                        });
                        
                        $("#<?= $_REQUEST['name_w'] ?>_nav_data_li").click(function() 
                        {
                            $("#<?= $_REQUEST['name_w'] ?>_nav_service_state_li").attr("class", "");
                            $("#<?= $_REQUEST['name_w'] ?>_nav_lines_li").attr("class", "");
                            $("#<?= $_REQUEST['name_w'] ?>_nav_data_li").attr("class", "active");
                            $("#table_<?= $_REQUEST['name_w'] ?>").carousel(2);
                        });

                        var valueInOrario = msg.data[0].commit.author.value_perc1;
                        valueInOrario = parseFloat(parseFloat(valueInOrario).toFixed(0));

                        var valueInAnticipo = msg.data[0].commit.author.value_perc2;
                        valueInAnticipo = parseFloat(parseFloat(valueInAnticipo).toFixed(0));

                        var valueInRitardo = msg.data[0].commit.author.value_perc3;
                        valueInRitardo = parseFloat(parseFloat(valueInRitardo).toFixed(0));
                        
                        switch(thresholdEval)
                        {
                            //Allarme attivo se il valore attuale è sotto la soglia
                            case '<':
                                if(valueInRitardo < threshold)
                                {
                                   //Allarme
                                   alarmSet = true;
                                }
                                break;

                            //Allarme attivo se il valore attuale è sopra la soglia
                            case '>':
                                if(valueInRitardo > threshold)
                                {
                                   //Allarme
                                   alarmSet = true;
                                }
                                break;

                            //Allarme attivo se il valore attuale è uguale alla soglia (errore sui float = 0.1%)
                            case '=':
                                if(valueInRitardo <= 0.1)
                                {
                                    //Allarme
                                    alarmSet = true;
                                }
                                break;    

                            //Non gestiamo altri operatori 
                            default:
                                break;
                         }
                         
                         
                        //NON CANCELLARE, VA ADATTATA AL NUOVO HTML DELL'HEADER 
                        if(alarmSet)
                        {
                            $("#<?= $_REQUEST['name_w'] ?>_alarmDiv").removeClass("alarmDiv");
                            $("#<?= $_REQUEST['name_w'] ?>_alarmDiv").addClass("alarmDivActive");
                        }

                        $("#table_<?= $_REQUEST['name_w'] ?>").css({backgroundColor: '<?= $_REQUEST['color_w'] ?>'});
                        $("#<?= $_REQUEST['name_w'] ?>_date_update").css({backgroundColor: '<?= $_REQUEST['color_w'] ?>'});

                        $("#measure_<?= $_REQUEST['name_w'] ?>_ataf_intime_value_p").html(valueInOrario + "%");
                        $("#measure_<?= $_REQUEST['name_w'] ?>_ataf_early_value_p").html(valueInAnticipo + "%");
                        $("#measure_<?= $_REQUEST['name_w'] ?>_ataf_late_value_p").html(valueInRitardo + "%");
                        
                        $('#table_<?= $_REQUEST['name_w'] ?>').on('slid.bs.carousel', function (ev) 
                        {
                            var id = ev.relatedTarget.id;
                            switch(id)
                            {
                                case "<?= $_REQUEST['name_w'] ?>_service":
                                    $("#<?= $_REQUEST['name_w'] ?>_nav_service_state_li").attr("class", "active ");
                                    $("#<?= $_REQUEST['name_w'] ?>_nav_lines_li").attr("class", "");
                                    $("#<?= $_REQUEST['name_w'] ?>_nav_data_li").attr("class", "");        
                                    break;
                            
                                case "<?= $_REQUEST['name_w'] ?>_lines":
                                    $("#<?= $_REQUEST['name_w'] ?>_nav_service_state_li").attr("class", "");
                                    $("#<?= $_REQUEST['name_w'] ?>_nav_lines_li").attr("class", "active");
                                    $("#<?= $_REQUEST['name_w'] ?>_nav_data_li").attr("class", "");        
                                    break;    
                            
                                case "<?= $_REQUEST['name_w'] ?>_date_update" :
                                    $("#<?= $_REQUEST['name_w'] ?>_nav_service_state_li").attr("class", "");
                                    $("#<?= $_REQUEST['name_w'] ?>_nav_lines_li").attr("class", "");
                                    $("#<?= $_REQUEST['name_w'] ?>_nav_data_li").attr("class", "active");        
                                    break;
                            }
                        });
                       
                       addLink("<?= $_REQUEST['name_w'] ?>", url, linkElement, divChartContainer, null);
                        
                        $('#source_<?= $_REQUEST['name_w'] ?>').on('click', function () {
                            $('#dialog_<?= $_REQUEST['name_w'] ?>').show();
                        });
                        
                        $('#close_popup_<?= $_REQUEST['name_w'] ?>').on('click', function () {
                            $('#dialog_<?= $_REQUEST['name_w'] ?>').hide();
                        });
                        
                        if(defaultTab !== -1)
                        {
                            $("#table_<?= $_REQUEST['name_w'] ?>").carousel(defaultTab);
                            $('#table_<?= $_REQUEST['name_w'] ?>').addClass('slide');
                        }
                        else
                        {
                            $('#table_<?= $_REQUEST['name_w'] ?>').addClass('slide');
                            $('#table_<?= $_REQUEST['name_w'] ?>').attr('data-interval', 4000);
                            $('#table_<?= $_REQUEST['name_w'] ?>').carousel('cycle');
                        }

                        var counter = <?= $_REQUEST['frequency_w'] ?>;
                        
                        $("#<?= $_REQUEST['name_w'] ?>").on('customResizeEvent', function(event){
                            <?= $_REQUEST['name_w'] ?>(false);
                        });
                        
                        $("#<?= $_REQUEST['name_w'] ?>").off('updateFrequency');
                        $("#<?= $_REQUEST['name_w'] ?>").on('updateFrequency', function(event){
                            clearInterval(countdown);
                            counter = event.newTimeToReload;
                            countdown = setInterval(function () 
                            {
                                counter--;
                                if(counter > 60) 
                                {
                                    $("#<?= $_REQUEST['name_w'] ?>_countdownDiv").text(Math.floor(counter / 60) + "m");
                                } 
                                else 
                                {
                                    $("#<?= $_REQUEST['name_w'] ?>_countdownDiv").text(counter + "s");
                                }
                                if (counter === 0) 
                                {
                                    $("#<?= $_REQUEST['name_w'] ?>_countdownDiv").text(counter + "s");
                                    $("#<?= $_REQUEST['name_w'] ?>").off('customResizeEvent');
                                    $('#table_<?= $_REQUEST['name_w'] ?>').off();
                                    if(alarmSet)
                                    {
                                        $("#<?= $_REQUEST['name_w'] ?>_alarmDiv").removeClass("alarmDivActive");
                                        $("#<?= $_REQUEST['name_w'] ?>_alarmDiv").addClass("alarmDiv");
                                    } 
                                    clearInterval(countdown);
                                    setTimeout(<?= $_REQUEST['name_w'] ?>(false), 1000);
                                }
                            }, 1000);
                        });
                        
                        countdown = setInterval(function () 
                        {
                            counter--;
                            if(counter > 60) 
                            {
                                $("#<?= $_REQUEST['name_w'] ?>_countdownDiv").text(Math.floor(counter / 60) + "m");
                            } 
                            else 
                            {
                                $("#<?= $_REQUEST['name_w'] ?>_countdownDiv").text(counter + "s");
                            }
                            if (counter === 0) 
                            {
                                $("#<?= $_REQUEST['name_w'] ?>_countdownDiv").text(counter + "s");
                                $("#<?= $_REQUEST['name_w'] ?>").off('customResizeEvent');
                                $('#table_<?= $_REQUEST['name_w'] ?>').off();
                                if(alarmSet)
                                {
                                    $("#<?= $_REQUEST['name_w'] ?>_alarmDiv").removeClass("alarmDivActive");
                                    $("#<?= $_REQUEST['name_w'] ?>_alarmDiv").addClass("alarmDiv");
                                } 
                                clearInterval(countdown);
                                setTimeout(<?= $_REQUEST['name_w'] ?>(false), 1000);
                            }
                        }, 1000);

                    },
                    error: function (dataError) 
                    {
                        JSON.stringify(dataError);
                    }
                });
            }
    });     
});
</script>

<div class="widget" id="<?= $_REQUEST['name_w'] ?>_div">
    <div class='ui-widget-content'>
        <!-- NON CANCELLARE! VA ADATTATA AL NUOVO HTML -->
        <!--<div id='<?= $_REQUEST['name_w'] ?>_alarmDiv' class="alarmDiv">
            <div id="<?= $_REQUEST['name_w'] ?>_desc" class="desc"></div><div class="icons-modify-widget"><a class="icon-cfg-widget" href="#"><span class="glyphicon glyphicon-cog glyphicon-modify-widget" aria-hidden="true"></span></a><a class="icon-remove-widget" href="#"><span class="glyphicon glyphicon-remove glyphicon-modify-widget" aria-hidden="true"></span></a></div><div id="countdown_<?= $_REQUEST['name_w'] ?>" class="countdown"></div> 
        </div>-->
        
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
        <div id="table_<?= $_REQUEST['name_w'] ?>" class="carousel ataf-table-widget" data-interval="false" data-pause="hover">
            <?php include '../widgets/commonModules/widgetDimControls.php'; ?>	
            <ul id="<?= $_REQUEST['name_w'] ?>_nav_ul" class="nav nav-tabs nav_ul">
                <li role="navigation" id="<?= $_REQUEST['name_w'] ?>_nav_service_state_li" class="active"><a disabled="true" class="atafTab">stato</a></li>
                <li role="navigation" id="<?= $_REQUEST['name_w'] ?>_nav_lines_li"><a disabled="true" class="atafTab">linee monitorate</a></li>
                <li role="navigation" id="<?= $_REQUEST['name_w'] ?>_nav_data_li"><a disabled="true" class="atafTab">dati</a></li>
            </ul>
              <!-- Indicators -->
              <!--<ol class="carousel-indicators">
                <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                <li data-target="#myCarousel" data-slide-to="1"></li>
                <li data-target="#myCarousel" data-slide-to="2"></li>
                <li data-target="#myCarousel" data-slide-to="3"></li>
              </ol>-->

            <!-- Wrapper for slides -->
            <div id="<?= $_REQUEST['name_w'] ?>_carousel" class="carousel-inner" role="listbox">
                <div id="<?= $_REQUEST['name_w'] ?>_service" class="item active atafService">
                        <div id="measure_<?= $_REQUEST['name_w'] ?>_intime" class="atafValueContainer">
                            <div id="<?= $_REQUEST['name_w'] ?>_ataf_intime" class="atafValueRoundContainer">
                                <div id="measure_<?= $_REQUEST['name_w'] ?>_ataf_intime_value_div" class="atafValue">
                                    <p id="measure_<?= $_REQUEST['name_w'] ?>_ataf_intime_value_p" class="atafValueP"></p> 
                                </div>
                                <div id="measure_<?= $_REQUEST['name_w'] ?>_ataf_intime_desc_div" class="atafDesc">
                                    <p id="measure_<?= $_REQUEST['name_w'] ?>_ataf_intime_desc_p" class="atafDescP">in orario</p> 
                                </div>
                            </div>
                        </div>
                    
                        <div id="measure_<?= $_REQUEST['name_w'] ?>_early" class="atafValueContainer">
                            <div id="<?= $_REQUEST['name_w'] ?>_ataf_early" class="atafValueRoundContainer">
                                <div id="measure_<?= $_REQUEST['name_w'] ?>_ataf_early_value_div" class="atafValue">
                                    <p id="measure_<?= $_REQUEST['name_w'] ?>_ataf_early_value_p" class="atafValueP"></p> 
                                </div>
                                <div id="measure_<?= $_REQUEST['name_w'] ?>_ataf_early_desc_div" class="atafDesc">
                                    <p id="measure_<?= $_REQUEST['name_w'] ?>_ataf_early_desc_p" class="atafDescP">in anticipo</p>
                                </div>
                            </div>
                        </div>
                    
                        <div id="measure_<?= $_REQUEST['name_w'] ?>_late" class="atafValueContainer">
                            <div id="<?= $_REQUEST['name_w'] ?>_ataf_late" class="atafValueRoundContainer">
                                <div id="measure_<?= $_REQUEST['name_w'] ?>_ataf_late_value_div" class="atafValue">
                                    <p id="measure_<?= $_REQUEST['name_w'] ?>_ataf_late_value_p" class="atafValueP"></p> 
                                </div>
                                <div id="measure_<?= $_REQUEST['name_w'] ?>_ataf_late_desc_div" class="atafDesc">
                                    <p id="measure_<?= $_REQUEST['name_w'] ?>_ataf_late_desc_p" class="atafDescP">in ritardo</p>
                                </div>
                            </div>
                        </div> 
                </div>
                
                <div id="<?= $_REQUEST['name_w'] ?>_lines" class="item">
                    <div class="atafUtility">
                        <div id="<?= $_REQUEST['name_w'] ?>_lines_container" class="atafLinesContainer">
                            <div id="<?= $_REQUEST['name_w'] ?>_line1" class="atafLineSingleContainer">2</div>
                            <div id="<?= $_REQUEST['name_w'] ?>_line2" class="atafLineSingleContainer">12</div>
                            <div id="<?= $_REQUEST['name_w'] ?>_line3" class="atafLineSingleContainer">33</div> 
                        </div>
                    </div>
                </div>
                
                <div id="<?= $_REQUEST['name_w'] ?>_date_update" class="atafLastUpdate item">
                    <!-- Div utilità per far funzionare il carousel con l'allineamento flex-->
                    <div class="atafUtility">
                        <div id="<?= $_REQUEST['name_w'] ?>_date_update_content" class="atafLastUpdateContent"></div>   
                    </div>   
                </div>
            </div>

            <!-- Left and right controls -->
            <!--<a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>-->
            </div>
    </div>
</div>