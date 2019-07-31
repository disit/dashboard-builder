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
        <?php
            $link = mysqli_connect($host, $username, $password);
            if (checkWidgetNameInDashboard($link, $_REQUEST['name_w'], $_REQUEST['id_dashboard']) === false) {
                eventLog("Returned the following ERROR in widgetTrendMentions.php for the widget ".escapeForHTML($_REQUEST['name_w'])." is not instantiated or allowed in this dashboard.");
                exit();
            }
        ?>

        var scroller1, scroller2, scrollBottom1, scrollBottom2, contentHeight, trendsNumber, quotesNumber, trendsContentHeight, quotesContentHeight, 
            rowPercHeight, rowPxHeight, fullRowPxHeight, showHeader, timeToReload, actualTab, countdown, timeToClearScroll, titleWidth, fontRatio, fullRowPercHeight, contentPercWidth, iconWidth, contentWidth = null;
        var hostFile = "<?= $_REQUEST['hostFile'] ?>";
        var widgetName = "<?= $_REQUEST['name_w'] ?>";
        var speed = 140;
        var defaultTab = parseInt("<?= $_REQUEST['defaultTab'] ?>");
        actualTab = 1;
        var hasTimer = "<?= $_REQUEST['hasTimer'] ?>";
        var embedWidget = <?= $_REQUEST['embedWidget'] ?>;
        var embedWidgetPolicy = '<?= $_REQUEST['embedWidgetPolicy'] ?>';	
        var headerHeight = 25;
        var showTitle = "<?= $_REQUEST['showTitle'] ?>";
    //    console.log("Widget Trend Mentions: " + widgetName);
        if(((embedWidget === true)&&(embedWidgetPolicy === 'auto'))||((embedWidget === true)&&(embedWidgetPolicy === 'manual')&&(showTitle === "no"))||((embedWidget === false)&&(showTitle === "no")))
        {
            var height = parseInt($("#<?= $_REQUEST['name_w'] ?>_div").prop("offsetHeight") - 23);
            $('#<?= $_REQUEST['name_w'] ?>_header').hide();
            showHeader = false;
        }
        else
        {
            var height = parseInt($("#<?= $_REQUEST['name_w'] ?>_div").prop("offsetHeight") - headerHeight - 23);
            $('#<?= $_REQUEST['name_w'] ?>_header').show();
            showHeader = true;
        }
        
        var counter = parseInt('<?= $_REQUEST['frequency_w'] ?>');
        
        function stepDownInterval1()
        {
            var pos = $('#<?= $_REQUEST['name_w'] ?>_content').scrollTop();
            if(pos < (scrollBottom1 - 15))
            {
                pos++;
            }
            else
            {
                pos = 0;
            }
            $('#<?= $_REQUEST['name_w'] ?>_content').scrollTop(pos);
        }
        
        function stepDownInterval2()
        {
            var pos = $('#<?= $_REQUEST['name_w'] ?>_content').scrollTop();
            
            if(pos < (scrollBottom2 - 15))
            {
                pos++;
            }
            else
            {
                pos = 0;
            }
            $('#<?= $_REQUEST['name_w'] ?>_content').scrollTop(pos);
        }
        
        $('[data-toggle="tooltip"]').tooltip(); 
        
        <?php
            $titlePatterns = array();
            $titlePatterns[0] = '/_/';
            $titlePatterns[1] = '/\'/';
            $replacements = array();
            $replacements[0] = ' ';
            $replacements[1] = '&apos;';
            $title = $_REQUEST['title_w'];
        ?>
        
        var hostFile = "<?= $_REQUEST['hostFile'] ?>";
        
        if(hostFile === "config")
        {
            titleWidth = parseInt(parseInt($("#<?= $_REQUEST['name_w'] ?>_div").width() - 25 - 50 - 25 - 2));
        }
        else
        {
            $("#<?= $_REQUEST['name_w'] ?>_buttonsDiv").css("display", "none");
            titleWidth = parseInt(parseInt($("#<?= $_REQUEST['name_w'] ?>_div").width() - 25 - 25 - 2));
        }
        
        $("#<?= $_REQUEST['name_w'] ?>_titleDiv").css("width", titleWidth + "px");
        $("#<?= $_REQUEST['name_w'] ?>_titleDiv").css("color", "<?= $_REQUEST['headerFontColor'] ?>");

        $("#<?= $_REQUEST['name_w'] ?>_countdownDiv").css("color", "<?= $_REQUEST['headerFontColor'] ?>");
        $("#<?= $_REQUEST['name_w'] ?>_loading").css("background-color", '<?= $_REQUEST['color_w'] ?>');
        
        
        var loadingFontDim = 13;
        var loadingIconDim = 20;
        
        $('#<?= $_REQUEST['name_w'] ?>_loading').css("height", height+"px");
        $('#<?= $_REQUEST['name_w'] ?>_loading p').css("font-size", loadingFontDim+"px");
        $('#<?= $_REQUEST['name_w'] ?>_loading i').css("font-size", loadingIconDim+"px");
        
        if(firstLoad !== false)
        {
            $('#<?= $_REQUEST['name_w'] ?>_loading').css("display", "block");
        }
        
        $("#<?= $_REQUEST['name_w'] ?>_content").css("height", height);
        $("#<?= $_REQUEST['name_w'] ?>_content").css("backgroundColor", '<?= $_REQUEST['color_w'] ?>');
        $("#<?= $_REQUEST['name_w'] ?>_tabsContainer").css("backgroundColor", '<?= $_REQUEST['color_w'] ?>');
        
        var colore_frame = "<?= $_REQUEST['frame_color_w'] ?>";
        
        $("#<?= $_REQUEST['name_w'] ?>_div").css({'background-color':colore_frame});
        $('#<?= $_REQUEST['name_w'] ?>_content').css("overflow-y", "scroll");
        $('#<?= $_REQUEST['name_w'] ?>_content').css("overflow-x", "auto");
		
		//DA QUI
		//setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
        
        $("#<?= $_REQUEST['name_w'] ?>_trends_li").click(function() 
        {
            actualTab = 1;
            $("#<?= $_REQUEST['name_w'] ?>_quotes_li").removeClass("active");
            $("#<?= $_REQUEST['name_w'] ?>_trends_li").addClass("active");
            clearInterval(scroller2);
            $("#<?= $_REQUEST['name_w'] ?>_content").scrollTop(0);
            $("#<?= $_REQUEST['name_w'] ?>_content").carousel(0);
            var calcContent = (trendsNumber * 30);
            var shownHeight = $("#<?= $_REQUEST['name_w'] ?>_content").prop("offsetHeight");
            scrollBottom1 = calcContent - shownHeight - 2;
            scroller1 = setInterval(stepDownInterval1, speed);
            $("#<?= $_REQUEST['name_w'] ?>_trends_li a").blur();
            $("#<?= $_REQUEST['name_w'] ?>_quotes_li a").blur();
            
        });
        
        $("#<?= $_REQUEST['name_w'] ?>_quotes_li").click(function() 
        {
            actualTab = 2;
            $("#<?= $_REQUEST['name_w'] ?>_trends_li").removeClass("active");
            $("#<?= $_REQUEST['name_w'] ?>_quotes_li").addClass("active");
            clearInterval(scroller1);
            $("#<?= $_REQUEST['name_w'] ?>_content").scrollTop(0);
            $("#<?= $_REQUEST['name_w'] ?>_content").carousel(1);
            var calcContent = (quotesNumber * 30);
            var shownHeight = $("#<?= $_REQUEST['name_w'] ?>_content").prop("offsetHeight");
            scrollBottom2 = calcContent - shownHeight - 2;
            scroller2 = setInterval(stepDownInterval2, speed);
            $("#<?= $_REQUEST['name_w'] ?>_trends_li a").blur();
            $("#<?= $_REQUEST['name_w'] ?>_quotes_li a").blur();
        });
        
        $.ajax({//Inizio AJAX getParametersWidgets.php
            url: "../widgets/getParametersWidgets.php",
            type: "GET",
            data: {"nomeWidget": ["<?= $_REQUEST['name_w'] ?>"]},
            async: true,
            dataType: 'json',
            success: function (msg) 
            {
                var sizeColumns = null;
                if (msg !== null)
                {
                    sizeColumns = parseInt(msg.param.size_columns);
                }
                //Fattore di ingrandimento font calcolato sull'altezza in righe, base 4.
                fontRatio = parseInt((sizeColumns / 4)*15);
                fontRatio = fontRatio.toString() + "px";
                
                contentHeight = $('#<?= $_REQUEST['name_w'] ?>_div').prop("offsetHeight") - 25 - 18;

                $('#<?= $_REQUEST['name_w'] ?>_trendsContainer').css("height", contentHeight + "px");
                $('#<?= $_REQUEST['name_w'] ?>_quotesContainer').css("height", contentHeight + "px");

                rowPercHeight =  Math.floor(30 * 100 / contentHeight);
                fullRowPercHeight = rowPercHeight;
                rowPxHeight = rowPercHeight * contentHeight / 100;
                //rowPxHeight = 30;
                //fullRowPxHeight = rowPxHeight;
                
                iconWidth = Math.floor(30 * 100 / ($('#<?= $_REQUEST['name_w'] ?>_div').prop("offsetWidth") - 17));
                contentPercWidth = 100 - iconWidth;
                //iconWidth = 30;
                //contentWidth = $('#<?= $_REQUEST['name_w'] ?>_div').prop("offsetWidth") - iconWidth - 17;
                
                $.ajax({
                    url: "../widgets/curlProxyForTwitterVg.php?url=<?=$internalTwitterVigilanceHost?>/query/query.php?trends=Firenze",
                    type: "GET",
                    async: true,
                    dataType: 'json',
                    success: function (msg) {
                        var noHashTrend = null;
                        var linkHashTrend = null;
                        
                        if(firstLoad !== false)
                        {
                            $('#<?= $_REQUEST['name_w'] ?>_loading').css("display", "none");
                            $("#<?= $_REQUEST['name_w'] ?>_tabsContainer").css("display", "block");
                            $('#<?= $_REQUEST['name_w'] ?>_content').css("display", "block");
                        }
                        
                        if((msg.contents) instanceof Array) 
                        {
                            $('#<?= $_REQUEST['name_w'] ?>_trendsContainer').empty();
                            trendsNumber = msg.contents.length;
                            for (var i = 0; i < trendsNumber; i++) 
                            {
                                noHashTrend = msg.contents[i].request.substring(1);
                                linkHashTrend = "<a href='https://twitter.com/search?q=%23" + noHashTrend + "&src=typd' target='_blank' data-toggle='tooltip' title='See tweets for this trend on Twitter'>" + msg.contents[i].request.toLowerCase() + "</a>";
                                var newRow = $('<div class="twitterRow"></div>');
                                var newVigIcon = $("<div class='vigilanceIcon azzurroGrad'></div>");
                                var vigilanceLink = "https://www.disit.org/tv/index.php?p=retweet_ricerche&ricerca=%23" + noHashTrend + "&dashboard=true";
                                var vigIcon= $("<a href='" + vigilanceLink + "' target='blank'><i class='fa fa-eye' data-toggle='tooltip' title='See statistics for this trend on Twitter Vigilance'></i></a>");
                                newVigIcon.append(vigIcon);
                                var newContent = $("<div class='twitterContent azzurroGrad'></div>");
                                trendsContentHeight = fullRowPxHeight * msg.contents.length;
                                
                                newContent.html(linkHashTrend);
                                newRow.append(newVigIcon);
                                newRow.append(newContent);
                                $('#<?= $_REQUEST['name_w'] ?>_trendsContainer').append(newRow);
                                newRow.css("width", "100%");
                                newRow.css("height", rowPercHeight + "%");
                                newVigIcon.css("width", iconWidth + "%");
                                newVigIcon.css("height", "100%");
                                newContent.css("width", contentPercWidth + "%");
                                newContent.css("height", "100%");
                            }
                            
                            if(sizeColumns <= 4)
                            {
                                $('#<?= $_REQUEST['name_w'] ?>_trendsContainer .azzurroGrad').css("font-size", "18px");
                            }
                            else
                            {
                                if(sizeColumns <= 5)
                                {
                                    $('#<?= $_REQUEST['name_w'] ?>_trendsContainer .azzurroGrad').css("font-size", "20px");
                                }
                                else
                                {
                                    if(sizeColumns <= 6)
                                    {
                                        $('#<?= $_REQUEST['name_w'] ?>_trendsContainer .azzurroGrad').css("font-size", "21px");
                                    }
                                    else
                                    {
                                        if(sizeColumns <= 7)
                                        {
                                            $('#<?= $_REQUEST['name_w'] ?>_trendsContainer .azzurroGrad').css("font-size", "22px");
                                        }
                                        else
                                        {
                                            $('#<?= $_REQUEST['name_w'] ?>_trendsContainer .azzurroGrad').css("font-size", "23px");
                                        }
                                    }
                                }
                            }
                            
                            $.ajax({
                                url: "../widgets/curlProxyForTwitterVg.php?url=<?=$internalTwitterVigilanceHost?>/query/query.php?mentions=Firenze",
                                type: "GET",
                                async: true,
                                dataType: 'json',
                                success: function (msg2) 
                                {
                                    var noAtMention = null;
                                    var linkMention = null;
                                    quotesNumber = msg2.contents.length;
                                    quotesContentHeight = fullRowPxHeight * quotesNumber;
                                    
                                    $('#<?= $_REQUEST['name_w'] ?>_quotesContainer').empty();
                                    for(var i = 0; i < quotesNumber; i++) 
                                    {
                                        noAtMention = msg2.contents[i].request.substring(1).toLowerCase();
                                        linkMention = "<a href='https://twitter.com/search?q=%40" + noAtMention + "&src=typd' target='_blank' data-toggle='tooltip' title='See Twitter page for this mention'>" + msg2.contents[i].request.toLowerCase() + "</a>";
                                        var newRow = $("<div class='twitterRow'></div>");
                                        var newVigIcon = $("<div class='vigilanceIcon turcheseGrad'></div>");
                                        var vigilanceLink = "https://www.disit.org/tv/index.php?p=retweet_ricerche&ricerca=%40" + noAtMention + "&dashboard=true";
                                        var vigIcon= $("<a href='" + vigilanceLink + "' target='blank'><i class='fa fa-eye' data-toggle='tooltip' title='See statistics for this mention on Twitter Vigilance'></i></a>");
                                        newVigIcon.append(vigIcon);
                                        var newContent = $("<div class='twitterContent turcheseGrad'></div>");
                                        
                                        newContent.html(linkMention);
                                        newRow.append(newVigIcon);
                                        newRow.append(newContent);
                                        
                                        $('#<?= $_REQUEST['name_w'] ?>_quotesContainer').append(newRow);
                                        
                                        newRow.css("width", "100%");
                                        newRow.css("height", rowPercHeight + "%");
                                        newVigIcon.css("width", iconWidth + "%");
                                        newVigIcon.css("height", "100%");
                                        newContent.css("width", contentPercWidth + "%");
                                        newContent.css("height", "100%");
                                    }
                                    
                                    if(sizeColumns <= 4)
                                    {
                                        $('#<?= $_REQUEST['name_w'] ?>_quotesContainer .turcheseGrad').css("font-size", "18px");
                                    }
                                    else
                                    {
                                        if(sizeColumns <= 5)
                                        {
                                            $('#<?= $_REQUEST['name_w'] ?>_quotesContainer .turcheseGrad').css("font-size", "20px");
                                        }
                                        else
                                        {
                                            if(sizeColumns <= 6)
                                            {
                                                $('#<?= $_REQUEST['name_w'] ?>_quotesContainer .turcheseGrad').css("font-size", "21px");
                                            }
                                            else
                                            {
                                                if(sizeColumns <= 7)
                                                {
                                                    $('#<?= $_REQUEST['name_w'] ?>_quotesContainer .turcheseGrad').css("font-size", "22px");
                                                }
                                                else
                                                {
                                                    $('#<?= $_REQUEST['name_w'] ?>_quotesContainer .turcheseGrad').css("font-size", "23px");
                                                }
                                            }
                                        }
                                    }

                                    //Listener all'evento slide del carousel
                                    $('#<?= $_REQUEST['name_w'] ?>_content').on('slid.bs.carousel', function (ev) 
                                    {
                                        var id = ev.relatedTarget.id;
                                        clearInterval(scroller1);
                                        clearInterval(scroller2);

                                        if(defaultTab === -1)
                                        {
                                            switch(id)
                                            {
                                                case "<?= $_REQUEST['name_w'] ?>_trendsContainer":
                                                    actualTab = 1;        
                                                    $("#<?= $_REQUEST['name_w'] ?>_trends_li").attr("class", "active");
                                                    $("#<?= $_REQUEST['name_w'] ?>_quotes_li").attr("class", "");
                                                    $('#<?= $_REQUEST['name_w'] ?>_content').scrollTop(0);
                                                    var calcContent = (trendsNumber * 30);
                                                    var shownHeight = $("#<?= $_REQUEST['name_w'] ?>_content").prop("offsetHeight");
                                                    scrollBottom1 = calcContent - shownHeight - 2;
                                                    scroller1 = setInterval(stepDownInterval1, speed);
                                                    break;

                                                case "<?= $_REQUEST['name_w'] ?>_quotesContainer":
                                                    actualTab = 2;
                                                    $("#<?= $_REQUEST['name_w'] ?>_trends_li").attr("class", "");
                                                    $("#<?= $_REQUEST['name_w'] ?>_quotes_li").attr("class", "active"); 
                                                    $('#<?= $_REQUEST['name_w'] ?>_content').scrollTop(0);
                                                    var calcContent = (quotesNumber * 30);
                                                    var shownHeight = $("#<?= $_REQUEST['name_w'] ?>_content").prop("offsetHeight");
                                                    scrollBottom2 = calcContent - shownHeight - 2;
                                                    scroller2 = setInterval(stepDownInterval2, speed);
                                                    break;    
                                            }
                                        }
                                    });

                                    $("#<?= $_REQUEST['name_w'] ?>_content").mouseenter(function() 
                                    {
                                        clearInterval(scroller1);
                                        clearInterval(scroller2);
                                    });

                                    $("#<?= $_REQUEST['name_w'] ?>_content").mouseleave(function(){
                                        clearInterval(scroller1);
                                        clearInterval(scroller2);

                                        switch(actualTab)
                                        {
                                            case 1:
                                                scroller1 = setInterval(stepDownInterval1, speed);
                                                break;

                                            case 2:
                                                scroller2 = setInterval(stepDownInterval2, speed);
                                                break;
                                        }
                                    });

                                    switch(defaultTab)
                                    {
                                        case 0:
                                            actualTab = 1;
                                            $("#<?= $_REQUEST['name_w'] ?>_trends_li").attr("class", "active");
                                            $("#<?= $_REQUEST['name_w'] ?>_quotes_li").attr("class", "");
                                            clearInterval(scroller1);
                                            clearInterval(scroller2);
                                            $('#<?= $_REQUEST['name_w'] ?>_content').carousel(0);
                                            var calcContent = (trendsNumber * 30);
                                            var shownHeight = $("#<?= $_REQUEST['name_w'] ?>_content").prop("offsetHeight");
                                            scrollBottom1 = calcContent - shownHeight - 2;
                                            scroller1 = setInterval(stepDownInterval1, speed);
                                            $('#<?= $_REQUEST['name_w'] ?>_content').addClass('slide');
                                            break;

                                        case 1:
                                            actualTab = 2;
                                            $("#<?= $_REQUEST['name_w'] ?>_trends_li").attr("class", "");
                                            $("#<?= $_REQUEST['name_w'] ?>_quotes_li").attr("class", "active");
                                            clearInterval(scroller1);
                                            clearInterval(scroller2);
                                            $('#<?= $_REQUEST['name_w'] ?>_content').carousel(1);
                                            var calcContent = (quotesNumber * 30);
                                            var shownHeight = $("#<?= $_REQUEST['name_w'] ?>_content").prop("offsetHeight");
                                            scrollBottom2 = calcContent - shownHeight - 2;
                                            scroller2 = setInterval(stepDownInterval2, speed);
                                            $('#<?= $_REQUEST['name_w'] ?>_content').addClass('slide');
                                            break;

                                        case -1:
                                            actualTab = 1;
                                            $("#<?= $_REQUEST['name_w'] ?>_trends_li").attr("class", "active");
                                            $("#<?= $_REQUEST['name_w'] ?>_quotes_li").attr("class", "");
                                            $('#<?= $_REQUEST['name_w'] ?>_content').addClass('slide');
                                            $('#<?= $_REQUEST['name_w'] ?>_content').attr('data-interval', 4000);
                                            $('#<?= $_REQUEST['name_w'] ?>_content').carousel('cycle');
                                            clearInterval(scroller1);
                                            clearInterval(scroller2);
                                            var calcContent = (trendsNumber * 30);
                                            var shownHeight = $("#<?= $_REQUEST['name_w'] ?>_content").prop("offsetHeight");
                                            scrollBottom1 = calcContent - shownHeight - 2;
                                            scroller1 = setInterval(stepDownInterval1, speed);
                                            break;
                                    }

                                    timeToClearScroll = (counter - 0.5) * 1000;
                                    setTimeout(function()
                                    {
                                        clearInterval(scroller1);
                                        clearInterval(scroller2);
                                    }, timeToClearScroll);
                                    
                                    $("#<?= $_REQUEST['name_w'] ?>").on('customResizeEvent', function(event){
                                        $("#<?= $_REQUEST['name_w'] ?>").off('customResizeEvent');
                                        clearInterval(countdown);
                                        clearInterval(scroller1);
                                        clearInterval(scroller2);
                                        $("#<?= $_REQUEST['name_w'] ?>_content").off();
                                        $("#<?= $_REQUEST['name_w'] ?>_content").scrollTop(0);
                                        $('#<?= $_REQUEST['name_w'] ?>_content').removeClass('slide');
                                        $("#<?= $_REQUEST['name_w'] ?>_trends_li").off();
                                        $("#<?= $_REQUEST['name_w'] ?>_quotes_li").off();
                                        quotesNumber = null;
                                        trendsNumber = null;
                                        <?= $_REQUEST['name_w'] ?>(false);
                                    });
                                    
                                    $(document).off('resizeHighchart_' + widgetName);
                                    $(document).on('resizeHighchart_' + widgetName, function(event)
                                    {
                                        showHeader = event.showHeader;
                                    });
                                    
                                    $("#<?= $_REQUEST['name_w'] ?>").off('updateFrequency');
                                    $("#<?= $_REQUEST['name_w'] ?>").on('updateFrequency', function(event){
                                        clearInterval(countdown);
                                        timeToReload = event.newTimeToReload;
                                        countdown = setInterval(function () 
                                        {
                                            $("#<?= $_REQUEST['name_w'] ?>_countdownDiv").text(counter);
                                            counter--;

                                            if(counter > 60)
                                            {
                                                $("#<?= $_REQUEST['name_w'] ?>_countdownDiv").text(Math.floor(counter / 60) + "m");
                                            } 
                                            else 
                                            {
                                                $("#<?= $_REQUEST['name_w'] ?>_countdownDiv").text(counter + "s");
                                            }
                                            if(counter === 0) 
                                            {
                                                $("#<?= $_REQUEST['name_w'] ?>").off('customResizeEvent');
                                                $("#<?= $_REQUEST['name_w'] ?>_countdownDiv").text(counter + "s");
                                                clearInterval(countdown);
                                                clearInterval(scroller1);
                                                clearInterval(scroller2);
                                                $("#<?= $_REQUEST['name_w'] ?>_content").off();
                                                $("#<?= $_REQUEST['name_w'] ?>_content").scrollTop(0);
                                                $('#<?= $_REQUEST['name_w'] ?>_content').removeClass('slide');
                                                $("#<?= $_REQUEST['name_w'] ?>_trends_li").off();
                                                $("#<?= $_REQUEST['name_w'] ?>_quotes_li").off();
                                                quotesNumber = null;
                                                trendsNumber = null;
                                                setTimeout(<?= $_REQUEST['name_w'] ?>(false), 1000);
                                            }
                                        }, 1000);
                                    });
                                    
                                    countdown = setInterval(function () 
                                    {
                                        $("#<?= $_REQUEST['name_w'] ?>_countdownDiv").text(counter);
                                        counter--;

                                        if(counter > 60)
                                        {
                                            $("#<?= $_REQUEST['name_w'] ?>_countdownDiv").text(Math.floor(counter / 60) + "m");
                                        } 
                                        else 
                                        {
                                            $("#<?= $_REQUEST['name_w'] ?>_countdownDiv").text(counter + "s");
                                        }
                                        if(counter === 0) 
                                        {
                                            $("#<?= $_REQUEST['name_w'] ?>").off('customResizeEvent');
                                            $("#<?= $_REQUEST['name_w'] ?>_countdownDiv").text(counter + "s");
                                            clearInterval(countdown);
                                            clearInterval(scroller1);
                                            clearInterval(scroller2);
                                            $("#<?= $_REQUEST['name_w'] ?>_content").off();
                                            $("#<?= $_REQUEST['name_w'] ?>_content").scrollTop(0);
                                            $('#<?= $_REQUEST['name_w'] ?>_content').removeClass('slide');
                                            $("#<?= $_REQUEST['name_w'] ?>_trends_li").off();
                                            $("#<?= $_REQUEST['name_w'] ?>_quotes_li").off();
                                            quotesNumber = null;
                                            trendsNumber = null;
                                            setTimeout(<?= $_REQUEST['name_w'] ?>(false), 1000);
                                        }
                                    }, 1000);
                                },
                                error: function(errorData)
                                {
                                    console.log("Error retrieving quotes from TV");
                                    console.log(JSON.stringify(errorData));
                                }
                            });//Fine AJAX più interno
                        } 
                        else 
                        {
                            $("#<?= $_REQUEST['name_w'] ?>_content").html("<p><b>Principali Twitter Trends:</b> nessun dato disponibile</p><p><b>Citazioni:</b> nessun dato disponibile</p>");
                        }
                    },
                    error: function(errorData)
                    {
                        console.log("Error retrieving trends from TV");
                        console.log(JSON.stringify(errorData));
                    }    
                });
            //Chiusura success getParametersWidgets
            },
            error: function(errorData)
            {
                console.log(JSON.stringify(errorData));
            }
        });    
 });
</script>

<div class="widget" id="<?= $_REQUEST['name_w'] ?>_div">
    <div class='ui-widget-content'>
        <?php include '../widgets/widgetHeader.php'; ?>
        <?php include '../widgets/widgetCtxMenu.php'; ?>
        <?php include '../widgets/commonModules/widgetDimControls.php'; ?>	
        <div id="<?= $_REQUEST['name_w'] ?>_loading" class="loadingDiv">
            <div class="loadingTextDiv">
                <p>Loading data, please wait</p>
            </div>
            <div class ="loadingIconDiv">
                <i class='fa fa-spinner fa-spin'></i>
            </div>
        </div>
        
        <div id="<?= $_REQUEST['name_w'] ?>_tabsContainer" class="twitterTabsContainer">
            <ul id="<?= $_REQUEST['name_w'] ?>_nav_ul" class="nav nav-tabs nav_ul twitterTabs">
                <li role="navigation" id="<?= $_REQUEST['name_w'] ?>_trends_li" class="active"><a disabled="true">trends</a></li>
                <li role="navigation" id="<?= $_REQUEST['name_w'] ?>_quotes_li"><a disabled="true">quotes</a></li>
            </ul>
        </div>
        
        <div id="<?= $_REQUEST['name_w'] ?>_content" class="twitterMainContent carousel" data-interval="false" data-pause="hover">
                
            <!-- Wrapper per il carousel -->
            <div id="<?= $_REQUEST['name_w'] ?>_carousel" class="carousel-inner" role="listbox">
                <div id="<?= $_REQUEST['name_w'] ?>_trendsContainer" class="item active"></div>
                <div id="<?= $_REQUEST['name_w'] ?>_quotesContainer" class="item"></div>
            </div>
        </div> 
    </div>	
</div> 