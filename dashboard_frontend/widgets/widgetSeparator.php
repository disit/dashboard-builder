<?php
/* Dashboard Builder.
   Copyright (C) 2017 DISIT Lab https://www.disit.org - University of Florence

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
    $(document).ready(function <?= $_GET['name'] ?>(firstLoad)
    {
        var hostFile = "<?= $_GET['hostFile'] ?>";
        var widgetName = "<?= $_GET['name'] ?>";
        var widgetContentColor = "<?= $_GET['color'] ?>";
        var widgetHeaderColor = "<?= $_GET['frame_color'] ?>";
        var widgetHeaderFontColor = "<?= $_GET['headerFontColor'] ?>";
        var widgetName = "<?= $_GET['name'] ?>";
        var widgetMainDivName = "<?= $_GET['name'] ?>_div";
        var widgetProperties, separatorHeight, styleParameters= null;
        var embedWidget = <?= $_GET['embedWidget'] ?>;
        var embedWidgetPolicy = '<?= $_GET['embedWidgetPolicy'] ?>';	
        var headerHeight = 25;
        var showTitle = "<?= $_GET['showTitle'] ?>";
	var showHeader = null;
        
        //Rimozione bordo per questo widget
        $("#" + widgetName).css("border", "none");
        
        if(((embedWidget === true)&&(embedWidgetPolicy === 'auto'))||((embedWidget === true)&&(embedWidgetPolicy === 'manual')&&(showTitle === "no"))||((embedWidget === false)&&(showTitle === "no")&&(hostFile === "index")))
	{
		showHeader = false;
	}
	else
	{
		showHeader = true;
	} 
        
        //Definizioni di funzione specifiche del widget
        //Fine definizioni di funzione 
        
        setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight);	
        
        if(hostFile === 'index')
        {
            $('#<?= $_GET['name'] ?>_header').hide();
            separatorHeight = parseInt($("#" + widgetName + "_div").prop("offsetHeight"));
        }
        else
        {
            $('#<?= $_GET['name'] ?>_header').show();
            separatorHeight = parseInt($("#" + widgetName + "_div").prop("offsetHeight") - 25);
            $("#" + widgetName + "_buttonsDiv").css("width", "50px");
            $("#" + widgetName + "_buttonsDiv").show();
            var titleDivWidth = $('#<?= $_GET['name'] ?>_div').width() - 50;
            $('#<?= $_GET['name'] ?>_titleDiv').css("width", titleDivWidth + "px");
            $('#<?= $_GET['name'] ?>_titleDiv').show();
        }
        
        $("#" + widgetName + "_separator").css("width", "100%");
        $("#" + widgetName + "_separator").css("height", separatorHeight);
        $("#" + widgetName + "_separator").css("border", "none");
        
        $("#" + widgetName + "_div").hover(function(){
            if(hostFile === 'config')
            {
               $(this).css("background-color", "yellow"); 
            }
        }, 
        function(){
            if(hostFile === 'config')
            {
               $(this).css("background-color", "transparent");
            }
        });
        
        widgetProperties = getWidgetProperties(widgetName);
        if((widgetProperties !== null) && (widgetProperties !== 'undefined'))
        {
           styleParameters = jQuery.parseJSON(widgetProperties.param.styleParameters); 
        }
    });//Fine document ready
</script>

<div class="widget" id="<?= $_GET['name'] ?>_div">
    <div class='ui-widget-content'>
        <div id='<?= $_GET['name'] ?>_header' class="widgetHeader">
            <div id="<?= $_GET['name'] ?>_titleDiv" class="titleDiv"></div>
            <div id="<?= $_GET['name'] ?>_buttonsDiv" class="buttonsContainer">
                <div class="singleBtnContainer"><a class="icon-cfg-widget" href="#"><span class="glyphicon glyphicon-cog glyphicon-modify-widget" aria-hidden="true"></span></a></div>
                <div class="singleBtnContainer"><a class="icon-remove-widget" href="#"><span class="glyphicon glyphicon-remove glyphicon-modify-widget" aria-hidden="true"></span></a></div>
            </div> 
        </div>
        
       <div id='<?= $_GET['name'] ?>_separator' class='widgetSeparator'></div>
    </div>	
</div> 