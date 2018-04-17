<!-- Main context menu btn -->
<div id="<?= $_REQUEST['name_w'] ?>_widgetCtxMenuBtnCnt" class="widgetCtxMenuBtnCnt centerWithFlex">
	<i id="<?= $_REQUEST['name_w'] ?>_widgetCtxMenuBtn" class="widgetCtxMenuBtn fa fa-caret-square-o-down" data-status="normal"></i>
</div>

<!-- Main context menu -->
<div id="<?= $_REQUEST['name_w'] ?>_widgetCtxMenu" data-widgetName="<?= $_REQUEST['name_w'] ?>" class="fullCtxMenu container-fluid widgetCtxMenu">
	<div class="row fullCtxMenuRow headerVisibility" data-index="0">
		<div class="col-xs-2 fullCtxMenuIcon centerWithFlex"><i class="fa"></i></div>
		<div class="col-xs-10 fullCtxMenuTxt">Hide header</div>
	</div>
	
	<div class="row fullCtxMenuRow headerColorRow hasSubmenu" data-index="1" data-boundTo="<?= $_REQUEST['name_w'] ?>_headerColorSubmenu">
		<div class="col-xs-2 fullCtxMenuIcon centerWithFlex"><i class="fa fa-paint-brush"></i></div>
		<div class="col-xs-10 fullCtxMenuTxt">Header color</div>
	</div>
	<div id="<?= $_REQUEST['name_w'] ?>_headerColorSubmenu" data-clicked="false" data-boundTo="headerColorRow" class="fullCtxMenu fullCtxSubmenu dashboardCtxMenu widgetSubmenu container-fluid">
		<div class="row">
			<div id="<?= $_REQUEST['name_w'] ?>_headerColorPicker" class="col-xs-12"></div>
		</div>
		<div class="row">
			<div class="col-xs-1 ctxMenuPaletteColor" data-color="rgb(255, 255, 255)"></div>
			<div class="col-xs-1 ctxMenuPaletteColor" data-color="rgb(255, 217, 0)"></div>
			<div class="col-xs-1 ctxMenuPaletteColor" data-color="rgb(255, 153, 51)"></div>
			<div class="col-xs-1 ctxMenuPaletteColor" data-color="rgb(255, 51, 0)"></div>
			<div class="col-xs-1 ctxMenuPaletteColor" data-color="rgb(204, 0, 0)"></div>
			<div class="col-xs-1 ctxMenuPaletteColor" data-color="rgb(102, 255, 51)"></div>
			<div class="col-xs-1 ctxMenuPaletteColor" data-color="rgb(0, 204, 0)"></div>
			<div class="col-xs-1 ctxMenuPaletteColor" data-color="rgb(0, 255, 255)"></div>
			<div class="col-xs-1 ctxMenuPaletteColor" data-color="rgb(51, 204, 255)"></div>
			<div class="col-xs-1 ctxMenuPaletteColor" data-color="rgb(0, 153, 204)"></div>
			<div class="col-xs-1 ctxMenuPaletteColor" data-color="rgb(179, 179, 179)"></div>
			<div class="col-xs-1 ctxMenuPaletteColor" data-color="rgb(0, 0, 0)"></div>
		</div>
		<div class="row contextMenuBtnsRow">
			<div class="col-xs-6 centerWithFlex">
				<button type="button" class="contextMenuCancelBtn" id="<?= $_REQUEST['name_w'] ?>_headerColorCancelBtn">Undo</button>
			</div>
			<div class="col-xs-6 centerWithFlex">
				<button type="button" class="contextMenuConfirmBtn" id="<?= $_REQUEST['name_w'] ?>_headerColorConfirmBtn">Apply</button>
			</div>
		</div>
		<div class="row contextMenuMsgRow">
			<div class="col-xs-12 centerWithFlex"></div>
		</div>
	</div>
	
	<div class="row fullCtxMenuRow titleColorRow hasSubmenu" data-index="2" data-boundTo="<?= $_REQUEST['name_w'] ?>_titleColorSubmenu">
		<div class="col-xs-2 fullCtxMenuIcon centerWithFlex"><i class="fa fa-paint-brush"></i></div>
		<div class="col-xs-10 fullCtxMenuTxt">Title color</div>
	</div>
	<div id="<?= $_REQUEST['name_w'] ?>_titleColorSubmenu" data-clicked="false" data-boundTo="titleColorRow" class="fullCtxMenu fullCtxSubmenu dashboardCtxMenu widgetSubmenu container-fluid">
		<div class="row">
			<div id="<?= $_REQUEST['name_w'] ?>_titleColorPicker" class="col-xs-12"></div>
		</div>
		<div class="row">
			<div class="col-xs-1 ctxMenuPaletteColor" data-color="rgb(255, 255, 255)"></div>
			<div class="col-xs-1 ctxMenuPaletteColor" data-color="rgb(255, 217, 0)"></div>
			<div class="col-xs-1 ctxMenuPaletteColor" data-color="rgb(255, 153, 51)"></div>
			<div class="col-xs-1 ctxMenuPaletteColor" data-color="rgb(255, 51, 0)"></div>
			<div class="col-xs-1 ctxMenuPaletteColor" data-color="rgb(204, 0, 0)"></div>
			<div class="col-xs-1 ctxMenuPaletteColor" data-color="rgb(102, 255, 51)"></div>
			<div class="col-xs-1 ctxMenuPaletteColor" data-color="rgb(0, 204, 0)"></div>
			<div class="col-xs-1 ctxMenuPaletteColor" data-color="rgb(0, 255, 255)"></div>
			<div class="col-xs-1 ctxMenuPaletteColor" data-color="rgb(51, 204, 255)"></div>
			<div class="col-xs-1 ctxMenuPaletteColor" data-color="rgb(0, 153, 204)"></div>
			<div class="col-xs-1 ctxMenuPaletteColor" data-color="rgb(179, 179, 179)"></div>
			<div class="col-xs-1 ctxMenuPaletteColor" data-color="rgb(0, 0, 0)"></div>
		</div>
		<div class="row contextMenuBtnsRow">
			<div class="col-xs-6 centerWithFlex">
				<button type="button" class="contextMenuCancelBtn" id="<?= $_REQUEST['name_w'] ?>_titleColorCancelBtn">Undo</button>
			</div>
			<div class="col-xs-6 centerWithFlex">
				<button type="button" class="contextMenuConfirmBtn" id="<?= $_REQUEST['name_w'] ?>_titleColorConfirmBtn">Apply</button>
			</div>
		</div>
		<div class="row contextMenuMsgRow">
			<div class="col-xs-12 centerWithFlex"></div>
		</div>
	</div>
	
	
	<div class="row fullCtxMenuRow advancedOptionsRow" data-index="2">
		<div class="col-xs-2 fullCtxMenuIcon centerWithFlex"><i class="fa fa-cogs"></i></div>
		<div class="col-xs-10 fullCtxMenuTxt">More options</div>
	</div>
	<div class="row fullCtxMenuRow delWidgetRow" data-index="3">
		<div class="col-xs-2 fullCtxMenuIcon centerWithFlex"><i class="fa fa-close"></i></div>
		<div class="col-xs-10 fullCtxMenuTxt">Delete widget</div>
	</div>
	<div class="row fullCtxMenuRow quitRow" data-index="4">
		<div class="col-xs-2 fullCtxMenuIcon centerWithFlex"><i class="fa fa-mail-reply"></i></div>
		<div class="col-xs-10 fullCtxMenuTxt">Quit</div>
	</div>
</div>

<script type='text/javascript'>
	$(document).ready(function ()
        {
           if("<?= $_REQUEST['hostFile'] ?>" === 'config')
	   {
		//Click su bottone quit
		$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu .quitRow').off('click');
		$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu .quitRow').click(function(){
			$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu .widgetSubmenu').hide();
			$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu').hide();
			$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu .widgetSubmenu').each(function(i){
				$(this).attr('data-clicked', 'false');	
			});
			
		});
		
		//Click su bottone menu di contesto
		$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenuBtn').off('click');
		$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenuBtn').click(function(){
			if($('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu').is(':visible'))
			{
				$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu .widgetSubmenu').hide();
				$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu').hide();
				$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu .widgetSubmenu').each(function(i){
					$(this).attr('data-clicked', 'false');	
				});
			}
			else
			{
				var widgetDistanceFromRightScreen = parseInt($(window).width() + $(document).scrollLeft() - $('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenuBtnCnt').offset().left);	
				if($('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu').width() > widgetDistanceFromRightScreen)
				{
					$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu').css('left', parseInt($('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenuBtnCnt').offset().left - $('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu').width() + 12) + 'px');
					$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu').attr('data-side', 'left');
				}
				else
				{
					$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu').css('left', parseInt( $('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenuBtnCnt').offset().left) + 'px');
					$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu').attr('data-side', 'right');
				}
				$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu').css('top', parseInt($('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenuBtn').offset().top + 25) + 'px');
				$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu').show();
			}
		});

		//Mostra/nascondi header
		$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu .headerVisibility').off('click');
		$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu .headerVisibility').click(function(){
			var contentHeight, showTitle = null;
			if($('#<?= $_REQUEST['name_w'] ?>_header').is(':visible'))
			{
				$('#<?= $_REQUEST['name_w'] ?>_header').hide();
				$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu .headerVisibility .fullCtxMenuIcon i').removeClass('fa-eye-slash');
				$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu .headerVisibility .fullCtxMenuIcon i').addClass('fa-eye');
				$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu .headerVisibility .fullCtxMenuTxt').html('Show header');
				contentHeight = parseInt($("#<?= $_REQUEST['name_w'] ?>_div").prop("offsetHeight"));
				showTitle = 'no';
				$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenuBtnCnt').css('background-color', 'rgba(51, 64, 69, 0.7)');
				$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenuBtn').css('color', 'white');
			}
			else
			{
				$('#<?= $_REQUEST['name_w'] ?>_header').show();
				$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu .headerVisibility .fullCtxMenuIcon i').removeClass('fa-eye');
				$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu .headerVisibility .fullCtxMenuIcon i').addClass('fa-eye-slash');
				$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu .headerVisibility .fullCtxMenuTxt').html('Hide header');
				contentHeight = parseInt($("#<?= $_REQUEST['name_w'] ?>_div").prop("offsetHeight") - $("#<?= $_REQUEST['name_w'] ?>_header").prop("offsetHeight"));
				showTitle = 'yes';
				$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenuBtnCnt').css('background-color', $('#<?= $_REQUEST['name_w'] ?>_header').css('background-color'));
				$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenuBtn').css('color', $('#<?= $_REQUEST['name_w'] ?>_header').css('color'));
			}
			
			$("#<?= $_REQUEST['name_w'] ?>_content").css("height", contentHeight + 'px');
			
			//Innesco di evento cambio altezza diagramma per gli widgets Highcharts
			$.event.trigger({
				type: "resizeHighchart_<?= $_REQUEST['name_w'] ?>"
			}); 
			
			//Update parametro su DB
			$.ajax({
				url: "../controllers/updateWidget.php",
				data: {
					action: "updateTitleVisibility",
					widgetName: "<?= $_REQUEST['name_w'] ?>", 
					showTitle: showTitle
				},
				type: "POST",
				async: true,
				dataType: 'json',
				success: function(data) 
				{
					if(data.detail !== 'Ok')
					{
						if($('#<?= $_REQUEST['name_w'] ?>_header').is(':visible'))
						{
							$('#<?= $_REQUEST['name_w'] ?>_header').hide();
							$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu .headerVisibility .fullCtxMenuIcon i').removeClass('fa-eye-slash');
							$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu .headerVisibility .fullCtxMenuIcon i').addClass('fa-eye');
							$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu .headerVisibility .fullCtxMenuTxt').html('Show header');
							contentHeight = parseInt($("#<?= $_REQUEST['name_w'] ?>_div").prop("offsetHeight"));
						}
						else
						{
							$('#<?= $_REQUEST['name_w'] ?>_header').show();
							$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu .headerVisibility .fullCtxMenuIcon i').removeClass('fa-eye');
							$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu .headerVisibility .fullCtxMenuIcon i').addClass('fa-eye-slash');
							$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu .headerVisibility .fullCtxMenuTxt').html('Hide header');
							contentHeight = parseInt($("#<?= $_REQUEST['name_w'] ?>_div").prop("offsetHeight") - $("#<?= $_REQUEST['name_w'] ?>_header").prop("offsetHeight"));
						}
						
						$("#<?= $_REQUEST['name_w'] ?>_content").css("height", contentHeight + 'px');
					}
				},
				error: function(errorData)
				{
					if($('#<?= $_REQUEST['name_w'] ?>_header').is(':visible'))
					{
						$('#<?= $_REQUEST['name_w'] ?>_header').hide();
						$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu .headerVisibility .fullCtxMenuIcon i').removeClass('fa-eye-slash');
						$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu .headerVisibility .fullCtxMenuIcon i').addClass('fa-eye');
						$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu .headerVisibility .fullCtxMenuTxt').html('Show header');
						contentHeight = parseInt($("#<?= $_REQUEST['name_w'] ?>_div").prop("offsetHeight"));
					}
					else
					{
						$('#<?= $_REQUEST['name_w'] ?>_header').show();
						$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu .headerVisibility .fullCtxMenuIcon i').removeClass('fa-eye');
						$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu .headerVisibility .fullCtxMenuIcon i').addClass('fa-eye-slash');
						$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu .headerVisibility .fullCtxMenuTxt').html('Hide header');
						contentHeight = parseInt($("#<?= $_REQUEST['name_w'] ?>_div").prop("offsetHeight") - $("#<?= $_REQUEST['name_w'] ?>_header").prop("offsetHeight"));
					}
				}
			});
		});
		
		//Apertura di submenu (qualsiasi)
		$("#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu .hasSubmenu").click(function(){
			
			var submenuId = $(this).attr('data-boundTo');
			
			$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu .widgetSubmenu').each(function(i){
				if($(this).attr('id') !== submenuId)
				{
					$(this).hide();
				}
			});
			
			var widgetDistanceFromRightScreen = null;
			
			if($('#' + submenuId).is(':visible'))
			{
				$('#' + submenuId).hide();
			}
			else
			{
				$('#' + submenuId).css('top', parseInt($(this).attr('data-index')*$(this).height())); 
				
				if($("#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu").attr('data-side') === 'left')
				{
					//Menu principale sulla sinistra, submenu sulla sinistra
					if($('#' + submenuId).attr('data-clicked') === 'false')
					{
						$('#' + submenuId).css('left', '-' + parseInt($('#' + submenuId).outerWidth() + 25) + 'px');
					}
					else
					{
						$('#' + submenuId).css('left', '-' + parseInt($('#' + submenuId).outerWidth() + 5) + 'px');	
					}
				}
				else
				{
					widgetDistanceFromRightScreen = parseInt($(window).width() + $(document).scrollLeft() - $('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenuBtnCnt').offset().left - $('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu').width());	
					if($('#' + submenuId).width() > widgetDistanceFromRightScreen)
					{
						//Menu principale sulla destra, submenu sulla sinistra
						console.log("Entrato");
						if($('#' + submenuId).attr('data-clicked') === 'false')
						{
							$('#' + submenuId).css('left', '-' + parseInt($('#' + submenuId).outerWidth() + 25) + 'px');
						}
						else
						{
							$('#' + submenuId).css('left', '-' + parseInt($('#' + submenuId).outerWidth() + 5) + 'px');	
						}
					}
					else
					{
						//Menu principale sulla destra, spazio ulteriore per submenu sulla destra
						$('#' + submenuId).css('left', $('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu').outerWidth() + 'px');
					}
					
				}
				
				$('#' + submenuId).show(); 	
			}
			
			if($('#' + submenuId).attr('data-clicked') === 'false')
			{
				$('#' + submenuId).attr('data-clicked', 'true');
			}
		});
		
		//Main
	    $('body').prepend($('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu')); 
		
		if($('#<?= $_REQUEST['name_w'] ?>_header').is(':visible'))
		{
			$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenuBtnCnt').css('background-color', '<?= $_REQUEST['frame_color_w'] ?>');
			$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenuBtn').css('color', '<?= $_REQUEST['headerFontColor'] ?>');
		}
		else
		{
			$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenuBtnCnt').css('background-color', 'rgba(51, 64, 69, 0.7)');
			$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenuBtn').css('color', 'white');
		}

		$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu .ctxMenuPaletteColor').each(function(i){
			$(this).css('background-color', $(this).attr('data-color'));
		});	
		
		var widgetDistanceFromRightScreen = parseInt($(window).width() + $(document).scrollLeft() - $('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenuBtnCnt').offset().left);	
		if($('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu').width() > widgetDistanceFromRightScreen)
		{
			$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu').css('left', parseInt($('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenuBtnCnt').offset().left - $('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu').width() + 12) + 'px');
			$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu').attr('data-side', 'left');
		}
		else
		{
			$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu').css('left', parseInt( $('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenuBtnCnt').offset().left) + 'px');
			$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu').attr('data-side', 'right');
		}
		$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu').css('top', parseInt($('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenuBtn').offset().top + 25) + 'px');

		if($('#<?= $_REQUEST['name_w'] ?>_header').is(':visible'))
		{
			$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenuBtnCnt').css('background-color', $('#<?= $_REQUEST['name_w'] ?>_header').css('background-color'));
			$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenuBtnCnt').css('color', $('#<?= $_REQUEST['name_w'] ?>_header').css('color'));
			$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu .headerVisibility .fullCtxMenuIcon i').addClass('fa-eye-slash');
			$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu .headerVisibility .fullCtxMenuTxt').html('Hide header');
		}
		else
		{
			$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenuBtnCnt').css('background-color', 'rgba(51, 64, 69, 0.7)');
			$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenuBtnCnt').css('color', 'white');
			$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu .headerVisibility .fullCtxMenuIcon i').addClass('fa-eye');
			$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu .headerVisibility .fullCtxMenuTxt').html('Show header');
		}
		
		//Effetto hover su righe menu
		$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu .fullCtxMenuRow').hover(function(){
			$(this).css('color', 'white');
			$(this).css('background-color', 'rgba(0, 162, 211, 1)');
		}, function(){
			$(this).css('color', 'rgb(51, 64, 69)');
			$(this).css('background-color', 'transparent');
		}); 
		
		//Instanziamento color picker colore header + shortcuts + azioni
		$('#<?= $_REQUEST['name_w'] ?>_header').attr('data-currentColor', $('#<?= $_REQUEST['name_w'] ?>_header').css('background-color')); 
		
		$('#<?= $_REQUEST['name_w'] ?>_headerColorPicker').colorpicker({
			format: null,
			useAlpha: false,
			customClass: 'dashHeaderColorPicker',
			inline: true,
			container: true,
			color: $('#<?= $_REQUEST['name_w'] ?>_header').css('background-color')
		}).on('changeColor', function(e){
			var newColor = $("#<?= $_REQUEST['name_w'] ?>_headerColorPicker").colorpicker('getValue');
			$('#<?= $_REQUEST['name_w'] ?>_header').css('background-color', newColor);
			$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenuBtnCnt').css('background-color', newColor);
			$('#<?= $_REQUEST['name_w'] ?>_header').attr('data-newColor', newColor);
		});
		
		$('#<?= $_REQUEST['name_w'] ?>_headerColorSubmenu div.ctxMenuPaletteColor').click(function(){
			$('#<?= $_REQUEST['name_w'] ?>_header').css('background-color', $(this).attr('data-color'));
			$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenuBtnCnt').css('background-color', $(this).attr('data-color'));
			$('#<?= $_REQUEST['name_w'] ?>_header').attr('data-newColor', $(this).attr('data-color'));
			$("#<?= $_REQUEST['name_w'] ?>_headerColorPicker").colorpicker('setValue', $(this).attr('data-color'));
		});
		
		$('#<?= $_REQUEST['name_w'] ?>_headerColorCancelBtn').click(function(){
			$("#<?= $_REQUEST['name_w'] ?>_headerColorPicker").colorpicker('setValue', $('#<?= $_REQUEST['name_w'] ?>_header').attr('data-currentColor'));
			$('#<?= $_REQUEST['name_w'] ?>_header').css('background-color', $('#<?= $_REQUEST['name_w'] ?>_header').attr('data-currentColor'));
			$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenuBtnCnt').css('background-color', $('#<?= $_REQUEST['name_w'] ?>_header').attr('data-currentColor'));
			$('#<?= $_REQUEST['name_w'] ?>_header').attr('data-newColor', $('#<?= $_REQUEST['name_w'] ?>_header').attr('data-currentColor'));
		});
		
		$('#<?= $_REQUEST['name_w'] ?>_headerColorConfirmBtn').click(function(){
			$(this).parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Saving&nbsp;<i class="fa fa-circle-o-notch fa-spin" style="font-size:14px"></i>');
			$(this).parents('div.container-fluid').find('div.contextMenuMsgRow').show();

			var button = $(this);

			$.ajax({
				url: "../controllers/updateWidget.php",
				data: {
					action: "updateHeaderColor",
					widgetName: "<?= $_REQUEST['name_w'] ?>", 
					newColor: $('#<?= $_REQUEST['name_w'] ?>_header').attr('data-newColor')
				},
				type: "POST",
				async: true,
				dataType: 'json',
				success: function(data) 
				{
					if(data.detail === 'Ok')
					{
						button.parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Saved&nbsp;<i class="fa fa-thumbs-up" style="font-size:14px"></i>');
						$('#<?= $_REQUEST['name_w'] ?>_header').attr('data-currentColor', $('#<?= $_REQUEST['name_w'] ?>_header').attr('data-newColor'));
						setTimeout(function(){
							button.parents('div.container-fluid').find('div.contextMenuMsgRow').hide();
						}, 1000);
					}
					else
					{
						button.parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Error&nbsp;<i class="fa fa-thumbs-down" style="font-size:14px"></i>');
						setTimeout(function(){
							button.parents('div.container-fluid').find('div.contextMenuMsgRow').hide();
						}, 1000);
					}
				},
				error: function(errorData)
				{
					button.parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Error&nbsp;<i class="fa fa-thumbs-down" style="font-size:14px"></i>');
					setTimeout(function(){
						button.parents('div.container-fluid').find('div.contextMenuMsgRow').hide();
					}, 1000);
				}
			});
		});
		
		//Instanziamento color picker colore font titolo + shortcuts + azioni
		$('#<?= $_REQUEST['name_w'] ?>_header').attr('data-currentTitleColor', $('#<?= $_REQUEST['name_w'] ?>_header').css('color')); 
		
		$('#<?= $_REQUEST['name_w'] ?>_titleColorPicker').colorpicker({
			format: null,
			useAlpha: false,
			customClass: 'dashHeaderColorPicker',
			inline: true,
			container: true,
			color: $('#<?= $_REQUEST['name_w'] ?>_header').css('color')
		}).on('changeColor', function(e){
			var newColor = $("#<?= $_REQUEST['name_w'] ?>_titleColorPicker").colorpicker('getValue');
			$('#<?= $_REQUEST['name_w'] ?>_titleDiv').css('color', newColor);
			$('#<?= $_REQUEST['name_w'] ?> .info_source').css('color', newColor);
			$('#<?= $_REQUEST['name_w'] ?>_countdownDiv').css('color', newColor);
			$('#<?= $_REQUEST['name_w'] ?>_countdownDiv').css('border-color', newColor);
			$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenuBtn').css('color', newColor);
			$('#<?= $_REQUEST['name_w'] ?>_header').attr('data-newTitleColor', newColor);
		});
		
		$('#<?= $_REQUEST['name_w'] ?>_titleColorSubmenu div.ctxMenuPaletteColor').click(function(){
			$('#<?= $_REQUEST['name_w'] ?>_titleDiv').css('color', $(this).attr('data-color'));
			$('#<?= $_REQUEST['name_w'] ?> .info_source').css('color', $(this).attr('data-color'));
			$('#<?= $_REQUEST['name_w'] ?>_countdownDiv').css('color', $(this).attr('data-color'));
			$('#<?= $_REQUEST['name_w'] ?>_countdownDiv').css('border-color', $(this).attr('data-color'));
			$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenuBtn').css('color', $(this).attr('data-color'));
			$('#<?= $_REQUEST['name_w'] ?>_header').attr('data-newTitleColor', $(this).attr('data-color'));
			$("#<?= $_REQUEST['name_w'] ?>_titleColorPicker").colorpicker('setValue', $(this).attr('data-color'));
		});
		
		$('#<?= $_REQUEST['name_w'] ?>_titleColorCancelBtn').click(function(){
			$("#<?= $_REQUEST['name_w'] ?>_titleColorPicker").colorpicker('setValue', $('#<?= $_REQUEST['name_w'] ?>_header').attr('data-currentTitleColor'));
			$('#<?= $_REQUEST['name_w'] ?>_titleDiv').css('color', $('#<?= $_REQUEST['name_w'] ?>_header').attr('data-currentTitleColor'));
			$('#<?= $_REQUEST['name_w'] ?> .info_source').css('color', $('#<?= $_REQUEST['name_w'] ?>_header').attr('data-currentTitleColor'));
			$('#<?= $_REQUEST['name_w'] ?>_countdownDiv').css('color', $('#<?= $_REQUEST['name_w'] ?>_header').attr('data-currentTitleColor'));
			$('#<?= $_REQUEST['name_w'] ?>_countdownDiv').css('border-color', $('#<?= $_REQUEST['name_w'] ?>_header').attr('data-currentTitleColor'));
			$('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenuBtn').css('color', $('#<?= $_REQUEST['name_w'] ?>_header').attr('data-currentTitleColor'));
			$('#<?= $_REQUEST['name_w'] ?>_header').attr('data-newTitleColor', $('#<?= $_REQUEST['name_w'] ?>_header').attr('data-currentTitleColor'));
		});
		
		$('#<?= $_REQUEST['name_w'] ?>_titleColorConfirmBtn').click(function(){
			$(this).parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Saving&nbsp;<i class="fa fa-circle-o-notch fa-spin" style="font-size:14px"></i>');
			$(this).parents('div.container-fluid').find('div.contextMenuMsgRow').show();

			var button = $(this);

			$.ajax({
				url: "../controllers/updateWidget.php",
				data: {
					action: "updateTitleColor",
					widgetName: "<?= $_REQUEST['name_w'] ?>", 
					newColor: $('#<?= $_REQUEST['name_w'] ?>_header').attr('data-newTitleColor')
				},
				type: "POST",
				async: true,
				dataType: 'json',
				success: function(data) 
				{
					if(data.detail === 'Ok')
					{
						button.parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Saved&nbsp;<i class="fa fa-thumbs-up" style="font-size:14px"></i>');
						$('#<?= $_REQUEST['name_w'] ?>_header').attr('data-currentTitleColor', $('#<?= $_REQUEST['name_w'] ?>_header').attr('data-newTitleColor'));
						setTimeout(function(){
							button.parents('div.container-fluid').find('div.contextMenuMsgRow').hide();
						}, 1000);
					}
					else
					{
						button.parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Error&nbsp;<i class="fa fa-thumbs-down" style="font-size:14px"></i>');
						setTimeout(function(){
							button.parents('div.container-fluid').find('div.contextMenuMsgRow').hide();
						}, 1000);
					}
				},
				error: function(errorData)
				{
					button.parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Error&nbsp;<i class="fa fa-thumbs-down" style="font-size:14px"></i>');
					setTimeout(function(){
						button.parents('div.container-fluid').find('div.contextMenuMsgRow').hide();
					}, 1000);
				}
			});
		});
            }
            else
            {
                $('#<?= $_REQUEST['name_w'] ?>_widgetCtxMenu').remove();
            }
	});
</script>