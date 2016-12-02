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
    var contentHeight = null;
    var shownHeight = null;
    var speed = 50;
    var rewindDelay = 1500;
    var scrollBottom = null;
    
    $(document).ready(function <?= $_GET['name'] ?>(firstLoad)  
    {
        var scroller = null;
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
        
        name = "<?= $_REQUEST['name'] ?>";
        $("#<?= $_GET['name'] ?>_logo").css("background-color", '<?= $_GET['frame_color'] ?>');
        
        var height = parseInt($("#<?= $_GET['name'] ?>_div").prop("offsetHeight") - 25);
        var loadingFontDim = 13; 
        var loadingIconDim = 20;
        $('#<?= $_GET['name'] ?>_loading').css("height", height+"px");
        $('#<?= $_GET['name'] ?>_loading p').css("font-size", loadingFontDim+"px");
        $('#<?= $_GET['name'] ?>_loading i').css("font-size", loadingIconDim+"px");
        $("#<?= $_GET['name'] ?>_loading").css("background-color", '<?= $_GET['color'] ?>');
        if(firstLoad != false)
        {
            $('#<?= $_GET['name'] ?>_loading').css("display", "block");
        }
        
        $("#<?= $_GET['name'] ?>_content").css("height", height);
        
        var sizeRowsWidget = null;
        var fontRatio = null;
        var fontRatioSmall = null;
          
        var link_w = "<?= $_GET['link_w'] ?>";
        var divChartContainer = $('#<?= $_GET['name'] ?>_logoPc');
        var linkElement = $('#<?= $_GET['name'] ?>_link_w');
        
         $.ajax({//Inizio AJAX getParametersWidgets.php
            url: "../widgets/getParametersWidgets.php",
            type: "GET",
            data: {"nomeWidget": ["<?= $_GET['name'] ?>"]},
            async: false,
            dataType: 'json',
            success: function (msg) 
            {
                var moveDownRef = null;
                var contentSel = null; 
                var idWidget = null;
                var idDash = null;
                var name = null;
                
                var counter = <?= $_GET['freq'] ?>;
                
                if (msg != null)
                {
                    udm = msg.param.udm;
                    sizeRowsWidget = parseInt(msg.param.size_rows);
                    idWidget = msg.param.Id;
                    idDash = msg.param.id_dashboard;
                }
                
                contentSel = "#ProtezioneCivile_" + idDash + "_widgetProtezioneCivile" + idWidget + "_content";
                
                $.ajax({
                    url: "http://protezionecivile.comune.fi.it/?cat=5&feed=json",
                    type: "GET",
                    async: false,
                    dataType: 'jsonp',
                    success: function (msg) 
                    {
                        $("#<?= $_GET['name'] ?>_content").css("background-color", '<?= $_GET['color'] ?>');
                        
                        if(msg == null)
                        {
                            if(firstLoad != false)
                            {
                                $('#<?= $_GET['name'] ?>_loading').css("display", "none");
                                $('#<?= $_GET['name'] ?>_content').css("display", "block");
                            }
                            $('#<?= $_GET['name'] ?>_content').html("<p style='text-align: center; font-size: 18px;'>Nessun dato disponibile</p>");
                        }
                        else
                        {
                            //Fattore di ingrandimento font calcolato sull'altezza in righe, base 4.
                            fontRatio = parseInt((sizeRowsWidget / 4)*90);
                            fontRatioSmall = parseInt((fontRatio / 100)*40);
                            fontRatio = fontRatio.toString() + "%";
                            fontRatioSmall = fontRatioSmall.toString() + "%";

                            var id = msg[0].permalink;
                            var title = msg[0].title;
                            var permalink = msg[0].permalink;
                            var content = msg[0].content;
                            var date = msg[0].date;
                            var author = msg[0].author;
                            var categories = msg[0].categories;
                            var tags = msg[0].tags;
                            
                            content = content.replace(/\[iframe.*\]/g, "");
                            var fakeContent = '<p>Regione Toscana prevede:<b><b></b></b></p>' +
                                '<b>A3 &#8211; Arno-Firenze</b><br/>' +
                                '<table width="100%" style="border-collapse:collapse">'+
                                '<tr>'+
                                '<th width="33%">RISCHIO</th>'+
                                '<th width="33%">TEMPI</th>'+
                                '<th width="33%">CRITICITÀ</th>'+
                                '</tr>'+
                                '<tr>'+
                                '<td style="border-bottom: 1pt solid black;">TEMPORALI</td>' +
                                '<td style="border-bottom: 1pt solid black;">da 06/11/2016<br/>'+
                                'a 06/11/2016</td>'+
                                '<td style="border-bottom: 1pt solid black;"><b>ARANCIONE</b></td>'+
                                '</tr>'+
                                '<tr>'+
                                '<td style="border-bottom: 1pt solid black;">TEMPORALI</td>' +
                                '<td style="border-bottom: 1pt solid black;">da 15/11/2016<br />'+
                                'a 16/11/2016</td>'+
                                '<td style="border-bottom: 1pt solid black;"><b>ROSSO</b></td>'+
                                '</tr>'+
                                '<tr>'+
                                '<td style="border-bottom: 1pt solid black;">NEVE</td>' +
                                '<td style="border-bottom: 1pt solid black;">da 06/12/2016<br />'+
                                'a 06/12/2016</td>'+
                                '<td style="border-bottom: 1pt solid black;"><b>ARANCIONE</b></td>'+
                                '</tr>'+
                                '<tr>'+
                                '<td style="border-bottom: 1pt solid black;">NEVE</td>' +
                                '<td style="border-bottom: 1pt solid black;">da 07/12/2016<br />'+
                                'a 08/11/2016</td>'+
                                '<td style="border-bottom: 1pt solid black;"><b>ARANCIONE</b></td>'+
                                '</tr>'+
                                '<tr >'+
                                '<td style="border-bottom: 1pt solid black;">FORTE VENTO</td>' +
                                '<td style="border-bottom: 1pt solid black;">da 12/12/2016<br />'+
                                'a 06/11/2016</td>'+
                                '<td style="border-bottom: 1pt solid black;"><b>ARANCIONE</b></td>'+
                                '</tr>'+
                                '<tr>'+
                                '<td style="border-bottom: 1pt solid black;">TEMPORALI</td>' +
                                '<td style="border-bottom: 1pt solid black;">da 15/12/2016<br />'+
                                'a 16/12/2016</td>'+
                                '<td style="border-bottom: 1pt solid black;"><b>ARANCIONE</b></td>'+
                                '</tr>'+
                                '<tr>'+
                                '<td style="border-bottom: 1pt solid black;">NEVE</td>' +
                                '<td style="border-bottom: 1pt solid black;">da 20/12/2016<br />'+
                                'a 21/12/2016</td>'+
                                '<td style="border-bottom: 1pt solid black;"><b>ARANCIONE</b></td>'+
                                '</tr>'+
                                '</table>';
                                       
                            $(contentSel).append(fakeContent);
                            if(firstLoad != false)
                            {
                                $('#<?= $_GET['name'] ?>_loading').css("display", "none");
                                $(contentSel).css("display", "block");
                            }
                            
                            $(contentSel).scrollTop(0);
                            contentHeight = $(contentSel)[0].scrollHeight;
                            shownHeight = $(contentSel).prop("offsetHeight");
                            scrollBottom = contentHeight - shownHeight;
                            
                            scroller = setTimeout(autoScroll, 2000);
                            $("#<?= $_GET['name'] ?>_content").scroll(scrollerListener);
                            
                            $(contentSel).mouseenter(function() 
                            {
                                $("#<?= $_GET['name'] ?>_content").off("scroll");
                                clearTimeout(scroller);
                            });
                            
                            $(contentSel).mouseleave(function() 
                            {
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
                        }
                        
                        if(link_w.trim()) 
                        {
                            if(linkElement.length === 0)
                            {
                               linkElement = $("<a id='<?= $_GET['name'] ?>_link_w' href='<?= $_GET['link_w'] ?>' target='_blank' class='elementLink2'>");
                               divChartContainer.wrap(linkElement); 
                            }
                        }
                        var counter = <?= $_GET['freq'] ?>;
                        var countdown = setInterval(function () 
                        {
                            var ref = "#ProtezioneCivile_" + idDash + "_widgetProtezioneCivile" + idWidget + "_div .pcCountdown"; // 
                            $(ref).text(counter);
                            counter--;
                            if (counter > 60) 
                            {
                                $(ref).text(Math.floor(counter / 60) + "m");
                            } 
                            else 
                            {
                                $(ref).text(counter + "s");
                            }
                            if (counter === 0) 
                            {
                                $(ref).text(counter + "s");
                                clearTimeout(scroller);
                                $("#<?= $_GET['name'] ?>_content").off();
                                //clearInterval(moveDownRef);
                                $(contentSel).html("");
                                clearInterval(countdown);
                                setTimeout(<?= $_GET['name'] ?>(false), 1000);
                            }
                        }, 1000);
                    }
                });
            }
        });   
});
                            
       
</script>

<div class="widget" id="<?= $_GET['name'] ?>_div">
    <div class='ui-widget-content'>
        <div id='<?= $_GET['name'] ?>_logo' class="pcLogosContainer">
            <div id="<?= $_GET['name'] ?>_info" class="pcInfoContainer">
                <a id ="info_modal" href="#" class="info_source">
                    <img id="source_<?= $_GET['name'] ?>" src="../management/img/info.png" class="source_button">
                </a>
            </div>
            <div id="<?= $_GET['name'] ?>_logoPc" class="logoPc">
                <img src="../img/protezioneCivile.png">
            </div>
            
            <div id="<?= $_GET['name'] ?>_iconsModifyWidget" class="iconsModifyPcWidget">
                <a class="icon-cfg-widget" href="#">
                    <span class="glyphicon glyphicon-cog glyphicon-modify-widget" aria-hidden="true"></span>
                </a>
                <a class="icon-remove-widget" href="#">
                    <span class="glyphicon glyphicon-remove glyphicon-modify-widget" aria-hidden="true"></span>
                </a>
                <div id="countdown_<?= $_GET['name'] ?>" class="pcCountdown"></div>
            </div>
            <div id="<?= $_GET['name'] ?>_pcCountdownContainer" class="pcCountdownContainer">
                <div id="countdown_<?= $_GET['name'] ?>" class="pcCountdown"></div>
            </div>
        </div>
        <div id="<?= $_GET['name'] ?>_loading" class="loadingDiv">
            <div class="loadingTextDiv">
                <p>Loading data, please wait</p>
            </div>
            <div class ="loadingIconDiv">
                <i class='fa fa-spinner fa-spin'></i>
            </div>
        </div>
        <div id='<?= $_GET['name'] ?>_content' class="content pcContainer"></div>
    </div>	
</div> 
