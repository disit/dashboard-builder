<?php
$title_w =  filter_var(html_entity_decode($_REQUEST['title_w'], ENT_QUOTES|ENT_HTML5), FILTER_SANITIZE_STRING);
$name_w = filter_input(INPUT_POST,'name_w',FILTER_SANITIZE_STRING);
$link = mysqli_connect($host, $username, $password);
if (checkWidgetNameInDashboard($link, $name_w, $_REQUEST['id_dashboard']) === false) {
    eventLog("Returned the following ERROR in widgetHeader.php for the widget ".escapeForHTML($name_w)." is not instantiated or allowed in this dashboard.");
    exit();
}
if ($_REQUEST['frequency_w'] && checkVarType($_REQUEST['frequency_w'], "integer") === false) {
    eventLog("Returned the following ERROR in widgetHeader.php for the widget ".escapeForHTML($name_w)." is not an alphanum :".$_REQUEST['frequency_w']);
    exit();  
}
?>
<div id='<?= $name_w ?>_header' class="widgetHeader">
        <!-- Info button -->		
	<div id="<?= $name_w ?>_infoButtonDiv" class="infoButtonContainer">
	   <a id ="<?= $name_w ?>_infoBtn" href="#" class="info_source"><i id="source_<?= $name_w ?>" class="source_button fa fa-info-circle" style="display:none"></i></a>
	   <i class="material-icons gisDriverPin" data-onMap="false">navigation</i>
	</div>
	
	<!-- Title div -->
	<div id="<?= $name_w ?>_titleDiv" contenteditable="false" data-underEdit="false" class="titleDiv inplaceEditable" data-currentTitle="<?= $title_w ?>" data-newTitle="<?= $title_w ?>">
	</div>
        
        <!-- Title context menu -->
        <div id="<?= $name_w ?>_titleMenu" class="applicationCtxMenu compactMenu dashboardCtxMenu widgetTitleMenu">
            <div class="compactMenuBtns">
                <button type="button" class="compactMenuCancelBtn" id="<?= $name_w ?>_WidgetTitleCancelBtn"><i class="fa fa-remove"></i></button> 
                <button type="button" class="compactMenuConfirmBtn" id="<?= $name_w ?>_WidgetTitleConfirmBtn"><i class="fa fa-floppy-o" aria-hidden="true"></i></button>
            </div>
            <div class="compactMenuMsg centerWithFlex">

            </div>
        </div>
	
	<!-- Countdown div -->
	<div id="<?= $name_w ?>_countdownContainerDiv" class="countdownContainer">
	   <div id="<?= $name_w ?>_countdownDiv" class="countdown"></div> 
	</div> 
        
        <div id="<?= $name_w ?>_countdownMenu" class="applicationCtxMenu compactMenu dashboardCtxMenu widgetTitleMenu">
             
            <div class="row">
                <div id="<?= $name_w ?>_updateFreqHourCnt" class="col-xs-2">
                    <input type="text" id="<?= $name_w ?>_updateFreqHour" maxlength="2" class="updateFreqField centerWithFlex"/>
                </div>
                <div class="col-xs-2 centerWithFlex updateFreqLbl">
                    h
                </div>    
                <div id="<?= $name_w ?>_updateFreqMinCnt" class="col-xs-2">
                    <input type="text" id="<?= $name_w ?>_updateFreqMin" maxlength="2" class="updateFreqField centerWithFlex"/>
                </div>
                <div class="col-xs-2 centerWithFlex updateFreqLbl">
                    m
                </div>
                <div id="<?= $name_w ?>_updateFreqSecCnt" class="col-xs-2">
                    <input type="text" id="<?= $name_w ?>_updateFreqSec" maxlength="2" class="updateFreqField centerWithFlex"/>
                </div>
                <div class="col-xs-2 centerWithFlex updateFreqLbl">
                    s
                </div>
            </div>   
            <div class="compactMenuBtns row centerWithFlex">
                <button type="button" class="compactMenuCancelBtn" id="<?= $name_w ?>_FreqCancelBtn" style="margin-right: 3px;"><i class="fa fa-remove"></i></button> 
                <button type="button" class="compactMenuConfirmBtn" id="<?= $name_w ?>_FreqConfirmBtn"><i class="fa fa-floppy-o" aria-hidden="true"></i></button>
            </div>
            <div class="compactMenuMsg centerWithFlex">

            </div>
        </div>

    <!-- Prev e Next Buttons -->
    <div id="<?= $name_w ?>_timeControlsContainer" class="timeControlsContainer" style="display:none;">
        <div id="<?= $name_w ?>_prevButton" class="timeControlsBtnCnt col-xs-2 timControlsPrevBtn" data-active="false" style="color:white; padding-left:5px;">
            <div id="<?= $name_w ?>_timeTrendPrevBtn">
                <i class="fa fa-arrow-circle-left" style="cursor:pointer;"></i>
            </div>
        </div>
        <div id="<?= $name_w ?>_nextButton" class="timeControlsBtnCnt col-xs-2 timControlNextBtn" data-active="false" style="color:white;">
            <div id="<?= $name_w ?>_timeTrendNextBtn">
                <i class="fa fa-arrow-circle-right" style="cursor:pointer;"></i>
            </div>
        </div>
    </div>

	<div id="<?= $name_w ?>_buttonsDiv">
	   <div class="singleBtnContainer"><a class="iconFullscreenModal" href="#" data-toggle="tooltip" title="Fullscreen popup"><span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span></a></div>
	   <div class="singleBtnContainer"><a class="iconFullscreenTab" href="#" data-toggle="tooltip" title="Fullscreen new tab"><span class="glyphicon glyphicon-new-window" aria-hidden="true"></span></a></div>
	</div>	
</div>
<script type="text/javascript" src="../js/moment-timezone-with-data.js"></script>
<script type='text/javascript'>
    $(document).ready(function()
    {
     //   console.log("Widget Header of : " + "<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>");
        $('#<?= $name_w ?>_infoBtn').hide();
        $('#source_<?= $name_w ?>').hide();

        if ("<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['type_w'])) ?>" == "widgetTimeTrend") {
         //   var widgetHeader = $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header');
         //   var titleDiv = $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_titleDiv');
         //   var newControlPrevNext = $('<div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_timeControlsContainer" class="timeControlsContainer"></div>');
         //   var prevBtn = $('<div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton" class="timeControlsBtnCnt col-xs-2 timControlsPrevBtn" data-active="false" style="color:white; padding-left:5px;">'
         //       +'<div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_timeTrendPrevBtn">'
         //           +'<i class="fa fa-arrow-circle-left" style="cursor:pointer;"></i>'
         //       +'</div></div>');

        //    newControlPrevNext.append(prevBtn);

         /*   var selectTimeMenu = $('<div class="custom-select" style="display: inline; width:34%;">'
                +'<select>'+
                +'<option value="0">Select car:</option>'
                +'<option value="1">1 H</option>'
                +'<option value="2">12 H</option>'
                +'<option value="3">1 D</option>'
                +'<option value="4">1 W</option>'
                +'<option value="5">1 M</option>'
                +'</select>'
                +'</div>');

            newControlPrevNext.append(selectTimeMenu);  */

         //   var nextBtn = $('<div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton" class="timeControlsBtnCnt col-xs-2 timControlNextBtn" data-active="false" style="color:white;">'
         //       +'<div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_timeTrendNextBtn">'
         //           +'<i class="fa fa-arrow-circle-right" style="cursor:pointer;"></i>'
         //       +'</div></div>');

         //   newControlPrevNext.append(nextBtn);
         //   titleDiv.css("width", "80%");
         //   widgetHeader.append(newControlPrevNext);

            $("#<?= $name_w ?>_timeControlsContainer").show();

        }

        var ckEditorContent = null;
        $("#<?= $name_w ?>_titleDiv").html("<?= filter_var(html_entity_decode($_REQUEST['title_w'], ENT_QUOTES|ENT_HTML5), FILTER_SANITIZE_STRING) ?>");
        
        $('#<?= $name_w ?>_titleMenu').css('z-index', '401');
        $('#<?= $name_w ?>_countdownMenu').css('z-index', '401');
        $('#<?= $name_w ?>_titleMenu').css("top", $('#<?= $name_w ?>_header').height() + "px");
        $('#<?= $name_w ?>_titleMenu').css("left", ($('#<?= $name_w ?>_header').width() - $('#<?= $name_w ?>_titleMenu').width())/2 + "px");
        $('#<?= $name_w ?>_countdownMenu').css("top", $('#<?= $name_w ?>_header').height() + "px");
        $('#<?= $name_w ?>_countdownMenu').css("left", ($('#<?= $name_w ?>_header').width() - 111) + "px");
        
        $('#<?= $name_w ?>_countdownMenu .col-xs-2').css('padding-left', '0px');
        $('#<?= $name_w ?>_countdownMenu .col-xs-2').css('padding-right', '0px');
        
        if("<?= escapeForJS($_REQUEST['hostFile']) ?>" === "config")
        {
            $('#source_<?= $name_w ?>').show();
            $('#<?= $name_w ?>_infoBtn').show();
            $('#<?= $name_w ?>_infoBtn').click(function(){
                $('#widgetInfoModal .modalHeader').html("<?= $title_w ?>");
                $('#widgetInfoModalWidgetName').val("<?= $name_w ?>");
                
                //Ripristino eventuale titolo dashboard lasciato a mezzo
                if($("#dashboardTitle span").html().trim() === '')
                {
                    $("#dashboardSubtitle span").html('No subtitle');
                }
                else
                {
                    $("#dashboardTitle span").html(decodeURI($("#dashboardTitle").attr('data-currentTitle')));
                }

                $("#dashboardTitle span").attr('data-underEdit', 'false');
                $("#dashboardTitle span").attr('contenteditable', false);

                //Ripristino eventuale sottotitolo dashboard lasciato a mezzo
                if($("#dashboardSubtitle span").html().trim() === '')
                {
                    $("#dashboardSubtitle span").html('No subtitle');
                }
                else
                {
                    $("#dashboardSubtitle span").html($("#dashboardSubtitle").attr('data-currentSubtitle'));
                }

                $("#dashboardSubtitle span").attr('data-underEdit', 'false');
                $("#dashboardSubtitle span").attr('contenteditable', false);

                //Ripristino eventuali titoli widgets
                $('div.titleDiv').each(function(i)
                {
                    $(this).attr("contenteditable", false);
                    $(this).attr("data-underEdit", false);
                    var currentTitle = $(this).attr('data-currentTitle');
                    currentTitle = currentTitle.replace(/\\\\/g, "&bsol;");
                    $(this).html(currentTitle);
                });
                
                $('#widgetInfoModal').modal('show');
                    $.ajax({
                        url: "../management/get_data.php",
                        type: "GET",
                        data: {
                            "action": "get_info_widget",
                            "widget_info": "<?= $name_w ?>"
                        },
                        async: true,
                        dataType: 'json',
                        success: function (data) 
                        {
                            if((data.info_mess !== null)&&(data.info_mess !== undefined)&&(data.info_mess.trim() !== ''))
                            {
                                ckEditorContent = data.info_mess;
                                CKEDITOR.instances['widgetInfoEditor'].setData(ckEditorContent);
                            }
                            else
                            {
                                CKEDITOR.instances['widgetInfoEditor'].setData('');
                            }
                        },
                        error: function(errorData)
                        {
                            //TBD
                        }
                    });
            });
            
            var updateFreqHour = Math.floor(parseInt(<?= $_REQUEST['frequency_w'] ?>)/3600);
            var updateFreqMin = Math.floor((parseInt(<?= $_REQUEST['frequency_w'] ?>) - updateFreqHour*3600)/60);
            var updateFreqSec = parseInt(<?= $_REQUEST['frequency_w'] ?>) - updateFreqHour*3600 - updateFreqMin*60;
            
            $('#<?= $name_w ?>_updateFreqHour').val(updateFreqHour);
            $('#<?= $name_w ?>_updateFreqMin').val(updateFreqMin);
            $('#<?= $name_w ?>_updateFreqSec').val(updateFreqSec);
            
            $('#<?= $name_w ?>_updateFreqHour').off('input');
            $('#<?= $name_w ?>_updateFreqHour').on('input',function(e)
            {
                var reg = new RegExp('^\\d+$');
                
                if($(this).val().trim() !== '')
                {
                    if(reg.test($(this).val()))
                    {
                        $('#<?= $name_w ?>_FreqConfirmBtn').removeClass('disabled');
                        $(this).css('background-color', 'transparent');
                    }
                    else
                    {
                        $('#<?= $name_w ?>_FreqConfirmBtn').addClass('disabled');
                        $(this).css('background-color', '#ffcccc');
                    }
                }
                else
                {
                    $('#<?= $name_w ?>_FreqConfirmBtn').addClass('disabled');
                    $(this).css('background-color', '#ffcccc');
                }
            });
            
            $('#<?= $name_w ?>_updateFreqMin').off('input');
            $('#<?= $name_w ?>_updateFreqMin').on('input',function(e)
            {
                var reg = new RegExp('^\\d+$');
                
                if($(this).val().trim() !== '')
                {
                    if(reg.test($(this).val()))
                    {
                        if((parseInt($(this).val()) >= 0)&&(parseInt($(this).val()) <= 59))
                        {
                            $('#<?= $name_w ?>_FreqConfirmBtn').removeClass('disabled');
                            $(this).css('background-color', 'transparent');
                        }
                        else
                        {
                            $('#<?= $name_w ?>_FreqConfirmBtn').addClass('disabled');
                            $(this).css('background-color', '#ffcccc');
                        }
                    }
                    else
                    {
                        $('#<?= $name_w ?>_FreqConfirmBtn').addClass('disabled');
                        $(this).css('background-color', '#ffcccc');
                    }
                }
                else
                {
                    $('#<?= $name_w ?>_FreqConfirmBtn').addClass('disabled');
                    $(this).css('background-color', '#ffcccc');
                }
            });
            
            $('#<?= $name_w ?>_updateFreqSec').off('input');
            $('#<?= $name_w ?>_updateFreqSec').on('input',function(e)
            {
                var reg = new RegExp('^\\d+$');
                
                if($(this).val().trim() !== '')
                {
                    if(reg.test($(this).val()))
                    {
                        if((parseInt($(this).val()) >= 0)&&(parseInt($(this).val()) <= 59))
                        {
                            $('#<?= $name_w ?>_FreqConfirmBtn').removeClass('disabled');
                            $(this).css('background-color', 'transparent');
                        }
                        else
                        {
                            $('#<?= $name_w ?>_FreqConfirmBtn').addClass('disabled');
                            $(this).css('background-color', '#ffcccc');
                        }
                    }
                    else
                    {
                        $('#<?= $name_w ?>_FreqConfirmBtn').addClass('disabled');
                        $(this).css('background-color', '#ffcccc');
                    }
                }
                else
                {
                    $('#<?= $name_w ?>_FreqConfirmBtn').addClass('disabled');
                    $(this).css('background-color', '#ffcccc');
                }
            });
            
            $('#<?= $name_w ?>_FreqCancelBtn').off('click');
            $('#<?= $name_w ?>_FreqCancelBtn').click(function(){
                var updateFreqHour = Math.floor(parseInt(<?= $_REQUEST['frequency_w'] ?>)/3600);
                var updateFreqMin = Math.floor((parseInt(<?= $_REQUEST['frequency_w'] ?>) - updateFreqHour*3600)/60);
                var updateFreqSec = parseInt(<?= $_REQUEST['frequency_w'] ?>) - updateFreqHour*3600 - updateFreqMin*60;

                $('#<?= $name_w ?>_updateFreqHour').val(updateFreqHour);
                $('#<?= $name_w ?>_updateFreqMin').val(updateFreqMin);
                $('#<?= $name_w ?>_updateFreqSec').val(updateFreqSec);
                $('#<?= $name_w ?>_countdownMenu').hide();
            });
            
            $('#<?= $name_w ?>_FreqConfirmBtn').off('click');
            $('#<?= $name_w ?>_FreqConfirmBtn').click(function(){
                if(!$(this).hasClass('disabled'))
                {
                    var button = $(this);
                    button.parents('div.compactMenu').find('div.compactMenuMsg').show();
                    $(this).parents('div.compactMenu').find('div.compactMenuMsg').html('Saving&nbsp;<i class="fa fa-circle-o-notch fa-spin" style="font-size:14px"></i>');

                    var newFreq = parseInt($('#<?= $name_w ?>_updateFreqHour').val())*3600 + parseInt($('#<?= $name_w ?>_updateFreqMin').val())*60 + parseInt($('#<?= $name_w ?>_updateFreqSec').val());

                    $.ajax({
                        url: "../controllers/updateWidget.php",
                        data: {
                            action: "updateFrequency",
                            widgetName: "<?= $name_w ?>",
                            newFreq: newFreq,
                        },
                        type: "POST",
                        async: true,
                        dataType: 'json',
                        success: function(data) 
                        {
                            if(data.detail === 'Ok')
                            {
                                button.parents('div.compactMenu').find('div.compactMenuMsg').html('Saved&nbsp;<i class="fa fa-thumbs-up" style="font-size:14px"></i>');

                                $("#<?= $name_w ?>").trigger({
                                    type: "updateFrequency",
                                    newTimeToReload: newFreq
                                });

                                setTimeout(function(){
                                    button.parents('div.compactMenu').find('div.compactMenuMsg').hide();
                                    $('#<?= $name_w ?>_countdownMenu').hide();
                                }, 1000);
                            }
                            else
                            {
                                button.parents('div.compactMenu').find('div.compactMenuMsg').html('Error&nbsp;<i class="fa fa-thumbs-down" style="font-size:14px"></i>');
                                setTimeout(function(){
                                    button.parents('div.compactMenu').find('div.compactMenuMsg').hide();
                                }, 1000);
                            }
                        },
                        error: function(errorData)
                        {
                            button.parents('div.compactMenu').find('div.compactMenuMsg').html('Error&nbsp;<i class="fa fa-thumbs-down" style="font-size:14px"></i>');
                            setTimeout(function(){
                                button.parents('div.compactMenu').find('div.compactMenuMsg').hide();
                            }, 1000);
                        },
                        complete: function()
                        {
                        }
                    });
                }
            });
            
            $("#<?= $name_w ?>").on('customResizeEvent', function(event){
                $('#<?= $name_w ?>_titleMenu').css("top", $('#<?= $name_w ?>_header').height() + "px");
                $('#<?= $name_w ?>_titleMenu').css("left", ($('#<?= $name_w ?>_header').width() - $('#<?= $name_w ?>_titleMenu').width())/2 + "px");
                $('#<?= $name_w ?>_countdownMenu').css("top", $('#<?= $name_w ?>_header').height() + "px");
                $('#<?= $name_w ?>_countdownMenu').css("left", ($('#<?= $name_w ?>_header').width() - 111) + "px");
            });

            $('#<?= $name_w ?>_titleDiv').hover(function()
            {
                $(this).html("Click to edit");
            }, 
            function()
            {
                var currentTitle = $(this).attr('data-currentTitle');
                currentTitle = currentTitle.replace(/\\\\/g, "&bsol;");
                $(this).html(currentTitle);
            });
            
            $('#<?= $name_w ?>_countdownContainerDiv').hover(function(){
                $(this).css('cursor', 'pointer');
            }, 
            function(){
                $(this).css('cursor', 'normal');
            });
            
            $("#<?= $name_w ?>_countdownContainerDiv").click(function(){
                //Ripristino eventuale titolo dashboard lasciato a mezzo
                if($("#dashboardTitle span").html().trim() === '')
                {
                    $("#dashboardSubtitle span").html('No subtitle');
                }
                else
                {
                    $("#dashboardTitle span").html($("#dashboardTitle").attr('data-currentTitle'));
                }

                $("#dashboardTitle span").attr('data-underEdit', 'false');
                $("#dashboardTitle span").attr('contenteditable', false);

                //Ripristino eventuale sottotitolo dashboard lasciato a mezzo
                if($("#dashboardSubtitle span").html().trim() === '')
                {
                    $("#dashboardSubtitle span").html('No subtitle');
                }
                else
                {
                    $("#dashboardSubtitle span").html($("#dashboardSubtitle").attr('data-currentSubtitle'));
                }

                $("#dashboardSubtitle span").attr('data-underEdit', 'false');
                $("#dashboardSubtitle span").attr('contenteditable', false);

                //Ripristino eventuali titoli widgets
                $('div.titleDiv').each(function(i)
                {
                    $(this).attr("contenteditable", false);
                    $(this).attr("data-underEdit", false);
                    var currentTitle = $(this).attr('data-currentTitle');
                    currentTitle = currentTitle.replace(/\\\\/g, "&bsol;");
                    $(this).html(currentTitle);
                });
                
                
                if($('#<?= $name_w ?>_countdownMenu').is(':visible'))
                {
                    $('#<?= $name_w ?>_countdownMenu').hide();
                }
                else
                {
                    $('.applicationCtxMenu').hide();
                    $('#<?= $name_w ?>_countdownMenu').show();
                }
            });

            $('#<?= $name_w ?>_titleDiv').off('click');
            $('#<?= $name_w ?>_titleDiv').click(function()
            {
                if($('#draggingWidget').val() === 'false')
                {
                    if($('#<?= $name_w ?>_titleDiv').attr('data-underEdit') === 'false')
                    {
                        //Chiusura tutti menu pregressi
                        $('.applicationCtxMenu').hide();
                        
                        //Ripristino eventuale titolo dashboard lasciato a mezzo
                        if($("#dashboardTitle span").html().trim() === '')
                        {
                            $("#dashboardSubtitle span").html('No subtitle');
                        }
                        else
                        {
                            $("#dashboardTitle span").html($("#dashboardTitle").attr('data-currentTitle'));
                        }

                        $("#dashboardTitle span").attr('data-underEdit', 'false');
                        $("#dashboardTitle span").attr('contenteditable', false);

                        //Ripristino eventuale sottotitolo dashboard lasciato a mezzo
                        if($("#dashboardSubtitle span").html().trim() === '')
                        {
                            $("#dashboardSubtitle span").html('No subtitle');
                        }
                        else
                        {
                            $("#dashboardSubtitle span").html($("#dashboardSubtitle").attr('data-currentSubtitle'));
                        }

                        $("#dashboardSubtitle span").attr('data-underEdit', 'false');
                        $("#dashboardSubtitle span").attr('contenteditable', false);
                        
                        //Ripristino titolo di tutti gli altri widgets
                        $('div.titleDiv').each(function(i)
                        {
                            if($(this).attr('id') !== "#<?= $name_w ?>_titleDiv")
                            {
                                $(this).attr("contenteditable", false);
                                $(this).attr("data-underEdit", false);
                                var currentTitle = $(this).attr('data-currentTitle');
                                currentTitle = currentTitle.replace(/\\\\/g, "&bsol;");
                                $(this).html(currentTitle);
                            }
                        });
                        
                        //Abilitazione edit titolo per widget scelto
                        $("#<?= $name_w ?>_titleDiv").attr("contenteditable", true);
                        $('#<?= $name_w ?>_titleDiv').attr('data-underEdit', true);
                        $(".gridster ul").gridster().data('gridster').disable();
                        var currentTitle = $("#<?= $name_w ?>_titleDiv").attr('data-currentTitle');
                        currentTitle = currentTitle.replace(/\\\\/g, "&bsol;");
                        $('#<?= $name_w ?>_titleDiv').html(currentTitle);
                        $('#<?= $name_w ?>_titleDiv').off('mouseenter');
                        $('#<?= $name_w ?>_titleDiv').off('mouseleave');
                        
                        $('#<?= $name_w ?>_titleMenu').show();
                    }
                    
                }
            });

            $('#<?= $name_w ?>_WidgetTitleCancelBtn').click(function(){
                $(".gridster ul").gridster().data('gridster').enable();
                $("#<?= $name_w ?>_titleDiv").attr("contenteditable", "false");
                $('#<?= $name_w ?>_titleDiv').attr('data-underEdit', 'false');
                $('#<?= $name_w ?>_titleDiv').html($("#<?= $name_w ?>_titleDiv").attr('data-currentTitle'));
                $("#<?= $name_w ?>_titleDiv").attr('data-newTitle', $("#<?= $name_w ?>_titleDiv").attr('data-currentTitle'));
                $('#<?= $name_w ?>_titleMenu').hide();
                $('#<?= $name_w ?>_titleDiv').hover(function(){
                    $(this).html("Click to edit");
                }, 
                function(){
                    $(this).html($(this).attr('data-currentTitle'));
                });
            });

            $('#<?= $name_w ?>_WidgetTitleConfirmBtn').click(function(){
                var button = $(this);
                button.parents('div.compactMenu').find('div.compactMenuMsg').show();
                $(this).parents('div.compactMenu').find('div.compactMenuMsg').html('Saving&nbsp;<i class="fa fa-circle-o-notch fa-spin" style="font-size:14px"></i>');

                $.ajax({
                    url: "../controllers/updateWidget.php",
                    data: {
                        action: "updateTitle",
                        widgetName: "<?= $name_w ?>",
                        newTitle: $('#<?= $name_w ?>_titleDiv').html(),
                    },
                    type: "POST",
                    async: true,
                    dataType: 'json',
                    success: function(data) 
                    {
                        if(data.detail === 'Ok')
                        {
                            button.parents('div.compactMenu').find('div.compactMenuMsg').html('Saved&nbsp;<i class="fa fa-thumbs-up" style="font-size:14px"></i>');
                            $("#<?= $name_w ?>_titleDiv").attr('data-currentTitle', $("#<?= $name_w ?>_titleDiv").html());
                            $("#<?= $name_w ?>_titleDiv").attr('data-newTitle', $("#<?= $name_w ?>_titleDiv").html());

                            setTimeout(function(){
                                button.parents('div.compactMenu').find('div.compactMenuMsg').hide();
                                $('#<?= $name_w ?>_titleMenu').hide();
                                $("#<?= $name_w ?>_titleDiv").attr("contenteditable", "false");
                                $('#<?= $name_w ?>_titleDiv').attr('data-underEdit', 'false');
                                $(".gridster ul").gridster().data('gridster').enable();
                            }, 1000);

                        }
                        else if (data.detail === 'queryQuotesKo')
                        {
                            button.parents('div.compactMenu').find('div.compactMenuMsg').html('Error: single or double quotes not allowed&nbsp;<i class="fa fa-thumbs-down" style="font-size:14px"></i>');
                            setTimeout(function(){
                                button.parents('div.compactMenu').find('div.compactMenuMsg').hide();
                            }, 1500);
                        }
                        else
                        {
                            button.parents('div.compactMenu').find('div.compactMenuMsg').html('Error&nbsp;<i class="fa fa-thumbs-down" style="font-size:14px"></i>');
                            setTimeout(function(){
                                button.parents('div.compactMenu').find('div.compactMenuMsg').hide();
                            }, 1000);
                        }
                    },
                    error: function(errorData)
                    {
                        button.parents('div.compactMenu').find('div.compactMenuMsg').html('Error&nbsp;<i class="fa fa-thumbs-down" style="font-size:14px"></i>');
                        setTimeout(function(){
                            button.parents('div.compactMenu').find('div.compactMenuMsg').hide();
                        }, 1000);
                    },
                    complete: function()
                    {
                        $('#<?= $name_w ?>_titleDiv').hover(function(){
                            $(this).html("Click to edit");
                        }, 
                        function(){
                            $(this).html($(this).attr('data-currentTitle'));
                        });
                    }
                });
            });
        }
        else
        {
            $("#<?= $name_w ?>_titleDiv").attr("contenteditable", "false");
            $.ajax({
                url: "../management/get_data.php",
                type: "GET",
                data: {
                    "action": "get_info_widget",
                    "widget_info": "<?= $name_w ?>"
                },
                async: true,
                dataType: 'json',
                success: function (data) 
                {
                    if((data.info_mess === null)||(data.info_mess.trim() === '')||(data.info_mess.trim() === undefined))
                    {
                        $('#<?= $name_w ?>_infoBtn').hide();
                    //    console.log("Info Button NOT shown for widget <?= $name_w ?>: ");
                    }
                    else
                    {
                    //    console.log("Info Button shown for widget <?= $name_w ?>: " + data.info_mess);
                        $('#source_<?= $name_w ?>').show();
                        $('#<?= $name_w ?>_infoBtn').show();
                        $('#<?= $name_w ?>_infoBtn').click(function(){
                            $('#widgetInfoModal .modalHeader .col-xs-10').html("<?= $title_w ?>");
                            $('#widgetInfoModal').modal('show');
                            
                            $.ajax({
                                url: "../management/get_data.php",
                                type: "GET",
                                data: {
                                    "action": "get_info_widget",
                                    "widget_info": "<?= $name_w ?>"
                                },
                                async: true,
                                dataType: 'json',
                                success: function (data) 
                                {
                                    $('#widgetInfoModalBodyView').html(data.info_mess);
                                },
                                error: function(errorData)
                                {
                                    //TBD
                                }
                            });        
                            
                        });
                    }
                },
                error: function(errorData)
                {
                    //TBD
                }
            });
        }
    });
</script>