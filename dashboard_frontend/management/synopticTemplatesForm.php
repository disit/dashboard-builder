<?php
/* Dashboard Builder.
  Copyright (C) 2018 DISIT Lab https://www.disit.org - University of Florence

  This program is free software: you can redistribute it and/or modify
   it under the terms of the GNU Affero General Public License as
   published by the Free Software Foundation, either version 3 of the
   License, or (at your option) any later version.
   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU Affero General Public License for more details.
   You should have received a copy of the GNU Affero General Public License
   along with this program.  If not, see <http://www.gnu.org/licenses/>. */

   include('../config.php');
if (!isset($_SESSION)) {
    session_start();
}

if ((!$_SESSION['isPublic'] && isset($_SESSION['newLayout']) && $_SESSION['newLayout'] === true) || ($_SESSION['isPublic'] && $_COOKIE['layout'] == "new_layout")) {

include('process-form.php');
/* if (!isset($_SESSION)) {
  session_start();
}   */

$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);

checkSession('Manager');
?>

<!DOCTYPE html>
<html class="dark">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php include "mobMainMenuClaim.php" ?></title>
    
    

    <!-- Bootstrap Core CSS -->
    <link href="../css/s4c-css/bootstrap/bootstrap.css" rel="stylesheet">
    <link href="../css/s4c-css/bootstrap/bootstrap-colorpicker.min.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="../js/jquery-1.10.1.min.js"></script>

    <!-- JQUERY UI -->
    <script src="../js/jqueryUi/jquery-ui.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../js/bootstrap.min.js"></script>

    <!-- Custom Core JavaScript -->
    <script src="../js/bootstrap-colorpicker.min.js"></script>

    <!-- Bootstrap toggle button -->
    <link href="../bootstrapToggleButton/css/bootstrap-toggle.min.css" rel="stylesheet">
    <script src="../bootstrapToggleButton/js/bootstrap-toggle.min.js"></script>

    <link href="../bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet">
    <script src="../bootstrap3-editable/js/bootstrap-editable.js"></script>

    <!-- Bootstrap table -->
    <link rel="stylesheet" href="../boostrapTable/dist/bootstrap-table.css">
    <script src="../boostrapTable/dist/bootstrap-table.js"></script>
    <!-- Questa inclusione viene sempre DOPO bootstrap-table.js -->
    <script src="../boostrapTable/dist/locale/bootstrap-table-en-US.js"></script>

    <!-- Dynatable -->
    <link rel="stylesheet" href="../dynatable/jquery.dynatable.css">
    <script src="../dynatable/jquery.dynatable.js"></script>

    <!-- Bootstrap slider -->
    <script src="../bootstrapSlider/bootstrap-slider.js"></script>
    <link href="../bootstrapSlider/css/bootstrap-slider.css" rel="stylesheet"/>

    <!-- Filestyle -->
    <script type="text/javascript" src="../js/filestyle/src/bootstrap-filestyle.min.js"></script>

    <link href="https://fonts.googleapis.com/css?family=Cabin:400,500,600,700|Catamaran|Varela+Round" rel="stylesheet">

    
    <!-- Custom CSS -->
    <?php include "theme-switcher.php"?>
    
    <!-- Custom scripts -->
    <script type="text/javascript" src="../js/dashboard_mng.js"></script>
  </head>
  <body class="guiPageBody">
    <?php include "../cookie_banner/cookie-banner.php"; ?>
    <div class="container-fluid">
    <?php include "sessionExpiringPopup.php" ?> 

      <div class="mainContainer">
      <div class="menuFooter-container">
        <?php include "mainMenu.php" ?>
        <?php include "footer.php" ?>
      </div>
        <div class="col-xs-12 col-md-10" id="mainCnt">
          <!-- MOBILE MENU -->
          <!-- <div class="row hidden-md hidden-lg">
              <div id="mobHeaderClaimCnt" class="col-xs-12 hidden-md hidden-lg centerWithFlex">
                  <?php include "mobMainMenuClaim.php" ?>
              </div>
          </div> -->
          <div class="row header-container">
            <div id="mobLogo"><?php include "logoS4cSVG.php"; ?></div>
            <div id="headerTitleCnt"><?=$_GET["name"]?"Edit":"New"?> <?= _("Synoptic Template")?></div>
            <div class="user-menu-container">
              <?php include "loginPanel.php" ?>
            </div>
            <div class="col-lg-2 hidden-md hidden-lg centerWithFlex" id="headerMenuCnt"><?php include "mobMainMenu.php" ?></div>
          </div>
          <div class="row">
            <div class="col-xs-12" id="mainContentCnt"> 
              <form id="addSynopticTemplateForm" action="../controllers/addSynopticTemplate.php" method="POST">    
			  <input type="hidden" id="ownership" name="ownership" value="private">
              <div class="row mainContentRow" style="background-color: transparent">
				
				<div class="col-xs-12 col-sm-6 col-md-3">
                    <div class="col-xs-12 synopticTemplateLabel centerWithFlex">
                        <?= _("Name")?> *
                    </div>
                    <div class="col-xs-12">
                        <?php if(!$_GET["name"]) { ?>
						<input type="text" id="name" name="name" class="form-control" required="required"></input>
						<?php } else { ?>
						<input type="text" id="name" name="name" class="form-control" style="background-color:#eee;" value="<?=htmlentities($_GET["name"])?>" disabled></input>
						<input type="hidden" name="name" value="<?=htmlentities($_GET["name"])?>">
						<input type="hidden" id="editMode" name="edit" value="1">
						<?php } ?>
                    </div> 
                </div>
				
                <div class="col-xs-12 col-sm-6 col-md-3">
                    <div class="col-xs-12 synopticTemplateLabel centerWithFlex">
                        <?= _("Nature")?> *
                    </div>
                    <div class="col-xs-12">
                        <input type="hidden" id="nature_old" name="nature_old" value="<?=htmlentities($_GET["nature"])?>"></input>
						<select id="nature" name="nature" class="form-control" required="required"><option value=""></option></select>
                    </div> 
                </div>
				
				<div class="col-xs-12 col-sm-6 col-md-3">
                    <div class="col-xs-12 synopticTemplateLabel centerWithFlex">
                        <?= _("Subnature")?> *
                    </div>
                    <div class="col-xs-12">
                        <input type="hidden" id="subnature_old" name="subnature_old" value="<?=htmlentities($_GET["subnature"])?>"></input>
						<select id="subnature" name="subnature" class="form-control" required="required"><option value=""></option></select>
                    </div> 
                </div>
				
				<div class="col-xs-12 col-sm-6 col-md-3">
                   <div class="col-xs-12 synopticTemplateLabel centerWithFlex">
                        <?= _("SVG Template File")?> *
                    </div>
                    <div class="col-xs-12">
                        <input id="getTemplate" name="getTemplate" type="file" <?=$_GET["name"]?'':'required="required"'?> class="filestyle form-control" data-badge="false" data-input="true" data-size="nr" data-buttonname="btn-primary" data-buttontext="File" tabindex="-1" style="position: absolute; clip: rect(0px, 0px, 0px, 0px);" accept=".svg">
                    </div>  
                </div>
				<div class="col-xs-12 col-sm-6 col-md-3">
                   <div class="col-xs-12 synopticTemplateLabel centerWithFlex">
                        <?= _("Icon")?> 
                    </div>
                    <div class="col-xs-12">
                        <input id="getIcon" name="getIcon" type="file" class="filestyle form-control" data-badge="false" data-input="true" data-size="nr" data-buttonname="btn-primary" data-buttontext="File" tabindex="-1" style="position: absolute; clip: rect(0px, 0px, 0px, 0px);">
                    </div>  
                </div>
                <div class="col-xs-12" id="addSynopticTemplateBtnRow">
					<button type="button" id="addSynopticTemplateCancelBtn" class="btn cancelBtn  pull-right" data-dismiss="modal"><?= _("Reset")?></button>  
					<button type="submit" id="addSynopticTemplateConfirmBtn" class="btn confirmBtn pull-right" style="margin-right:15px;"><?= _("Save")?></button>									
					<button type="button" id="addSynopticTemplateBackBtn" class="btn cancelBtn  " data-dismiss="modal" style="margin-left:15px;"><?= _("Back")?></button>  
                </div>
                <div class="col-xs-12" id="addSynopticTemplateResultsRow">
                    <div class="col-xs-12 col-sm-6 col-sm-offset-3 centerWithFlex" id="addSynopticTemplateResultMsg"></div>
                    <div class="col-xs-12 col-sm-6 col-sm-offset-3 centerWithFlex" id="addSynopticTemplateResultBtns">
                        <button type="button" id="addSynopticTemplateOpenNewBtn" class="btn confirmBtn"><?= _("Open template")?></button>
                        <!-- <button type="button" id="addSynopticTemplateOpenListBtn" class="btn confirmBtn">Open templates list</button> -->
                        <button type="button" id="addSynopticTemplateNoActionBtn" class="btn confirmBtn"><?= _("No further action")?></button>
                    </div>
                </div>    
              </div>
              </form>    
            </div>
          </div>
        </div>
      </div>
    </div>

   
  </body>
</html>

<script type='text/javascript'>
    $(document).ready(function () 
    {
        var sessionEndTime = "<?php echo $_SESSION['sessionEndTime']; ?>";
        $('#sessionExpiringPopup').css("top", parseInt($('body').height() - $('#sessionExpiringPopup').height()) + "px");
        $('#sessionExpiringPopup').css("left", parseInt($('body').width() - $('#sessionExpiringPopup').width()) + "px");
        
        setInterval(function(){
            var now = parseInt(new Date().getTime() / 1000);
            var difference = sessionEndTime - now;
            
            if(difference === 300)
            {
                $('#sessionExpiringPopupTime').html("5 minutes");
                $('#sessionExpiringPopup').show();
                $('#sessionExpiringPopup').css("opacity", "1");
                setTimeout(function(){
                    $('#sessionExpiringPopup').css("opacity", "0");
                    setTimeout(function(){
                        $('#sessionExpiringPopup').hide();
                    }, 1000);
                }, 4000);
            }
            
            if(difference === 120)
            {
                $('#sessionExpiringPopupTime').html("2 minutes");
                $('#sessionExpiringPopup').show();
                $('#sessionExpiringPopup').css("opacity", "1");
                setTimeout(function(){
                    $('#sessionExpiringPopup').css("opacity", "0");
                    setTimeout(function(){
                        $('#sessionExpiringPopup').hide();
                    }, 1000);
                }, 4000);
            }
            
            if((difference > 0)&&(difference <= 60))
            {
                $('#sessionExpiringPopup').show();
                $('#sessionExpiringPopup').css("opacity", "1");
                $('#sessionExpiringPopupTime').html(difference + " seconds");
            }
            
            if(difference <= 0)
            {
                location.href = "logout.php?sessionExpired=true";
            }
        }, 1000);
        
        $('#mainContentCnt').height($('#mainMenuCnt').height() - $('#headerTitleCnt').height());
        // $('#iotApplicationsIframeCnt').height($('#mainMenuCnt').height() - $('#headerTitleCnt').height());
        
        $(window).resize(function(){
            $('#mainContentCnt').height($('#mainMenuCnt').height() - $('#headerTitleCnt').height());
            // $('#iotApplicationsIframeCnt').height($('#mainMenuCnt').height() - $('#headerTitleCnt').height());
            $('#sessionExpiringPopup').css("top", parseInt($('body').height() - $('#sessionExpiringPopup').height()) + "px");
            $('#sessionExpiringPopup').css("left", parseInt($('body').width() - $('#sessionExpiringPopup').width()) + "px");
        });
        
        $('#mainMenuCnt .mainMenuLink[id=<?= escapeForJS($_REQUEST['linkId']) ?>] div.mainMenuItemCnt').addClass("mainMenuItemCntActive");
        $('#mobMainMenuPortraitCnt .mainMenuLink[id=<?= escapeForJS($_REQUEST['linkId']) ?>] .mobMainMenuItemCnt').addClass("mainMenuItemCntActive");
        $('#mobMainMenuLandCnt .mainMenuLink[id=<?= escapeForJS($_REQUEST['linkId']) ?>] .mobMainMenuItemCnt').addClass("mainMenuItemCntActive");
        
        if($('div.mainMenuSubItemCnt').parents('a[id=<?= escapeForJS($_REQUEST['linkId']) ?>]').length > 0)
        {
            var fatherMenuId = $('div.mainMenuSubItemCnt').parents('a[id=<?= escapeForJS($_REQUEST['linkId']) ?>]').attr('data-fathermenuid');
            $("#" + fatherMenuId).attr('data-submenuVisible', 'true');
            $('#mainMenuCnt a.mainMenuSubItemLink[data-fatherMenuId=' + fatherMenuId + ']').show();
            $("#" + fatherMenuId).find('.submenuIndicator').removeClass('fa-caret-down');
            $("#" + fatherMenuId).find('.submenuIndicator').addClass('fa-caret-up');
            $('div.mainMenuSubItemCnt').parents('a[id=<?= escapeForJS($_REQUEST['linkId']) ?>]').find('div.mainMenuSubItemCnt').addClass("subMenuItemCntActive");
        }
        
        $('#color_hf').css("background-color", '#ffffff');
            
        $("#logoutBtn").off("click");
        $("#logoutBtn").click(function(event)
        {
           event.preventDefault();
           location.href = "logout.php";
        });
        
        $('#addSynopticTemplateCancelBtn').click(function(){
            $('#addSynopticTemplateForm')[0].reset();
        });
		
		$('#addSynopticTemplateBackBtn').click(function(){
            //location.href = "synopticTemplates.php?linkId=synopticTemplatesLink&pageTitle=Templates&fromSubmenu=false&sorts[title]=1";
			window.history.go(-1); return false;
        });
        
        $('#addSynopticTemplateForm').on("submit", function(event)
        {

			event.preventDefault();           
			
			$.ajax({
				url: "../controllers/getSynopticTemplate.php",
				data: {
					//orgFilter: "<?= @$_SESSION['loggedOrganization'] ?>",
					param: location.href.includes("AllOrgs")?"AllOrgs":"",
					role: "<?= ($_SESSION['isPublic'] ? 'Public' : $_SESSION['loggedRole']) ?>",
					name: $('#name').val(),
					force: true
				},
				type: "GET",
				async: true,
				dataType: 'json',
				success: function(data) 
				{
					
					/*if(data.applications.length > 0) {
						var halt = false;
						data.applications.forEach(function(template){
								if(template.user != "<?=@$_SESSION['loggedUsername']?>") {
									alert('A template already exists with that name.\nPlease choose a different name.');
									halt = true;
								}
								else if ('<?=htmlentities($_GET["name"],ENT_QUOTES)?>' != template.unique_name_id && !confirm('A template already exists with that name. Overwrite?')) {
									halt = true;
								}
						});						
						if(halt) return;
					}*/
					if(data.applications.length > 0) {
						var halt = false;
						data.applications.forEach(function(template){
							if('<?=htmlentities($_GET["name"],ENT_QUOTES)?>' != template.unique_name_id) {
								halt = true;
							}
						});
						if(halt) {
							alert('A template already exists with that name.\nPlease choose a different name.');
							return;
						}
					}
					
					// $('#addSynopticTemplateBtnRow').hide();
					$('#addSynopticTemplateResultsRow').show();
					$('#addSynopticTemplateOpenNewBtn').off('click');
					$('#addSynopticTemplateOpenListBtn').off('click');
					$('#addSynopticTemplateNoActionBtn').off('click'); 
			            
					$.ajax({
						url: $(document.getElementById('addSynopticTemplateForm')).attr("action"),
						type: $(document.getElementById('addSynopticTemplateForm')).attr("method"),
						dataType: "JSON",
						data: new FormData(document.getElementById('addSynopticTemplateForm')),
						processData: false,
						contentType: false,
						success: function (data, status)
						{
							if(data.result === 'Ok')
							{
								
								if(1 !== $("#editMode").val()) {
									$("select").attr("disabled","disabled");
									$("input[type=file]").attr("disabled","disabled");
									$("button.pull-right").attr("disabled","disabled");
								}
			
								$('#addSynopticTemplateResultMsg').html("Synoptic template saved correctly"+(!data.inSync?", but the categorization of its related instances could be out of sync":""));
								
								$('#addSynopticTemplateOpenNewBtn').click(function(){
									window.open(data.url, '_blank');
								});
								
								$('#addSynopticTemplateOpenListBtn').click(function(){
									// location.href = "synopticTemplates.php?linkId=synopticTemplatesLink&pageTitle=Templates&fromSubmenu=false&sorts[title]=1";
									$("#synopticTemplatesList").click();
								});
								
								$('#addSynopticTemplateNoActionBtn').click(function(){
									$('#addSynopticTemplateResultsRow').hide();
									$('#addSynopticTemplateBtnRow').show();
									$('#addSynopticTemplateForm')[0].reset();
								});
								
							}
							else
							{
								$('#addSynopticTemplateResultBtns').hide();
								$('#addSynopticTemplateResultMsg').html("Error saving synoptic template. "+data.detail);
								setTimeout(function(){
									$('#addSynopticTemplateResultsRow').hide();
									$('#addSynopticTemplateResultBtns').show();
									$('#addSynopticTemplateBtnRow').show();
								}, 2000);
							}
						},
						error: function (xhr, desc, err)
						{
							$('#addSynopticTemplateResultBtns').hide();
							$('#addSynopticTemplateResultMsg').html("Error saving synoptic template. "+desc);
							setTimeout(function(){
								$('#addSynopticTemplateResultsRow').hide();
								$('#addSynopticTemplateResultBtns').show();
								$('#addSynopticTemplateBtnRow').show();
							}, 2000);
						}
					});      
					
				},
				error: function(xhr, desc, err)
				{
					$('#addSynopticTemplateResultBtns').hide();
					$('#addSynopticTemplateResultMsg').html("Error saving synoptic template. "+desc);
					setTimeout(function(){
						$('#addSynopticTemplateResultsRow').hide();
						$('#addSynopticTemplateResultBtns').show();
						$('#addSynopticTemplateBtnRow').show();
					}, 2000);					
				}
			});
			
        });
		
		$.ajax({
			url: "<?=$synTplNatSnatSrc?>",
			type: "GET",
			async: true,
			dataType: "JSON",
			success: function(cats) 
			{
				if(cats.result == "OK") {
					cats.content.forEach(function(cat){
						if(cat["type"] == "nature") {
							if('<?=htmlentities($_GET["nature"],ENT_QUOTES)?>' == cat["value"]) {
								$("select#nature").append($("<option value=\""+cat["value"]+"\" selected>"+cat["label"]+"</option>"));
								cat["children_id"].forEach(function(childCatId) {
									cats.content.forEach(function(childCat){
										if(childCat.id == childCatId) {
											if('<?=htmlentities($_GET["subnature"],ENT_QUOTES)?>' == childCat["value"]) {
												$("select#subnature").append($("<option value=\""+childCat["value"]+"\" selected>"+childCat["label"]+"</option>"));
											}
											else {
												$("select#subnature").append($("<option value=\""+childCat["value"]+"\">"+childCat["label"]+"</option>"));
											}
										}
									});

								});
							}
							else {
								$("select#nature").append($("<option value=\""+cat["value"]+"\">"+cat["label"]+"</option>"));
							}
						}
					});
				}
			}
		});
		
		$("select#nature").on("change",function(event){
			$.ajax({
				url: "<?=$synTplNatSnatSrc?>",
				type: "GET",
				async: true,
				dataType: "JSON",
				success: function(cats) 
				{
					if(cats.result == "OK") {
						cats.content.forEach(function(cat){
							if( $("select#nature").val() == cat["value"] && cat["type"] == "nature") {
								$("select#subnature").empty();
								$("select#subnature").append($("<option value=\"\"></option>"));
								cat["children_id"].forEach(function(childCatId) {
									cats.content.forEach(function(childCat){
										if(childCat.id == childCatId) {
											$("select#subnature").append($("<option value=\""+childCat["value"]+"\">"+childCat["label"]+"</option>"));
										}
									});									
								});
							}
						});
					}
				}
			});
		});
                        
    });
</script>

<?php } else {
    include('../s4c-legacy-management/synopticTemplatesForm.php');
}
?>