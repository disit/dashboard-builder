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
    $stopFlag = 1;
   header("Cache-Control: private, max-age=$cacheControlMaxAge");
?>

<link rel="stylesheet" href="../css/widgetOnOffButton.css">

<script type='text/javascript'>
    $(document).ready(function <?= $_REQUEST['name_w'] ?>(firstLoad)
    {
        var hostFile = "<?= $_REQUEST['hostFile'] ?>";
        var widgetName = "<?= $_REQUEST['name_w'] ?>";
        var widgetContentColor = "<?= $_REQUEST['color_w'] ?>";
        var widgetHeaderColor = "<?= $_REQUEST['frame_color_w'] ?>";
        var widgetHeaderFontColor = "<?= $_REQUEST['headerFontColor'] ?>";
        var widgetName = "<?= $_REQUEST['name_w'] ?>";
        var widgetMainDivName = "<?= $_REQUEST['name_w'] ?>_div";
        var linkElement = $('#<?= $_REQUEST['name_w'] ?>_link_w');
        var color = '<?= $_REQUEST['color_w'] ?>';
        var fontSize = "<?= $_REQUEST['fontSize'] ?>";
        var fontColor = "<?= $_REQUEST['fontColor'] ?>";
        var button = $('#<?= $_REQUEST['name_w'] ?>_button');
        var buttonText = '<?= $_REQUEST['title_w'] ?>'.replace(/_/g, " ");
        var url = "<?= $_REQUEST['link_w'] ?>";
        var hasChangeMetric = false;
        var justClicked = false;
        var embedWidget = <?= $_REQUEST['embedWidget'] ?>;
        var embedWidgetPolicy = '<?= $_REQUEST['embedWidgetPolicy'] ?>';
        var showTitle = "<?= $_REQUEST['showTitle'] ?>";
		var hasTimer = "<?= $_REQUEST['hasTimer'] ?>";
        var widgetProperties, buttonHeight, widgetTargetList, originalHeaderColor, originalBorderColor, originalTitle, 
            originalHeaderFontColor, styleParameters, innerWidth, innerHeight, innerTop, innerLeft,
            outerMinDim, innerMinDim, outerBorderRadius, innerBorderRadius, widgetWidthCells, widgetHeightCells,
            minDim, minDimCells, minDimName, showHeader, buttonPercentWidth, buttonPercentHeight, target = null;
		
		$("#" + widgetName + "_countdownContainerDiv").hide();
		
        if(((embedWidget === true)&&(embedWidgetPolicy === 'auto'))||((embedWidget === true)&&(embedWidgetPolicy === 'manual')&&(showTitle === "no"))||((embedWidget === false)&&(showTitle === "no")))
        {
            showHeader = false;
        }
        else
        {
            showHeader = true;
        }  
        
        //Rimozione bordo per questo widget
        $("#" + widgetName).css("border", "none");
        
        if(url === "null")
        {
            url = null;
        }
		
        //Definizioni di funzione specifiche del widget
        function populateWidget()
        {
            showWidgetContent(widgetName);
            $("#<?= $_REQUEST['name_w'] ?>_content").show();
            $("#<?= $_REQUEST['name_w'] ?>_button").show();
            
            //$('#<?= $_REQUEST['name_w'] ?>_div').css("position", "relative");
            if(showHeader)
            {
                $('#<?= $_REQUEST['name_w'] ?>_content').height($('#<?= $_REQUEST['name_w'] ?>_div').height() - $('#<?= $_REQUEST['name_w'] ?>_header').height());
            }
            else
            {
                $('#<?= $_REQUEST['name_w'] ?>_content').height($('#<?= $_REQUEST['name_w'] ?>_div').height());
            }
            
            if($("#<?= $_REQUEST['name_w'] ?>_content").width() > $("#<?= $_REQUEST['name_w'] ?>_content").height())
            {
                minDim = $("#<?= $_REQUEST['name_w'] ?>_content").height();
                minDimCells = widgetHeightCells;
                minDimName = "height";
            }
            else
            {
                minDim = $("#<?= $_REQUEST['name_w'] ?>_content").width();
                minDimCells = widgetWidthCells;
                minDimName = "width";
            }
            
            if(showHeader)
            {
                $('#<?= $_REQUEST['name_w'] ?>_header').show();
                if((2*widgetWidthCells) === widgetHeightCells)
                {
                    buttonPercentHeight = ($('#<?= $_REQUEST['name_w'] ?>_content').height() - 15)*100/$('#<?= $_REQUEST['name_w'] ?>_content').height();
                    buttonPercentWidth = ($('#<?= $_REQUEST['name_w'] ?>_content').height()-15)*100/$('#<?= $_REQUEST['name_w'] ?>_content').width();
                }
                else
                {
                    buttonPercentHeight = ($('#<?= $_REQUEST['name_w'] ?>_content').height()-15)*100/$('#<?= $_REQUEST['name_w'] ?>_content').height();
                    buttonPercentWidth = ($('#<?= $_REQUEST['name_w'] ?>_content').width()-15)*100/$('#<?= $_REQUEST['name_w'] ?>_content').width();
                }
            }
            else
            {
                $('#<?= $_REQUEST['name_w'] ?>_header').hide();
                buttonPercentHeight = ($('#<?= $_REQUEST['name_w'] ?>_content').height()-15)*100/$('#<?= $_REQUEST['name_w'] ?>_content').height();
                buttonPercentWidth = ($('#<?= $_REQUEST['name_w'] ?>_content').width()-15)*100/$('#<?= $_REQUEST['name_w'] ?>_content').width();
            }
            
            $('#<?= $_REQUEST['name_w'] ?>_button').css("width", buttonPercentWidth + "%");
            $('#<?= $_REQUEST['name_w'] ?>_button').css("height", buttonPercentHeight + "%");
            $('#<?= $_REQUEST['name_w'] ?>_button').css("left", parseFloat(100 - buttonPercentWidth)/2 + "%");
            $('#<?= $_REQUEST['name_w'] ?>_button').css("top", parseFloat(100 - buttonPercentHeight)/2 + "%");
            $('#<?= $_REQUEST['name_w'] ?>_button').css("font-size", minDim*0.3 + "px");
            $('#<?= $_REQUEST['name_w'] ?>_button').css("border-radius", minDim*outerBorderRadius/200);
            
            $('#<?= $_REQUEST['name_w'] ?>_button').css("background-color", color);
            $('#<?= $_REQUEST['name_w'] ?>_button').css("color", fontColor);
            $('#<?= $_REQUEST['name_w'] ?>_buttonText').css("text-shadow", "1px 1px 1px rgba(0,0,0,0.35)");
            $('#<?= $_REQUEST['name_w'] ?>_buttonText span').text(buttonText);
            $("#" + widgetName + "_button").css("border", "none");
            $('#<?= $_REQUEST['name_w'] ?>_buttonBackground').css("display", "flex");
            $('#<?= $_REQUEST['name_w'] ?>_buttonInnerBackground').css("background-color", "transparent");
            
            var hoverColor = lighterColor(color, -0.2);
        
            $('#<?= $_REQUEST['name_w'] ?>_button').hover(function()
            {
                $(this).css("background-color", hoverColor);
            }, 
            function()
            {
                $(this).css("background-color", color);
            });

            if (styleParameters.openNewTab === "no") {
                var stopFlag = 1;
            }
            
            if(styleParameters.hasImage === 'no')
            {
                $('#<?= $_REQUEST['name_w'] ?>_buttonBackground').hide();
                $('#<?= $_REQUEST['name_w'] ?>_buttonInnerBackground').hide();
                
                if(styleParameters.showText === "yes")
                {
                    $('#<?= $_REQUEST['name_w'] ?>_buttonText').css("height", "100%");
                    $('#<?= $_REQUEST['name_w'] ?>_buttonBackground').css("display", "flex");
                }
                else
                {
                  //  console.log("ARRIVA IN SHOW-IMAGE = NO! ");
                 //   $('#<?= $_REQUEST['name_w'] ?>_buttonBackground').css("display", "none");
                    $('#<?= $_REQUEST['name_w'] ?>_buttonText').css("display", "none");
                }
            }
            else
            {
                innerWidth = parseInt(styleParameters.imageWidth);
                innerHeight = parseInt(styleParameters.imageHeight);
                innerTop = Math.floor((100 - innerHeight)/2);
                innerLeft = Math.floor((100 - innerWidth)/2);
                
                $('#<?= $_REQUEST['name_w'] ?>_buttonInnerBackground').css("width", innerWidth + "%");
                $('#<?= $_REQUEST['name_w'] ?>_buttonInnerBackground').css("height", innerHeight + "%");
                $('#<?= $_REQUEST['name_w'] ?>_buttonInnerBackground').css("top", innerTop + "%");
                $('#<?= $_REQUEST['name_w'] ?>_buttonInnerBackground').css("left", innerLeft + "%");
                $('#<?= $_REQUEST['name_w'] ?>_buttonInnerBackground').css("background-image", "url(../img/widgetButtonImages/" + widgetName + "/" + styleParameters.imageName + ")");
                $('#<?= $_REQUEST['name_w'] ?>_buttonInnerBackground').css("background-size", "100% 100%");
                $('#<?= $_REQUEST['name_w'] ?>_buttonInnerBackground').css("background-repeat", "no-repeat");
                $('#<?= $_REQUEST['name_w'] ?>_buttonInnerBackground').css("background-position", "center center");
                
                if(styleParameters.showText === "yes")
                {
                    $('#<?= $_REQUEST['name_w'] ?>_buttonBackground').css("height", "80%");
                    $('#<?= $_REQUEST['name_w'] ?>_buttonText').css("height", "20%");
                    $('#<?= $_REQUEST['name_w'] ?>_buttonBackground').css("border-radius", outerBorderRadius + "px");
                    
                    $('#<?= $_REQUEST['name_w'] ?>_buttonBackground').css("display", "flex");
                    $('#<?= $_REQUEST['name_w'] ?>_buttonBackground').show();
                    $('#<?= $_REQUEST['name_w'] ?>_buttonInnerBackground').show();
                }
                else
                {
                 //   console.log("ARRIVA IN SHOW-IMAGE = YES! ")
                //    $('#<?= $_REQUEST['name_w'] ?>_buttonBackground').css("display", "none");
                    $('#<?= $_REQUEST['name_w'] ?>_buttonText').css("display", "none");
                    $('#<?= $_REQUEST['name_w'] ?>_buttonBackground').css("height", "100%");
                    $('#<?= $_REQUEST['name_w'] ?>_buttonBackground').css("border-radius", outerBorderRadius + "px");
                    
                    $('#<?= $_REQUEST['name_w'] ?>_buttonBackground').show();
                    $('#<?= $_REQUEST['name_w'] ?>_buttonInnerBackground').show();
                }
                
                if($('#<?= $_REQUEST['name_w'] ?>_buttonInnerBackground').width() > $('#<?= $_REQUEST['name_w'] ?>_buttonInnerBackground').height())
                {
                   innerMinDim = $('#<?= $_REQUEST['name_w'] ?>_buttonInnerBackground').height();
                }
                else
                {
                   innerMinDim = $('#<?= $_REQUEST['name_w'] ?>_buttonInnerBackground').width();
                }

                innerBorderRadius = Math.floor((outerBorderRadius / 200)*innerMinDim); //Dividiamo per due oltre che per 100, è un raggio
                $('#<?= $_REQUEST['name_w'] ?>_buttonInnerBackground').css("border-radius", innerBorderRadius + "px");
            }
            
            if((widgetTargetList !== null)&&(widgetTargetList !== undefined))
            {
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

                button.mousedown(function()
                {
                   $('#<?= $_REQUEST['name_w'] ?>_button').addClass('onOffButtonActive');
                });

                button.mouseup(function()
                {
                    $('#<?= $_REQUEST['name_w'] ?>_button').css("background-color", color);
                    $('#<?= $_REQUEST['name_w'] ?>_button').removeClass('onOffButtonActive');

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
                   if (styleParameters.openNewTab == null )
                   {
                       target = "_blank";
                   }
                   else
                   {
                       if (styleParameters.openNewTab === "yes")
                       {
                           target = "_blank";
                      //     console.log("Apre NUOVO TAB !" + target);
                       }
                       else
                       {
                           target = "_self";
                       //    console.log("Apre All'interno dello STESSO TAB ! " + target);
                       }
                   }
                   addLink(widgetMainDivName, url, linkElement, button, target);
               }
            }
            
            
           
            $('#<?= $_REQUEST['name_w'] ?>_buttonText').textfill({
                maxFontPixels: fontSize
            });

        }//Fine funzione populateWidget()
        
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
        
        function resizeWidget()
        {
            setWidgetLayout(hostFile, widgetMainDivName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, hasTimer);
            
            //QUI LASCIARLO, L'OMOLOGO IN SETWIDGETLAYOUT SU QUESTO WIDGET NON FUNZIONA E NON SI CAPISCE PERCHE'
            var widgetCtxMenuBtnCntLeft = $("#<?= $_REQUEST['name_w'] ?>").width() - $("#<?= $_REQUEST['name_w'] ?>_widgetCtxMenuBtnCnt").width();
            $("#<?= $_REQUEST['name_w'] ?>_widgetCtxMenuBtnCnt").css("left", widgetCtxMenuBtnCntLeft + "px");
            
            if(showHeader)
            {
                $('#<?= $_REQUEST['name_w'] ?>_content').height($('#<?= $_REQUEST['name_w'] ?>_div').height() - $('#<?= $_REQUEST['name_w'] ?>_header').height());
            }
            else
            {
                $('#<?= $_REQUEST['name_w'] ?>_content').height($('#<?= $_REQUEST['name_w'] ?>_div').height());
            }
            
            if($("#<?= $_REQUEST['name_w'] ?>_chartContainer").width() > $("#<?= $_REQUEST['name_w'] ?>_chartContainer").height())
            {
                minDim = $("#<?= $_REQUEST['name_w'] ?>_chartContainer").height();
                minDimCells = widgetHeightCells;
                minDimName = "height";
            }
            else
            {
                minDim = $("#<?= $_REQUEST['name_w'] ?>_chartContainer").width();
                minDimCells = widgetWidthCells;
                minDimName = "width";
            }
            
            if(showHeader)
            {
                $('#<?= $_REQUEST['name_w'] ?>_header').show();
                if((2*widgetWidthCells) === widgetHeightCells)
                {
                    buttonPercentHeight = ($('#<?= $_REQUEST['name_w'] ?>_content').height() - 20)*100/$('#<?= $_REQUEST['name_w'] ?>_content').height();
                    buttonPercentWidth = ($('#<?= $_REQUEST['name_w'] ?>_content').height()-20)*100/$('#<?= $_REQUEST['name_w'] ?>_content').width();
                }
                else
                {
                    buttonPercentHeight = ($('#<?= $_REQUEST['name_w'] ?>_content').height()-20)*100/$('#<?= $_REQUEST['name_w'] ?>_content').height();
                    buttonPercentWidth = ($('#<?= $_REQUEST['name_w'] ?>_content').width()-20)*100/$('#<?= $_REQUEST['name_w'] ?>_content').width();
                }
            }
            else
            {
                $('#<?= $_REQUEST['name_w'] ?>_header').hide();
                buttonPercentHeight = ($('#<?= $_REQUEST['name_w'] ?>_content').height()-20)*100/$('#<?= $_REQUEST['name_w'] ?>_content').height();
                buttonPercentWidth = ($('#<?= $_REQUEST['name_w'] ?>_content').width()-20)*100/$('#<?= $_REQUEST['name_w'] ?>_content').width();
            }
            
            $('#<?= $_REQUEST['name_w'] ?>_button').css("width", buttonPercentWidth + "%");
            $('#<?= $_REQUEST['name_w'] ?>_button').css("height", buttonPercentHeight + "%");
            $('#<?= $_REQUEST['name_w'] ?>_button').css("left", parseFloat(100 - buttonPercentWidth)/2 + "%");
            $('#<?= $_REQUEST['name_w'] ?>_button').css("top", parseFloat(100 - buttonPercentHeight)/2 + "%");
            
            $('#<?= $_REQUEST['name_w'] ?>_buttonText').textfill({
                maxFontPixels: fontSize
            });
        }
        //Fine definizioni di funzione 
        
        setWidgetLayout(hostFile, "<?= $_REQUEST['name_w'] ?>", widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, hasTimer);
        $('#<?= $_REQUEST['name_w'] ?>_div').parents('li.gs_w').off('resizeWidgets');
        $('#<?= $_REQUEST['name_w'] ?>_div').parents('li.gs_w').on('resizeWidgets', resizeWidget);
        
        $("#" + widgetName + "_button").focus(function(){
           $(this).css("outline", "none");
        });
        
        widgetProperties = getWidgetProperties(widgetName);
        if((widgetProperties !== null) && (widgetProperties !== 'undefined'))
        {
           widgetTargetList = JSON.parse(widgetProperties.param.parameters);
           styleParameters = jQuery.parseJSON(widgetProperties.param.styleParameters); 
           widgetWidthCells = parseInt(widgetProperties.param.size_columns);
           widgetHeightCells = parseInt(widgetProperties.param.size_rows);
           outerBorderRadius = parseInt(styleParameters.borderRadius);
           if((widgetTargetList !== null)&&(widgetTargetList !== undefined))
           {
               for(var index in widgetTargetList.changeMetricTargetsJson)
                {
                    if(widgetTargetList.changeMetricTargetsJson[index] !== "noMetricChange")
                    {
                        hasChangeMetric = true;
                        break;
                    }
                }
           }
           
           populateWidget();  
        }
        
        $('#<?= $_REQUEST['name_w'] ?>_content').css('background-color', 'transparent');
        $('#<?= $_REQUEST['name_w'] ?> .pcPhoto').hide();
        
        $(document).off('resizeHighchart_' + widgetName);
        $(document).on('resizeHighchart_' + widgetName, function(event) 
        {
            showHeader = event.showHeader;
        });
        
        $("#<?= $_REQUEST['name_w'] ?>").on('customResizeEvent', function(event){
            resizeWidget();
        });
    });//Fine document ready
</script>

<div class="widget" id="<?= $_REQUEST['name_w'] ?>_div">
    <?php include '../widgets/widgetHeader.php'; ?>
    <?php include '../widgets/widgetCtxMenu.php'; ?>

    <div id="<?= $_REQUEST['name_w'] ?>_content" class="content" style="position: relative">
        <?php include '../widgets/commonModules/widgetDimControls.php'; ?>	
        <div id="<?= $_REQUEST['name_w'] ?>_button" class="onOffButton" style="position: relative">
            <div id="<?= $_REQUEST['name_w'] ?>_buttonBefore" class="onOffButtonBefore"></div>
            <div id='<?= $_REQUEST['name_w'] ?>_buttonBackground' class='widgetButtonBackground centerWithFlex'>
               <div id="<?= $_REQUEST['name_w'] ?>_buttonInnerBackground"></div>
            </div> 
            <div id="<?= $_REQUEST['name_w'] ?>_buttonText" class="buttonTxtContainer">
                <span></span>
            </div>
            <div id="<?= $_REQUEST['name_w'] ?>_buttonAfter" class="onOffButtonAfter"></div>
        </div>
    </div>
</div> 