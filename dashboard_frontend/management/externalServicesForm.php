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
            <div id="headerTitleCnt">External services: upload</div>
            <div class="user-menu-container">
              <?php include "loginPanel.php" ?>
            </div>
            <div class="col-lg-2 hidden-md hidden-lg centerWithFlex" id="headerMenuCnt"><?php include "mobMainMenu.php" ?></div>
          </div>
          <div class="row">
            <div class="col-xs-12" id="mainContentCnt"> 
              <form id="addExternalServiceForm" action="../controllers/addExternalService.php" method="POST">    
              <div class="row mainContentRow" style="background-color: transparent">
                <div class="col-xs-12 col-sm-6 col-md-3">
                    <div class="col-xs-12 externalServiceLabel centerWithFlex">
                        Nature
                    </div>
                    <div class="col-xs-12">
                        <select id="nature" name="nature" class="form-control" required="required">
                            <option value="Accommodation">Accommodation</option>
                            <option value="Advertising">Advertising</option>
                            <option value="AgricultureAndLivestock">Agriculture and livestock</option>
                            <option value="Assistance">Assistance</option>
                            <option value="CivilAndEdilEngineering">Civil and edil engineering</option>
                            <option value="CulturalActivity">Cultural activity</option>
                            <option value="EducationAndResearch">Education and research</option>
                            <option value="Emergency">Emergency</option>
                            <option value="Entertainment">Entertainment</option>
                            <option value="Environment">Environment</option>
                            <option value="Financial">Financial</option>
                            <option value="FinancialService">Financial service</option>
                            <option value="From Dashboard to IOT Device">From Dashboard to IOT Device</option>
                            <option value="From IOT Application to Dashboard">From IOT Application to Dashboard</option>
                            <option value="From IOT Device to KB">From IOT Device to KB</option>
                            <option value="Generic">Generic</option>
                            <option value="Government and Security">Government and security</option>
                            <option value="GovernmentOffice">Government office</option>
                            <option value="Healthcare">Healthcare</option>
                            <option value="IndustryAndManufacturing">Industry and manufacturing</option>
                            <option value="Infrastructure">Infrastructure</option>
                            <option value="Km4City Application">Km4City application</option>
                            <option value="MiningAndQuarrying">Mining and quarrying</option>
                            <option value="Mobility and Transport">Mobility and transport</option>
                            <option value="Services POI and IOT">Services POI and IOT</option>
                            <option value="Shopping">Shopping</option>
                            <option value="ShoppingAndService">Shopping and service</option>
                            <option value="Social">Social</option>
                            <option value="Time">Time</option>
                            <option value="Tourism">Tourism</option>
                            <option value="TourismService">TourismService</option>
                            <option value="TransferServiceAndRenting">TransferServiceAndRenting</option>
                            <option value="UtilitiesAndSupply">Utilities and supply</option>
                            <option value="Wholesale">Wholesale</option>
                            <option value="WineAndFood">Wine and food</option>
                        </select>
                    </div> 
                </div>    
                <div class="col-xs-12 col-sm-6 col-md-3">
                    <div class="col-xs-12 externalServiceLabel centerWithFlex">
                        Subnature (name of the service)
                    </div>
                    <div class="col-xs-12">
                        <input type="text" id="subnature" name="subnature" class="form-control" required="required"></input>
                    </div> 
                </div>
                <div class="col-xs-12 col-sm-6 col-md-3">
                    <div class="col-xs-12 externalServiceLabel centerWithFlex">
                        URL
                    </div>
                    <div class="col-xs-12">
                        <input type="text" id="param" name="param" class="form-control" required="required"></input>
                    </div> 
                </div>
                <div class="col-xs-12 col-sm-6 col-md-3">
                   <div class="col-xs-12 externalServiceLabel centerWithFlex">
                        Icon
                    </div>
                    <div class="col-xs-12">
                        <input id="getIcon" name="getIcon" type="file" class="filestyle form-control" data-badge="false" data-input="true" data-size="nr" data-buttonname="btn-primary" data-buttontext="File" tabindex="-1" style="position: absolute; clip: rect(0px, 0px, 0px, 0px);">
                    </div>  
                </div>
                <div class="col-xs-12" id="addExternalServiceBtnRow">
                    <button type="submit" id="addExternalServiceConfirmBtn" class="btn confirmBtn pull-right">Upload</button>
                    <button type="button" id="addExternalServiceCancelBtn" class="btn cancelBtn pull-right" data-dismiss="modal">Reset</button>  
                </div>
                <div class="col-xs-12" id="addExternalServiceResultsRow">
                    <div class="col-xs-12 col-sm-6 col-sm-offset-3 centerWithFlex" id="addExternalServiceResultMsg"></div>
                    <div class="col-xs-12 col-sm-6 col-sm-offset-3 centerWithFlex" id="addExternalServiceResultBtns">
                        <button type="button" id="addExternalServiceOpenNewBtn" class="btn confirmBtn">Open new service</button>
                        <button type="button" id="addExternalServiceOpenListBtn" class="btn confirmBtn">Open services list</button>
                        <button type="button" id="addExternalServiceNoActionBtn" class="btn confirmBtn">No further action</button>
                    </div>
                </div>    
              </div>
              </form>    
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modale creazione app -->
    <div class="modal fade" id="modalAddIoTApp" tabindex="-1" role="dialog" aria-labelledby="modalAddIoTApp" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content modalContentWizardForm" style="top:150px;">
          <div class="modalHeader centerWithFlex" style="background-color: rgba(51, 64, 69, 1) !important">
            Create IoT Application
          </div>

          <div id="modalAddIoTAppBody" class="modal-body modalBody" style="background-color: rgba(108, 135, 147, 1) !important">   
            <div class="row iotAppModalRow">
              <div class="col-xs-4 centerWithFlex iotAppModalLbl">
                Application name:
              </div>
              <div class="col-xs-8">
                <input type="text" name="inputTitleIoTApp" id="inputTitleIoTApp" value="" class="form-control" style="width: 100%;" required> 
              </div>
            </div>
<?php if($_SESSION['loggedRole']!='Manager') : ?>
            <div class="row iotAppModalRow">
              <div class="col-xs-4 centerWithFlex iotAppModalLbl">
                Application type:
              </div>
              <div class="col-xs-8">
                <select id="applicationType" class="form-control">
                    <option value="basic">Basic</option>
                    <option value="advanced">Advanced</option>
                </select>    
              </div>
            </div>  
<?php endif; ?>
              <div class="row">
                <div class="col-xs-12" style="font-weight: bold !important; margin-top: 25px !important; color: white;">
                  By pressing the <b>Confirm</b> button you agree to the <a style="color:#aedaff" href="https://www.snap4city.org/drupal/node/47" target="_blank">Terms and Conditions</a> and <a href="https://www.snap4city.org/drupal/node/49" style="color:#aedaff" target="_blank">Privacy Policy</a>.
                </div>
              </div>                           
            </div>
            <div id="modalAddIoTAppFooter" class="modal-footer">
              <button type="button" id="modalAddIoTAppCancelBtn" class="btn cancelBtn" data-dismiss="modal">Cancel</button>
              <button type="button" id="modalAddIoTAppConfirmBtn" name="addDashboardWizardConfirmBtn" class="btn confirmBtn internalLink">Confirm</button>
            </div>
          </div>    <!-- Fine modal content -->
        </div> <!-- Fine modal dialog -->
      </div><!-- Fine modale -->
    <!-- Modale creazione app -->
    <div class="modal fade" id="modalEditIoTApp" tabindex="-1" role="dialog" aria-labelledby="modalEditIoTApp" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content modalContentWizardForm" style="top:150px;">
          <div class="modalHeader centerWithFlex" style="background-color: rgba(51, 64, 69, 1) !important">
            IoT Application Properties
          </div>

          <div id="modalEditIoTAppBody" class="modal-body modalBody" style="background-color: rgba(108, 135, 147, 1) !important">   
            <input type="hidden" id="iotAppIdHidden" name="iotAppIdHidden" />
            <div class="row iotAppModalRow">
              <div class="col-xs-4 centerWithFlex iotAppModalLbl">
                Application name:
              </div>
              <div class="col-xs-8">
                <input type="text" name="inputTitleEditIoTApp" id="inputTitleEditIoTApp" value="" class="form-control" style="width: 100%;" required> 
              </div>
            </div>
<?php if($_SESSION['loggedRole']!='Manager') : ?>
            <div class="row iotAppModalRow">
              <div class="col-xs-4 centerWithFlex iotAppModalLbl">
                Application type:
              </div>
              <div class="col-xs-8">
                <select id="appTypeEditIoTApp" class="form-control" disabled>
                    <option value="basic">Basic</option>
                    <option value="advanced">Advanced</option>
                </select>    
              </div>     
            </div>
<?php endif; ?>
            <div class="row iotAppModalRow">
              <div class="col-xs-4 centerWithFlex iotAppModalLbl">
                Created:
              </div>
              <div class="col-xs-8">
                <input type="text" readonly name="createdEditIoTApp" id="createdEditIoTApp" value="10 min" class="form-control">   
              </div>     
            </div>
            <div class="row iotAppModalRow">
              <div class="col-xs-12 centerWithFlex">
                <button type="button" id="modalEditIoTAppDeleteBtn" class="btn deleteBtn" data-dismiss="modal">Delete</button>
                <button type="button" id="modalEditIoTAppRestartBtn" class="btn restartBtn" data-dismiss="modal">Restart</button>
              </div>     
            </div>
            
            </div>
            <div id="modalAddIoTAppFooter" class="modal-footer">
              <button type="button" id="modalEditIoTAppCancelBtn" class="btn cancelBtn" data-dismiss="modal">Cancel</button>
              <button type="button" id="modalEditIoTAppConfirmBtn" name="addDashboardWizardConfirmBtn" class="btn confirmBtn internalLink">Update</button>
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
        $('#iotApplicationsIframeCnt').height($('#mainMenuCnt').height() - $('#headerTitleCnt').height());
        
        $(window).resize(function(){
            $('#mainContentCnt').height($('#mainMenuCnt').height() - $('#headerTitleCnt').height());
            $('#iotApplicationsIframeCnt').height($('#mainMenuCnt').height() - $('#headerTitleCnt').height());
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
        
        $('#addExternalServiceCancelBtn').click(function(){
            $('#addExternalServiceForm')[0].reset();
        });
        
        $('#addExternalServiceForm').on("submit", function(event)
        {
            event.preventDefault();
            
            $('#addExternalServiceBtnRow').hide();
            $('#addExternalServiceResultsRow').show();
            $('#addExternalServiceOpenNewBtn').off('click');
            $('#addExternalServiceOpenListBtn').off('click');
            $('#addExternalServiceNoActionBtn').off('click');
            
            $.ajax({
                url: $(this).attr("action"),
                type: $(this).attr("method"),
                dataType: "JSON",
                data: new FormData(this),
                processData: false,
                contentType: false,
                success: function (data, status)
                {
                    if(data.result === 'Ok')
                    {
                        $('#addExternalServiceResultMsg').html("New external service added correctly");
                        
                        $('#addExternalServiceOpenNewBtn').click(function(){
                            window.open(data.url, '_blank');
                        });
                        
                        $('#addExternalServiceOpenListBtn').click(function(){
                            location.href = "externalServices.php?linkId=externalServicesLink&fromSubmenu=false&sorts[title]=1";
                        });
                        
                        $('#addExternalServiceNoActionBtn').click(function(){
                            $('#addExternalServiceResultsRow').hide();
                            $('#addExternalServiceBtnRow').show();
                            $('#addExternalServiceForm')[0].reset();
                        });
                    }
                    else
                    {
                        $('#addExternalServiceResultBtns').hide();
                        $('#addExternalServiceResultMsg').html("Error adding new external service: please try again");
                        setTimeout(function(){
                            $('#addExternalServiceResultsRow').hide();
                            $('#addExternalServiceResultBtns').show();
                            $('#addExternalServiceBtnRow').show();
                        }, 2000);
                    }
                },
                error: function (xhr, desc, err)
                {
                    $('#addExternalServiceResultBtns').hide();
                    $('#addExternalServiceResultMsg').html("Error adding new external service: please try again");
                    setTimeout(function(){
                        $('#addExternalServiceResultsRow').hide();
                        $('#addExternalServiceResultBtns').show();
                        $('#addExternalServiceBtnRow').show();
                    }, 2000);
                }
            });       
        });
                        
    });
</script>

<?php } else {
    include('../s4c-legacy-management/externalServicesForm.php');
}
?>