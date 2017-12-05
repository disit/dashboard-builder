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

include('../config.php');
?>
<script type='text/javascript'>
    $(document).ready(function iframe_ataf() 
    {
        var url="<?= $serviceMapUrlPrefix ?>api/v1?queryId=5295ccef482480352adb90ff5a22d35e&format=html";
        
         $('#<?= $_GET['name'] ?>_desc').width('87%');
         $('#<?= $_GET['name'] ?>_desc').html('<span><div id="<?= $_GET['name'] ?>_desc_text" class="desc_text" title="<?= preg_replace('/_/', ' ', $_GET['title']) ?>"><?= preg_replace('/_/', ' ', $_GET['title']) ?></div><a id ="info_modal" href="#" class="info_source"><img id="source_<?= $_GET['name'] ?>" src="../management/img/info.png" class="source_button"></a></span>');
  
        
        $('#<?= $_GET['name'] ?>_map_content').attr('src', url);
        
        $('#source_<?= $_GET['name'] ?>').on('click', function () {
            $('#dialog_<?= $_GET['name'] ?>').show();
        });
        $('#close_popup_<?= $_GET['name'] ?>').on('click', function () {

            $('#dialog_<?= $_GET['name'] ?>').hide();
        })
    });
</script>
<div class="widget">
    <div class='ui-widget-content'>        
        <div id="<?= $_GET['name'] ?>_desc" class="desc"></div>
        <div class="icons-modify-widget">
            <div class="singleBtnContainer"><a class="icon-cfg-widget" href="#"><span class="glyphicon glyphicon-cog glyphicon-modify-widget" aria-hidden="true"></span></a></div>
            <div class="singleBtnContainer"><a class="icon-remove-widget" href="#"><span class="glyphicon glyphicon-remove glyphicon-modify-widget" aria-hidden="true"></span></a></div>
        </div>
        <iframe id="<?= $_GET['name'] ?>_map_content" class="map_bus_position"></iframe>
    </div>	
</div> 