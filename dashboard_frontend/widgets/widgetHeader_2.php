<div id='<?= $name_w2 ?>_header' class="widgetHeader">
        <!-- Info button -->		
    <!--	<div id="<?= $name_w2 ?>_infoButtonDiv" class="infoButtonContainer">
	   <a id ="<?= $name_w2 ?>_infoBtn" href="#" class="info_source"><i id="source_<?= $name_w2 ?>" class="source_button fa fa-info-circle"></i></a>
	   <i class="material-icons gisDriverPin" data-onMap="false">navigation</i>
	</div>  -->
	
	<!-- Title div -->
	<div id="<?= $name_w2 ?>_titleDiv" contenteditable="false" data-underEdit="false" class="titleDiv inplaceEditable" data-currentTitle="<?= $title2 ?>" data-newTitle="<?= $title2 ?>">
	</div>
        
        <!-- Title context menu -->
        <div id="<?= $name_w2 ?>_titleMenu" class="applicationCtxMenu compactMenu dashboardCtxMenu widgetTitleMenu">
            <div class="compactMenuBtns">
                <button type="button" class="compactMenuCancelBtn" id="<?= $name_w2 ?>_WidgetTitleCancelBtn"><i class="fa fa-remove"></i></button> 
                <button type="button" class="compactMenuConfirmBtn" id="<?= $name_w2 ?>_WidgetTitleConfirmBtn"><i class="fa fa-floppy-o" aria-hidden="true"></i></button>
            </div>
            <div class="compactMenuMsg centerWithFlex">

            </div>
        </div>
	
	<!-- Countdown div -->
	<!-- <div id="<?= $name_w2 ?>_countdownContainerDiv" class="countdownContainer">
	   <div id="<?= $name_w2 ?>_countdownDiv" class="countdown"></div> 
	</div>  --> 
        
        <div id="<?= $name_w2 ?>_countdownMenu" class="applicationCtxMenu compactMenu dashboardCtxMenu widgetTitleMenu">
             
            <div class="row">
                <div id="<?= $name_w2 ?>_updateFreqHourCnt" class="col-xs-2">
                    <input type="text" id="<?= $name_w2 ?>_updateFreqHour" maxlength="2" class="updateFreqField centerWithFlex"/>
                </div>
                <div class="col-xs-2 centerWithFlex updateFreqLbl">
                    h
                </div>    
                <div id="<?= $name_w2 ?>_updateFreqMinCnt" class="col-xs-2">
                    <input type="text" id="<?= $name_w2 ?>_updateFreqMin" maxlength="2" class="updateFreqField centerWithFlex"/>
                </div>
                <div class="col-xs-2 centerWithFlex updateFreqLbl">
                    m
                </div>
                <div id="<?= $name_w2 ?>_updateFreqSecCnt" class="col-xs-2">
                    <input type="text" id="<?= $name_w2 ?>_updateFreqSec" maxlength="2" class="updateFreqField centerWithFlex"/>
                </div>
                <div class="col-xs-2 centerWithFlex updateFreqLbl">
                    s
                </div>
            </div>   
            <div class="compactMenuBtns row centerWithFlex">
                <button type="button" class="compactMenuCancelBtn" id="<?= $name_w2 ?>_FreqCancelBtn" style="margin-right: 3px;"><i class="fa fa-remove"></i></button> 
                <button type="button" class="compactMenuConfirmBtn" id="<?= $name_w2 ?>_FreqConfirmBtn"><i class="fa fa-floppy-o" aria-hidden="true"></i></button>
            </div>
            <div class="compactMenuMsg centerWithFlex">

            </div>
        </div>

	<div id="<?= $name_w2 ?>_buttonsDiv">
	   <div class="singleBtnContainer"><a class="iconFullscreenModal" href="#" data-toggle="tooltip" title="Fullscreen popup"><span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span></a></div>
	   <div class="singleBtnContainer"><a class="iconFullscreenTab" href="#" data-toggle="tooltip" title="Fullscreen new tab"><span class="glyphicon glyphicon-new-window" aria-hidden="true"></span></a></div>
	</div>	
</div>

<script type='text/javascript'>
    $(document).ready(function()
    {
        console.log('entro im widgetheader2');
        var ckEditorContent = null;
        $("#<?= $name_w2 ?>_titleDiv").html("<?= html_entity_decode($title2, ENT_QUOTES|ENT_HTML5) ?>"); 
        
        $('#<?= $name_w2 ?>_titleMenu').css('z-index', '401');
        $('#<?= $name_w2 ?>_countdownMenu').css('z-index', '401');
        $('#<?= $name_w2 ?>_titleMenu').css("top", $('#<?= $name_w2 ?>_header').height() + "px");
        $('#<?= $name_w2 ?>_titleMenu').css("left", ($('#<?= $name_w2 ?>_header').width() - $('#<?= $name_w2 ?>_titleMenu').width())/2 + "px");
        $('#<?= $name_w2 ?>_countdownMenu').css("top", '584px');//$('#<?= $name_w2 ?>_header').height() + "px");
        $('#<?= $name_w2 ?>_countdownMenu').css("left", '133px');//($('#<?= $name_w2 ?>_header').width() - 111) + "px");
        
        $('#<?= $name_w2 ?>_countdownMenu .col-xs-2').css('padding-left', '0px');
        $('#<?= $name_w2 ?>_countdownMenu .col-xs-2').css('padding-right', '0px');
        
        if("<?= $hostfile ?>" === "config")
        {
            $('#<?= $name_w2 ?>_infoBtn').show();
          /*  $('#<?= $name_w2 ?>_infoBtn').click(function(){
                $('#widgetInfoModal .modalHeader').html("<?= $title2 ?>");
                $('#widgetInfoModalWidgetName').val("<?= $name_w2 ?>");
                
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
                            "widget_info": "<?= $name_w2 ?>"
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
            });*/
            
            var updateFreqHour = Math.floor(parseInt(<?= $frequency_w2 ?>)/3600);
            var updateFreqMin = Math.floor((parseInt(<?= $frequency_w2 ?>) - updateFreqHour*3600)/60);
            var updateFreqSec = parseInt(<?= $frequency_w2 ?>) - updateFreqHour*3600 - updateFreqMin*60;
            
            $('#<?= $name_w2 ?>_updateFreqHour').val(updateFreqHour);
            $('#<?= $name_w2 ?>_updateFreqMin').val(updateFreqMin);
            $('#<?= $name_w2 ?>_updateFreqSec').val(updateFreqSec);
            
            $('#<?= $name_w2 ?>_updateFreqHour').off('input');
            $('#<?= $name_w2 ?>_updateFreqHour').on('input',function(e)
            {
                var reg = new RegExp('^\\d+$');
                
                if($(this).val().trim() !== '')
                {
                    if(reg.test($(this).val()))
                    {
                        $('#<?= $name_w2 ?>_FreqConfirmBtn').removeClass('disabled');
                        $(this).css('background-color', 'transparent');
                    }
                    else
                    {
                        $('#<?= $name_w2 ?>_FreqConfirmBtn').addClass('disabled');
                        $(this).css('background-color', '#ffcccc');
                    }
                }
                else
                {
                    $('#<?= $name_w2 ?>_FreqConfirmBtn').addClass('disabled');
                    $(this).css('background-color', '#ffcccc');
                }
            });
            
            $('#<?= $name_w2 ?>_updateFreqMin').off('input');
            $('#<?= $name_w2 ?>_updateFreqMin').on('input',function(e)
            {
                var reg = new RegExp('^\\d+$');
                
                if($(this).val().trim() !== '')
                {
                    if(reg.test($(this).val()))
                    {
                        if((parseInt($(this).val()) >= 0)&&(parseInt($(this).val()) <= 59))
                        {
                            $('#<?= $name_w2 ?>_FreqConfirmBtn').removeClass('disabled');
                            $(this).css('background-color', 'transparent');
                        }
                        else
                        {
                            $('#<?= $name_w2 ?>_FreqConfirmBtn').addClass('disabled');
                            $(this).css('background-color', '#ffcccc');
                        }
                    }
                    else
                    {
                        $('#<?= $name_w2 ?>_FreqConfirmBtn').addClass('disabled');
                        $(this).css('background-color', '#ffcccc');
                    }
                }
                else
                {
                    $('#<?= $name_w2 ?>_FreqConfirmBtn').addClass('disabled');
                    $(this).css('background-color', '#ffcccc');
                }
            });
            
            $('#<?= $name_w2 ?>_updateFreqSec').off('input');
            $('#<?= $name_w2 ?>_updateFreqSec').on('input',function(e)
            {
                var reg = new RegExp('^\\d+$');
                
                if($(this).val().trim() !== '')
                {
                    if(reg.test($(this).val()))
                    {
                        if((parseInt($(this).val()) >= 0)&&(parseInt($(this).val()) <= 59))
                        {
                            $('#<?= $name_w2 ?>_FreqConfirmBtn').removeClass('disabled');
                            $(this).css('background-color', 'transparent');
                        }
                        else
                        {
                            $('#<?= $name_w2 ?>_FreqConfirmBtn').addClass('disabled');
                            $(this).css('background-color', '#ffcccc');
                        }
                    }
                    else
                    {
                        $('#<?= $name_w2 ?>_FreqConfirmBtn').addClass('disabled');
                        $(this).css('background-color', '#ffcccc');
                    }
                }
                else
                {
                    $('#<?= $name_w2 ?>_FreqConfirmBtn').addClass('disabled');
                    $(this).css('background-color', '#ffcccc');
                }
            });
            
            $('#<?= $name_w2 ?>_FreqCancelBtn').off('click');
            $('#<?= $name_w2 ?>_FreqCancelBtn').click(function(){
                var updateFreqHour = Math.floor(parseInt(<?= $frequency_w2 ?>)/3600);
                var updateFreqMin = Math.floor((parseInt(<?= $frequency_w2 ?>) - updateFreqHour*3600)/60);
                var updateFreqSec = parseInt(<?= $frequency_w2 ?>) - updateFreqHour*3600 - updateFreqMin*60;

                $('#<?= $name_w2 ?>_updateFreqHour').val(updateFreqHour);
                $('#<?= $name_w2 ?>_updateFreqMin').val(updateFreqMin);
                $('#<?= $name_w2 ?>_updateFreqSec').val(updateFreqSec);
                $('#<?= $name_w2 ?>_countdownMenu').hide();
            });
            
            $('#<?= $name_w2 ?>_FreqConfirmBtn').off('click');
            $('#<?= $name_w2 ?>_FreqConfirmBtn').click(function(){
                if(!$(this).hasClass('disabled'))
                {
                    var button = $(this);
                    button.parents('div.compactMenu').find('div.compactMenuMsg').show();
                    $(this).parents('div.compactMenu').find('div.compactMenuMsg').html('Saving&nbsp;<i class="fa fa-circle-o-notch fa-spin" style="font-size:14px"></i>');

                    var newFreq = parseInt($('#<?= $name_w2 ?>_updateFreqHour').val())*3600 + parseInt($('#<?= $name_w2 ?>_updateFreqMin').val())*60 + parseInt($('#<?= $name_w2 ?>_updateFreqSec').val());

                    $.ajax({
                        url: "../controllers/updateWidget.php",
                        data: {
                            action: "updateFrequency",
                            widgetName: "<?= $name_w2 ?>",
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

                                $("#<?= $name_w2 ?>").trigger({
                                    type: "updateFrequency",
                                    newTimeToReload: newFreq
                                });

                                setTimeout(function(){
                                    button.parents('div.compactMenu').find('div.compactMenuMsg').hide();
                                    $('#<?= $name_w2 ?>_countdownMenu').hide();
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
            
            $("#<?= $name_w2 ?>").on('customResizeEvent', function(event){
                $('#<?= $name_w2 ?>_titleMenu').css("top", $('#<?= $name_w2 ?>_header').height() + "px");
                $('#<?= $name_w2 ?>_titleMenu').css("left", ($('#<?= $name_w2 ?>_header').width() - $('#<?= $name_w2 ?>_titleMenu').width())/2 + "px");
                $('#<?= $name_w2 ?>_countdownMenu').css("top", $('#<?= $name_w2 ?>_header').height() + "px");
                $('#<?= $name_w2 ?>_countdownMenu').css("left", ($('#<?= $name_w2 ?>_header').width() - 111) + "px");
            });

        /*    $('#<?= $name_w2 ?>_titleDiv').hover(function()
            {
                $(this).html("Click to edit");
            }, 
            function()
            {
                var currentTitle = $(this).attr('data-currentTitle');
                currentTitle = currentTitle.replace(/\\\\/g, "&bsol;");
                $(this).html(currentTitle);
            }); */
            
            $('#<?= $name_w2 ?>_countdownContainerDiv').hover(function(){
                $(this).css('cursor', 'pointer');
            }, 
            function(){
                $(this).css('cursor', 'normal');
            });
            
            $("#<?= $name_w2 ?>_countdownContainerDiv").click(function(){
                console.log('_countdownContainerDiv');
                //Ripristino eventuale titolo dashboard lasciato a mezzo
                /*
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
                */

                //Ripristino eventuali titoli widgets
                $('div.titleDiv').each(function(i)
                {
                    $(this).attr("contenteditable", false);
                    $(this).attr("data-underEdit", false);
                    var currentTitle = $(this).attr('data-currentTitle');
                    currentTitle = currentTitle.replace(/\\\\/g, "&bsol;");
                    $(this).html(currentTitle);
                });
                
                
                if($('#<?= $name_w2 ?>_countdownMenu').is(':visible'))
                {
                    $('#<?= $name_w2 ?>_countdownMenu').hide();
                }
                else
                {
                    $('.applicationCtxMenu').hide();
                    $('#<?= $name_w2 ?>_countdownMenu').show();
                }
            });
            

            $('#<?= $name_w2 ?>_titleDiv').off('click');
            $('#<?= $name_w2 ?>_titleDiv').click(function()
            {
                if($('#draggingWidget').val() === 'false')
                {
                    if($('#<?= $name_w2 ?>_titleDiv').attr('data-underEdit') === 'false')
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
                            if($(this).attr('id') !== "#<?= $name_w2 ?>_titleDiv")
                            {
                                $(this).attr("contenteditable", false);
                                $(this).attr("data-underEdit", false);
                                var currentTitle = $(this).attr('data-currentTitle');
                                currentTitle = currentTitle.replace(/\\\\/g, "&bsol;");
                                $(this).html(currentTitle);
                            }
                        });
                        
                        //Abilitazione edit titolo per widget scelto
                        $("#<?= $name_w2 ?>_titleDiv").attr("contenteditable", true);
                        $('#<?= $name_w2 ?>_titleDiv').attr('data-underEdit', true);
                        $(".gridster ul").gridster().data('gridster').disable();
                        var currentTitle = $("#<?= $name_w2 ?>_titleDiv").attr('data-currentTitle');
                        currentTitle = currentTitle.replace(/\\\\/g, "&bsol;");
                        $('#<?= $name_w2 ?>_titleDiv').html(currentTitle);
                        $('#<?= $name_w2 ?>_titleDiv').off('mouseenter');
                        $('#<?= $name_w2 ?>_titleDiv').off('mouseleave');
                        
                        $('#<?= $name_w2 ?>_titleMenu').show();
                    }
                    
                }
            });

            $('#<?= $name_w2 ?>_WidgetTitleCancelBtn').click(function(){
                $(".gridster ul").gridster().data('gridster').enable();
                $("#<?= $name_w2 ?>_titleDiv").attr("contenteditable", "false");
                $('#<?= $name_w2 ?>_titleDiv').attr('data-underEdit', 'false');
                $('#<?= $name_w2 ?>_titleDiv').html($("#<?= $name_w2 ?>_titleDiv").attr('data-currentTitle'));
                $("#<?= $name_w2 ?>_titleDiv").attr('data-newTitle', $("#<?= $name_w2 ?>_titleDiv").attr('data-currentTitle'));
                $('#<?= $name_w2 ?>_titleMenu').hide();
                $('#<?= $name_w2 ?>_titleDiv').hover(function(){
                    $(this).html("Click to edit");
                }, 
                function(){
                    $(this).html($(this).attr('data-currentTitle'));
                });
            });

            $('#<?= $name_w2 ?>_WidgetTitleConfirmBtn').click(function(){
                var button = $(this);
                button.parents('div.compactMenu').find('div.compactMenuMsg').show();
                $(this).parents('div.compactMenu').find('div.compactMenuMsg').html('Saving&nbsp;<i class="fa fa-circle-o-notch fa-spin" style="font-size:14px"></i>');

                $.ajax({
                    url: "../controllers/updateWidget.php",
                    data: {
                        action: "updateTitle",
                        widgetName: "<?= $name_w2 ?>",
                        newTitle: $('#<?= $name_w2 ?>_titleDiv').html(),
                    },
                    type: "POST",
                    async: true,
                    dataType: 'json',
                    success: function(data) 
                    {
                        if(data.detail === 'Ok')
                        {
                            button.parents('div.compactMenu').find('div.compactMenuMsg').html('Saved&nbsp;<i class="fa fa-thumbs-up" style="font-size:14px"></i>');
                            $("#<?= $name_w2 ?>_titleDiv").attr('data-currentTitle', $("#<?= $name_w2 ?>_titleDiv").html());
                            $("#<?= $name_w2 ?>_titleDiv").attr('data-newTitle', $("#<?= $name_w2 ?>_titleDiv").html());

                            setTimeout(function(){
                                button.parents('div.compactMenu').find('div.compactMenuMsg').hide();
                                $('#<?= $name_w2 ?>_titleMenu').hide();
                                $("#<?= $name_w2 ?>_titleDiv").attr("contenteditable", "false");
                                $('#<?= $name_w2 ?>_titleDiv').attr('data-underEdit', 'false');
                                $(".gridster ul").gridster().data('gridster').enable();
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
                        $('#<?= $name_w2 ?>_titleDiv').hover(function(){
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
            $("#<?= $name_w2 ?>_titleDiv").attr("contenteditable", "false");
            $.ajax({
                url: "../management/get_data.php",
                type: "GET",
                data: {
                    "action": "get_info_widget",
                    "widget_info": "<?= $name_w2 ?>"
                },
                async: true,
                dataType: 'json',
                success: function (data) 
                {
                    if((data.info_mess === null)||(data.info_mess.trim() === '')||(data.info_mess.trim() === undefined))
                    {
                        $('#<?= $name_w2 ?>_infoBtn').hide();
                    }
                    else
                    {
                        $('#<?= $name_w2 ?>_infoBtn').show();
                        $('#<?= $name_w2 ?>_infoBtn').click(function(){
                            $('#widgetInfoModal .modalHeader .col-xs-10').html("<?= $title2 ?>");
                            $('#widgetInfoModal').modal('show');
                            
                            $.ajax({
                                url: "../management/get_data.php",
                                type: "GET",
                                data: {
                                    "action": "get_info_widget",
                                    "widget_info": "<?= $name_w2 ?>"
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