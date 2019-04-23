<div id='<?= $_REQUEST['name_w'] ?>_header' class="widgetHeader">
        <!-- Info button -->		
	<div id="<?= $_REQUEST['name_w'] ?>_infoButtonDiv" class="infoButtonContainer">
	   <a id ="<?= $_REQUEST['name_w'] ?>_infoBtn" href="#" class="info_source"><i id="source_<?= $_REQUEST['name_w'] ?>" class="source_button fa fa-info-circle"></i></a>
	   <i class="material-icons gisDriverPin" data-onMap="false">navigation</i>
	</div>
	
	<!-- Title div -->
	<div id="<?= $_REQUEST['name_w'] ?>_titleDiv" contenteditable="false" data-underEdit="false" class="titleDiv inplaceEditable" data-currentTitle="<?= $_REQUEST['title_w'] ?>" data-newTitle="<?= $_REQUEST['title_w'] ?>">
	</div>
        
        <!-- Title context menu -->
        <div id="<?= $_REQUEST['name_w'] ?>_titleMenu" class="applicationCtxMenu compactMenu dashboardCtxMenu widgetTitleMenu">
            <div class="compactMenuBtns">
                <button type="button" class="compactMenuCancelBtn" id="<?= $_REQUEST['name_w'] ?>_WidgetTitleCancelBtn"><i class="fa fa-remove"></i></button> 
                <button type="button" class="compactMenuConfirmBtn" id="<?= $_REQUEST['name_w'] ?>_WidgetTitleConfirmBtn"><i class="fa fa-floppy-o" aria-hidden="true"></i></button>
            </div>
            <div class="compactMenuMsg centerWithFlex">

            </div>
        </div>
	
	<!-- Countdown div -->
	<div id="<?= $_REQUEST['name_w'] ?>_countdownContainerDiv" class="countdownContainer">
	   <div id="<?= $_REQUEST['name_w'] ?>_countdownDiv" class="countdown"></div> 
	</div> 
        
        <div id="<?= $_REQUEST['name_w'] ?>_countdownMenu" class="applicationCtxMenu compactMenu dashboardCtxMenu widgetTitleMenu">
             
            <div class="row">
                <div id="<?= $_REQUEST['name_w'] ?>_updateFreqHourCnt" class="col-xs-2">
                    <input type="text" id="<?= $_REQUEST['name_w'] ?>_updateFreqHour" maxlength="2" class="updateFreqField centerWithFlex"/>
                </div>
                <div class="col-xs-2 centerWithFlex updateFreqLbl">
                    h
                </div>    
                <div id="<?= $_REQUEST['name_w'] ?>_updateFreqMinCnt" class="col-xs-2">
                    <input type="text" id="<?= $_REQUEST['name_w'] ?>_updateFreqMin" maxlength="2" class="updateFreqField centerWithFlex"/>
                </div>
                <div class="col-xs-2 centerWithFlex updateFreqLbl">
                    m
                </div>
                <div id="<?= $_REQUEST['name_w'] ?>_updateFreqSecCnt" class="col-xs-2">
                    <input type="text" id="<?= $_REQUEST['name_w'] ?>_updateFreqSec" maxlength="2" class="updateFreqField centerWithFlex"/>
                </div>
                <div class="col-xs-2 centerWithFlex updateFreqLbl">
                    s
                </div>
            </div>   
            <div class="compactMenuBtns row centerWithFlex">
                <button type="button" class="compactMenuCancelBtn" id="<?= $_REQUEST['name_w'] ?>_FreqCancelBtn" style="margin-right: 3px;"><i class="fa fa-remove"></i></button> 
                <button type="button" class="compactMenuConfirmBtn" id="<?= $_REQUEST['name_w'] ?>_FreqConfirmBtn"><i class="fa fa-floppy-o" aria-hidden="true"></i></button>
            </div>
            <div class="compactMenuMsg centerWithFlex">

            </div>
        </div>

	<div id="<?= $_REQUEST['name_w'] ?>_buttonsDiv">
	   <div class="singleBtnContainer"><a class="iconFullscreenModal" href="#" data-toggle="tooltip" title="Fullscreen popup"><span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span></a></div>
	   <div class="singleBtnContainer"><a class="iconFullscreenTab" href="#" data-toggle="tooltip" title="Fullscreen new tab"><span class="glyphicon glyphicon-new-window" aria-hidden="true"></span></a></div>
	</div>	
</div>
<script type="text/javascript" src="../js/moment-timezone-with-data.js"></script>
<script type='text/javascript'>
    $(document).ready(function()
    {
        var ckEditorContent = null;
        $("#<?= $_REQUEST['name_w'] ?>_titleDiv").html("<?= html_entity_decode($_REQUEST['title_w'], ENT_QUOTES|ENT_HTML5) ?>"); 
        
        $('#<?= $_REQUEST['name_w'] ?>_titleMenu').css('z-index', '401');
        $('#<?= $_REQUEST['name_w'] ?>_countdownMenu').css('z-index', '401');
        $('#<?= $_REQUEST['name_w'] ?>_titleMenu').css("top", $('#<?= $_REQUEST['name_w'] ?>_header').height() + "px");
        $('#<?= $_REQUEST['name_w'] ?>_titleMenu').css("left", ($('#<?= $_REQUEST['name_w'] ?>_header').width() - $('#<?= $_REQUEST['name_w'] ?>_titleMenu').width())/2 + "px");
        $('#<?= $_REQUEST['name_w'] ?>_countdownMenu').css("top", $('#<?= $_REQUEST['name_w'] ?>_header').height() + "px");
        $('#<?= $_REQUEST['name_w'] ?>_countdownMenu').css("left", ($('#<?= $_REQUEST['name_w'] ?>_header').width() - 111) + "px");
        
        $('#<?= $_REQUEST['name_w'] ?>_countdownMenu .col-xs-2').css('padding-left', '0px');
        $('#<?= $_REQUEST['name_w'] ?>_countdownMenu .col-xs-2').css('padding-right', '0px');
        
        if("<?= $_REQUEST['hostFile'] ?>" === "config")
        {
            $('#<?= $_REQUEST['name_w'] ?>_infoBtn').show();
            $('#<?= $_REQUEST['name_w'] ?>_infoBtn').click(function(){
                $('#widgetInfoModal .modalHeader').html("<?= $_REQUEST['title_w'] ?>");
                $('#widgetInfoModalWidgetName').val("<?= $_REQUEST['name_w'] ?>");
                
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
                            "widget_info": "<?= $_REQUEST['name_w'] ?>"
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
            
            $('#<?= $_REQUEST['name_w'] ?>_updateFreqHour').val(updateFreqHour);
            $('#<?= $_REQUEST['name_w'] ?>_updateFreqMin').val(updateFreqMin);
            $('#<?= $_REQUEST['name_w'] ?>_updateFreqSec').val(updateFreqSec);
            
            $('#<?= $_REQUEST['name_w'] ?>_updateFreqHour').off('input');
            $('#<?= $_REQUEST['name_w'] ?>_updateFreqHour').on('input',function(e)
            {
                var reg = new RegExp('^\\d+$');
                
                if($(this).val().trim() !== '')
                {
                    if(reg.test($(this).val()))
                    {
                        $('#<?= $_REQUEST['name_w'] ?>_FreqConfirmBtn').removeClass('disabled');
                        $(this).css('background-color', 'transparent');
                    }
                    else
                    {
                        $('#<?= $_REQUEST['name_w'] ?>_FreqConfirmBtn').addClass('disabled');
                        $(this).css('background-color', '#ffcccc');
                    }
                }
                else
                {
                    $('#<?= $_REQUEST['name_w'] ?>_FreqConfirmBtn').addClass('disabled');
                    $(this).css('background-color', '#ffcccc');
                }
            });
            
            $('#<?= $_REQUEST['name_w'] ?>_updateFreqMin').off('input');
            $('#<?= $_REQUEST['name_w'] ?>_updateFreqMin').on('input',function(e)
            {
                var reg = new RegExp('^\\d+$');
                
                if($(this).val().trim() !== '')
                {
                    if(reg.test($(this).val()))
                    {
                        if((parseInt($(this).val()) >= 0)&&(parseInt($(this).val()) <= 59))
                        {
                            $('#<?= $_REQUEST['name_w'] ?>_FreqConfirmBtn').removeClass('disabled');
                            $(this).css('background-color', 'transparent');
                        }
                        else
                        {
                            $('#<?= $_REQUEST['name_w'] ?>_FreqConfirmBtn').addClass('disabled');
                            $(this).css('background-color', '#ffcccc');
                        }
                    }
                    else
                    {
                        $('#<?= $_REQUEST['name_w'] ?>_FreqConfirmBtn').addClass('disabled');
                        $(this).css('background-color', '#ffcccc');
                    }
                }
                else
                {
                    $('#<?= $_REQUEST['name_w'] ?>_FreqConfirmBtn').addClass('disabled');
                    $(this).css('background-color', '#ffcccc');
                }
            });
            
            $('#<?= $_REQUEST['name_w'] ?>_updateFreqSec').off('input');
            $('#<?= $_REQUEST['name_w'] ?>_updateFreqSec').on('input',function(e)
            {
                var reg = new RegExp('^\\d+$');
                
                if($(this).val().trim() !== '')
                {
                    if(reg.test($(this).val()))
                    {
                        if((parseInt($(this).val()) >= 0)&&(parseInt($(this).val()) <= 59))
                        {
                            $('#<?= $_REQUEST['name_w'] ?>_FreqConfirmBtn').removeClass('disabled');
                            $(this).css('background-color', 'transparent');
                        }
                        else
                        {
                            $('#<?= $_REQUEST['name_w'] ?>_FreqConfirmBtn').addClass('disabled');
                            $(this).css('background-color', '#ffcccc');
                        }
                    }
                    else
                    {
                        $('#<?= $_REQUEST['name_w'] ?>_FreqConfirmBtn').addClass('disabled');
                        $(this).css('background-color', '#ffcccc');
                    }
                }
                else
                {
                    $('#<?= $_REQUEST['name_w'] ?>_FreqConfirmBtn').addClass('disabled');
                    $(this).css('background-color', '#ffcccc');
                }
            });
            
            $('#<?= $_REQUEST['name_w'] ?>_FreqCancelBtn').off('click');
            $('#<?= $_REQUEST['name_w'] ?>_FreqCancelBtn').click(function(){
                var updateFreqHour = Math.floor(parseInt(<?= $_REQUEST['frequency_w'] ?>)/3600);
                var updateFreqMin = Math.floor((parseInt(<?= $_REQUEST['frequency_w'] ?>) - updateFreqHour*3600)/60);
                var updateFreqSec = parseInt(<?= $_REQUEST['frequency_w'] ?>) - updateFreqHour*3600 - updateFreqMin*60;

                $('#<?= $_REQUEST['name_w'] ?>_updateFreqHour').val(updateFreqHour);
                $('#<?= $_REQUEST['name_w'] ?>_updateFreqMin').val(updateFreqMin);
                $('#<?= $_REQUEST['name_w'] ?>_updateFreqSec').val(updateFreqSec);
                $('#<?= $_REQUEST['name_w'] ?>_countdownMenu').hide();
            });
            
            $('#<?= $_REQUEST['name_w'] ?>_FreqConfirmBtn').off('click');
            $('#<?= $_REQUEST['name_w'] ?>_FreqConfirmBtn').click(function(){
                if(!$(this).hasClass('disabled'))
                {
                    var button = $(this);
                    button.parents('div.compactMenu').find('div.compactMenuMsg').show();
                    $(this).parents('div.compactMenu').find('div.compactMenuMsg').html('Saving&nbsp;<i class="fa fa-circle-o-notch fa-spin" style="font-size:14px"></i>');

                    var newFreq = parseInt($('#<?= $_REQUEST['name_w'] ?>_updateFreqHour').val())*3600 + parseInt($('#<?= $_REQUEST['name_w'] ?>_updateFreqMin').val())*60 + parseInt($('#<?= $_REQUEST['name_w'] ?>_updateFreqSec').val());

                    $.ajax({
                        url: "../controllers/updateWidget.php",
                        data: {
                            action: "updateFrequency",
                            widgetName: "<?= $_REQUEST['name_w'] ?>",
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

                                $("#<?= $_REQUEST['name_w'] ?>").trigger({
                                    type: "updateFrequency",
                                    newTimeToReload: newFreq
                                });

                                setTimeout(function(){
                                    button.parents('div.compactMenu').find('div.compactMenuMsg').hide();
                                    $('#<?= $_REQUEST['name_w'] ?>_countdownMenu').hide();
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
            
            $("#<?= $_REQUEST['name_w'] ?>").on('customResizeEvent', function(event){
                $('#<?= $_REQUEST['name_w'] ?>_titleMenu').css("top", $('#<?= $_REQUEST['name_w'] ?>_header').height() + "px");
                $('#<?= $_REQUEST['name_w'] ?>_titleMenu').css("left", ($('#<?= $_REQUEST['name_w'] ?>_header').width() - $('#<?= $_REQUEST['name_w'] ?>_titleMenu').width())/2 + "px");
                $('#<?= $_REQUEST['name_w'] ?>_countdownMenu').css("top", $('#<?= $_REQUEST['name_w'] ?>_header').height() + "px");
                $('#<?= $_REQUEST['name_w'] ?>_countdownMenu').css("left", ($('#<?= $_REQUEST['name_w'] ?>_header').width() - 111) + "px");
            });

            $('#<?= $_REQUEST['name_w'] ?>_titleDiv').hover(function()
            {
                $(this).html("Click to edit");
            }, 
            function()
            {
                var currentTitle = $(this).attr('data-currentTitle');
                currentTitle = currentTitle.replace(/\\\\/g, "&bsol;");
                $(this).html(currentTitle);
            });
            
            $('#<?= $_REQUEST['name_w'] ?>_countdownContainerDiv').hover(function(){
                $(this).css('cursor', 'pointer');
            }, 
            function(){
                $(this).css('cursor', 'normal');
            });
            
            $("#<?= $_REQUEST['name_w'] ?>_countdownContainerDiv").click(function(){
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
                
                
                if($('#<?= $_REQUEST['name_w'] ?>_countdownMenu').is(':visible'))
                {
                    $('#<?= $_REQUEST['name_w'] ?>_countdownMenu').hide();
                }
                else
                {
                    $('.applicationCtxMenu').hide();
                    $('#<?= $_REQUEST['name_w'] ?>_countdownMenu').show();
                }
            });

            $('#<?= $_REQUEST['name_w'] ?>_titleDiv').off('click');
            $('#<?= $_REQUEST['name_w'] ?>_titleDiv').click(function()
            {
                if($('#draggingWidget').val() === 'false')
                {
                    if($('#<?= $_REQUEST['name_w'] ?>_titleDiv').attr('data-underEdit') === 'false')
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
                            if($(this).attr('id') !== "#<?= $_REQUEST['name_w'] ?>_titleDiv")
                            {
                                $(this).attr("contenteditable", false);
                                $(this).attr("data-underEdit", false);
                                var currentTitle = $(this).attr('data-currentTitle');
                                currentTitle = currentTitle.replace(/\\\\/g, "&bsol;");
                                $(this).html(currentTitle);
                            }
                        });
                        
                        //Abilitazione edit titolo per widget scelto
                        $("#<?= $_REQUEST['name_w'] ?>_titleDiv").attr("contenteditable", true);
                        $('#<?= $_REQUEST['name_w'] ?>_titleDiv').attr('data-underEdit', true);
                        $(".gridster ul").gridster().data('gridster').disable();
                        var currentTitle = $("#<?= $_REQUEST['name_w'] ?>_titleDiv").attr('data-currentTitle');
                        currentTitle = currentTitle.replace(/\\\\/g, "&bsol;");
                        $('#<?= $_REQUEST['name_w'] ?>_titleDiv').html(currentTitle);
                        $('#<?= $_REQUEST['name_w'] ?>_titleDiv').off('mouseenter');
                        $('#<?= $_REQUEST['name_w'] ?>_titleDiv').off('mouseleave');
                        
                        $('#<?= $_REQUEST['name_w'] ?>_titleMenu').show();
                    }
                    
                }
            });

            $('#<?= $_REQUEST['name_w'] ?>_WidgetTitleCancelBtn').click(function(){
                $(".gridster ul").gridster().data('gridster').enable();
                $("#<?= $_REQUEST['name_w'] ?>_titleDiv").attr("contenteditable", "false");
                $('#<?= $_REQUEST['name_w'] ?>_titleDiv').attr('data-underEdit', 'false');
                $('#<?= $_REQUEST['name_w'] ?>_titleDiv').html($("#<?= $_REQUEST['name_w'] ?>_titleDiv").attr('data-currentTitle'));
                $("#<?= $_REQUEST['name_w'] ?>_titleDiv").attr('data-newTitle', $("#<?= $_REQUEST['name_w'] ?>_titleDiv").attr('data-currentTitle'));
                $('#<?= $_REQUEST['name_w'] ?>_titleMenu').hide();
                $('#<?= $_REQUEST['name_w'] ?>_titleDiv').hover(function(){
                    $(this).html("Click to edit");
                }, 
                function(){
                    $(this).html($(this).attr('data-currentTitle'));
                });
            });

            $('#<?= $_REQUEST['name_w'] ?>_WidgetTitleConfirmBtn').click(function(){
                var button = $(this);
                button.parents('div.compactMenu').find('div.compactMenuMsg').show();
                $(this).parents('div.compactMenu').find('div.compactMenuMsg').html('Saving&nbsp;<i class="fa fa-circle-o-notch fa-spin" style="font-size:14px"></i>');

                $.ajax({
                    url: "../controllers/updateWidget.php",
                    data: {
                        action: "updateTitle",
                        widgetName: "<?= $_REQUEST['name_w'] ?>",
                        newTitle: $('#<?= $_REQUEST['name_w'] ?>_titleDiv').html(),
                    },
                    type: "POST",
                    async: true,
                    dataType: 'json',
                    success: function(data) 
                    {
                        if(data.detail === 'Ok')
                        {
                            button.parents('div.compactMenu').find('div.compactMenuMsg').html('Saved&nbsp;<i class="fa fa-thumbs-up" style="font-size:14px"></i>');
                            $("#<?= $_REQUEST['name_w'] ?>_titleDiv").attr('data-currentTitle', $("#<?= $_REQUEST['name_w'] ?>_titleDiv").html());
                            $("#<?= $_REQUEST['name_w'] ?>_titleDiv").attr('data-newTitle', $("#<?= $_REQUEST['name_w'] ?>_titleDiv").html());

                            setTimeout(function(){
                                button.parents('div.compactMenu').find('div.compactMenuMsg').hide();
                                $('#<?= $_REQUEST['name_w'] ?>_titleMenu').hide();
                                $("#<?= $_REQUEST['name_w'] ?>_titleDiv").attr("contenteditable", "false");
                                $('#<?= $_REQUEST['name_w'] ?>_titleDiv').attr('data-underEdit', 'false');
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
                        $('#<?= $_REQUEST['name_w'] ?>_titleDiv').hover(function(){
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
            $("#<?= $_REQUEST['name_w'] ?>_titleDiv").attr("contenteditable", "false");
            $.ajax({
                url: "../management/get_data.php",
                type: "GET",
                data: {
                    "action": "get_info_widget",
                    "widget_info": "<?= $_REQUEST['name_w'] ?>"
                },
                async: true,
                dataType: 'json',
                success: function (data) 
                {
                    if((data.info_mess === null)||(data.info_mess.trim() === '')||(data.info_mess.trim() === undefined))
                    {
                        $('#<?= $_REQUEST['name_w'] ?>_infoBtn').hide();
                    }
                    else
                    {
                        $('#<?= $_REQUEST['name_w'] ?>_infoBtn').show();
                        $('#<?= $_REQUEST['name_w'] ?>_infoBtn').click(function(){
                            $('#widgetInfoModal .modalHeader .col-xs-10').html("<?= $_REQUEST['title_w'] ?>");
                            $('#widgetInfoModal').modal('show');
                            
                            $.ajax({
                                url: "../management/get_data.php",
                                type: "GET",
                                data: {
                                    "action": "get_info_widget",
                                    "widget_info": "<?= $_REQUEST['name_w'] ?>"
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