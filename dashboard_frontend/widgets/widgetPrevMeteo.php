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

    include('../config.php');
?>
<script type='text/javascript'>
    $(document).ready(function <?= $_GET['name'] ?>(firstLoad) 
    {
        $('#<?= $_GET['name'] ?>_desc').width('83%');  
        $('#<?= $_GET['name'] ?>_desc').html('<span><a id ="info_modal" href="#" class="info_source"><img id="source_<?= $_GET['name'] ?>" src="../management/img/info.png" class="source_button"></a><div id="<?= $_GET['name'] ?>_desc_text" class="desc_text" title="<?= preg_replace('/_/', ' ', $_GET['title']) ?>"><?= preg_replace('/_/', ' ', $_GET['title']) ?></div></span>');
        $("#<?= $_GET['name'] ?>_loading").css("background-color", '<?= $_GET['color'] ?>');
        var loadingFontDim = 13; 
        var loadingIconDim = 20;
        var height = parseInt($("#<?= $_GET['name'] ?>_div").prop("offsetHeight") - 25);
        $('#<?= $_GET['name'] ?>_loading').css("height", height+"px");
        $('#<?= $_GET['name'] ?>_loading p').css("font-size", loadingFontDim+"px");
        $('#<?= $_GET['name'] ?>_loading i').css("font-size", loadingIconDim+"px");
        if(firstLoad != false)
        {
            $('#<?= $_GET['name'] ?>_loading').css("display", "block");
        }
        
        var colore_frame = "<?= $_GET['frame_color'] ?>";
        var nome_wid = "<?= $_GET['name'] ?>_div";
        $("#<?= $_GET['name'] ?>_div").css({'background-color':colore_frame});
        
        var link_w = "<?= $_GET['link_w'] ?>";
        var divChartContainer = $('#<?= $_GET['name'] ?>_content');
        var linkElement = $('#<?= $_GET['name'] ?>_link_w');
        
        $("#<?= $_GET['name'] ?>_content").css("height", height);
        
        $.ajax({
            url: "../widgets/curlProxy.php?url=<?=$internalServiceMapUrlPrefix?>ajax/get-weather.jsp?nomeComune=<?= $_GET['city'] ?>",
            type: "GET",
            async: true,
            dataType: 'json',
            success: function (msg) 
            {
                if(firstLoad != false)
                {
                    $('#<?= $_GET['name'] ?>_loading').css("display", "none");
                    $('#<?= $_GET['name'] ?>_content').css("display", "block");
                }
                
                if(msg.contents.length==0)
                {
                    $('#<?= $_GET['name'] ?>_content').html("<div style='text-align: center; padding-top: 28px; font-size: 18px'>Nessuna previsione meteo disponibile o il comune scelto non &eacute; coperto dal servizio del Consorzio LaMMA della regione Toscana</div>");
                }
                else
                {   
                    var value = msg.contents;
                    value = value.replace(/\/WebAppGrafo\//g, "").replace(/\/ServiceMap\//g, "");
                    $('#<?= $_GET['name'] ?>_content').css({backgroundColor: '<?= $_GET['color'] ?>'}); 
                    $('#<?= $_GET['name'] ?>_content').html(value);
                    
                    $('a[title="Linked Open Graph"]').hide();
                
                    $('#<?= $_GET['name'] ?>_content').find('#meteo_title').hide();
                    $('#<?= $_GET['name'] ?>_content').find('.aggiornamento').hide();
                       
                
                    var last_update_meteo= $('#<?= $_GET['name'] ?>_content').find('.aggiornamento').text();
                    $('#<?= $_GET['name'] ?>_last_update').html(last_update_meteo);
                } 
                
                if (link_w.trim()) 
                {
                    if(linkElement.length == 0)
                    {
                       linkElement = $("<a id='<?= $_GET['name'] ?>_link_w' href='<?= $_GET['link_w'] ?>' target='_blank' class='elementLink2'>");
                       divChartContainer.wrap(linkElement); 
                    }
                }
                
                $('#source_<?= $_GET['name'] ?>').on('click', function () {
                    $('#dialog_<?= $_GET['name'] ?>').show();               
                });
                $('#close_popup_<?= $_GET['name'] ?>').on('click', function () {
                    $('#dialog_<?= $_GET['name'] ?>').hide();
                });

                var counter = <?= $_GET['freq'] ?>;
                var countdown = setInterval(function () {
                    counter--;
                    if (counter > 60){
                        $("#countdown_<?= $_GET['name'] ?>").text(Math.floor(counter/60)+"m");
                    }else{
                        $("#countdown_<?= $_GET['name'] ?>").text(counter+"s");
                    }
                    if (counter === 0) {
                        $("#countdown_<?= $_GET['name'] ?>").text(counter+"s");
                        clearInterval(countdown);
                        setTimeout(<?= $_GET['name'] ?>(false), 1000);
                    }
                }, 1000);
            }
        }); 
    });
</script>
<div class="widget" id="<?= $_GET['name'] ?>_div">
    <div class='ui-widget-content'>
        <div id="<?= $_GET['name'] ?>_desc" class='desc'></div><div class="icons-modify-widget"><a class="icon-cfg-widget" href="#"><span class="glyphicon glyphicon-cog glyphicon-modify-widget" aria-hidden="true"></span></a><a class="icon-remove-widget" href="#"><span class="glyphicon glyphicon-remove glyphicon-modify-widget" aria-hidden="true"></span></a></div><div id="countdown_<?= $_GET['name'] ?>" class="countdown"></div>
        <div id="<?= $_GET['name'] ?>_loading" class="loadingDiv">
            <div class="loadingTextDiv">
                <p>Loading data, please wait</p>
            </div>
            <div class ="loadingIconDiv">
                <i class='fa fa-spinner fa-spin'></i>
            </div>
        </div>
        <div id="<?= $_GET['name'] ?>_content" class="content"></div>
    </div>          
</div>