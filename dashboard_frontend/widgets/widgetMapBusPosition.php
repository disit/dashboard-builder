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
        
         $('#<?= $_REQUEST['name_w'] ?>_desc').width('87%');
         $('#<?= $_REQUEST['name_w'] ?>_desc').html('<span><div id="<?= $_REQUEST['name_w'] ?>_desc_text" class="desc_text" title="<?= preg_replace('/_/', ' ', $_REQUEST['title_w']) ?>"><?= preg_replace('/_/', ' ', $_REQUEST['title_w']) ?></div><a id ="info_modal" href="#" class="info_source"><img id="source_<?= $_REQUEST['name_w'] ?>" src="../management/img/info.png" class="source_button"></a></span>');
		 var hasTimer = "<?= $_REQUEST['hasTimer'] ?>";
  
        
        $('#<?= $_REQUEST['name_w'] ?>_map_content').attr('src', url);
        
        $('#source_<?= $_REQUEST['name_w'] ?>').on('click', function () {
            $('#dialog_<?= $_REQUEST['name_w'] ?>').show();
        });
        $('#close_popup_<?= $_REQUEST['name_w'] ?>').on('click', function ()
        {
            $('#dialog_<?= $_REQUEST['name_w'] ?>').hide();
        });
		
		$('#<?= $_REQUEST['name_w'] ?>_countdownContainerDiv').remove();
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
            <p id="<?= $_REQUEST['name_w'] ?>_noDataAlert" style='text-align: center; font-size: 18px; display:none'>Nessun dato disponibile</p>
            <iframe id="<?= $_REQUEST['name_w'] ?>_map_content" class="map_bus_position"></iframe>
        </div>
    </div>	
</div> 

<!--<div class="widget">
    <div class='ui-widget-content'>        
        <div id="<?= $_REQUEST['name_w'] ?>_desc" class="desc"></div>
        <div class="icons-modify-widget">
            <div class="singleBtnContainer"><a class="icon-cfg-widget" href="#"><span class="glyphicon glyphicon-cog glyphicon-modify-widget" aria-hidden="true"></span></a></div>
            <div class="singleBtnContainer"><a class="icon-remove-widget" href="#"><span class="glyphicon glyphicon-remove glyphicon-modify-widget" aria-hidden="true"></span></a></div>
        </div>
        
    </div>	
</div>--> 