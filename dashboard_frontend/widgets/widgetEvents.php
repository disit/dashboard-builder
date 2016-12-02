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
    $(document).ready(function event_data(firstLoad) 
    {
        var scroller = null;
        var scrollBottom = null;
        var speed = 50;
        function autoScroll()
        {
            var pos = $("#<?= $_GET['name'] ?>_content").scrollTop();
            pos++;
            $("#<?= $_GET['name'] ?>_content").scrollTop(pos); 
            clearTimeout(scroller);
            scroller = setTimeout(arguments.callee, speed);
        }
        
        function scrollerListener()
        {
            var pos = parseInt($("#<?= $_GET['name'] ?>_content").scrollTop());
            if(pos == scrollBottom)
            {
                clearTimeout(scroller);
                setTimeout(function()
                {
                    $("#<?= $_GET['name'] ?>_content").scrollTop(0);
                    scroller = setTimeout(autoScroll, speed);
                }, 2000);
            }
        }
        
        var loadingFontDim = 13; 
        var loadingIconDim = 20;
        
        
        $('#<?= $_GET['name'] ?>_desc').width('74%');
        $('#<?= $_GET['name'] ?>_desc').html('<span><a id ="info_modal" href="#" class="info_source"><img id="source_<?= $_GET['name'] ?>" src="../management/img/info.png" class="source_button"></a><div id="<?= $_GET['name'] ?>_desc_text" class="desc_text" title="<?= preg_replace('/_/', ' ', $_GET['title']) ?>"><?= preg_replace('/_/', ' ', $_GET['title']) ?></div></span>');
        $('#<?= $_GET['name'] ?>_desc_text').css("width", "80%");
        $('#<?= $_GET['name'] ?>_desc_text').css("height", "100%");
        $("#<?= $_GET['name'] ?>_loading").css("background-color", '<?= $_GET['color'] ?>');
        var height = parseInt($("#<?= $_GET['name'] ?>_div").prop("offsetHeight") - 25);
        $("#<?= $_GET['name'] ?>_content").css("height", height);
        $("#<?= $_GET['name'] ?>_content").css("background-color", '<?= $_GET['color'] ?>');
        $('#<?= $_GET['name'] ?>_loading').css("height", height+"px");
        $('#<?= $_GET['name'] ?>_loading p').css("font-size", loadingFontDim+"px");
        $('#<?= $_GET['name'] ?>_loading i').css("font-size", loadingIconDim+"px");
        if(firstLoad != false)
        {
            $('#<?= $_GET['name'] ?>_loading').css("display", "block");
        }
        
        var colore_frame = "<?= $_GET['frame_color'] ?>";
       
        $("#<?= $_GET['name'] ?>_div").css({'background-color':colore_frame});
        
        $.ajax({
            url: "../widgets/curlProxy.php?url=<?=$internalServiceMapUrlPrefix?>api/v1/events/?range=day",
            type: "GET",
            async: true,
            dataType: 'json',
            success: function (msg) {
                $.ajax({
                    url: "../widgets/getParametersWidgets.php",
                    type: "GET",
                    data: {"nomeWidget": ["<?= $_GET['name'] ?>"]},
                    async: true,
                    dataType: 'json',
                    success: function (msg2) {
                        var parametri = msg2.param.parameters;
                        var contenuto = jQuery.parseJSON(parametri);
                        var sizeRowsWidget = parseInt(msg2.param.size_rows);
                        
                        if(sizeRowsWidget >= 3)
                        {
                            $('#<?= $_GET['name'] ?>_desc_text').css("width", "90%");
                        }
                    }
                });
                
                var newRow = null;
                var newIcon = null;
                var newContent = null;
                var eventContentW = null;
                var eventsNumber = msg.contents.Event.features.length;
                var contentHeight = eventsNumber * 55;
                var eventType = null;
                var eventName = null;
                var startDate = null;
                var endDate = null;
                var description = null;
                var serviceUri = null;
                var eventId = null;
                var icon = null;
                var feeIcon = null;
                var address = null;
                var freeEvent = null;
                
                if($('#<?= $_GET['name'] ?>_content').height() < contentHeight)
                {
                    eventContentW = parseInt($('#<?= $_GET['name'] ?>_div').width() - 45 - 22);
                }
                else
                {
                    eventContentW = parseInt($('#<?= $_GET['name'] ?>_div').width() - 45 - 5);
                }
                
                $('#<?= $_GET['name'] ?>_content').empty();
                
                for (var i = 0; i < eventsNumber; i = i + 3)
                {
                    for(var z =0; z < 3; z++)
                    {
                        if((i + z) < eventsNumber)
                        {
                            eventType = msg.contents.Event.features[i+z].properties.categoryIT;
                            eventName = msg.contents.Event.features[i+z].properties.name.toLowerCase();
                            if(eventName.length > 77)
                            {
                                eventName = eventName.substring(0, 70) + "...";
                            }
                            
                            startDate = msg.contents.Event.features[i+z].properties.startDate;
                            endDate = msg.contents.Event.features[i+z].properties.endDate;
                            description = msg.contents.Event.features[i+z].properties.descriptionIT;
                            serviceUri = msg.contents.Event.features[i+z].properties.serviceUri;
                            address = msg.contents.Event.features[i+z].properties.address + " " + msg.contents.Event.features[i+z].properties.civic;
                            freeEvent = msg.contents.Event.features[i+z].properties.freeEvent;
                            var index = serviceUri.indexOf("Event");
                            eventId = serviceUri.substring(index);
                            
                            newRow = $("<div class='eventsRow'></div>");
                            newIcon = $("<div class='eventIcon turchese'></div>");
                            var newIconUp = $("<div class='eventIconUp'></div>");
                            var newIconDown = $("<div class='eventIconDown'></div>");
                            newIcon.append(newIconUp);
                            newIcon.append(newIconDown);
                            newContent = $("<div class='eventContent turcheseGrad'></div>");
                            newContent.css("width", eventContentW + "px");
                            newContent.html("<div class='eventName'>" + eventName + "</div>" + "<div class='eventTime'><i class='fa fa-calendar' style='font-size:13px'></i>&nbsp;&nbsp;" + startDate + " to " + endDate + "</div>" + "<div class='eventAddress'><a href='http://servicemap.disit.org/WebAppGrafo/api/v1/?serviceUri=http://www.disit.org/km4city/resource/" + eventId + "&format=html' class='eventLink' target='_blank'><i class='material-icons' style='font-size:16px'>place</i>&nbsp;" + address + "</a></div>");
                            
                            switch (eventType)
                            {
                                case "Mostre":
                                    icon= $("<i class='fa fa-bank'></i>");
                                    newIconUp.append(icon);
                                    break;
                                    
                                case "News":
                                    icon= $("<i class='fa fa-newspaper-o'></i>");
                                    newIconUp.append(icon);
                                    break;
                                    
                                case "Aperture straordinarie, visite guidate":
                                    icon= $("<i class='material-icons' style='font-size:36px'>group</i>");
                                    newIconUp.append(icon);
                                    break;    
                                    
                                default:
                                    icon= $("<i class='fa fa-calendar-check-o'></i>");
                                    newIconUp.append(icon);
                                    break;
                            }
                            
                            if(freeEvent == 'NO')
                            {
                                feeIcon = $("<i class='fa fa-euro'></i>");
                                newIconDown.append(feeIcon); 
                            }
                            else
                            {
                                newIconDown.html("free");
                            }
                               
                            
                            switch(z)
                            {
                                case 0:
                                    newIcon.addClass("turchese");
                                    newContent.addClass("turcheseGrad");
                                    break;

                                case 1:
                                    newIcon.addClass("arancio");
                                    newContent.addClass("arancioGrad");
                                    break;

                                case 2:
                                    newIcon.addClass("viola");
                                    newContent.addClass("violaGrad");
                                    break;    
                            }
                            
                            newRow.append(newIcon);
                            newRow.append(newContent);
                            $('#<?= $_GET['name'] ?>_content').append(newRow);
                            newRow.css("background-color", "<?= $_GET['color'] ?>");
                            
                            if((i + z) == (eventsNumber - 1))
                            {
                                newRow.css("margin-bottom", "0px");
                            }
                            $("#<?= $_GET['name'] ?>_content").scrollTop(0);
                        }
                    }
                }
                if(firstLoad != false)
                {
                    $('#<?= $_GET['name'] ?>_loading').css("display", "none");
                    $("#<?= $_GET['name'] ?>_content").css("backgroundColor", '<?= $_GET['color'] ?>');
                    $('#<?= $_GET['name'] ?>_content').css("display", "block");
                }
                
                var content = $("#<?= $_GET['name'] ?>_content").prop("scrollHeight");
                var shownHeight = $("#<?= $_GET['name'] ?>_content").prop("offsetHeight");
                scrollBottom = content - shownHeight;
    
                scroller = setTimeout(autoScroll, 2000);
                $("#<?= $_GET['name'] ?>_content").scroll(scrollerListener);
                
                $("#<?= $_GET['name'] ?>_content").mouseenter(function() 
                {
                    $("#<?= $_GET['name'] ?>_content").off("scroll");
                    clearTimeout(scroller);
                });
    
                $("#<?= $_GET['name'] ?>_content").mouseleave(function(){
                    clearTimeout(scroller);
                    $("#<?= $_GET['name'] ?>_content").off("scroll");
                    if($("#<?= $_GET['name'] ?>_content").scrollTop() == scrollBottom)
                    {
                        setTimeout(function()
                        {
                            $("#<?= $_GET['name'] ?>_content").scrollTop(0);
                        }, 1000);
                    }
                    scroller = setTimeout(autoScroll, speed);
                    $("#<?= $_GET['name'] ?>_content").scroll(scrollerListener);
                });
                
                
                $('#source_<?= $_GET['name'] ?>').on('click', function () {
                    $('#dialog_<?= $_GET['name'] ?>').show();

                });
                $('#close_popup_<?= $_GET['name'] ?>').on('click', function () {

                    $('#dialog_<?= $_GET['name'] ?>').hide();

                });

                var counter = <?= $_GET['freq'] ?>;
                var countdown = setInterval(function () {
                    $("#countdown_event_data").text(counter);
                    counter--;
                    if (counter > 60) {
                        $("#countdown_<?= $_GET['name'] ?>").text(Math.floor(counter / 60) + "m");
                    } else {
                        $("#countdown_<?= $_GET['name'] ?>").text(counter + "s");
                    }
                    if (counter === 0) {
                        $("#countdown_<?= $_GET['name'] ?>").text(counter + "s");
                        clearTimeout(scroller);
                        $("#<?= $_GET['name'] ?>_content").off();
                        clearInterval(countdown);
                        setTimeout(event_data(false), 1000);
                    }
                }, 1000);
            },
            error: function (jqXHR, textStatus, errorThrow) {
                alert(errorThrow);
            }
        });
    });
</script>

<div class="widget" id="<?= $_GET['name'] ?>_div">
    <div class='ui-widget-content'>
        <div id="<?= $_GET['name'] ?>_desc" class="desc"></div><div class="icons-modify-widget"><a class="icon-cfg-widget" href="#"><span class="glyphicon glyphicon-cog glyphicon-modify-widget" aria-hidden="true"></span></a><a class="icon-remove-widget" href="#"><span class="glyphicon glyphicon-remove glyphicon-modify-widget" aria-hidden="true"></span></a></div><div id="countdown_<?= $_GET['name'] ?>" class="countdown"></div>  
        <div id="<?= $_GET['name'] ?>_loading" class="loadingDiv">
            <div class="loadingTextDiv">
                <p>Loading data, please wait</p>
            </div>
            <div class ="loadingIconDiv">
                <i class='fa fa-spinner fa-spin'></i>
            </div>
        </div>
        <div id="<?= $_GET['name'] ?>_content" class="content event_data">
            
        </div> 
    </div>	
</div> 