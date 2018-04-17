<div id='<?= $_REQUEST['name_w'] ?>_header' class="widgetHeader">
    <!-- Info button -->		
	<div id="<?= $_REQUEST['name_w'] ?>_infoButtonDiv" class="infoButtonContainer">
	   <a id ="info_modal" href="#" class="info_source"><i id="source_<?= $_REQUEST['name_w'] ?>" class="source_button fa fa-info-circle"></i></a>
	   <i class="material-icons gisDriverPin" data-onMap="false">navigation</i>
	</div>
	
	<!-- Title div -->
	<div id="<?= $_REQUEST['name_w'] ?>_titleDiv" class="titleDiv">
	   <img class="pcPhoto" src="../img/protezioneCivile.png" height="25" width="99" style="margin-left: auto; margin-right: auto">
	</div>
	
	<!-- Countdown div -->
	<div id="<?= $_REQUEST['name_w'] ?>_countdownContainerDiv" class="countdownContainer">
		<div id="<?= $_REQUEST['name_w'] ?>_countdownDiv" class="countdown"></div> 
	</div> 

	<div id="<?= $_REQUEST['name_w'] ?>_buttonsDiv">
	   <div class="singleBtnContainer"><a class="iconFullscreenModal" href="#" data-toggle="tooltip" title="Fullscreen popup"><span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span></a></div>
	   <div class="singleBtnContainer"><a class="iconFullscreenTab" href="#" data-toggle="tooltip" title="Fullscreen new tab"><span class="glyphicon glyphicon-new-window" aria-hidden="true"></span></a></div>
	</div>	
</div>