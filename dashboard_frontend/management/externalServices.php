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


if(!isset($_SESSION)) {
    session_start();
}

if ((!$_SESSION['isPublic'] && isset($_SESSION['newLayout']) && $_SESSION['newLayout'] === true) || ($_SESSION['isPublic'] && $_COOKIE['layout'] == "new_layout")) {

    include('../config.php');
    
    $link = mysqli_connect($host, $username, $password);
    mysqli_select_db($link, $dbname);
    
    checkSession('Public');
?>

<!DOCTYPE html>
<html class="dark">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title><?php include "mobMainMenuClaim.php" ?></title>
        
        <script type="text/javascript">
           const setTheme = (theme) => {
           document.documentElement.className = theme;
           localStorage.setItem('theme', theme);
           }
           const getTheme = () => {
           const theme = localStorage.getItem('theme');
           theme && setTheme(theme);
           }
           getTheme();
        </script>

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

       <!-- Font awesome icons -->
        <link rel="stylesheet" href="../css/s4c-css/fontawesome-free-6.2.0-web/css/all.min.css">
        <link href="https://fonts.googleapis.com/css?family=Cabin:400,500,600,700|Catamaran|Varela+Round" rel="stylesheet"> 
         <!-- Custom CSS -->
         <link href="../css/s4c-css/s4c-dashboard.css?v=<?php echo time();?>" rel="stylesheet">
         <link href="../css/s4c-css/s4c-dashboardList.css?v=<?php echo time();?>" rel="stylesheet">
         <link href="../css/s4c-css/s4c-dashboardView.css?v=<?php echo time();?>" rel="stylesheet">
         <link href="../css/s4c-css/s4c-addWidgetWizard2.css?v=<?php echo time();?>" rel="stylesheet">
         <link href="../css/s4c-css/s4c-addDashboardTab.css?v=<?php echo time();?>" rel="stylesheet">
         <link href="../css/s4c-css/s4c-dashboard_configdash.css?v=<?php echo time();?>" rel="stylesheet">
         <link href="../css/s4c-css/s4c-iotApplications.css?v=<?php echo time();?>" rel="stylesheet">
        
        <!-- Custom scripts -->
        <script type="text/javascript" src="../js/dashboard_mng.js"></script>
        
        <!--<link href="https://fonts.googleapis.com/css?family=Cabin:400,500,600,700|Catamaran|Varela+Round" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">-->
    </head>
    <body class="guiPageBody">
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
                        <div id="headerTitleCnt">
                            <script type="text/javascript">
                                <?php
                                if(isset($_GET['pageTitle']))
                                {?>
                                    document.write("<?php echo escapeForJS($_GET['pageTitle']); ?>");
                                <?php
                                }
                                ?>
                            </script>
                        </div>
                        <div class="user-menu-container">
                          <?php include "loginPanel.php" ?>
                        </div>
                        <div class="col-xs-2 hidden-md hidden-lg centerWithFlex" id="headerMenuCnt"><?php include "mobMainMenu.php" ?></div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12" id="mainContentCnt">
                            <div class="row mainContentRow" id="iotApplicationsIframeRow">
                                <div class="col-xs-12 mainContentCellCnt" id="iotApplicationsIframeCnt">
                                    <iframe id="iotApplicationsIframe" allow="geolocation"></iframe>
                                </div>
                            </div>    
                            
                            
                            <div class="row mainContentRow" id="dashboardsListTableRow">
                                <div class="col-xs-12 mainContentCellCnt" >
                                    <div id="dashboardsListMenu" class="row">
                                        <!--<div id="dashboardListsViewMode" class="hidden-xs col-sm-6 col-md-2 dashboardsListMenuItem">
                                            <div class="dashboardsListMenuItemContent centerWithFlex col-xs-12">
                                                <input id="dashboardListsViewModeInput" type="checkbox">
                                            </div>
                                        </div>-->
                                        <div id="dashboardListsCardsSort" class="col-xs-12 col-sm-6 col-md-1 col-md-offset-2 dashboardsListMenuItem">
                                            <div class="dashboardsListMenuItemContent centerWithFlex col-xs-12">
                                                <div class="col-xs-6 centerWithFlex">
                                                    <div class="dashboardsListSortBtnCnt">
                                                        <i class="fa fa-sort-alpha-asc dashboardsListSort"></i>
                                                    </div> 
                                                </div>
                                                <div class="col-xs-6 centerWithFlex">
                                                    <div class="dashboardsListSortBtnCnt">
                                                        <i class="fa fa-sort-alpha-desc dashboardsListSort"></i>
                                                    </div>    
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        if((@$_SESSION['loggedRole']) === 'RootAdmin')
                                        {
                                        ?>
                                        <div id="dashboardListsCardsOrgsSort" class="col-xs-6 col-sm-4 col-md-2 dashboardsListMenuItem">
                                            <div class="dashboardsListMenuItemContent centerWithFlex col-xs-12 col-md-6">
                                                <div class="col-xs-6 centerWithFlex">
                                                    <script type="text/javascript">
                                                        if(location.href.includes("AllOrgs") != false) {
                                                            document.write('<div id="extServiceList" class="dashboardsListSortOrgsBtnCnt" data-toggle="tooltip" data-placement="bottom" title="All Organizations"></div>');
                                                        } else {
                                                            document.write('<div id="extServiceList" class="dashboardsListSortOrgsBtnCnt" data-toggle="tooltip" data-placement="bottom" title="My Organizations"></div>');
                                                        }
                                                    </script>
                                                </div>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <div id="dashboardListsPages" class="col-xs-12 col-sm-6 col-md-3 dashboardsListMenuItem">
                                           <!--<div class="dashboardsListMenuItemTitle centerWithFlex col-xs-4">
                                                List<br>pages
                                            </div>-->
                                            <div class="dashboardsListMenuItemContent centerWithFlex col-xs-12">
                                                
                                            </div>
                                        </div>
                                        
                                        <div id="dashboardListsSearchFilter" class="col-xs-12 col-sm-6 col-md-4 dashboardsListMenuItem">
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
                                    
                                    <table id="list_dashboard" class="table">
                                        <thead class="dashboardsTableHeader">
                                            <tr>
                                                <th data-dynatable-column="title_header"><?= _("Title")?></th>
                                                <th data-dynatable-column="user"><?= _("Creator")?></th>
                                                <th data-dynatable-column="creation_date"><?= _("Creation date")?></th>
                                                <th data-dynatable-column="last_edit_date"><?= _("Last edit date")?></th>
                                                <th data-dynatable-column="status_dashboard"><?= _("Status")?></th>
                                                <th><?= _("Edit")?></th>
                                                <th><?= _("View")?></th>
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

<script type='text/javascript'>
    $(document).ready(function () 
    {
        var dashboardsList = null;
        var orgFilter = "<?php echo @$_SESSION['loggedOrganization']; ?>";
        var param = "";
        if (location.href.includes("AllOrgs")) {
            param = "AllOrgs";
        }
        console.log("External Services.")
        var loggedRole = "<?php echo @$_SESSION['loggedRole']; ?>";
        var sessionEndTime = "<?php echo @$_SESSION['sessionEndTime']; ?>";
        $('#sessionExpiringPopup').css("top", parseInt($('body').height() - $('#sessionExpiringPopup').height()) + "px");
        $('#sessionExpiringPopup').css("left", parseInt($('body').width() - $('#sessionExpiringPopup').width()) + "px");

        if(location.href.includes("AllOrgs") != false) {
            //   documnet.write("<i class="fa fa-cubes dashboardsListSort" data-active="false" ></i>");
            var divList = document.getElementById('extServiceList');
            var strToAppend = "<i class=\"fa fa-cubes dashboardsListSort\" data-active=\"false\" ></i>";
            //     divList.innerHtml(strToAppend);
            divList.insertAdjacentHTML('beforeend', strToAppend);
        } else  if (loggedRole == "RootAdmin") {
            var divList = document.getElementById('extServiceList');
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
        
        var loggedRole = "<?= ($_SESSION['isPublic'] ? 'Public' : $_SESSION['loggedRole']) ?>";
        var loggedType = "<?= @$_SESSION['loggedType'] ?: '' ?>";
        var usr = "<?= @$_SESSION['loggedUsername'] ?: '' ?>";
        var tableFirstLoad = true;
            
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

            var newRow = '<tr data-dashTitle="' + record.title_header + '" data-uniqueid="' + record.Id + '" data-authorName="' + record.user + '"><td class="' + cssClass + '" style="font-weight: bold">' + title + '</td><td class="' + cssClass + '">' + user + '</td><td class="' + cssClass + '">' + record.creation_date + '</td><td class="' + cssClass + '">' + record.last_edit_date + '</td><td class="' + cssClass + '">' + statusBtn + '</td><td class="' + cssClass + '"><button type="button" class="editDashBtn"><?=_("edit")?></button></td><td class="' + cssClass + '"><button type="button" class="viewDashBtn"><?=_("view")?></button></td></tr>';

            return newRow;
        }
    
        function myCardsWriter(rowIndex, record, columns, cellWriter)
        {
            var title = record.sub_nature;

            if(title.length > 100)
            {
               title = title.substr(0, 100) + " ...";
            }

             var cardDiv = '<div data-uniqueid="' + record.id + '" data-title="' + title + '" data-url="' + record.parameters + '" data-icon="' + record.microAppExtServIcon + '" class="dashboardsListCardDiv col-xs-12 col-sm-6">' + 
                               '<div class="dashboardsListCardInnerDiv">' +
                                 '<div class="dashboardsListCardOverlayDiv"></div>' +
                                  '<div class="dashboardsListCardOverlayTxt"><i class="fa-solid fa-eye"></i><?=_("View")?></div>' +
                                  '<div class="dashboardsListCardImgDiv"></div>' + 
                                  '<div class="cardLinkBtn"><button class="cardButton" style="font-size:8px;float: right;"><?=_("New Tab")?></button></div>' +
                               //   '<div id="cardLinkBtn" style="font-size:8px;float: right;">New Tab</div>' +
                                  '<div class="dashboardsListCardTitleDiv"><span class="dashboardListCardTitleSpan">' + title + '</span><span class="dashboardListCardTypeSpan" data-hasIotModal="true">' + record.nature + '</span></div>' +
                                  '<div class="dashboardsListCardClick2EditDiv">' + 
                                      //'<button type="button" class="editDashBtnCard">Edit</button>' + 
                                  '</div>' +  
                               '</div>' +
                            '</div>';   

             return cardDiv;
        }
            
            //Nuova tabella
            $.ajax({
                url: "../controllers/getExternalServices.php",
                data: {
                    orgFilter: "<?= @$_SESSION['loggedOrganization'] ?>",
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
                            //$(this).find('div.dashboardsListCardImgDiv').css("background-image", "url(../img/externalServices/" + $(this).attr('data-uniqueid') + "/" + $(this).attr('data-icon') + ")");
                            $(this).find('div.dashboardsListCardImgDiv').css("background-image", "url(../img/externalServices/" + $(this).attr('data-icon') + ")");
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
                                
                                $('#dashboardsListTableRow').hide();
                                $('#iotApplicationsIframeRow').show();
                            //    var extContentHeaderTitleCnt = $('#headerTitleCnt')[0].firstChild.data + ": " + $(this)[0].parentNode.children[0].firstChild.innerText;
                                var extContentHeaderTitleCnt = $('#headerTitleCnt')[0].innerText + ": " + $(this)[0].parentNode.children[1].firstChild.innerText;
                                $('#headerTitleCnt').html(extContentHeaderTitleCnt);
                                $('#mainContentCnt').css('padding', '0px 0px 0px 0px');
                                $('#iotApplicationsIframeCnt').css('padding-left', '0px');
                                $('#iotApplicationsIframeCnt').css('padding-right', '0px');
                                $('#iotApplicationsIframe').attr('src', url);
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
                            window.open(url);
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
                    if (loggedRole == "RootAdmin") {
                        if (!location.href.includes("AllOrgs")) {
                            $('#dashboardListsCardsOrgsSort div.dashboardsListSortOrgsBtnCnt').eq(0).attr("data-active", true);
                            $('#dashboardListsCardsOrgsSort div.dashboardsListSortOrgsBtnCnt').eq(0).css('background-color', 'rgba(255, 204, 0, 1)');
                        } else {
                            $('#dashboardListsCardsOrgsSort div.dashboardsListSortOrgsBtnCnt').eq(0).attr("data-active", false);
                            $('#dashboardListsCardsOrgsSort div.dashboardsListSortOrgsBtnCnt').eq(0).css('background-color', 'rgba(0, 162, 211, 1)');
                        }
                    }

                    $('#dashboardListsCardsOrgsSort i.dashboardsListSort').eq(0).click(function(){

                        if($('#dashboardListsCardsOrgsSort div.dashboardsListSortOrgsBtnCnt').eq(0).attr("data-active") === "true")
                        {
                            $('#dashboardListsCardsOrgsSort div.dashboardsListSortOrgsBtnCnt').eq(0).attr("data-active", "false");
                            location.href = "../management/externalServices.php?linkId=externalServicesList&fromSubmenu=false&sorts[title_header]=1&param=AllOrgs&pageTitle=" + $('#headerTitleCnt')[0].innerText;
                        }
                        else
                        {
                            $('#dashboardListsCardsOrgsSort div.dashboardsListSortOrgsBtnCnt').eq(0).attr("data-active", "true");
                            location.href = "../management/externalServices.php?linkId=externalServicesList&fromSubmenu=false&sorts[title_header]=1&pageTitle=" + $('#headerTitleCnt')[0].innerText;
                        }
                    });

                },
                error: function(errorData)
                {
                    
                }
            });
    });
</script>

<?php } else {
    include('../s4c-legacy-management/externalServices.php');
}
?>