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
        var hostFile = "<?= $_GET['hostFile'] ?>";
        var widgetName = "<?= $_GET['name'] ?>";
        var divContainer = $('#<?= $_GET['name'] ?>_button');
        var widgetContentColor = "<?= $_GET['color'] ?>";
        var widgetHeaderColor = "<?= $_GET['frame_color'] ?>";
        var widgetHeaderFontColor = "<?= $_GET['headerFontColor'] ?>";
        var widgetName = "<?= $_GET['name'] ?>_div";
        var linkElement = $('#<?= $_GET['name'] ?>_link_w');
        var color = '<?= $_GET['color'] ?>';
        var fontSize = "<?= $_GET['fontSize'] ?>";
        var fontColor = "<?= $_GET['fontColor'] ?>";
        var button = $('#<?= $_GET['name'] ?>_button');
        var buttonText = '<?= $_GET['title'] ?>'.replace(/_/g, " ");
        var url = "<?= $_GET['link_w'] ?>";
        
        if(url === "null")
        {
            url = null;
        }
        
        //Definizioni di funzione specifiche del widget
        function lighterColor(hex, lum) 
        {
            hex = String(hex).replace(/[^0-9a-f]/gi, '');
            if (hex.length < 6) {
                    hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
            }
            lum = lum || 0;
            var rgb = "#", c, i;
            for (i = 0; i < 3; i++) {
                    c = parseInt(hex.substr(i*2,2), 16);
                    c = Math.round(Math.min(Math.max(0, c + (c * lum)), 255)).toString(16);
                    rgb += ("00"+c).substr(c.length);
            }
            return rgb;
        }
        //Fine definizioni di funzione 
        
        setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor);
        addLink(widgetName, url, linkElement, divContainer);
        
        if(hostFile === 'index')
        {
            $('#<?= $_GET['name'] ?>_header').hide();
        }
        else
        {
            $('#<?= $_GET['name'] ?>_header').show();
            var titleDivWidth = $('#<?= $_GET['name'] ?>_div').width() - 40;
            $('#<?= $_GET['name'] ?>_titleDiv').css("width", titleDivWidth + "px");
        }
        
        button.css("background-color", color);
        $('#<?= $_GET['name'] ?>_button').css("font-size", fontSize +"px");
        $('#<?= $_GET['name'] ?>_button').css("color", fontColor);
        $('#<?= $_GET['name'] ?>_button .ui-button-text').css("text-shadow", "1px 1px 1px rgba(0,0,0,0.35)");
        $('#<?= $_GET['name'] ?>_button .ui-button-text').text(buttonText);
        
        var hoverColor = lighterColor(color, -0.3);
        
        button.hover(function(){
            $(this).css("background-color", hoverColor);
            }, function(){
            $(this).css("background-color", color);
        });
    });//Fine document ready
</script>

<div class="widget" id="<?= $_GET['name'] ?>_div">
    <div class='ui-widget-content'>
        <div id='<?= $_GET['name'] ?>_header' class="widgetHeader">
            <div id="<?= $_GET['name'] ?>_titleDiv" class="titleDiv"></div>
            <div id="<?= $_GET['name'] ?>_buttonsDiv" class="buttonsContainer">
                <a class="icon-cfg-widget" href="#"><span class="glyphicon glyphicon-cog glyphicon-modify-widget" aria-hidden="true"></span></a>
                <a class="icon-remove-widget" href="#"><span class="glyphicon glyphicon-remove glyphicon-modify-widget" aria-hidden="true"></span></a>
            </div> 
        </div>
        
        <button type="button" id='<?= $_GET['name'] ?>_button' class="btn btn-primary button">
            <span class="ui-button-text"></span>
        </button>
    </div>	
</div> 