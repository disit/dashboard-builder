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
        var hostFile = "<?= $_REQUEST['hostFile'] ?>";
        var widgetName = "<?= $_REQUEST['name_w'] ?>";
        var widgetContentColor = "<?= $_REQUEST['color_w'] ?>";
        var widgetHeaderColor = "<?= $_REQUEST['frame_color_w'] ?>";
        var widgetHeaderFontColor = "<?= $_REQUEST['headerFontColor'] ?>";
        var widgetName = "<?= $_REQUEST['name_w'] ?>";
        var widgetProperties, separatorHeight, styleParameters= null;
        var embedWidget = <?= $_REQUEST['embedWidget'] ?>;
        var embedWidgetPolicy = '<?= $_REQUEST['embedWidgetPolicy'] ?>';	
        var headerHeight = 25;
        var showTitle = "<?= $_REQUEST['showTitle'] ?>";
        var showHeader = null;
        var hasTimer = "<?= $_REQUEST['hasTimer'] ?>";
        //Rimozione bordo per questo widget
        $("#" + widgetName).css("border", "none");
        
        if(showTitle === "no")
        {
            showHeader = false;
        }
        else
        {
            showHeader = true;
        }
        
        function resizeWidget()
        {
            setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
        }
        
        //Definizioni di funzione specifiche del widget
        //Fine definizioni di funzione 
        
        setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
        
        $('#<?= $_REQUEST['name_w'] ?>_div').parents('li.gs_w').off('resizeWidgets');
        $('#<?= $_REQUEST['name_w'] ?>_div').parents('li.gs_w').on('resizeWidgets', resizeWidget);
        
        if(hostFile === 'index')
        {
            $('#<?= $_REQUEST['name_w'] ?>_header').hide();
            separatorHeight = parseInt($("#" + widgetName + "_div").prop("offsetHeight"));
        }
        else
        {
            //$('#<?= $_REQUEST['name_w'] ?>_header').show();
            separatorHeight = parseInt($("#" + widgetName + "_div").prop("offsetHeight") - 25);
            $("#" + widgetName + "_buttonsDiv").css("width", "50px");
            //$("#" + widgetName + "_buttonsDiv").show();
            var titleDivWidth = $('#<?= $_REQUEST['name_w'] ?>_div').width() - 50;
            $('#<?= $_REQUEST['name_w'] ?>_titleDiv').css("width", titleDivWidth + "px");
            //$('#<?= $_REQUEST['name_w'] ?>_titleDiv').show();
        }
        
        $("#" + widgetName + "_separator").css("width", "100%");
        $("#" + widgetName + "_separator").css("height", separatorHeight);
        $("#" + widgetName + "_separator").css("border", "none");
        
        if(hostFile === 'config')
        {
            $("#" + widgetName + "_div").css("background-color", "rgba(0, 0, 0, 0.1) !important"); 
        }
        
        /*$("#" + widgetName + "_div").hover(function(){
            if(hostFile === 'config')
            {
               $("#" + widgetName + "_div").css("background-color", "yellow"); 
            }
        }, 
        function(){
            if(hostFile === 'config')
            {
               $("#" + widgetName + "_div").css("background-color", "rgba(0, 0, 0, 0.1) !important"); 
            }
        });*/
        
        widgetProperties = getWidgetProperties(widgetName);
        if((widgetProperties !== null) && (widgetProperties !== 'undefined'))
        {
           styleParameters = jQuery.parseJSON(widgetProperties.param.styleParameters); 
        }
		
        $('#<?= $_REQUEST['name_w'] ?>_countdownContainerDiv').remove();
        
        $("#<?= $_REQUEST['name_w'] ?>").on('customResizeEvent', function(event){
            resizeWidget();
        });
        
        $(document).on('resizeHighchart_' + widgetName, function(event)
        {
            showHeader = event.showHeader;
        });
        
        //$("#" + widgetName + "_header").hide();

    });//Fine document ready
</script>

<div class="widget" id="<?= $_REQUEST['name_w'] ?>_div">
    <div class='ui-widget-content'>
        <?php include '../widgets/widgetHeader.php'; ?>
        <?php include '../widgets/widgetCtxMenu.php'; ?>
        
       <div id='<?= $_REQUEST['name_w'] ?>_separator' class='widgetSeparator'>
           <?php include '../widgets/commonModules/widgetDimControls.php'; ?>	
       </div>
    </div>	
</div> 