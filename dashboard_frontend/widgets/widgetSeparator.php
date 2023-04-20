<?php
/* Dashboard Builder.
   Copyright (C) 2018 DISIT Lab https://www.disit.org - University of Florence

   This program is free software: you can redistribute it and/or modify
   it under the terms of the GNU Affero General Public License as
   published by the Free Software Foundation, either version 3 of the
   License, or (at your option) any later version.
   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU Affero General Public License for more details.
   You should have received a copy of the GNU Affero General Public License
   along with this program.  If not, see <http://www.gnu.org/licenses/>. */
   include('../config.php');
   header("Cache-Control: private, max-age=$cacheControlMaxAge");
   
   $name_w = sanitizeString('name_w');
?>

<script type='text/javascript'>
    $(document).ready(function <?= $name_w ?>(firstLoad)
    {
        var hostFile = "<?= escapeForJS($_REQUEST['hostFile']) ?>";
        var widgetContentColor = "<?= escapeForJS($_REQUEST['color_w']) ?>";
        var widgetHeaderColor = "<?= sanitizeString('frame_color_w') ?>";
        var widgetHeaderFontColor = "<?= escapeForJS($_REQUEST['headerFontColor']) ?>";
        var widgetName = "<?= $name_w ?>";
        var widgetProperties, separatorHeight, styleParameters= null;
        var embedWidget = <?= $_REQUEST['embedWidget']=='true' ? 'true' : 'false' ?>;
        var embedWidgetPolicy = '<?= escapeForJS($_REQUEST['embedWidgetPolicy']) ?>';	
        var headerHeight = 25;
        var showTitle = "<?= escapeForJS($_REQUEST['showTitle']) ?>";
        var showHeader = null;
        var hasTimer = "<?= escapeForJS($_REQUEST['hasTimer']) ?>";
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
        
        $('#<?= $name_w ?>_div').parents('li.gs_w').off('resizeWidgets');
        $('#<?= $name_w ?>_div').parents('li.gs_w').on('resizeWidgets', resizeWidget);
        
        if(hostFile != 'config')
        {
            $('#<?= $name_w ?>_header').hide();
            separatorHeight = parseInt($("#" + widgetName + "_div").prop("offsetHeight"));
        }
        else
        {
            //$('#<?= $name_w ?>_header').show();
            separatorHeight = parseInt($("#" + widgetName + "_div").prop("offsetHeight") - 25);
            $("#" + widgetName + "_buttonsDiv").css("width", "50px");
            //$("#" + widgetName + "_buttonsDiv").show();
            var titleDivWidth = $('#<?= $name_w ?>_div').width() - 50;
            $('#<?= $name_w ?>_titleDiv').css("width", titleDivWidth + "px");
            //$('#<?= $name_w ?>_titleDiv').show();
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
		
        $('#<?= $name_w ?>_countdownContainerDiv').remove();
        
        $("#<?= $name_w ?>").on('customResizeEvent', function(event){
            resizeWidget();
        });
        
        $(document).on('resizeHighchart_' + widgetName, function(event)
        {
            showHeader = event.showHeader;
        });
        
        //$("#" + widgetName + "_header").hide();

    });//Fine document ready
</script>

<div class="widget" id="<?= $name_w ?>_div">
    <div class='ui-widget-content'>
        <?php include '../widgets/widgetHeader.php'; ?>
        <?php include '../widgets/widgetCtxMenu.php'; ?>
        
       <div id='<?= $name_w ?>_separator' class='widgetSeparator'>
           <?php include '../widgets/commonModules/widgetDimControls.php'; ?>	
       </div>
    </div>	
</div> 