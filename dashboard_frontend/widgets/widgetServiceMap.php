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
    $(document).ready(function iframe(firstLoad) 
    {
        $('#<?= $_GET['name'] ?>_desc').width('87%');
        $('#<?= $_GET['name'] ?>_desc').html('<span><a id ="info_modal" href="#" class="info_source"><img id="source_<?= $_GET['name'] ?>" src="../management/img/info.png" class="source_button"></a><div id="<?= $_GET['name'] ?>_desc_text" class="desc_text" title="<?= preg_replace('/_/', ' ', $_GET['title']) ?>"><?= preg_replace('/_/', ' ', $_GET['title']) ?></div></span>');
        $('#<?= $_GET['name'] ?>_desc_text').css("width", "90%");
        $('#<?= $_GET['name'] ?>_desc_text').css("height", "100%");
        $("#<?= $_GET['name'] ?>_loading").css("background-color", '<?= $_GET['color'] ?>');
        var height = parseInt($("#<?= $_GET['name'] ?>_div").prop("offsetHeight") - 25);
        $("#<?= $_GET['name'] ?>_map_content").css("height", height);
        $('#<?= $_GET['name'] ?>_loading').css("height", height);
        if(firstLoad != false)
        {
            $('#<?= $_GET['name'] ?>_loading').css("display", "block");
        }
        
        var colore_frame = "<?= $_GET['frame_color'] ?>";
        var nome_wid = "<?= $_GET['name'] ?>_div";
        $("#<?= $_GET['name'] ?>_div").css({'background-color':colore_frame});
        
        var mapQuery = null;
        $.ajax({
            url: "../widgets/getServiceQuery.php",
            type: "GET",
            data: {"nomeMetrica": ["<?= $_GET['metric'] ?>"]},
            async: true,
            dataType: 'json',
            success: function (msg) 
            {
                var query = msg.param.query;
                if(query !== null)
                {
                    if(firstLoad != false)
                    {
                        $('#<?= $_GET['name'] ?>_loading').css("display", "none");
                        $('#<?= $_GET['name'] ?>_content').css("display", "block");
                    }
                    mapQuery = query;
                    $('#<?= $_GET['name'] ?>_map_content').css("display", "block"); 
                    $('#<?= $_GET['name'] ?>_map_content').attr('src', mapQuery);    
                }
                else
                {
                   //TBD
                }
            },
                    
            error: function (jqXHR, textStatus, errorThrow) {
                alert(errorThrow);
            }
        });
        
        $('#source_<?= $_GET['name'] ?>').on('click', function () 
        {
            $('#dialog_<?= $_GET['name'] ?>').show();
        });
        $('#close_popup_<?= $_GET['name'] ?>').on('click', function () 
        {
            $('#dialog_<?= $_GET['name'] ?>').hide();
        });
    });//Fine document ready
    
</script>
<div class="widget" id="<?= $_GET['name'] ?>_div">
    <div class='ui-widget-content'>        
        <div id="<?= $_GET['name'] ?>_desc" class="desc"></div><div class="icons-modify-widget"><a class="icon-cfg-widget" href="#"><span class="glyphicon glyphicon-cog glyphicon-modify-widget" aria-hidden="true"></span></a><a class="icon-remove-widget" href="#"><span class="glyphicon glyphicon-remove glyphicon-modify-widget" aria-hidden="true"></span></a></div>
        <div id="<?= $_GET['name'] ?>_loading" class="loadingDiv">
            <div class="loadingTextDiv">
                <p>Loading data, please wait</p>
            </div>
            <div class ="loadingIconDiv">
                <i class='fa fa-spinner fa-spin'></i>
            </div>
        </div>
        <iframe id="<?= $_GET['name'] ?>_map_content" class="map"></iframe>
    </div>	
</div> 