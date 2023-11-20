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

if(!isset($_SESSION)) {
    session_start();
}

if ((!$_SESSION['isPublic'] && isset($_SESSION['newLayout']) && $_SESSION['newLayout'] === true) || ($_SESSION['isPublic'] && $_COOKIE['layout'] == "new_layout")) {

    include('../TourRepository.php');
    
    checkSession('Public');
    
    $link = mysqli_connect($host, $username, $password);
    mysqli_select_db($link, $dbname);
    $tourRepo = new TourRepository($host, $username, $password, $dbname);
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
       
       <!-- Bootstrap editable tables -->
       <!--<link href="https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
       <script src="https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>-->
       
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
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/shepherd.js@8/dist/css/shepherd.min.css">
        <!-- <link rel="stylesheet" href="../css/shepherd.min.css"> -->
        <link href="../css/s4c-css/s4c-snapTour.css" rel="stylesheet">
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
                        <div id="headerTitleCnt">
                            <script type="text/javascript">
                                  <?php
                                  if(isset($_GET['pageTitle']))
                                  {
                                  ?>
                                    document.write("<?php echo escapeForJS($_GET['pageTitle']); ?>");
                                  <?php
                                  }
                                  ?>

                            </script>
                        </div>
                        <div class="user-menu-container">
                          <?php include "loginPanel.php" ?>
                        </div>
                        <div class="col-lg-2 hidden-md hidden-lg centerWithFlex" id="headerMenuCnt"><?php include "mobMainMenu.php" ?></div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12" id="mainContentCnt">
                            <div class="row mainContentRow" id="iotApplicationsIframeRow">
                                <div class="col-xs-12 mainContentCellCnt" id="iotApplicationsIframeCnt">
                                    <iframe id="iotApplicationsIframe"></iframe>
                                </div>
                            </div>    
                            
                            
                            <div class="row mainContentRow" id="dashboardsListTableRow">
                                <div class="col-xs-12 mainContentCellCnt" >
                                   <div class="filterListBar">
                                   <button type="button" class="collapsible"><span></span></button>
                                    <div class="content">
                                    <div id="dashboardsListMenu" class="row">
                                        <!--<div id="dashboardListsViewMode" class="hidden-xs col-sm-6 col-md-2 dashboardsListMenuItem">
                                            <div class="dashboardsListMenuItemContent centerWithFlex col-xs-12">
                                                <input id="dashboardListsViewModeInput" type="checkbox">
                                            </div>
                                        </div>-->
                                        <div id="dashboardListsCardsSort" class="dashboardsListMenuItem">
                                            <div class="dashboardsListMenuItemContent centerWithFlex col-xs-12">
                                                <div class="col-xs-6 centerWithFlex">
                                                    <div class="dashboardsListSortBtnCnt" data-toggle="tooltip" data-placement="bottom" title="Sort ascending">
                                                        <i class="fa fa-sort-alpha-asc dashboardsListSort"></i>
                                                    </div> 
                                                </div>
                                                <div class="col-xs-6 centerWithFlex">
                                                    <div class="dashboardsListSortBtnCnt" data-toggle="tooltip" data-placement="bottom" title="Sort descending">
                                                        <i class="fa fa-sort-alpha-desc dashboardsListSort"></i>
                                                    </div>    
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        if((@$_SESSION['loggedRole']) === 'RootAdmin')
                                        {
                                        ?>
                                        <div id="dashboardListsCardsOrgsSort" class="dashboardsListMenuItem">
                                            <div class="dashboardsListMenuItemContent centerWithFlex col-xs-12 col-md-6 col-md-2">
                                                <div class="col-xs-6 centerWithFlex">
                                                    <script type="text/javascript">
                                                        if(location.href.includes("AllOrgs") != false) {
                                                            document.write('<div id="microAppList" class="dashboardsListSortOrgsBtnCnt" data-toggle="tooltip" data-placement="bottom" title="All Organizations"></div>');
                                                        } else {
                                                            document.write('<div id="microAppList" class="dashboardsListSortOrgsBtnCnt" data-toggle="tooltip" data-placement="bottom" title="My Organizations"></div>');
                                                        }
                                                    </script>
                                                </div>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <div id="dashboardListsPages" class="dashboardsListMenuItem">
                                           <!--<div class="dashboardsListMenuItemTitle centerWithFlex col-xs-4">
                                                List<br>pages
                                            </div>-->
                                            <div class="dashboardsListMenuItemContent centerWithFlex col-xs-12">
                                                
                                            </div>
                                        </div>
                                        
                                        <div id="dashboardListsSearchFilter" class="dashboardsListMenuItem">
                                            <!--<div class="dashboardsListMenuItemTitle centerWithFlex col-xs-3">
                                                Search
                                            </div>-->
                                            <div class="dashboardsListMenuItemContent centerWithFlex col-xs-12">
                                                <div class="input-group">
                                                    <div class="input-group-btn">
                                                      <button type="button" id="searchDashboardBtn" class="btn"><i class="fa fa-search"></i></button>
                                                      <button type="button" id="resetSearchDashboardBtn" class="btn"><i class="fa fa-close"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                   </div>
                                </div>
                                                                        
                                    <table id="list_dashboard" class="table">
                                        <thead class="dashboardsTableHeader">
                                            <tr>
                                                <th data-dynatable-column="title_header"><?= _("Title")?></th>
                                                <th data-dynatable-column="user"><?= _("Creator")?></th>
                                                <th data-dynatable-column="creation_date"><?= _("Creation date")?></th>
                                                <th data-dynatable-column="last_edit_date"><?= _("Last edit date")?></th>
                                                <th data-dynatable-column="status_dashboard"><?= _("Status")?></th>
                                                <th>Edit</th>
                                                <th>View</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                    
                                    <div id="list_dashboard_cards" class="container-fluid">
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </body>
</html>
<script src="https://cdn.jsdelivr.net/npm/shepherd.js@8/dist/js/shepherd.min.js"></script>
<!-- <script src="../js/shepherd.min.js"></script> -->
<script src="../js/snapTour.js"></script>
<script type='text/javascript'>
    $(document).ready(function ()
    {
        console.log("Microapplication.");
        var dashboardsList = null;
        var sessionEndTime = "<?php echo @$_SESSION['sessionEndTime']; ?>";
        var orgFilter = "<?php echo @$_SESSION['loggedOrganization']; ?>";
        var orgLang = "<?php echo @$_SESSION['orgLang']; ?>";
        var param = "";
        if (location.href.includes("AllOrgs")) {
            param = "AllOrgs";
        }
        var loggedRole = "<?= (@$_SESSION['isPublic'] ? 'Public' : @$_SESSION['loggedRole']) ?>";
        $('#sessionExpiringPopup').css("top", parseInt($('body').height() - $('#sessionExpiringPopup').height()) + "px");
        $('#sessionExpiringPopup').css("left", parseInt($('body').width() - $('#sessionExpiringPopup').width()) + "px");

        if(location.href.includes("AllOrgs") != false) {
            //   documnet.write("<i class="fa fa-cubes dashboardsListSort" data-active="false" ></i>");
            var divList = document.getElementById('microAppList');
            var strToAppend = "<i class=\"fa fa-cubes dashboardsListSort\" data-active=\"false\" ></i>";
       //     divList.innerHtml(strToAppend);
            divList.insertAdjacentHTML('beforeend', strToAppend);
        } else if (loggedRole == "RootAdmin") {
            var divList = document.getElementById('microAppList');
            var strToAppend = "<i class=\"fa fa-cube dashboardsListSort\" data-active=\"false\" ></i>";
            //     divList.innerHtml(strToAppend);
            divList.insertAdjacentHTML('beforeend', strToAppend);
        }

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
            
        function myRowWriter(rowIndex, record, columns, cellWriter)
        {
            var statusBtn, cssClass = null;
            var title = record.title_header;

            if(rowIndex%2 !== 0)
            {
                cssClass = 'blueRow';
            }
            else
            {
                cssClass = 'whiteRow';
            }

            if(title.length > 75)
            {
               title = title.substr(0, 75) + " ...";
            }

            var user = record.user;
            if(user.length > 75)
            {
               user = user.substr(0, 75) + " ...";
            }

            if((record.status_dashboard === '0')||(record.status_dashboard === 0))
            {
                statusBtn = '<input type="checkbox" data-toggle="toggle" class="changeDashboardStatus">';
            }
            else
            {
                statusBtn = '<input type="checkbox" checked data-toggle="toggle" class="changeDashboardStatus">';
            }

            var newRow = '<tr data-dashTitle="' + record.title_header + '" data-uniqueid="' + record.Id + '" data-authorName="' + record.user + '"><td class="' + cssClass + '" style="font-weight: bold">' + title + '</td><td class="' + cssClass + '">' + user + '</td><td class="' + cssClass + '">' + record.creation_date + '</td><td class="' + cssClass + '">' + record.last_edit_date + '</td><td class="' + cssClass + '">' + statusBtn + '</td><td class="' + cssClass + '"><button type="button" class="editDashBtn"><?= _("edit")?></button></td><td class="' + cssClass + '"><button type="button" class="viewDashBtn"><?= _("view")?></button></td></tr>';

            return newRow;
        }
    
        function myCardsWriter(rowIndex, record, columns, cellWriter)
        {
            var title = record.sub_nature;

            if(title.length > 100)
            {
               title = title.substr(0, 100) + " ...";
            }

             var cardDiv = '<div data-uniqueid="' + record.id + '" data-title="' + title + '" data-url="' + record.parameters + '" data-icon="' + record.microAppExtServIcon + '" data-org="' + record.organizations + '" data-lat="' + record.latitude + '" data-lng="' + record.longitude + '" class="dashboardsListCardDiv col-xs-12 col-sm-6">' +
                               '<div class="dashboardsListCardInnerDiv">' +
                               '<div class="dashboardsListCardOverlayDiv"></div>' +
                                '<div class="dashboardsListCardOverlayTxt"><i class="fa-solid fa-eye"></i><?= _("View")?></div>' +
                                '<div class="dashboardsListCardImgDiv"></div>' +
                               '<div class="cardLinkBtn"><button class="cardButton" style="font-size:8px;float: right;"><?= _("New Tab")?></button></div>' +
                                  '<div class="dashboardsListCardTitleDiv"><span class="dashboardListCardTitleSpan">' + title + '</span>' +
                               //         '<button class="cardButton" style="font-size:8px;float: right;">New Tab</button>' +
                                  '</div>' +
                                  '<div class="dashboardsListCardClick2EditDiv">' +
                                  '</div>' +  
                               '</div>' +
                            '</div>';   

             return cardDiv;
        }
            
            //Nuova tabella
            $.ajax({
                url: "../controllers/getMicroApplications.php",
                data: {
                    orgFilter: orgFilter,
                    param: param,
                    role: loggedRole
                },
                type: "GET",
                async: true,
                dataType: 'json',
                success: function(data) 
                {
                    dashboardsList = data.applications;
                    //Ricordati di metterlo PRIMA dell'istanziamento della tabella
                    $('#list_dashboard_cards').bind('dynatable:afterProcess', function(e, dynatable){
                        $('#dashboardsListTableRow').css('padding-top', '0px');
                        $('#dashboardsListTableRow').css('padding-bottom', '0px');
                        
                        $('#dashboardListsViewModeInput').bootstrapToggle({
                            on: 'View as table',
                            off: 'View as cards',
                            onstyle: 'default',
                            offstyle: 'info',
                            size: 'normal'
                        });
                        
                        $('label.toggle-off').css("background-color", "rgba(0, 162, 211, 1)");
                        $('label.toggle-off').css("font-weight", "bold");
                        $('label.toggle-off').css("padding-left", "18px");
                        $('label.toggle-on').css("background-color", "rgba(255, 204, 0, 1)");
                        $('label.toggle-on').css("color", "rgba(255, 255, 255, 1)");
                        $('label.toggle-on').css("font-weight", "bold");
                        $('label.toggle-on').css("padding-right", "24px");
                        
                        /*$('#dashboardListsViewModeInput').change(function() {
                            if($(this).prop('checked'))
                            {
                                //Visione a tabella
                                $('#list_dashboard_cards').hide();
                                $('#list_dashboard').show();
                                $("#dynatable-pagination-links-list_dashboard_cards").hide();
                                $("#dynatable-query-search-list_dashboard_cards").hide();
                                $('#dashboardListsCardsSort').hide();
                                $('#dashboardListsPages').removeClass('col-md-3');
                                $('#dashboardListsPages').addClass('col-md-4');
                                $("#dashboardListsItemsPerPage").show();
                                $("#dynatable-pagination-links-list_dashboard").show();
                                $("#dynatable-query-search-list_dashboard").show();
                                
                                $('#searchDashboardBtn').off('click');
                                $('#searchDashboardBtn').click(function(){
                                    var dynatable = $('#list_dashboard').data('dynatable');
                                    dynatable.queries.run();
                                }); 

                                $('#resetSearchDashboardBtn').off('click');
                                $('#resetSearchDashboardBtn').click(function(){
                                    var dynatable = $('#list_dashboard').data('dynatable');
                                    $("#dynatable-query-search-list_dashboard").val("");
                                    dynatable.queries.runSearch("");
                                }); 
                            }
                            else
                            {
                                //Visione a cards
                                $('#list_dashboard').hide();
                                $('#list_dashboard_cards').show();
                                $("#dynatable-pagination-links-list_dashboard").hide();
                                $("#dynatable-query-search-list_dashboard").hide();
                                $("#dashboardListsItemsPerPage").hide();
                                $('#dashboardListsCardsSort').show();
                                $('#dashboardListsPages').removeClass('col-md-4');
                                $('#dashboardListsPages').addClass('col-md-3');
                                $("#dynatable-query-search-list_dashboard_cards").show();
                                $("#dynatable-pagination-links-list_dashboard_cards").show();
                                
                                $('#searchDashboardBtn').off('click');
                                $('#searchDashboardBtn').click(function(){
                                    var dynatable = $('#list_dashboard_cards').data('dynatable');
                                    dynatable.queries.run();
                                }); 

                                $('#resetSearchDashboardBtn').off('click');
                                $('#resetSearchDashboardBtn').click(function(){
                                    var dynatable = $('#list_dashboard_cards').data('dynatable');
                                    $("#dynatable-query-search-list_dashboard_cards").val("");
                                    dynatable.queries.runSearch("");
                                }); 
                            }
                        });*/


                        //Ricicliamolo come link CREATE NEW
                        $('#link_add_dashboard').off('click');
                        $('#link_add_dashboard').click(function(){
                            
                            
                        });
                        
                        $("#dynatable-pagination-links-list_dashboard_cards").appendTo("#dashboardListsPages div.dashboardsListMenuItemContent");
                        //$("#dynatable-pagination-links-list_dashboard_cards li").eq(0).remove();
                        $("#dynatable-pagination-links-list_dashboard_cards li").eq(0).remove();
                        //$("#dynatable-pagination-links-list_dashboard_cards li").eq($("#dynatable-pagination-links-list_dashboard_cards li").length - 1).remove();
                        $('#dashboardListsPages div.dashboardsListMenuItemContent').css("font-family", "Montserrat");
                        $('#dashboardListsPages div.dashboardsListMenuItemContent').css("font-weight", "bold");
                        $('#dashboardListsPages div.dashboardsListMenuItemContent').css("color", "white");
                        $('#dashboardListsPages div.dashboardsListMenuItemContent a').css("font-family", "Montserrat");
                        $('#dashboardListsPages div.dashboardsListMenuItemContent a').css("font-weight", "bold");
                        $('#dashboardListsPages div.dashboardsListMenuItemContent a').css("color", "white");
                        $("ul#dynatable-pagination-links-list_dashboard_cards").css("-webkit-padding-start", "0px");
                        $("ul#dynatable-pagination-links-list_dashboard_cards").css("-webkit-margin-before", "0px");
                        $("ul#dynatable-pagination-links-list_dashboard_cards").css("-webkit-margin-after", "0px");
                        $("ul#dynatable-pagination-links-list_dashboard_cards").css("padding", "0px");
                        
                        $("#dynatable-query-search-list_dashboard_cards").prependTo("#dashboardListsSearchFilter div.dashboardsListMenuItemContent div.input-group");
                        $('#dynatable-search-list_dashboard_cards').remove();
                        $("#dynatable-query-search-list_dashboard_cards").css("border", "none");
                        $("#dynatable-query-search-list_dashboard_cards").attr("placeholder", "Filter");
                        $("#dynatable-query-search-list_dashboard_cards").css("width", "100%");
                        $("#dynatable-query-search-list_dashboard_cards").addClass("form-control");
                        
                        $('#list_dashboard_cards div.dashboardsListCardDiv').each(function(i){
                            //$(this).find('div.dashboardsListCardImgDiv').css("background-image", "url(../img/microApplications/" + $(this).attr('data-uniqueid') + "/" + $(this).attr('data-icon') + ")");
                        //    $(this).find('div.dashboardsListCardDiv').css("padding", "10px 25px 35px 25px");

                            $(this).find('div.dashboardsListCardImgDiv').css("background-image", "url(../img/microApplications/" + $(this).attr('data-icon') + ")");
                            //$(this).find('div.dashboardsListCardImgDiv').css("background-size", "100% auto");
                            //$(this).find('div.dashboardsListCardImgDiv').css("background-repeat", "no-repeat");
                            //$(this).find('div.dashboardsListCardImgDiv').css("background-position", "center top");
                            $(this).find('div.dashboardsListCardInnerDiv').css("width", "100%");
                            $(this).find('div.dashboardsListCardInnerDiv').css("height", $(this).height() + "px");
                            $(this).find('div.dashboardsListCardOverlayDiv').css("height", $(this).find('div.dashboardsListCardImgDiv').height() + "px");
                            $(this).find('div.dashboardsListCardOverlayTxt').css("height", $(this).find('div.dashboardsListCardImgDiv').height() + "px");
                            
                            $(this).find('.dashboardsListCardImgDiv').off('mouseenter');
                            $(this).find('.dashboardsListCardImgDiv').off('mouseleave');
                            
                            $(this).find('.dashboardsListCardOverlayTxt').hover(function(){
                                $(this).parents('.dashboardsListCardDiv').find('div.dashboardsListCardOverlayTxt').css("opacity", "1");
                                $(this).parents('.dashboardsListCardDiv').find('div.dashboardsListCardOverlayDiv').css("opacity", "0.8");
                                $(this).css("cursor", "pointer");
                            }, function(){
                                $(this).parents('.dashboardsListCardDiv').find('div.dashboardsListCardOverlayTxt').css("opacity", "0");
                                $(this).parents('.dashboardsListCardDiv').find('div.dashboardsListCardOverlayDiv').css("opacity", "0.05");
                                $(this).css("cursor", "normal");
                            });
                            
                            $(this).find('.dashboardsListCardOverlayTxt').off('click');
                            $(this).find('.dashboardsListCardOverlayTxt').click(function() 
                            {
                                var url = $(this).parents('div.dashboardsListCardDiv').attr('data-url');
                                var orgFilterSpec = $(this).parents('div.dashboardsListCardDiv').attr('data-org');
                                if (orgFilterSpec.includes("[") && orgFilterSpec.includes("]")) {
                                    if (orgFilterSpec.includes(orgFilter)) {
                                        orgFilterSpec = orgFilter;
                                    }
                                }
                            //    var microAppHeaderTitleCnt = $('#headerTitleCnt')[0].firstChild.data + ": " + $(this)[0].parentNode.children[0].firstChild.innerText;
                                var microAppHeaderTitleCnt = $('#headerTitleCnt')[0].innerText + ": " + $(this)[0].parentNode.children[1].firstChild.innerText;
                                $('#headerTitleCnt').html(microAppHeaderTitleCnt);
                                $('#dashboardsListTableRow').hide();
                                $('#iotApplicationsIframeRow').show();
                                $('#mainContentCnt').css('padding', '0px 0px 0px 0px');
                                $('#iotApplicationsIframeCnt').css('padding-left', '0px');
                                $('#iotApplicationsIframeCnt').css('padding-right', '0px');
                                var defaultLat = "43.7712";
                                var defaultLng = "11.256";
                                var lat = defaultLat;
                                var lng = defaultLng;

                                if ($(this).parents('div.dashboardsListCardDiv').attr('data-lat')) {
                                    if ($(this).parents('div.dashboardsListCardDiv').attr('data-lat') != "null" && $(this).parents('div.dashboardsListCardDiv').attr('data-lat') != '') {
                                        microAppLat = $(this).parents('div.dashboardsListCardDiv').attr('data-lat');
                                }
                                }
                                if ($(this).parents('div.dashboardsListCardDiv').attr('data-lng') != null) {
                                    if ($(this).parents('div.dashboardsListCardDiv').attr('data-lng') != "null" && $(this).parents('div.dashboardsListCardDiv').attr('data-lng') != '') {
                                        microAppLng = $(this).parents('div.dashboardsListCardDiv').attr('data-lng');
                                    }
                                }

                                $.ajax({
                                    url: "../controllers/getOrganizationParameters.php",
                                    data: {
                                        action: "getSpecificOrgParameters",
                                        param: orgFilterSpec
                                    },
                                    type: "GET",
                                    async: true,
                                    dataType: 'json',
                                    success: function (data) {
                                        var orgLatLng = data.orgGpsCentreLatLng;
                                        if (microAppLat === null) {
                                        microAppLat = orgLatLng.split(",")[0].trim();
                                        }
                                        if (microAppLng === null) {
                                        microAppLng = orgLatLng.split(",")[1].trim();
                                        }
                                        if (orgLang === null || orgLang === undefined) {
                                            orgLang = "";
                                        }
                                    //    $('#iotApplicationsIframe').attr('src', url + '&coordinates='+microAppLat+';'+microAppLng+'&lang='+orgLang+'&maxDistance=0.3&maxResults=150');
                                        $('#iotApplicationsIframe').attr('src', url + '&coordinates='+microAppLat+';'+microAppLng+'&lang='+orgLang);
                                        microAppLat = null;
                                        microAppLng = null;
                                    },
                                    error: function (errorData) {
                                        console.log("Errore in reperimento parametri Org specifica: ");
                                        console.log(JSON.stringify(errorData));
                                    }
                                });

                             //   $('#iotApplicationsIframe').attr('src', url + '&coordinates='+microAppLat+';'+microAppLng+'&lang=ita&maxDistance=0.3&maxResults=150');
                            });
                        });
                        
                        $('#dashboardListsViewMode').hide();
                        
                        $('#searchDashboardBtn').off('click');
                        $('#searchDashboardBtn').click(function(){
                            var dynatable = $('#list_dashboard_cards').data('dynatable');
                            dynatable.queries.run();
                        }); 
                        
                        $('#resetSearchDashboardBtn').off('click');
                        $('#resetSearchDashboardBtn').click(function(){
                            var dynatable = $('#list_dashboard_cards').data('dynatable');
                            $("#dynatable-query-search-list_dashboard_cards").val("");
                            dynatable.queries.runSearch("");
                        });

                        $(this).find(".cardLinkBtn").click(function(){
                            var url = $(this).parents('div.dashboardsListCardDiv').attr('data-url');
                            var defaultLat = "43.7712";
                            var defaultLng = "11.256";
                            var lat = defaultLat;
                            var lng = defaultLng;

                            if ($(this).parents('div.dashboardsListCardDiv').attr('data-lat')) {
                                if ($(this).parents('div.dashboardsListCardDiv').attr('data-lat') != "null" && $(this).parents('div.dashboardsListCardDiv').attr('data-lat') != '') {
                                    microAppLat = $(this).parents('div.dashboardsListCardDiv').attr('data-lat');
                            }
                            }
                            if ($(this).parents('div.dashboardsListCardDiv').attr('data-lng') != null) {
                                if ($(this).parents('div.dashboardsListCardDiv').attr('data-lng') != "null" && $(this).parents('div.dashboardsListCardDiv').attr('data-lng') != '') {
                                    microAppLng = $(this).parents('div.dashboardsListCardDiv').attr('data-lng');
                                }
                            }

                            var orgFilterSpec = $(this).parents('div.dashboardsListCardDiv').attr('data-org');
                            if (orgFilterSpec.includes("[") && orgFilterSpec.includes("]")) {
                                if (orgFilterSpec.includes(orgFilter)) {
                                    orgFilterSpec = orgFilter;
                                }
                            }

                            $.ajax({
                                url: "../controllers/getOrganizationParameters.php",
                                data: {
                                    action: "getSpecificOrgParameters",
                                    param: orgFilterSpec
                                },
                                type: "GET",
                                async: true,
                                dataType: 'json',
                                success: function (data) {
                                    var orgLatLng = data.orgGpsCentreLatLng;
                                    if (microAppLat === null) {
                                    microAppLat = orgLatLng.split(",")[0].trim();
                                    }
                                    if (microAppLng === null) {
                                    microAppLng = orgLatLng.split(",")[1].trim();
                                    }
                                    if (orgLang === null || orgLang === undefined) {
                                        orgLang = "";
                                    }
                                //    window.open(url + '&coordinates='+microAppLat+';'+microAppLng+'&lang='+orgLang+'&maxDistance=0.3&maxResults=150');
                                    window.open(url + '&coordinates='+microAppLat+';'+microAppLng+'&lang='+orgLang);
                                    microAppLat = null;
                                    microAppLng = null;
                                },
                                error: function (errorData) {
                                    console.log("Errore in reperimento parametri Org specifica: ");
                                    console.log(JSON.stringify(errorData));
                                }
                            });
                        });
                        
                      });

                    
                    $('#list_dashboard_cards').dynatable({
                        table: {
                            bodyRowSelector: 'div'
                          },
                        dataset: {
                          records: data.applications,
                          perPageDefault: 10,
                          perPageOptions: [5, 10, 15]
                        },
                        writers: {
                            _rowWriter: myCardsWriter
                        },
                        inputs: {
                            paginationLinkPlacement: 'before'
                        },
                        features: {
                            recordCount: false,
                            perPageSelect: false,
                            search: true
                        }
                      });
                      
                      var dynatable = $('#list_dashboard_cards').data('dynatable');
                      dynatable.sorts.clear();
                      dynatable.sorts.add('sub_nature', 1); // 1=ASCENDING, -1=DESCENDING
                      dynatable.process();
                      
                      $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(0).css('background-color', 'rgba(255, 204, 0, 1)');
                      $('#dashboardListsCardsSort i.dashboardsListSort').eq(0).click(function(){
                          var dynatable = $('#list_dashboard_cards').data('dynatable');
                          dynatable.sorts.clear();
                          dynatable.sorts.add('sub_nature', 1); // 1=ASCENDING, -1=DESCENDING
                          dynatable.process();
                          $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(1).css('background-color', 'rgba(0, 162, 211, 1)');
                          $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(0).css('background-color', 'rgba(255, 204, 0, 1)');
                      });
                      
                      $('#dashboardListsCardsSort i.dashboardsListSort').eq(1).click(function(){
                          var dynatable = $('#list_dashboard_cards').data('dynatable');
                          dynatable.sorts.clear();
                          dynatable.sorts.add('sub_nature', -1); // 1=ASCENDING, -1=DESCENDING
                          dynatable.process();
                          $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(0).css('background-color', 'rgba(0, 162, 211, 1)');
                          $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(1).css('background-color', 'rgba(255, 204, 0, 1)');
                      });

                      // Toggle-Button for Filtering by Organization

                      if (!location.href.includes("AllOrgs")) {
                          $('#dashboardListsCardsOrgsSort div.dashboardsListSortOrgsBtnCnt').eq(0).attr("data-active", true);
                          $('#dashboardListsCardsOrgsSort div.dashboardsListSortOrgsBtnCnt').eq(0).css('background-color', 'rgba(255, 204, 0, 1)');
                      } else {
                          $('#dashboardListsCardsOrgsSort div.dashboardsListSortOrgsBtnCnt').eq(0).attr("data-active", false);
                          $('#dashboardListsCardsOrgsSort div.dashboardsListSortOrgsBtnCnt').eq(0).css('background-color', 'rgba(0, 162, 211, 1)');
                      }

                      $('#dashboardListsCardsOrgsSort i.dashboardsListSort').eq(0).click(function(){
                            if($('#dashboardListsCardsOrgsSort div.dashboardsListSortOrgsBtnCnt').eq(0).attr("data-active") === "true")
                            {
                                $('#dashboardListsCardsOrgsSort div.dashboardsListSortOrgsBtnCnt').eq(0).attr("data-active", "false");
                                location.href = "../management/microApplications.php?linkId=microApplicationsList&fromSubmenu=false&sorts[title_header]=1&param=AllOrgs&pageTitle=" + $('#headerTitleCnt')[0].innerText;
                            }
                            else
                            {
                                $('#dashboardListsCardsOrgsSort div.dashboardsListSortOrgsBtnCnt').eq(0).attr("data-active", "true");
                                location.href = "../management/microApplications.php?linkId=microApplicationsList&fromSubmenu=false&sorts[title_header]=1&pageTitle=" + $('#headerTitleCnt')[0].innerText;
                            }
                      });


                    /*$('#list_dashboard').bind('dynatable:afterProcess', function(e, dynatable){
                        $('span.dynatable-per-page-label').remove();
                        
                        //$('#dynatable-per-page-list_dashboard').parents('span.dynatable-per-page').appendTo("#dashboardListsItemsPerPage div.dashboardsListMenuItemContent");
                        //$('#dynatable-per-page-list_dashboard').addClass('form-control');
                        
                        $("#dynatable-pagination-links-list_dashboard").appendTo("#dashboardListsPages div.dashboardsListMenuItemContent");
                        $("#dynatable-pagination-links-list_dashboard li").eq(0).remove();
                        //$("#dynatable-pagination-links-list_dashboard li").eq(0).remove();
                        //$("#dynatable-pagination-links-list_dashboard li").eq($("#dynatable-pagination-links-list_dashboard li").length - 1).remove();
                        $('#dashboardListsPages div.dashboardsListMenuItemContent').css("font-family", "Montserrat");
                        $('#dashboardListsPages div.dashboardsListMenuItemContent').css("font-weight", "bold");
                        $('#dashboardListsPages div.dashboardsListMenuItemContent').css("color", "white");
                        $('#dashboardListsPages div.dashboardsListMenuItemContent a').css("font-family", "Montserrat");
                        $('#dashboardListsPages div.dashboardsListMenuItemContent a').css("font-weight", "bold");
                        $('#dashboardListsPages div.dashboardsListMenuItemContent a').css("color", "white");
                        $("ul#dynatable-pagination-links-list_dashboard").css("-webkit-padding-start", "0px");
                        $("ul#dynatable-pagination-links-list_dashboard").css("-webkit-margin-before", "0px");
                        $("ul#dynatable-pagination-links-list_dashboard").css("-webkit-margin-after", "0px");
                        $("ul#dynatable-pagination-links-list_dashboard").css("padding", "0px");
                        
                        $("#dynatable-query-search-list_dashboard").prependTo("#dashboardListsSearchFilter div.dashboardsListMenuItemContent div.input-group");
                        $('#dynatable-search-list_dashboard').remove();
                        $("#dynatable-query-search-list_dashboard").css("border", "none");
                        $("#dynatable-query-search-list_dashboard").attr("placeholder", "Filter by dashboard title, author...");
                        $("#dynatable-query-search-list_dashboard").css("width", "100%");
                        $("#dynatable-query-search-list_dashboard").addClass("form-control");
                        
                        $('#list_dashboard input.changeDashboardStatus').bootstrapToggle({
                            on: "On",
                            off: "Off",
                            onstyle: "primary",
                            offstyle: "default",
                            size: "mini"
                        });
                        
                        $('#list_dashboard tbody input.changeDashboardStatus').off('change');
                        $('#list_dashboard tbody input.changeDashboardStatus').change(function() {
                            if($(this).prop('checked') === false)
                            {
                                var newStatus = 0;
                            }
                            else
                            {
                                var newStatus = 1;
                            }

                            $.ajax({
                                url: "process-form.php",
                                data: {
                                    modify_status_dashboard: true,
                                    dashboardId: $(this).parents('tr').attr('data-uniqueid'),
                                    newStatus: newStatus
                                },
                                type: "POST",
                                async: true,
                                success: function(data)
                                {
                                    if(data !== "Ok")
                                    {
                                        console.log("Error updating dashboard status");
                                        console.log(data);
                                        alert("Error updating dashboard status");
                                        location.reload();
                                    }
                                    else
                                    {
                                        if($('#dashboardTotActiveCnt .pageSingleDataCnt').html() !== "-")
                                        {
                                            if(newStatus === 0)
                                            {
                                                $('#dashboardTotActiveCnt .pageSingleDataCnt').html(parseInt($('#dashboardTotActiveCnt .pageSingleDataCnt').html()) - 1);
                                            }
                                            else
                                            {
                                                $('#dashboardTotActiveCnt .pageSingleDataCnt').html(parseInt($('#dashboardTotActiveCnt .pageSingleDataCnt').html()) + 1);
                                            }
                                        }
                                    }
                                },
                                error: function(errorData)
                                {
                                    console.log("Error updating dashboard status");
                                    console.log(errorData);
                                    alert("Error updating dashboard status");
                                    location.reload();
                                }
                            });
                        });
                        
                        $('#list_dashboard button.editDashBtn').off('click');
                        $('#list_dashboard button.editDashBtn').click(function() 
                        {
                            var dashboardId = $(this).parents('tr').attr('data-uniqueid');
                            var dashboardTitle = $(this).parents('tr').attr('data-dashTitle');
                            var dashboardAuthorName = $(this).parents('tr').attr('data-authorName');
                            
                            window.open("../management/dashboard_configdash.php?dashboardId=" + dashboardId + "&dashboardAuthorName=" + dashboardAuthorName + "&dashboardEditorName=" + encodeURI("<?= $_SESSION['loggedUsername']?>" + "&dashboardTitle=" + encodeURI(dashboardTitle)));
                        });
                        
                        $('#list_dashboard button.viewDashBtn').off('click');
                        $('#list_dashboard button.viewDashBtn').click(function () 
                        {
                            var dashboardId = $(this).parents('tr').attr("data-uniqueid");
                            window.open("../view/index.php?iddasboard=" + btoa(dashboardId));
                        });
                    });
                    
                    
                      $('#list_dashboard').dynatable({
                        dataset: {
                          records: data,
                          perPageDefault: 20,
                          perPageOptions: [5, 10, 20, 30, 40]
                        },
                        writers: {
                            _rowWriter: myRowWriter
                        },
                        features: {
                            recordCount: false,
                            perPageSelect: false
                        },
                        inputs: {
                            perPagePlacement: 'after'
                        }
                      });
                      $("#dynatable-pagination-links-list_dashboard").hide();
                      $("#dynatable-query-search-list_dashboard").hide();*/
                      
                },
                error: function(errorData)
                {
                    
                }
            });
    });
    
    $(function() {
        const steps = JSON.parse('<?= serializeToJsonString($tourRepo->getTourSteps("preRegisterTour")) ?>');
        const session = JSON.parse('<?= serializeToJsonString($_SESSION) ?>');
        SnapTour.init(steps, {
            isPublic: session.isPublic,
            resetTimeout: 1000 * 60 * 60 * 24 // 24 hour as ms. if left blank the default is 24h
            //resetTimeout: 1000 * 60 * 5
        });
    });
</script>
<script>
var coll = document.getElementsByClassName("collapsible");
var i;

for (i = 0; i < coll.length; i++) {
  coll[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var content = this.nextElementSibling;
    if (content.style.display === "block") {
      content.style.display = "none";
    } else {
      content.style.display = "block";
    }
  });
}
</script>

<?php } else {
    include('../s4c-legacy-management/microApplications.php');
}
?>