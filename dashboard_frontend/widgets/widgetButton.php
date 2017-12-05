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
        var linkElement = $('#<?= $_GET['name'] ?>_link_w');
        var color = '<?= $_GET['color'] ?>';
        var fontSize = "<?= $_GET['fontSize'] ?>";
        var fontColor = "<?= $_GET['fontColor'] ?>";
        var button = $('#<?= $_GET['name'] ?>_button');
        var buttonText = '<?= $_GET['title'] ?>'.replace(/_/g, " ");
        var url = "<?= $_GET['link_w'] ?>";
        var hasChangeMetric = false;
        var justClicked = false;
        var widgetProperties, buttonHeight, widgetTargetList, originalHeaderColor, originalBorderColor, originalTitle, 
            originalHeaderFontColor, styleParameters, innerWidth, innerHeight, innerTop, innerLeft,
            outerMinDim, innerMinDim, outerBorderRadius, innerBorderRadius = null;
        
        //Rimozione bordo per questo widget
        $("#" + widgetName).css("border", "none");
        
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
            for (i = 0; i < 3; i++) 
            {
               c = parseInt(hex.substr(i*2,2), 16);
               c = Math.round(Math.min(Math.max(0, c + (c * lum)), 255)).toString(16);
               rgb += ("00"+c).substr(c.length);
            }
            return rgb;
        }
        //Fine definizioni di funzione 
        
        setWidgetLayout(hostFile, widgetMainDivName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor);
        
        $("#" + widgetName + "_button").css("width", "98%");
        if(hostFile === 'index')
        {
            $('#<?= $_GET['name'] ?>_header').hide();
            $("#" + widgetName + "_button").css("height", "98%");
        }
        else
        {
            $('#<?= $_GET['name'] ?>_header').show();
            buttonHeight = $("#" + widgetName + "_div").prop("offsetHeight") - 25;
            var widgetHeight = $("#" + widgetName + "_div").prop("offsetHeight");
            var contentHeight = parseFloat(buttonHeight / widgetHeight);
            contentHeight = (contentHeight * 100) - 2;
            $("#" + widgetName + "_button").css("height", contentHeight + "%");
            $("#" + widgetName + "_buttonsDiv").css("width", "50px");
            $("#" + widgetName + "_buttonsDiv").show();
            var titleDivWidth = $('#<?= $_GET['name'] ?>_div').width() - 50;
            $('#<?= $_GET['name'] ?>_titleDiv').css("width", titleDivWidth + "px");
            $('#<?= $_GET['name'] ?>_titleDiv').show();
        }
        
        button.css("background-color", color);
        $('#<?= $_GET['name'] ?>_button').css("font-size", fontSize +"px");
        $('#<?= $_GET['name'] ?>_button').css("color", fontColor);
        $('#<?= $_GET['name'] ?>_buttonText').css("text-shadow", "1px 1px 1px rgba(0,0,0,0.35)");
        $('#<?= $_GET['name'] ?>_buttonText').text(buttonText);
        $("#" + widgetName + "_button").css("border", "none");
        $('#<?= $_GET['name'] ?>_buttonBackground').addClass("centerWithFlex");
        
        $("#" + widgetName + "_button").focus(function(){
           $(this).css("outline", "none");
        });
        
        widgetProperties = getWidgetProperties(widgetName);
        if((widgetProperties !== null) && (widgetProperties !== 'undefined'))
        {
           widgetTargetList = JSON.parse(widgetProperties.param.parameters);
           styleParameters = jQuery.parseJSON(widgetProperties.param.styleParameters); 
           
           for(var index in widgetTargetList.changeMetricTargetsJson)
           {
               if(widgetTargetList.changeMetricTargetsJson[index] !== "noMetricChange")
               {
                   hasChangeMetric = true;
                   break;
               }
           }
           
           if((widgetTargetList.geoTargetsJson.length > 0)||(hasChangeMetric))
           {
               button.hover(
                  function() 
                  {
                     originalHeaderColor = {
                         changeMetricTargetsJson: {},
                         geoTargetsJson: []
                     };
                     originalBorderColor = {
                         changeMetricTargetsJson: {},
                         geoTargetsJson: []
                     };
                     originalTitle = {
                         changeMetricTargetsJson: {},
                         geoTargetsJson: []
                     };
                     originalHeaderFontColor = {
                         changeMetricTargetsJson: {},
                         geoTargetsJson: []
                     };
                     
                     for(var widgetName in widgetTargetList.changeMetricTargetsJson)
                     {
                        if(widgetTargetList.changeMetricTargetsJson[widgetName] !== "noMetricChange")
                        {
                           originalHeaderColor.changeMetricTargetsJson[widgetName] = $("#" + widgetName + "_header").css("background-color");
                           originalBorderColor.changeMetricTargetsJson[widgetName] = $("#" + widgetName).css("border-color");
                           originalTitle.changeMetricTargetsJson[widgetName] = $("#" + widgetName + "_titleDiv").html();
                           originalHeaderFontColor.changeMetricTargetsJson[widgetName] = $("#" + widgetName + "_titleDiv").css("color");
                           
                           $("#" + widgetName + "_header").css("background", hoverColor);
                           $("#" + widgetName).css("border-color", hoverColor);
                           $("#" + widgetName + "_titleDiv").html(buttonText);
                           $("#" + widgetName + "_titleDiv").css("color", fontColor);
                        }
                     }
                     
                     for(var i = 0; i < widgetTargetList.geoTargetsJson.length; i++)
                     {
                        originalHeaderColor.geoTargetsJson[i] = $("#" + widgetTargetList.geoTargetsJson[i] + "_header").css("background-color");
                        originalBorderColor.geoTargetsJson[i] = $("#" + widgetTargetList.geoTargetsJson[i]).css("border-color");
                        originalTitle.geoTargetsJson[i] = $("#" + widgetTargetList.geoTargetsJson[i] + "_titleDiv").html();
                        originalHeaderFontColor.geoTargetsJson[i] = $("#" + widgetTargetList.geoTargetsJson[i] + "_titleDiv").css("color");
                        
                        $("#" + widgetTargetList.geoTargetsJson[i] + "_header").css("background", hoverColor);
                        $("#" + widgetTargetList.geoTargetsJson[i]).css("border-color", hoverColor);
                        $("#" + widgetTargetList.geoTargetsJson[i] + "_titleDiv").html(buttonText);
                        $("#" + widgetTargetList.geoTargetsJson[i] + "_titleDiv").css("color", fontColor);
                     }
                  }, 
                  function() 
                  {
                     if(justClicked === false)
                     {
                        for(var widgetName in widgetTargetList.changeMetricTargetsJson)
                        {
                           if(widgetTargetList.changeMetricTargetsJson[widgetName] !== "noMetricChange")
                           {
                              $("#" + widgetName + "_header").css("background", originalHeaderColor.changeMetricTargetsJson[widgetName]);
                              $("#" + widgetName).css("border-color", originalBorderColor.changeMetricTargetsJson[widgetName]); 
                              $("#" + widgetName + "_titleDiv").html(originalTitle.changeMetricTargetsJson[widgetName]);
                              $("#" + widgetName + "_titleDiv").css("color", originalHeaderFontColor.changeMetricTargetsJson[widgetName]);
                           }
                        } 

                        for(var i = 0; i < widgetTargetList.geoTargetsJson.length; i++)
                        {
                           $("#" + widgetTargetList.geoTargetsJson[i] + "_header").css("background", originalHeaderColor.geoTargetsJson[i]);
                           $("#" + widgetTargetList.geoTargetsJson[i]).css("border-color", originalBorderColor.geoTargetsJson[i]);
                           $("#" + widgetTargetList.geoTargetsJson[i] + "_titleDiv").html(originalTitle.geoTargetsJson[i]);
                           $("#" + widgetTargetList.geoTargetsJson[i] + "_titleDiv").css("color", originalHeaderFontColor.geoTargetsJson[i]);
                        } 
                     }
                     else
                     {
                         justClicked = false;
                     }
                  }
               );
    
            button.click(function()
            {
               justClicked = true; 
                
               for(var widgetName in widgetTargetList.changeMetricTargetsJson)
               {
                    $.event.trigger({
                        type: "changeMetricFromButton_" + widgetName,
                        targetWidget: widgetName,
                        newMetricName: widgetTargetList.changeMetricTargetsJson[widgetName],
                        newTargetTitle: buttonText,
                        newHeaderAndBorderColor: color,
                        newHeaderFontColor: fontColor
                    }); 
               }
                
               for(var i = 0; i < widgetTargetList.geoTargetsJson.length; i++)
               {
                  $("#" + widgetTargetList.geoTargetsJson[i] + "_driverWidgetType").val("button"); 
                  $("#" + widgetTargetList.geoTargetsJson[i] + "_buttonUrl").val(url);
                  $("#" + widgetTargetList.geoTargetsJson[i] + "_iFrame").attr("src", url);
               }
            });
           }
           else
           {
              addLink(widgetMainDivName, url, linkElement, button);
           }
           
           if($("#<?= $_GET['name'] ?>_button").width() > $("#<?= $_GET['name'] ?>_button").height())
           {
              outerMinDim = $("#<?= $_GET['name'] ?>_button").height();
           }
           else
           {
              outerMinDim = $("#<?= $_GET['name'] ?>_button").width();
           }
           
           outerBorderRadius = Math.floor((parseInt(styleParameters.borderRadius) / 200)*outerMinDim); //Dividiamo per due oltre che per 100, è un raggio
           
           $('#<?= $_GET['name'] ?>_button').css("border-radius", outerBorderRadius + "px");
           
           
           if(styleParameters.hasImage === 'no')
           {
                $('#<?= $_GET['name'] ?>_buttonBackground').hide();
                $('#<?= $_GET['name'] ?>_buttonInnerBackground').hide();
                
                if(styleParameters.showText === "yes")
                {
                    $('#<?= $_GET['name'] ?>_buttonText').css("height", "100%");
                    $('#<?= $_GET['name'] ?>_buttonText').show();
                }
                else
                {
                    $('#<?= $_GET['name'] ?>_buttonText').hide();
                }
           }
           else
           {
                innerWidth = parseInt(styleParameters.imageWidth);
                innerHeight = parseInt(styleParameters.imageHeight);
                innerTop = Math.floor((100 - innerHeight)/2);
                innerLeft = Math.floor((100 - innerWidth)/2);
                
                $('#<?= $_GET['name'] ?>_buttonInnerBackground').css("width", innerWidth + "%");
                $('#<?= $_GET['name'] ?>_buttonInnerBackground').css("height", innerHeight + "%");
                $('#<?= $_GET['name'] ?>_buttonInnerBackground').css("top", innerTop + "%");
                $('#<?= $_GET['name'] ?>_buttonInnerBackground').css("left", innerLeft + "%");
                $('#<?= $_GET['name'] ?>_buttonInnerBackground').css("background-color", color);
                $('#<?= $_GET['name'] ?>_buttonInnerBackground').css("background-image", "url(../img/widgetButtonImages/" + widgetName + "/" + styleParameters.imageName + ")");
                $('#<?= $_GET['name'] ?>_buttonInnerBackground').css("background-size", "100% 100%");
                $('#<?= $_GET['name'] ?>_buttonInnerBackground').css("background-repeat", "no-repeat");
                $('#<?= $_GET['name'] ?>_buttonInnerBackground').css("background-position", "center center");
                
                if(styleParameters.showText === "yes")
                {
                    $('#<?= $_GET['name'] ?>_buttonBackground').css("height", "80%");
                    $('#<?= $_GET['name'] ?>_buttonText').css("height", "20%");
                    $('#<?= $_GET['name'] ?>_buttonBackground').css("border-radius", outerBorderRadius + "px");
                    
                    $('#<?= $_GET['name'] ?>_buttonText').show();
                    $('#<?= $_GET['name'] ?>_buttonBackground').show();
                    $('#<?= $_GET['name'] ?>_buttonInnerBackground').show();
                }
                else
                {
                    $('#<?= $_GET['name'] ?>_buttonText').hide();
                    $('#<?= $_GET['name'] ?>_buttonBackground').css("height", "100%");
                    $('#<?= $_GET['name'] ?>_buttonBackground').css("border-radius", outerBorderRadius + "px");
                    
                    $('#<?= $_GET['name'] ?>_buttonBackground').show();
                    $('#<?= $_GET['name'] ?>_buttonInnerBackground').show();
                }
                
                if($('#<?= $_GET['name'] ?>_buttonInnerBackground').width() > $('#<?= $_GET['name'] ?>_buttonInnerBackground').height())
                {
                   innerMinDim = $('#<?= $_GET['name'] ?>_buttonInnerBackground').height();
                }
                else
                {
                   innerMinDim = $('#<?= $_GET['name'] ?>_buttonInnerBackground').width();
                }

                innerBorderRadius = Math.floor((parseInt(styleParameters.borderRadius) / 200)*innerMinDim); //Dividiamo per due oltre che per 100, è un raggio
                $('#<?= $_GET['name'] ?>_buttonInnerBackground').css("border-radius", innerBorderRadius + "px");
           }
           
            $('#<?= $_GET['name'] ?>_button').mousedown(function(){
               $(this).css("box-shadow", "1px 1px 2px black inset");
            });

            $('#<?= $_GET['name'] ?>_button').mouseup(function(){
               $(this).css("box-shadow", "1px 1px 2px black");
            });
           
           var hoverColor = lighterColor(color, -0.2);
        
            button.hover(function()
            {
                $(this).css("background-color", hoverColor);
                $('#<?= $_GET['name'] ?>_buttonBackground').css("background-color", hoverColor);
                $('#<?= $_GET['name'] ?>_buttonInnerBackground').css("background-color", hoverColor);
            }, 
            function()
            {
                $(this).css("background-color", color);
                $('#<?= $_GET['name'] ?>_buttonBackground').css("background-color", color);
                $('#<?= $_GET['name'] ?>_buttonInnerBackground').css("background-color", color);
            });
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
        
       <div id='<?= $_GET['name'] ?>_button' class='widgetButton'>
           <div id='<?= $_GET['name'] ?>_buttonBackground' class='widgetButtonBackground'>
               <div id="<?= $_GET['name'] ?>_buttonInnerBackground"></div>
           </div> 
           <div id='<?= $_GET['name'] ?>_buttonText' class='widgetButtonTextContainer centerWithFlex'></div>
        </div>
    </div>	
</div> 