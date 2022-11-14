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
    include('../TourRepository.php');
    session_start();
    
    checkSession('Public');

    $tourRepo = new TourRepository($host, $username, $password,$dbname);
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

       <!-- Bootstrap table -->
       <link rel="stylesheet" href="../boostrapTable/dist/bootstrap-table.css">
       <script src="../boostrapTable/dist/bootstrap-table.js"></script>
       <!-- Questa inclusione viene sempre DOPO bootstrap-table.js -->
       <script src="../boostrapTable/dist/locale/bootstrap-table-en-US.js"></script>

       <!-- Font awesome icons -->
        <link rel="stylesheet" href="../js/fontAwesome/css/font-awesome.min.css">

        <link href="https://fonts.googleapis.com/css?family=Cabin:400,500,600,700|Catamaran|Varela+Round" rel="stylesheet">
        
        <!-- Custom CSS -->
        <!--<link href="../css/dashboard.css" rel="stylesheet">
        
        <!-- Custom scripts -->
        <script src="../js/accountManagement.js"></script>
        
        <!--<link href="https://fonts.googleapis.com/css?family=Cabin:400,500,600,700|Catamaran|Varela+Round" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">-->
        
        
        
        <!-- incluso da me-->
        
       <!-- Bootstrap editable tables -->
       <link href="../bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet">
       <script src="../bootstrap3-editable/js/bootstrap-editable.js"></script>

       <!-- Bootstrap table -->
       <!-- Questa inclusione viene sempre DOPO bootstrap-table.js -->
       
       <!-- Dynatable -->
       <link rel="stylesheet" href="../dynatable/jquery.dynatable.css">
       <script src="../dynatable/jquery.dynatable.js"></script>
       
        <!-- Bootstrap Multiselect -->
        <script src="../js/bootstrap-multiselect_1.js"></script>
        <link href="../css/bootstrap-multiselect_1.css" rel="stylesheet">

        <!-- DataTables -->
        <script type="text/javascript" charset="utf8" src="../js/DataTables/datatables.js"></script>
        <link rel="stylesheet" type="text/css" href="../js/DataTables/datatables.css">
        <script type="text/javascript" charset="utf8" src="../js/DataTables/dataTables.bootstrap.min.js"></script>
        <script type="text/javascript" charset="utf8" src="../js/DataTables/dataTables.responsive.min.js"></script>
        <script type="text/javascript" charset="utf8" src="../js/DataTables/responsive.bootstrap.min.js"></script>
        <link rel="stylesheet" type="text/css" href="../css/DataTables/dataTables.bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="../css/DataTables/responsive.bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="../css/DataTables/jquery.dataTables.min.css">  

        <!-- Leaflet -->
        <!-- Versione locale: 1.3.1 --> 
        <script src="../leafletCore/leaflet.js"></script> 
        <link rel="stylesheet" href="../leafletCore/leaflet.css" />
        
        <!-- Bootstrap slider -->
        <script src="../bootstrapSlider/bootstrap-slider.js"></script>
        <link href="../bootstrapSlider/css/bootstrap-slider.css" rel="stylesheet"/>
        
        <!-- Filestyle -->
        <script type="text/javascript" src="../js/filestyle/src/bootstrap-filestyle.min.js"></script>

       <!-- Font awesome icons -->
        <link rel="stylesheet" href="../js/fontAwesome/css/font-awesome.min.css">
        
        <!-- Custom CSS -->
        <link href="../css/dashboard.css?v=<?php echo time();?>" rel="stylesheet">
        <link href="../css/dashboardList.css?v=<?php echo time();?>" rel="stylesheet">
        <link href="../css/dashboardView.css?v=<?php echo time();?>" rel="stylesheet">
        <link href="../css/addWidgetWizard2.css?v=<?php echo time();?>" rel="stylesheet">
        <link href="../css/addDashboardTab.css?v=<?php echo time();?>" rel="stylesheet">
        <link href="../css/dashboard_configdash.css?v=<?php echo time();?>" rel="stylesheet">
   
        
    <!-- fine incluso da me-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/shepherd.js@8/dist/css/shepherd.min.css">
  <!--  <link rel="stylesheet" href="../css/shepherd.min.css"> -->
    <link href="../css/snapTour.css" rel="stylesheet">     
    </head>
    <body class="guiPageBody">
        <div class="container-fluid">
            <?php include "sessionExpiringPopup.php" ?>
            <div class="row">
                <?php include "../s4c-legacy-management/mainMenu.php" ?>
                <div class="col-xs-12 col-md-10" id="mainCnt">
                    <div class="row hidden-md hidden-lg">
                        <div id="mobHeaderClaimCnt" class="col-xs-12 hidden-md hidden-lg centerWithFlex">
                            <?php include "mobMainMenuClaim.php" ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-10 col-md-12 centerWithFlex"  id="headerTitleCnt"></div>
                        <div class="col-xs-2 hidden-md hidden-lg centerWithFlex" id="headerMenuCnt"><?php include "mobMainMenu.php" ?></div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12" id="iframePagemainContentCnt">
                            <iframe id="iframeApp"></iframe>
                            
                            <!--<div id="secureContent" style="width: 100%; height: 100%; background-color: yellow">
                                <?php
                                    if((strpos($_SERVER['REQUEST_URI'], 'https://www.km4city.org/webapp-new-bad') !== false)&&(strpos($_SERVER['REQUEST_URI'], 'operation=annotation') !== false)) 
                                    {
                                        //$page = file_get_contents("https://www.km4city.org/webapp-new-bad/?operation=annotation&username=" . $_SESSION['loggedUsername'] . "&coordinates=coordinates=43.773066;11.256932%2526&language=en");
                                        //echo $page;
                                    }
                                    
                                ?>
                                
                            </div>-->
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/shepherd.js@8/dist/js/shepherd.min.js"></script>
        <!-- <script src="../js/shepherd.min.js"></script> -->
        <script src="../js/snapTour.js"></script>
        
    </body>
</html>
<?php 
$curr_lang = selectLanguage($localizations); 
?>

<script type='text/javascript'>
    $(document).ready(function () 
    {  
        console.log("Entrato in iFrame");
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
        
       $('#iframePagemainContentCnt').height($('#mainMenuCnt').height() - $('#headerTitleCnt').height());
        
        $(window).resize(function(){
            $('#iframePagemainContentCnt').height($('#mainMenuCnt').height() - $('#headerTitleCnt').height());
        });

        $('#mainMenuCnt .mainMenuLink[id=<?= escapeForJS(sanitizeGetString('linkId')) ?>] div.mainMenuItemCnt').addClass("mainMenuItemCntActive");
        $('#mobMainMenuPortraitCnt .mainMenuLink[id=<?= escapeForJS(sanitizeGetString('linkId')) ?>] .mobMainMenuItemCnt').addClass("mainMenuItemCntActive");
        $('#mobMainMenuLandCnt .mainMenuLink[id=<?= escapeForJS(sanitizeGetString('linkId')) ?>] .mobMainMenuItemCnt').addClass("mainMenuItemCntActive");
        
        if($('div.mainMenuSubItemCnt').parents('a[id=<?= escapeForJS(sanitizeGetString('linkId')) ?>]').length > 0)
        {
            var fatherMenuId = $('div.mainMenuSubItemCnt').parents('a[id=<?= escapeForJS(sanitizeGetString('linkId')) ?>]').attr('data-fathermenuid');
            $("#" + fatherMenuId).attr('data-submenuVisible', 'true');
            $('#mainMenuCnt a.mainMenuSubItemLink[data-fatherMenuId=' + fatherMenuId + ']').show();
            $("#" + fatherMenuId).find('.submenuIndicator').removeClass('fa-caret-down');
            $("#" + fatherMenuId).find('.submenuIndicator').addClass('fa-caret-up');
            $('div.mainMenuSubItemCnt').parents('a[id=<?= escapeForJS(sanitizeGetString('linkId')) ?>]').find('div.mainMenuSubItemCnt').addClass("subMenuItemCntActive");
        }

        // NEW PENTEST: mettere escapeForJS per $_REQUEST['pageTitle'] ?
        var curr_lang = '<?php echo $curr_lang?>';
        var pageTitle = ('<?php echo translate_string(sanitizeGetString('pageTitle'), $curr_lang, $link);?>');
        $('#headerTitleCnt').html(decodeURI(pageTitle));
        //$('#headerTitleCnt').html(decodeURI("<?= sanitizeGetString('pageTitle') ?>"));
       
        if('<?= escapeForJS(sanitizeGetString('linkUrl'))?>' === 'myAnnotationsOnServicesAndData')
        {
            $('#iframeApp').attr('src', '../api/personalAnnotationsSecureLoad.php');
        }
        else
        {
            $('#iframeApp').attr('src', '<?= escapeForJS(sanitizeGetString('linkUrl'))?>');
        }

        $("#link_start_wizard2").click(function() {
            $("#addWidgetWizard2").modal("show");
        });
    }); //Fine document ready
    
    $(function() {
        const steps = JSON.parse('<?= serializeToJsonString($tourRepo->getTourSteps("preRegisterTour")) ?>');
        const session = JSON.parse('<?= serializeToJsonString($_SESSION) ?>');
        SnapTour.init(steps, {
            isPublic: session.isPublic,
          //  resetTimeout: 1000 * 60 * 60 * 12 // 12 hour as ms. if left blank the default is 24h
            resetTimeout: 1000 * 60 * 5
        });
    });
</script>
</body>

</html>