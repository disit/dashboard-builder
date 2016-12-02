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
    
    $(document).ready(function <?= $_GET['name'] ?>() 
    {
        var colore_frame = "<?= $_GET['frame_color'] ?>";
        var nome_wid = "<?= $_GET['name'] ?>_div";
        $("#<?= $_GET['name'] ?>_div").css({'background-color':colore_frame});
        
        var button = $('#<?= $_GET['name'] ?>_button');
        var color = '<?= $_GET['color'] ?>';
        var buttonText = '<?= $_GET['title'] ?>'.replace(/_/g, " ");
        
        var fontSize = "<?= $_GET['fontSize'] ?>";
        var fontColor = "<?= $_GET['fontColor'] ?>";
         
        button.css("background-color", color);
        
        var hoverColor = lighterColor(color, -0.3);
        button.hover(function(){
            $(this).css("background-color", hoverColor);
            }, function(){
            $(this).css("background-color", color);
        });
        
        $('#<?= $_GET['name'] ?>_button').css("font-size", fontSize +"px");
        $('#<?= $_GET['name'] ?>_button').css("color", fontColor);
        $('#<?= $_GET['name'] ?>_button span').css("text-shadow", "1px 1px 1px rgba(0,0,0,0.35)");
        $('#<?= $_GET['name'] ?>_button span').text(buttonText);
         
        var link_w = "<?= $_GET['link_w'] ?>";
        var divChartContainer = $('#<?= $_GET['name'] ?>_button');
        var linkElement = $('#<?= $_GET['name'] ?>_link_w');
        var alarmSet = false;
        var $div2blink = null;
        var blinkInterval = null;
        if((link_w != null) && (link_w != "null") && (link_w != "") && (typeof link_w != "undefined")) 
        {
            if(linkElement.length === 0)
            {
               linkElement = $("<a id='<?= $_GET['name'] ?>_link_w' href='<?= $_GET['link_w'] ?>' target='_blank'>");
               divChartContainer.wrap(linkElement); 
            }
        } 
    });
</script>

<div class="widget" id="<?= $_GET['name'] ?>_div">
    <div class='ui-widget-content'>
        <div class="icons-modify-widget"><a class="icon-cfg-widget" href="#"><span class="glyphicon glyphicon-cog glyphicon-modify-widget" aria-hidden="true"></span></a><a class="icon-remove-widget" href="#"><span class="glyphicon glyphicon-remove glyphicon-modify-widget" aria-hidden="true"></span></a></div>
        <button type="button" id='<?= $_GET['name'] ?>_button' class="btn btn-primary button">
            <span class="ui-button-text"></span>
        </button>
    </div>	
</div> 