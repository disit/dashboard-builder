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
    $(document).ready(function <?= $_GET['name'] ?>(firstLoad) 
    {
        $("#<?= $_GET['name'] ?>_loading").css("background-color", '<?= $_GET['color'] ?>');
        height = parseInt($("#<?= $_GET['name'] ?>_div").prop("offsetHeight") - 25);
        $("#<?= $_GET['name'] ?>_statusContent").css("height", height);
        $("#<?= $_GET['name'] ?>_statusContent").css({backgroundColor: '<?= $_GET['color'] ?>'});
        $('#<?= $_GET['name'] ?>_desc').width('70%');
        $('#<?= $_GET['name'] ?>_desc').html('<span><a id ="info_modal" href="#" class="info_source"><img id="source_<?= $_GET['name'] ?>" src="../management/img/info.png" class="source_button"></a><div id="<?= $_GET['name'] ?>_desc_text" class="desc_text" title="<?= preg_replace('/_/', ' ', $_GET['title']) ?>"><?= preg_replace('/_/', ' ', $_GET['title']) ?></div></span>');
        $('#<?= $_GET['name'] ?>_desc_text').css("width", "80%");
        $('#<?= $_GET['name'] ?>_desc_text').css("height", "100%");
        
        var loadingFontDim = 13; 
        var loadingIconDim = 20;
        $('#<?= $_GET['name'] ?>_loading').css("height", height+"px");
        $('#<?= $_GET['name'] ?>_loading p').css("font-size", loadingFontDim+"px");
        $('#<?= $_GET['name'] ?>_loading i').css("font-size", loadingIconDim+"px");
        if(firstLoad != false)
        {
            $('#<?= $_GET['name'] ?>_loading').css("display", "block");
        }
        
        var height = null;
        var sizeRowsWidget = null;
        var fontRatio = null;
        var fontRatioSmall = null;
        var host = null;
        var user = null;
        var pass = null;
        var jobName = null;
        var status = null;
        var date = null;
        
        var colore_frame = "<?= $_GET['frame_color'] ?>";
        var nome_wid = "<?= $_GET['name'] ?>_div";
        $("#<?= $_GET['name'] ?>_div").css({'background-color':colore_frame});
        
        /**
         * This section of script sets widget's external link (only if not null from DB)
         */       
        var link_w = "<?= $_GET['link_w'] ?>";
        var divChartContainer = $('#<?= $_GET['name'] ?>_jobStateContainer');
        var linkElement = $('#<?= $_GET['name'] ?>_link_w');
        

        $.ajax({//Inizio AJAX getParametersWidgets.php
            url: "../widgets/getParametersWidgets.php",
            type: "GET",
            data: {"nomeWidget": ["<?= $_GET['name'] ?>"]},
            async: true,
            dataType: 'json',
            success: function (msg) 
            {
                if (msg != null)
                {
                    var parametri = msg.param.parameters;
                    var contenuto = jQuery.parseJSON(parametri);
                    host = contenuto.host;
                    user = contenuto.user;
                    pass = contenuto.pass;
                    jobName = contenuto.jobName;
                    sizeRowsWidget = parseInt(msg.param.size_rows);
                }
                
                $.ajax({
                    url: "../widgets/getProcessStatus.php",
                    data: {action: "getSingleStatus", host: host, user: user, pass: pass, jobName: jobName},
                    type: "POST",
                    async: true,
                    dataType: 'json',
                    success: function (msg) 
                    {
                        if(msg == null)
                        {
                            $('#<?= $_GET['name'] ?>_statusContent').html("");
                            $("#<?= $_GET['name'] ?>_statusContent").html("<p style='text-align: center; font-size: 18px;'>Nessun dato disponibile</p>");
                        }
                        else
                        {
                            status = msg[0].status;
                            date = msg[0].date;
                            
                            if(firstLoad != false)
                            {
                                $('#<?= $_GET['name'] ?>_loading').css("display", "none");
                                $('#<?= $_GET['name'] ?>_statusContent').css("display", "block");
                            }
                            
                            contentHeight = parseInt($("#<?= $_GET['name'] ?>_statusContent").prop("offsetHeight") - $("#<?= $_GET['name'] ?>_jobName").prop("offsetHeight")) - 8;
                            $("#<?= $_GET['name'] ?>_statusContent a").css("height", contentHeight);
                            $("#<?= $_GET['name'] ?>_jobStateContainer").css("height", contentHeight);
                            
                            //Fattore di ingrandimento font calcolato sull'altezza in righe, base 4.
                            fontRatio = parseInt((sizeRowsWidget / 4)*100);
                            var fontRatioState = parseInt(fontRatio*1.3);
                            var fontRatioDate = parseInt(fontRatio*0.9);
                            var fontRatioIcon = parseInt((sizeRowsWidget / 4)*42);
                            fontRatio = fontRatio.toString() + "%";
                            fontRatioState = fontRatioState.toString() + "%";
                            fontRatioDate = fontRatioDate.toString() + "%";
                            fontRatioIcon = fontRatioIcon.toString() + "px";
                            
                            $("#<?= $_GET['name'] ?>_jobName").css("font-size", fontRatio);
                            $("#<?= $_GET['name'] ?>_jobName").html(contenuto.jobName);
                            
                            switch(status)
                            {
                                case "SUCCESS":
                                    $("#<?= $_GET['name'] ?>_jobStateContainer").attr("class", "statoJobContainerOk");
                                    $("#<?= $_GET['name'] ?>_jobStateIcon").html("<i class='fa fa-check' style='font-size:" + fontRatioIcon + "'></i>");
                                    break;
                                    
                                case "RUNNING":
                                    $("#<?= $_GET['name'] ?>_jobStateContainer").attr("class", "statoJobContainerRunning");
                                    $("#<?= $_GET['name'] ?>_jobStateIcon").html("<i class='fa fa-circle-o-notch fa-spin' style='font-size:" + fontRatioIcon + "'></i>");
                                    break;
                                    
                                case "MISFIRED":
                                    $("#<?= $_GET['name'] ?>_jobStateContainer").attr("class", "statoJobContainerKo");
                                    $("#<?= $_GET['name'] ?>_jobStateIcon").html("<i class='fa fa-close' style='font-size:" + fontRatioIcon + "'></i>");
                                    break;
                                
                                case "FAILED":
                                    $("#<?= $_GET['name'] ?>_jobStateContainer").attr("class", "statoJobContainerKo");
                                    $("#<?= $_GET['name'] ?>_jobStateIcon").html("<i class='fa fa-close' style='font-size:" + fontRatioIcon + "'></i>");
                                    break; 
                            }
                            
                            $("#<?= $_GET['name'] ?>_jobState").css("font-size", fontRatioState);
                            $("#<?= $_GET['name'] ?>_jobState").html(status);
                            $("#<?= $_GET['name'] ?>_jobDate").css("font-size", fontRatioDate);
                            $("#<?= $_GET['name'] ?>_jobDate").html(date);
                            
                            $('#source_<?= $_GET['name'] ?>').on('click', function () 
                            {
                                $('#dialog_<?= $_GET['name'] ?>').show();
                            });
                            $('#close_popup_<?= $_GET['name'] ?>').on('click', function () 
                            {
                                $('#dialog_<?= $_GET['name'] ?>').hide();
                            });
                        }
                        
                        if (link_w.trim()) 
                        {
                            if(linkElement.length === 0)
                            {
                               linkElement = $("<a id='<?= $_GET['name'] ?>_link_w' href='<?= $_GET['link_w'] ?>' target='_blank' class='elementLink2'>");
                               divChartContainer.wrap(linkElement); 
                            }
                        }

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
        <div id='<?= $_GET['name'] ?>_desc' class="desc"></div><div class="icons-modify-widget"><a class="icon-cfg-widget" href="#"><span class="glyphicon glyphicon-cog glyphicon-modify-widget" aria-hidden="true"></span></a><a class="icon-remove-widget" href="#"><span class="glyphicon glyphicon-remove glyphicon-modify-widget" aria-hidden="true"></span></a></div><div id="countdown_<?= $_GET['name'] ?>" class="countdown"></div> 
            <div id="<?= $_GET['name'] ?>_loading" class="loadingDiv">
            <div class="loadingTextDiv">
                <p>Loading data, please wait</p>
            </div>
            <div class ="loadingIconDiv">
                <i class='fa fa-spinner fa-spin'></i>
            </div>
            </div>
            <div id='<?= $_GET['name'] ?>_statusContent' class="content"> <!-- style="border-style: solid; border-color: red; border-width: 3px;" -->
                <div id='<?= $_GET['name'] ?>_jobName'  class="nomeJob"></div> <!-- style="border-style: solid; border-color: orange; border-width: 3px" -->
                <div id='<?= $_GET['name'] ?>_jobStateContainer' style="margin-left: auto; margin-right: auto">
                    <div id='<?= $_GET['name'] ?>_jobStateIcon' class="statoJobIcona"></div>
                    <div id='<?= $_GET['name'] ?>_jobState' class="statoJob"></div>
                    <div id='<?= $_GET['name'] ?>_jobDate' class="dataJob"></div>
                </div>
            </div>
    </div>	
</div> 