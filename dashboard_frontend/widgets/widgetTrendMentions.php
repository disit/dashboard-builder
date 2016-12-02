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
    $(document).ready(function <?= $_GET['name'] ?>(firstLoad, scrollBottom1Par, scrollBottom2Par) 
    {
        var scroller = null;
        var speed = 75;
        var scrollBottom1 = null;
        var scrollBottom2 = null;
        var actualTab = 1;
        
        if(firstLoad == false)
        {
            scrollBottom1 = scrollBottom1Par;
            scrollBottom2 = scrollBottom2Par;
        }

        function autoScroll()
        {
            var pos = parseInt($("#<?= $_GET['name'] ?>_content").scrollTop());
            $("#<?= $_GET['name'] ?>_content").scrollTop(pos + 1);
            var posNew = parseInt($("#<?= $_GET['name'] ?>_content").scrollTop());
            if(posNew > pos)
            {
                clearTimeout(scroller);
                scroller = setTimeout(arguments.callee, speed);
            }
        }

        function scrollerListener()
        {
            var pos = parseInt($("#<?= $_GET['name'] ?>_content").scrollTop());
            
            switch(actualTab)
            {
                case 1:
                    if(pos == scrollBottom1)
                    {
                        clearTimeout(scroller);
                        setTimeout(function()
                        {
                            $("#<?= $_GET['name'] ?>_content").scrollTop(0);
                            scroller = setTimeout(autoScroll, speed);
                        }, 2000);
                    }
                    break;
                    
                case 2:
                    if(pos == scrollBottom2)
                    {
                        clearTimeout(scroller);
                        setTimeout(function()
                        {
                            $("#<?= $_GET['name'] ?>_content").scrollTop(0);
                            scroller = setTimeout(autoScroll, speed);
                        }, 2000);
                    }
                    break;
            } 
        }
        
        $('[data-toggle="tooltip"]').tooltip(); 
        
        $('#<?= $_GET['name'] ?>_desc').width('74%');
        $('#<?= $_GET['name'] ?>_desc').html('<span><a id ="info_modal" href="#" class="info_source"><img id="source_<?= $_GET['name'] ?>" src="../management/img/info.png" class="source_button"></a><div id="<?= $_GET['name'] ?>_desc_text" class="desc_text" title="<?= preg_replace('/_/', ' ', $_GET['title']) ?>"><?= preg_replace('/_/', ' ', $_GET['title']) ?></div></span>');
        $('#<?= $_GET['name'] ?>_desc_text').css("width", "90%");
        $('#<?= $_GET['name'] ?>_desc_text').css("height", "100%");
        $("#<?= $_GET['name'] ?>_loading").css("background-color", '<?= $_GET['color'] ?>');
        var height = parseInt($("#<?= $_GET['name'] ?>_div").prop("offsetHeight") - 25 - 23);
        var loadingFontDim = 13;
        var loadingIconDim = 20;
        $('#<?= $_GET['name'] ?>_loading').css("height", height+"px");
        $('#<?= $_GET['name'] ?>_loading p').css("font-size", loadingFontDim+"px");
        $('#<?= $_GET['name'] ?>_loading i').css("font-size", loadingIconDim+"px");
        if(firstLoad != false)
        {
            $('#<?= $_GET['name'] ?>_loading').css("display", "block");
        }
        
        $("#<?= $_GET['name'] ?>_content").css("height", height);
        $("#<?= $_GET['name'] ?>_content").css("backgroundColor", '<?= $_GET['color'] ?>');
        $("#<?= $_GET['name'] ?>_tabsContainer").css("backgroundColor", '<?= $_GET['color'] ?>');
        
        
        var sizeRowsWidget = null;
        var icon = null;
        var eventContentW = null;
        var trendsNumber = null;
        var quotesNumber = null;
        var trendsContentHeight = null;
        var quotesContentHeight = null;
        var fakeTrendsDiv = false;
        var fakeQuotesDiv = false;
        
        var colore_frame = "<?= $_GET['frame_color'] ?>";
        var nome_wid = "<?= $_GET['name'] ?>_div";
        $("#<?= $_GET['name'] ?>_div").css({'background-color':colore_frame});
        $('#<?= $_GET['name'] ?>_content').css("overflow", "auto");
        
        
        $("#<?= $_GET['name'] ?>_trends_li").click(function() 
        {
            actualTab = 1;
            $("#<?= $_GET['name'] ?>_quotes_li").removeClass("active");
            $("#<?= $_GET['name'] ?>_trends_li").addClass("active");
            clearTimeout(scroller);
            $("#<?= $_GET['name'] ?>_content").off("scroll");
            $("#<?= $_GET['name'] ?>_content").scrollTop(0);
            $("#<?= $_GET['name'] ?>_content").carousel(0);
            var calcContent = (trendsNumber * 35) - 5;
            var shownHeight = $("#<?= $_GET['name'] ?>_content").prop("offsetHeight");
            scrollBottom1 = calcContent - shownHeight;
            scroller = setTimeout(autoScroll, 2000);
            $("#<?= $_GET['name'] ?>_content").scroll(scrollerListener);
            $("#<?= $_GET['name'] ?>_trends_li a").blur();
            $("#<?= $_GET['name'] ?>_quotes_li a").blur();
            
        });
        
        $("#<?= $_GET['name'] ?>_quotes_li").click(function() 
        {
            actualTab = 2;
            $("#<?= $_GET['name'] ?>_trends_li").removeClass("active");
            $("#<?= $_GET['name'] ?>_quotes_li").addClass("active");
            clearTimeout(scroller);
            $("#<?= $_GET['name'] ?>_content").off("scroll");
            $("#<?= $_GET['name'] ?>_content").scrollTop(0);
            $("#<?= $_GET['name'] ?>_content").carousel(1);
            var calcContent = (quotesNumber * 35) - 5;
            var shownHeight = $("#<?= $_GET['name'] ?>_content").prop("offsetHeight");
            scrollBottom2 = calcContent - shownHeight;
            scroller = setTimeout(autoScroll, 2000);
            $("#<?= $_GET['name'] ?>_content").scroll(scrollerListener);
            $("#<?= $_GET['name'] ?>_trends_li a").blur();
            $("#<?= $_GET['name'] ?>_quotes_li a").blur();
        });
    
        $.ajax({//Inizio AJAX getParametersWidgets.php
            url: "../widgets/getParametersWidgets.php",
            type: "GET",
            data: {"nomeWidget": ["<?= $_GET['name'] ?>"]},
            async: true,
            dataType: 'json',
            success: function (msg) {
                var sizeColumns = null;
                if (msg !== null)
                {
                    sizeColumns = parseInt(msg.param.size_columns);
                }
                
                //Fattore di ingrandimento font calcolato sull'altezza in righe, base 4.
                fontRatio = parseInt((sizeColumns / 4)*15);
                fontRatio = fontRatio.toString() + "px";
                eventContentW = parseInt($('#<?= $_GET['name'] ?>_div').width() - 70 - 17);
                
        
                $.ajax({
                    url: "../widgets/curlProxyForTwitterVg.php?url=<?=$internalTwitterVigilanceHost?>/query/query.php?trends=Firenze",
                    type: "GET",
                    async: true,
                    dataType: 'json',
                    success: function (msg) {
                        var noHashTrend = null;
                        var linkHashTrend = null;
                        var valueTrends = "";
                        var titleTrends = "";
                        
                        if(firstLoad != false)
                        {
                            $('#<?= $_GET['name'] ?>_loading').css("display", "none");
                            $("#<?= $_GET['name'] ?>_tabsContainer").css("display", "block");
                            $('#<?= $_GET['name'] ?>_content').css("display", "block");
                        }
                        
                        if((msg.contents) instanceof Array) 
                        {
                            trendsNumber = msg.contents.length;
                            for (var i = 0; i < trendsNumber; i++) 
                            {
                                noHashTrend = msg.contents[i].request.substring(1);
                                linkHashTrend = "<a href='https://twitter.com/search?q=%23" + noHashTrend + "&src=typd' target='_blank' data-toggle='tooltip' title='See tweets for this trend on Twitter'>" + msg.contents[i].request.toLowerCase() + "</a>";
                                var newRow = $("<div class='twitterRow'></div>");
                                var newIcon = $("<div class='twitterIcon '></div>");
                                var newVigIcon = $("<div class='vigilanceIcon'></div>");
                                var vigilanceLink = "http://www.disit.org/tv/index.php?p=retweet_ricerche&ricerca=%23" + noHashTrend + "&dashboard=true";
                                var vigIcon= $("<a href='" + vigilanceLink + "' target='blank'><i class='fa fa-eye' data-toggle='tooltip' title='See statistics for this trend on Twitter Vigilance'></i></a>");
                                var icon = $("<i class='fa fa-twitter'></i>");
                                newIcon.append(icon);
                                newVigIcon.append(vigIcon);
                                var newContent = $("<div class='twitterContent azzurroGrad'></div>");
                                trendsContentHeight = 35 * msg.contents.length;
                                
                                newContent.html(linkHashTrend);
                                newRow.append(newIcon);
                                newRow.append(newVigIcon);
                                newRow.append(newContent);
                                
                                if(i  == (msg.contents.length - 1))
                                {
                                    newRow.css("margin-bottom", "0px");
                                }
                                $('#<?= $_GET['name'] ?>_trendsContainer').append(newRow);
                            }
                            
                            $('#<?= $_GET['name'] ?>_trendsContainer .azzurroGrad').css("width", eventContentW + "px");
                            switch(sizeColumns)
                            {
                                case 4: 
                                    $('#<?= $_GET['name'] ?>_trendsContainer .azzurroGrad').css("font-size", "18px");
                                    break;
                                    
                                case 5:
                                    $('#<?= $_GET['name'] ?>_trendsContainer .azzurroGrad').css("font-size", "20px");
                                    break;    
                                    
                                case 6: 
                                    $('#<?= $_GET['name'] ?>_trendsContainer .azzurroGrad').css("font-size", "21px");
                                    break;
                                    
                                case 7: 
                                    $('#<?= $_GET['name'] ?>_trendsContainer .azzurroGrad').css("font-size", "22px");
                                    break;
                                    
                                case 8:
                                    $('#<?= $_GET['name'] ?>_trendsContainer .azzurroGrad').css("font-size", "23px");
                                    break;
                                    
                                default:
                                    $('#<?= $_GET['name'] ?>_trendsContainer .azzurroGrad').css("font-size", "20px");
                                    break;
                            }
                            
                            
                            if($('#<?= $_GET['name'] ?>_content').height() > trendsContentHeight)
                            {
                                var diff = parseInt($('#<?= $_GET['name'] ?>_content').height() - trendsContentHeight + 6);
                                var fakeDiv = $("<div></div>");
                                fakeDiv.css("width", "50%");
                                fakeDiv.css("height", diff + "px");
                                $('#<?= $_GET['name'] ?>_trendsContainer').append(fakeDiv);
                                fakeTrendsDiv = true;
                            }
                            
                            $.ajax({
                                url: "../widgets/curlProxyForTwitterVg.php?url=<?=$internalTwitterVigilanceHost?>/query/query.php?mentions=Firenze",
                                type: "GET",
                                async: true,
                                dataType: 'json',
                                success: function (msg2) 
                                {
                                    var valueMentions = "";
                                    var titleMentions = "";
                                    var noAtMention = null;
                                    var linkMention = null;
                                    quotesNumber = msg2.contents.length;
                                    quotesContentHeight = 35 * quotesNumber;
                                    for(var i = 0; i < quotesNumber; i++) 
                                    {
                                        noAtMention = msg2.contents[i].request.substring(1).toLowerCase();
                                        linkMention = "<a href='https://twitter.com/search?q=%40" + noAtMention + "&src=typd' target='_blank' data-toggle='tooltip' title='See Twitter page for this mention'>" + msg2.contents[i].request.toLowerCase() + "</a>";
                                        var newRow = $("<div class='twitterRow'></div>");
                                        var newIcon = $("<div class='twitterIcon turchese'></div>");
                                        var newVigIcon = $("<div class='vigilanceIcon turchese'></div>");
                                        var icon= $("<i class='fa fa-twitter'></i>");
                                        var vigilanceLink = "http://www.disit.org/tv/index.php?p=retweet_ricerche&ricerca=%40" + noAtMention + "&dashboard=true";
                                        var vigIcon= $("<a href='" + vigilanceLink + "' target='blank'><i class='fa fa-eye' data-toggle='tooltip' title='See statistics for this mention on Twitter Vigilance'></i></a>");
                                        newIcon.append(icon);
                                        newVigIcon.append(vigIcon);
                                        var newContent = $("<div class='twitterContent turcheseGrad'></div>");
                                        
                                        newContent.html(linkMention);
                                        newRow.append(newIcon);
                                        newRow.append(newVigIcon);
                                        newRow.append(newContent);
                                        if(i  == (quotesNumber - 1))
                                        {
                                            newRow.css("margin-bottom", "0px");
                                        }
                                        $('#<?= $_GET['name'] ?>_quotesContainer').append(newRow);
                                    }
                                    
                                    $('#<?= $_GET['name'] ?>_quotesContainer .turcheseGrad').css("width", eventContentW + "px");
                                    
                                    switch(sizeColumns)
                                    {
                                        case 4:
                                            $('#<?= $_GET['name'] ?>_quotesContainer .turcheseGrad').css("font-size", "18px");
                                            break;
                                            
                                        case 5:
                                            $('#<?= $_GET['name'] ?>_quotesContainer .turcheseGrad').css("font-size", "20px");
                                            break;    

                                        case 6: 
                                            $('#<?= $_GET['name'] ?>_quotesContainer .turcheseGrad').css("font-size", "21px");
                                            break;
                                            
                                        case 7: 
                                            $('#<?= $_GET['name'] ?>_quotesContainer .turcheseGrad').css("font-size", "22px");
                                            break;
                                            
                                        case 8:
                                            $('#<?= $_GET['name'] ?>_quotesContainer .turcheseGrad').css("font-size", "23px");
                                            break;

                                        default:
                                            $('#<?= $_GET['name'] ?>_quotesContainer .turcheseGrad').css("font-size", "20px");
                                            break;
                                    }
                                    
                                    if($('#<?= $_GET['name'] ?>_content').height() > quotesContentHeight)
                                    {
                                        var diff = parseInt($('#<?= $_GET['name'] ?>_content').height() - quotesContentHeight + 6);
                                        var fakeDiv = $("<div></div>");
                                        fakeDiv.css("width", "50%");
                                        fakeDiv.css("height", diff + "px");
                                        $('#<?= $_GET['name'] ?>_quotesContainer').append(fakeDiv);
                                        fakeQuotesDiv = true;
                                    }
                                }
                            });
                        } 
                        else 
                        {
                            $("#<?= $_GET['name'] ?>_content").html("<p><b>Principali Twitter Trends:</b> nessun dato disponibile</p><p><b>Citazioni:</b> nessun dato disponibile</p>");
                        }
                        
                        var calcContent1 = (trendsNumber * 35) - 5;
                        var calcContent2 = (quotesNumber * 35) - 5;
                        var shownHeight = $("#<?= $_GET['name'] ?>_content").prop("offsetHeight");
                        if((firstLoad != false) && (actualTab == 1))
                        {
                            scrollBottom1 = calcContent1 - shownHeight;
                        }
                        
                        if((firstLoad != false) && (actualTab == 2))
                        {
                            scrollBottom2 = calcContent2 - shownHeight;
                        }
                        
                        $('#<?= $_GET['name'] ?>_content').on('slid.bs.carousel', function (ev) 
                        {
                            var id = ev.relatedTarget.id;
                            switch(id)
                            {
                                case "<?= $_GET['name'] ?>_trendsContainer":  
                                    $("#<?= $_GET['name'] ?>_trends_li").attr("class", "active");
                                    $("#<?= $_GET['name'] ?>_quotes_li").attr("class", "");        
                                    break;
                            
                                case "<?= $_GET['name'] ?>_quotesContainer":
                                    $("#<?= $_GET['name'] ?>_trends_li").attr("class", "");
                                    $("#<?= $_GET['name'] ?>_quotes_li").attr("class", "active");        
                                    break;    
                            }
                        });
                        
                        if(!fakeTrendsDiv)
                        {
                            scroller = setTimeout(autoScroll, 2000);
                            $("#<?= $_GET['name'] ?>_content").scroll(scrollerListener);
                        }
                        
                        $("#<?= $_GET['name'] ?>_content").mouseenter(function() 
                        {
                            $("#<?= $_GET['name'] ?>_content").off("scroll");
                            clearTimeout(scroller);
                        });
                        
                        $("#<?= $_GET['name'] ?>_content").mouseleave(function(){
                            clearTimeout(scroller);
                            $("#<?= $_GET['name'] ?>_content").off("scroll");
                            
                            scroller = setTimeout(autoScroll, speed);
                            $("#<?= $_GET['name'] ?>_content").scroll(scrollerListener);
 
                            if(($("#<?= $_GET['name'] ?>_content").scrollTop() == scrollBottom1) && (actualTab == 1))
                            {
                                $("#<?= $_GET['name'] ?>_content").scrollTop(0);
                            }
                            
                            if(($("#<?= $_GET['name'] ?>_content").scrollTop() == scrollBottom2) && (actualTab == 2))
                            {
                                $("#<?= $_GET['name'] ?>_content").scrollTop(0);
                            }
                        });
                       
                        $('#source_<?= $_GET['name'] ?>').on('click', function () {
                            $('#dialog_<?= $_GET['name'] ?>').show();

                        });

                        $('#close_popup_<?= $_GET['name'] ?>').on('click', function () {
                            $('#dialog_<?= $_GET['name'] ?>').hide();
                        });

                        var counter = <?= $_GET['freq'] ?>;
                        var countdown = setInterval(function () {
                            $("#countdown_<?= $_GET['name'] ?>").text(counter);
                            counter--;
                            if (counter > 60) {
                                $("#countdown_<?= $_GET['name'] ?>").text(Math.floor(counter / 60) + "m");
                            } else {
                                $("#countdown_<?= $_GET['name'] ?>").text(counter + "s");
                            }
                            if (counter === 0) {
                                $("#countdown_<?= $_GET['name'] ?>").text(counter + "s");
                                clearInterval(countdown);
                                $('#<?= $_GET['name'] ?>_trendsContainer').empty();
                                $('#<?= $_GET['name'] ?>_quotesContainer').empty();
                                clearTimeout(scroller);
                                $("#<?= $_GET['name'] ?>_content").off();
                                $("#<?= $_GET['name'] ?>_content").scrollTop(0);
                                $("#<?= $_GET['name'] ?>_content").carousel(0);
                                $("#<?= $_GET['name'] ?>_trends_li").off();
                                $("#<?= $_GET['name'] ?>_quotes_li").off();
                                quotesNumber = null;
                                trendsNumber = null;
                                setTimeout(<?= $_GET['name'] ?>(false, scrollBottom1, scrollBottom2), 1000);
                            }
                        }, 1000);
                    }
                });
            }//Chiusura success getParametersWidgets
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
        
        <div id="<?= $_GET['name'] ?>_tabsContainer" class="twitterTabsContainer">
            <ul id="<?= $_GET['name'] ?>_nav_ul" class="nav nav-tabs nav_ul twitterTabs">
                <li role="navigation" id="<?= $_GET['name'] ?>_trends_li" class="active"><a disabled="true">trends</a></li>
                <li role="navigation" id="<?= $_GET['name'] ?>_quotes_li"><a disabled="true">quotes</a></li>
            </ul>
        </div>
        
        <div id="<?= $_GET['name'] ?>_content" class="twitterContent carousel slide" data-interval="false" data-pause="hover">
            <!-- Wrapper per il carousel -->
            <div id="<?= $_GET['name'] ?>_carousel" class="carousel-inner" role="listbox">
                <div id="<?= $_GET['name'] ?>_trendsContainer" class="item active">  
                </div>
                <div id="<?= $_GET['name'] ?>_quotesContainer" class="item">   
                </div>
            </div>
        </div> 
    </div>	
</div> 