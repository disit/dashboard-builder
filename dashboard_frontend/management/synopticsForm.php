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
include('process-form.php');
if (!isset($_SESSION)) 
{
  session_start();
}

$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);

checkSession('Manager');
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php include "mobMainMenuClaim.php" ?></title>

    <!-- Bootstrap Core CSS -->
    <link href="../css/bootstrap.css" rel="stylesheet">

    <link href="../css/bootstrap-colorpicker.min.css" rel="stylesheet">

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

    <!-- Font awesome icons -->
    <link rel="stylesheet" href="../js/fontAwesome/css/font-awesome.min.css">

    <link href="https://fonts.googleapis.com/css?family=Cabin:400,500,600,700|Catamaran|Varela+Round" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../css/dashboard.css" rel="stylesheet">
    <link href="../css/dashboardList.css" rel="stylesheet">
    <link href="../css/synopticsForm.css" rel="stylesheet">

    <!-- Custom scripts -->
    <script type="text/javascript" src="../js/dashboard_mng.js"></script>
  </head>
  <body class="guiPageBody">
    <div class="container-fluid">
    <?php include "sessionExpiringPopup.php" ?> 

      <div class="row mainRow">
      <?php include "mainMenu.php" ?>
        <div class="col-xs-12 col-md-10" id="mainCnt">
          <div class="row hidden-md hidden-lg">
            <div id="mobHeaderClaimCnt" class="col-xs-12 hidden-md hidden-lg centerWithFlex">
              <?php include "mobMainMenuClaim.php" ?>
            </div>
          </div>
          <div class="row">
            <div class="col-xs-10 col-md-12 centerWithFlex" id="headerTitleCnt">New Synoptic</div>
            <div class="col-xs-2 hidden-md hidden-lg centerWithFlex" id="headerMenuCnt"><?php include "mobMainMenu.php" ?></div>
          </div>
          <div class="row">
            <div class="col-xs-12" id="mainContentCnt" style='background-color: rgba(138, 159, 168, 1)'> 
              <form id="addSynopticForm" action="../controllers/addSynoptic.php" method="POST">    
			  <input type="hidden" id="ownership" name="ownership" value="private">
              
			  <div class="row mainContentRow" style="background-color: transparent">
                <div class="col-xs-12 col-sm-6 col-md-3 centerWithFlex">
					<h2>Synoptic</h2>
                </div>

				<div class="col-xs-12 col-sm-6 col-md-3">
                    <div class="col-xs-12 synopticLabel centerWithFlex">
                        Template
                    </div>
                    <div class="col-xs-12">
                        <?php if($_GET["template"]) { ?> 
							<select id="low_level_type" class="form-control" disabled></select><input type="hidden" name="low_level_type" value="<?=htmlentities($_GET["template"])?>">
						<?php } else { ?>
							<select id="low_level_type" name="low_level_type" class="form-control" required="required"></select> 
						<?php } ?>
						
                    </div> 
                </div>

				 <div class="col-xs-12 col-sm-6 col-md-3">
                    <div class="col-xs-12 synopticLabel centerWithFlex">
                        Name
                    </div>
                    <div class="col-xs-12">
                        <input type="text" id="unique_name_id" name="unique_name_id" class="form-control" required="required"></input>
                    </div> 
                </div>
				
				<!-- <div class="col-xs-12 col-sm-6 col-md-3">
                   <div class="col-xs-12 synopticLabel centerWithFlex">
                        Icon
                    </div>
                    <div class="col-xs-12">
                        <input id="getIcon" name="getIcon" type="file" class="filestyle form-control" data-badge="false" data-input="true" data-size="nr" data-buttonname="btn-primary" data-buttontext="File" tabindex="-1" style="position: absolute; clip: rect(0px, 0px, 0px, 0px);">
                    </div>  
                </div> -->
				

			  </div>
				
				<?php if($_GET["template"]) { ?>
				
				<div id="inputs" class="row mainContentRow" style="background-color: transparent">
				 <div class="col-xs-12 col-sm-6 col-md-3">
					<div style="margin-left:14px;"><h2>Read variables</h2>Select the variables from the lists</div>
                </div>
				</div>
				
				<div id="outputs" class="row mainContentRow" style="background-color: transparent">
				  <div class="col-xs-12 col-sm-6 col-md-3">
					<div style="margin-left:14px;"><h2>Write variables</h2>Select the variables from the lists</div>
                  </div>
				</div>
				<?php } ?>
			  
			
			<!-- <div class="row mainContentRow" style="background-color: transparent"> -->
				<div class="col-xs-12 " id="addSynopticBtnRow">
						<button type="button" id="addSynopticCancelBtn" class="btn cancelBtn pull-right" data-dismiss="modal">Reset</button>
						<button type="submit" id="addSynopticConfirmBtn" class="btn confirmBtn pull-right" style="margin-right:15px;">Save</button>							
						<button type="button" id="addSynopticBackBtn" class="btn cancelBtn " data-dismiss="modal" style="margin-left:15px;">Back</button> 

				</div>				
			<!-- </div> -->
				
			<!-- <div class="row mainContentRow" style="background-color: transparent">			 -->	
                <div class="col-xs-12" id="addSynopticResultsRow">
                    <div class="col-xs-12 col-sm-6 col-sm-offset-3 centerWithFlex" id="addSynopticResultMsg"></div>
                    <div class="col-xs-12 col-sm-6 col-sm-offset-3 centerWithFlex" id="addSynopticResultBtns">
                        <button type="button" id="addSynopticOpenNewBtn" class="btn confirmBtn">Open Synoptic</button>
                        <!-- <button type="button" id="addSynopticOpenListBtn" class="btn confirmBtn">Open synoptics list</button> -->
                        <button type="button" id="addSynopticNoActionBtn" class="btn confirmBtn">No further action</button>
                    </div>
                </div>    
			  <!-- </div> -->
			  
              </form>    
            </div>
          </div>
        </div>
      </div>
    </div>
	
	<!-- Modale nuova shared variable -->
	<div class="modal fade" id="modalDelDash" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog" role="document">
		  <div class="modal-content">
			<div class="modalHeader centerWithFlex">
			  New Shared Variable
			</div>
			<input type="hidden" id="dashIdDelHidden" name="dashIdDelHidden" />
			<div id="delDashModalBody" class="modal-body modalBody">
				<div class="row">
					<div id="delDashNameMsg" class="col-xs-12 modalCell">
						<div class="modalDelMsg col-xs-12 centerWithFlex">
							Variable name:
						</div>
					</div>
				</div>
				<div class="row">
					<div id="delDashNameMsg" class="col-xs-12 modalCell">
						<div class="modalDelMsg col-xs-12 centerWithFlex">
							shared_<input type="text" class="form-control" id="newSharedVariableName" placeholder="New shared variable name">
						</div>
						<div class="modalDelMsg col-xs-12 centerWithFlex delegationsModalMsg" id="newGroupDelegatedMsg">
							
						</div>
					</div>
				</div>
			</div>
			<div id="delDashModalFooter" class="modal-footer">
			  <button type="button" id="delDashCancelBtn" class="btn cancelBtn" data-dismiss="modal">Cancel</button>
			  <button type="button" id="delDashConfirmBtn" class="btn confirmBtn internalLink disabled" data-dismiss="modal">Confirm</button>
			</div>
		  </div>
		</div>
	</div>
	<!-- Fine nuova shared variable -->    
	
	<!-- Modale impostazione valore costante -->
	<div class="modal fade" id="modalDelDash2" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog" role="document">
		  <div class="modal-content">
			<div class="modalHeader centerWithFlex">
			  Set to a fixed value
			</div>
			<input type="hidden" id="dashIdDelHidden2" name="dashIdDelHidden2" />
			<div id="delDashModalBody" class="modal-body modalBody">
				<div class="row">
					<div id="delDashNameMsg" class="col-xs-12 modalCell">
						<div class="modalDelMsg col-xs-12 centerWithFlex">
							Value:
						</div>
					</div>
				</div>
				<div class="row">
					<div id="delDashNameMsg" class="col-xs-12 modalCell">
						<div class="modalDelMsg col-xs-12 centerWithFlex">
							<input type="text" class="form-control" id="fixedValue" placeholder="Put the value here">
						</div>
						<div class="modalDelMsg col-xs-12 centerWithFlex delegationsModalMsg" id="newGroupDelegatedMsg">
							
						</div>
					</div>
				</div>
			</div>
			<div id="delDashModalFooter" class="modal-footer">
			  <button type="button" id="delDashCancelBtn2" class="btn cancelBtn" data-dismiss="modal">Cancel</button>
			  <button type="button" id="delDashConfirmBtn2" class="btn confirmBtn internalLink" data-dismiss="modal">Confirm</button>
			</div>
		  </div>
		</div>
	</div>
	<!-- Fine modale impostazione valore costante --> 
		
  </body>
</html>

<script type='text/javascript'>
    
	var HTMLEncode = function(str) {
		var i = str.length,
			aRet = [];

		while (i--) {
			var iC = str[i].charCodeAt();
			if (iC < 65 || iC > 127 || (iC>90 && iC<97)) {
				aRet[i] = '&#'+iC+';';
			} else {
				aRet[i] = str[i];
			}
		}
		return aRet.join('');
	};
		
	var trunc = function(str, n){
	  return (str.length > n) ? str.substr(0, n-1) + '&hellip;' : str;
	};
	
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
        
        $(window).resize(function(){
            $('#mainContentCnt').height($('#mainMenuCnt').height() - $('#headerTitleCnt').height());
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
        
        $('#addSynopticCancelBtn').click(function(){
            $('#addSynopticForm')[0].reset();
        });
		
		$('#addSynopticBackBtn').click(function(){
            //location.href = "synoptics.php?linkId=synopticsLink&pageTitle=Synoptics&fromSubmenu=false&sorts[title]=1";
			window.history.go(-1); return false;
        });
        
        $('#addSynopticForm').on("submit", function(event)
        {

			event.preventDefault();           
			
			$.ajax({
				url: "../controllers/getSynoptic.php",
				data: {
					//orgFilter: "<?= @$_SESSION['loggedOrganization'] ?>",
					param: location.href.includes("AllOrgs")?"AllOrgs":"",
					role: "<?= ($_SESSION['isPublic'] ? 'Public' : $_SESSION['loggedRole']) ?>",
					lowLevelType: $('#low_level_type').val(),
					uniqueNameId: $('#unique_name_id').val()
				},
				type: "GET",
				async: true,
				dataType: 'json',
				success: function(data) 
				{
					
					if(data.applications.length > 0) {
							alert('A synoptic already exists with that name and template. Please choose a different name.');
							return;
					}
					
					$('#addSynopticResultsRow').show();
					$('#addSynopticOpenNewBtn').off('click');
					$('#addSynopticOpenListBtn').off('click');
					$('#addSynopticNoActionBtn').off('click'); 
			            
					$.ajax({
						url: $(document.getElementById('addSynopticForm')).attr("action"),
						type: $(document.getElementById('addSynopticForm')).attr("method"),
						dataType: "JSON",
						data: new FormData(document.getElementById('addSynopticForm')),
						processData: false,
						contentType: false,
						success: function (data, status)
						{
							if(data.result === 'Ok')
							{
								
								$("select").attr("disabled","disabled");
								$("input[type=text]").attr("disabled","disabled");
								$("button.pull-right").attr("disabled","disabled");
			
								$('#addSynopticResultMsg').html("New synoptic saved correctly");
								
								$('#addSynopticOpenNewBtn').click(function(){
									window.open(data.url, '_blank');
								});
								
								$('#addSynopticOpenListBtn').click(function(){
									//location.href = "synoptics.php?linkId=synopticsLink&pageTitle=Synoptics&fromSubmenu=false&sorts[title]=1";
									$("#synopticsList").click();
								});
								
								$('#addSynopticNoActionBtn').click(function(){
									$('#addSynopticResultsRow').hide();
									$('#addSynopticBtnRow').show();
									$('#addSynopticForm')[0].reset();
								});
							}
							else
							{
								$('#addSynopticResultBtns').hide();
								$('#addSynopticResultMsg').html("Error saving new synoptic. "+data.detail);
								setTimeout(function(){
									$('#addSynopticResultsRow').hide();
									$('#addSynopticResultBtns').show();
									$('#addSynopticBtnRow').show();
								}, 2000);
							}
						},
						error: function (xhr, desc, err)
						{
							$('#addSynopticResultBtns').hide();
							$('#addSynopticResultMsg').html("Error saving new synoptic. "+desc);
							setTimeout(function(){
								$('#addSynopticResultsRow').hide();
								$('#addSynopticResultBtns').show();
								$('#addSynopticBtnRow').show();
							}, 2000);
						}
					});      
					
				},
				error: function(xhr, desc, err)
				{
					$('#addSynopticResultBtns').hide();
					$('#addSynopticResultMsg').html("Error saving new synoptic. "+desc);
					setTimeout(function(){
						$('#addSynopticResultsRow').hide();
						$('#addSynopticResultBtns').show();
						$('#addSynopticBtnRow').show();
					}, 2000);					
				}
			});
			
        });
		
		var currTpl = '';
		$("#low_level_type").on("change",function(){
			if(currTpl == '') {
				currTpl = $("#low_level_type").val();
				location.replace("?template="+$("#low_level_type").val());
			}
			else if(confirm("Switch to different template? Variable mappings will go lost.")) {
				currTpl = $("#low_level_type").val();
				location.replace("?template="+$("#low_level_type").val());
			}
		});
		
		$.ajax({
			url: "../controllers/getSynopticTemplates.php",
			data: {
				//orgFilter: "<?= @$_SESSION['loggedOrganization'] ?>",
				param: location.href.includes("AllOrgs")?"AllOrgs":"",
				role: "<?= ($_SESSION['isPublic'] ? 'Public' : $_SESSION['loggedRole']) ?>"
			},
			type: "GET",
			async: true,
			dataType: 'json',
			success: function(data) {
				$("select#low_level_type").append($("<option value=\"\"></option>"));
				data.applications.forEach(function(record){
					$("select#low_level_type").append($("<option value=\""+record.low_level_type+"\" "+('<?=str_replace("'","\\'",$_GET["template"])?>' == record.low_level_type+'' ? "selected" : "")+">"+record.low_level_type+" - "+(record.ownership == "public" ? (record.user != '<?=$_SESSION["loggedUsername"]?>' ? "Public - " : "My Own: Public - ") : (record.user != '<?=$_SESSION["loggedUsername"]?>' ? ('<?=$_SESSION["loggedRole"]?>' == 'RootAdmin' ? "Private - ":"Delegated"): "My Own - ") )+(record.ownership == "private" && record.user != '<?=$_SESSION["loggedUsername"]?>' ? (record.user?" by "+record.user+" - ":" - "):"")+record.organizations+"</option>"));
					if('<?=str_replace("'","\\'",$_GET["template"])?>' == record.low_level_type) {						
						$.ajax({
							url: "../img/synopticTemplates/svg/"+record.parameters.substring(record.parameters.lastIndexOf("/")),
							data: {},
							type: "GET",
							async: true,
							dataType: 'html',
							success: function(svg) {
								var inputs = {};
								var outputs = {};
								$(svg).find("*[data-siow]").each(function(){
									$(this).data("siow").forEach(function(siow){
										if(siow.originator == "server") {
											if(!Object.keys(inputs).includes(siow.event)) {
												inputObj = {};
												inputObj["name"] = siow.event;
												siow.actions.forEach(function(action){
													if(action["validate"]) inputObj["dataType"] = action["validate"];
												});
												if(!inputObj["dataType"]) inputObj["dataType"] = "any";
												inputs[siow.event] = inputObj;
											}
										}
										else {
											siow.actions.forEach(function(action){
												if(!Object.keys(outputs).includes(JSON.parse(action.format).tag)){
													var outObj = {};
													outObj["name"] = JSON.parse(action.format).tag;
													if(action["validate"]) outObj["dataType"] = action["validate"];
													if(!outObj["dataType"]) outObj["dataType"] = "any";
													outputs[JSON.parse(action.format).tag] = outObj;
												}
											});
										}
									});
								});	

								$.ajax({
									url: "../controllers/getVarListForSynoptic.php",
									data: {},
									type: "GET",
									async: true,
									dataType: "JSON",
									success: function(varList) {
										Object.keys(inputs).forEach(function(input) {
										$("#inputs").append($("<div class=\"col-xs-12 col-sm-6 col-md-3\"><div class=\"col-xs-12 synopticLabel centerWithFlex\">"+input+"</div>"+
											"<div class=\"col-xs-12\"><select id=\"input_"+input+"\" name=\"input_"+input+"\" class=\"form-control varpicker\"></select></div></div>"));
											$("#input_"+input).append("<option value=\""+input.replace(/[^\w\.-]/g,'').substr(0,249)+"\"></option>");									
											Object.keys(varList).forEach(function(oneVarID) {
												if( [ varList[oneVarID]["dataType"], "any" ].includes(inputs[input]["dataType"])) {
												//if( !varList[oneVarID]["isPublic"] ) {
													var myown = "";
													if(varList[oneVarID]["isMyOwn"]) myown = "My own";
													if(varList[oneVarID]["isDelegated"]) myown = "Delegated";
													if('<?=$_SESSION["loggedRole"]?>' == 'RootAdmin') myown = (myown=="Delegated"?"Delegated - ":"") + " " + varList[oneVarID]["username"]+" ("+varList[oneVarID]["organizations"].substring(4,varList[oneVarID]["organizations"].indexOf(','))+")";
													if(myown) myown = ' - '+myown.trim();
													var ownership = varList[oneVarID]["ownership"].charAt(0).toUpperCase() + varList[oneVarID]["ownership"].slice(1);
													if(!(varList[oneVarID]["isSensor"] || varList[oneVarID]["isActuator"] || varList[oneVarID]["isShared"])) $("#input_"+input).append("<option value=\"" + varList[oneVarID]["id"] + "\">MyKPI - " + varList[oneVarID]["id"] + " - " + varList[oneVarID]["valueName"] + myown + " - " + ownership + "</option>");
													else if(varList[oneVarID]["isSensor"]) $("#input_"+input).append("<option value=\"" + varList[oneVarID]["id"] + "\">Sensor - " + varList[oneVarID]["valueName"] + " " + varList[oneVarID]["valueType"] + myown + " - " + ownership + "</option>");
													else if(varList[oneVarID]["isShared"]) $("#input_"+input).append("<option value=\"" + varList[oneVarID]["varName"] + "\">Shared  - " + varList[oneVarID]["varName"] + "</option>");													
													if(varList[parseInt(oneVarID)]["isFavourite"] && varList[parseInt(oneVarID)+1] && !varList[parseInt(oneVarID)+1]["isFavourite"]) $("#input_"+input).append("<option value=\""+input.replace(/[^\w\.-]/g,'').substr(0,249)+"\"></option>");													
												//}
												}
											});	

											$("#input_"+input).append("<option value=\""+input.replace(/[^\w\.-]/g,'').substr(0,249)+"\"></option>");
											
											$("#input_"+input).append("<option value=\"do_create_new_shared_variable\">New shared variable&hellip;</option>");
											$("#input_"+input).change(function(){
												if(this.value == "do_create_new_shared_variable") {
													$('#dashIdDelHidden').val("#input_"+input);
													$('#modalDelDash').modal('show');
												}
											});
											
											$("#input_"+input).append("<option value=\"do_set_to_fixed_value\">Set to a fixed value&hellip;</option>");
											$("#input_"+input).change(function(){
												if(this.value == "do_set_to_fixed_value") {
													$('#dashIdDelHidden2').val("#input_"+input);
													$('#modalDelDash2').modal('show');
												}
											});
											
										});
										if(Object.keys(inputs).length == 0) $("#inputs").hide();
										Object.keys(outputs).forEach(function(output) {
												$("#outputs").append($("<div class=\"col-xs-12 col-sm-6 col-md-3\"><div class=\"col-xs-12 synopticLabel centerWithFlex\">"+output+"</div>"+
													"<div class=\"col-xs-12\"><select id=\"output_"+output+"\" name=\"output_"+output+"\" class=\"form-control varpicker\"></select></div></div>"));
												$("#output_"+output).append("<option value=\""+output.replace(/[^\w\.-]/g,'').substr(0,249)+"\"></option>");									
												Object.keys(varList).forEach(function(oneVarID) {
													if(varList[oneVarID]["isMyOwn"] || varList[oneVarID]["isShared"]) {
														if( [ varList[oneVarID]["dataType"], "any" ].includes(outputs[output]["dataType"])) {
															var myown = "";
															if(varList[oneVarID]["isMyOwn"]) myown = "My own";
															if(varList[oneVarID]["isDelegated"]) myown = "Delegated";
															if('<?=$_SESSION["loggedRole"]?>' == 'RootAdmin') myown = (myown=="Delegated"?"Delegated - ":"") + " " + varList[oneVarID]["username"]+" "+varList[oneVarID]["organizations"].substring(4,varList[oneVarID]["organizations"].indexOf(','))+")";
															if(myown) myown = ' - '+myown.trim();
															var ownership = varList[oneVarID]["ownership"].charAt(0).toUpperCase() + varList[oneVarID]["ownership"].slice(1);
															if(!(varList[oneVarID]["isSensor"] || varList[oneVarID]["isActuator"] || varList[oneVarID]["isShared"])) $("#output_"+output).append("<option value=\"" + varList[oneVarID]["id"] + "\">MyKPI - " + varList[oneVarID]["id"] + " - " + varList[oneVarID]["valueName"] + myown + " - " + ownership + "</option>");		
															else if(varList[oneVarID]["isActuator"]) $("#output_"+output).append("<option value=\"" + varList[oneVarID]["id"] + "\">Actuator - " + varList[oneVarID]["valueName"] + " " + varList[oneVarID]["valueType"] + myown + " - " + ownership + "</option>");	
															else if(varList[oneVarID]["isShared"]) $("#output_"+output).append("<option value=\"" + varList[oneVarID]["varName"] + "\">Shared  - " + varList[oneVarID]["varName"] + "</option>");	
															if(varList[parseInt(oneVarID)]["isFavourite"] && varList[parseInt(oneVarID)+1] && !varList[parseInt(oneVarID)+1]["isFavourite"]) $("#output_"+output).append("<option value=\"\"></option>");	
														}															
													}
												});
												
												$("#output_"+output).append("<option value=\""+output.replace(/[^\w\.-]/g,'').substr(0,249)+"\"></option>");
												
												$("#output_"+output).append("<option value=\"do_create_new_shared_variable\">New shared variable&hellip;</option>");
												$("#output_"+output).change(function(){
													if(this.value == "do_create_new_shared_variable") {
														$('#dashIdDelHidden').val("#output_"+output);
														$('#modalDelDash').modal('show');
													}
												});
												
												/* $("#output_"+output).append("<option value=\"do_set_to_fixed_value\">Set to a fixed value&hellip;</option>");
												$("#output_"+output).change(function(){
													if(this.value == "do_set_to_fixed_value") {
														$('#dashIdDelHidden2').val("#output_"+output);
														$('#modalDelDash2').modal('show');
													}
												});		 */										
												
										});
										
										if(Object.keys(outputs).length == 0) $("#outputs").hide();										
								
									}
									
								});
								
							}
						});
					}
				});				
			}
		});
		
		$('#delDashConfirmBtn').off("click");
		$('#delDashConfirmBtn').click(function(){
			$("select.varpicker").prepend('<option value="shared_'+$("#newSharedVariableName").val()+'">New Shared - shared_'+$("#newSharedVariableName").val()+'</option>');	
			$($("#dashIdDelHidden").val()).val("shared_"+$("#newSharedVariableName").val());
		});
			
		String.prototype.toKafkaTopic = function() {
			str = this;
			str = str.split(" ").join("_").replace(/[^\w\.-]/g,'').substr(0,249);
			return str;
		};
        $("#newSharedVariableName").keyup(function(){
			if(!this.value) {
				$('#newGroupDelegatedMsg').css('color', '#f3cf58');
				$('#newGroupDelegatedMsg').html('The variable name cannot be empty');
				$('#delDashConfirmBtn').addClass('disabled');	
			}
			else if(this.value != this.value.toKafkaTopic()) {
				$('#newGroupDelegatedMsg').css('color', '#f3cf58');
				$('#newGroupDelegatedMsg').html('Please only use letters, digits, underscores, dashes, and dots.');
				$('#delDashConfirmBtn').addClass('disabled');
			}
			else if($("option[value=shared_"+this.value+"]").length > 0) {
				$('#newGroupDelegatedMsg').css('color', '#f3cf58');
				$('#newGroupDelegatedMsg').html('A shared variable already exists with that name');
				$('#delDashConfirmBtn').addClass('disabled');		
			}
			else {
				$('#newGroupDelegatedMsg').css('color', 'white');
				$('#newGroupDelegatedMsg').html('The name is good. Hit CONFIRM to proceed.');
				$('#delDashConfirmBtn').removeClass('disabled');		
			}				
		});
		
		$('#delDashConfirmBtn2').off("click");
		$('#delDashConfirmBtn2').click(function(){
			$($("#dashIdDelHidden2").val()).prepend('<option value="const_'+window.btoa($("#fixedValue").val())+'">Fixed value: '+HTMLEncode(trunc($("#fixedValue").val(),20))+'</option>');	
			$($("#dashIdDelHidden2").val()).val("const_"+window.btoa($("#fixedValue").val()));
		});	
		
    });
</script>  