<div id="mainMenuCnt">
        <div id="headerClaimCnt">
            <?php
                include 'config.php';
                include_once '../locale.php';
                include_once '../translation.php';
                
                error_reporting(E_ERROR);
                date_default_timezone_set('Europe/Rome');
                
                $link = mysqli_connect($host, $username, $password);
                mysqli_select_db($link, $dbname);
                
                $domainId = null;
                
                $currDom = $_SERVER['HTTP_HOST'];
				//echo ($currDom);

                $domQ = "SELECT * FROM Dashboard.Domains WHERE domains LIKE '%$currDom%'";
                $r = mysqli_query($link, $domQ);

                 if($r)
                 {
                     if(mysqli_num_rows($r) > 0)
                     {
                         $row = mysqli_fetch_assoc($r);
                         $domainId = $row['id'];
                         include "logoS4cSVG.php";
                     }
                     else
                     {
                          echo 'Claim';
                     }
                 }
                 else
                 {
                     echo 'Claim';
                 }
                
              // $curr_lang ="";
               if ($localizationEnabled){
                 // if(strpos($localizationsRoles, "Public")){
                        if (isset($_REQUEST['lang'])){
                           $curr_lang =$_REQUEST['lang']; 
                        }
                  }
               
             $curr_lang = selectLanguage($localizations);
            // $curr_lang =$lang;
             //echo('$curr_lang:  '.$curr_lang);
               // $flagicon ='';
                if (($curr_lang !== '')&&($curr_lang !== null)){
                    $flagicon ='../img/flagicons/'.$curr_lang.'.png';
                }else{
                    //$flagicon ='../img/flagicons/en_US.png';
                }
            ?>
        </div>
            
            <!-- <a href="#" class="dash-public-btn" alt="Public Dashboards"><span>Dashboards (Public)</span></a>   -->
            <?php
            buildMenu($link, $domainId, "dashboardsLink", "mySnap4City", $curr_lang);
            ?>
            
            <?php if(!$_SESSION['isPublic']) : ?>
            <div class="tourBtnSet">
                <!-- <a href="#" class="myS4C-btn" alt="My Snap4City"><span>My Snap4City</span></a>
                <a href="#" class="tour-btn" alt="Tour again"><span>Tour again</span></a>   -->
                <?php
                buildMenu($link, $domainId, "snap4cityPortalLink", "mySnap4City", $curr_lang);
                buildMenu($link, $domainId, "resettour", "tourAgain", $curr_lang);
                ?>
            </div> 
            <?php endif; ?>
            <div class="centerWithFlex">
                <input id="searchMenu" type="text" placeholder="Search element" style="color:black;background-color:white" autocomplete="off">
            </div>
            <div id="mainMenuScrollableCnt">
                 <?php

                 /*   function checkFAIcon($ico) {
                        if ($ico == "fa fa-file-code-o") {
                            $ico = "fa-regular fa-file-code";
                        }
                        if ($ico == "fa fa-hdd-o") {
                            $ico = "fa-regular fa-hard-drive";
                        }
                    //    if (strpos($ico, "fa-sticky-note-o") !== false) {
                        if ($ico == "\tfa fa-sticky-note-o" || $ico == "fa fa-sticky-note-o") {
                            $ico = "fa-solid fa-note-sticky";
                        }
                        return $ico;
                    }   */

                    if(isset($_SESSION['loggedOrganization'])) {
                      $organization = $_SESSION['loggedOrganization'];
                      $organizationSql = $organization; 
                    } else {
                      $organization = "None";
                      $organizationSql = "Other";
                    }
                    
                    $link = mysqli_connect($host, $username, $password);
                    mysqli_select_db($link, $dbname);

                    $menuQuery = "SELECT * FROM Dashboard.MainMenu WHERE domain = $domainId ORDER BY menuOrder ASC";
                    $r = mysqli_query($link, $menuQuery);

                    if($r)
                    {
                        while($row = mysqli_fetch_assoc($r))
                        {
                            $menuItemId = $row['id'];
                            $linkUrl = $row['publicLinkUrl']!=null && $_SESSION['isPublic'] ? $row['publicLinkUrl']: $row['linkUrl'];
                            $linkId = $row['linkId'];
                            $icon = $row['icon'];  
                            $text = $row['text'];
                            $privileges = $row['privileges'];      
                            $userType = $row['userType']; 
                            $externalApp = $row['externalApp'];
                            $openMode = $row['openMode'];
                            $iconColor = $row['iconColor'];
                            $pageTitle = $row['pageTitle'];
                            $externalApp = $row['externalApp'];
                            $allowedOrgs = $row['organizations'];

                            if (strcmp($linkId, "userprofileLink") != 0 && strcmp($linkId, "snap4cityPortalLink") != 0 && strcmp($linkId, "resettour") != 0 && strcmp($linkId, "dashboardsLink") != 0) {
                                if($allowedOrgs=='*' || strpos($allowedOrgs, "'".$organizationSql) !== false || $_SESSION['loggedRole'] == 'RootAdmin') {
                                    /* if ($externalApp == 'yes') {
                                        if ($openMode == 'newTab') {
                                            if ($linkUrl == 'submenu') {
                                                $newItem = '<a href="' . $linkUrl . '" id="' . $linkId . '" data-externalApp="' . $externalApp . '" data-openMode="' . $openMode . '" data-linkUrl="' . $linkUrl . '" data-pageTitle="' . $pageTitle . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuLink" target="_blank">' .
                                                    '<div class="col-md-12 mainMenuItemCnt">' .
                                                    '<i class="' . $icon . '" style="color: ' . $iconColor . '"></i>&nbsp;&nbsp;&nbsp;' . $text . '&nbsp;&nbsp;&nbsp;<i class="fa fa-chevron-down submenuIndicator" style="color: white"></i>' .
                                                    '</div>' .
                                                    '</a>';
                                            } else {
                                                $newItem = '<a href="' . $linkUrl . '" id="' . $linkId . '" data-externalApp="' . $externalApp . '" data-openMode="' . $openMode . '" data-linkUrl="' . $linkUrl . '" data-pageTitle="' . $pageTitle . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuLink" target="_blank">' .
                                                    '<div class="col-md-12 mainMenuItemCnt">' .
                                                    '<i class="' . $icon . '" style="color: ' . $iconColor . '"></i>&nbsp;&nbsp;&nbsp;' . $text .
                                                    '</div>' .
                                                    '</a>';
                                            }
                                        } else {
                                            //CASO IFRAME
                                            if ($linkUrl == 'submenu') {
                                                $newItem = '<a href="' . $linkUrl . '" id="' . $linkId . '" data-externalApp="' . $externalApp . '" data-openMode="' . $openMode . '" data-linkUrl="' . $linkUrl . '" data-pageTitle="' . $pageTitle . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuLink mainMenuIframeLink">' .
                                                    '<div class="col-md-12 mainMenuItemCnt">' .
                                                    '<i class="' . $icon . '" style="color: ' . $iconColor . '"></i>&nbsp;&nbsp;&nbsp;' . $text . '&nbsp;&nbsp;&nbsp;<i class="fa fa-chevron-down submenuIndicator" style="color: white"></i>' .
                                                    '</div>' .
                                                    '</a>';
                                            } else {
                                                $newItem = '<a href="' . $linkUrl . '" id="' . $linkId . '" data-externalApp="' . $externalApp . '" data-openMode="' . $openMode . '" data-linkUrl="' . $linkUrl . '" data-pageTitle="' . $pageTitle . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuLink mainMenuIframeLink">' .
                                                    '<div class="col-md-12 mainMenuItemCnt">' .
                                                    '<i class="' . $icon . '" style="color: ' . $iconColor . '"></i>&nbsp;&nbsp;&nbsp;' . $text .
                                                    '</div>' .
                                                    '</a>';
                                            }
                                        }
                                    } else {
                                        if ($linkUrl == 'submenu') {
                                            $newItem = '<a href="' . $linkUrl . '" id="' . $linkId . '" data-externalApp="' . $externalApp . '" data-openMode="' . $openMode . '" data-linkUrl="' . $linkUrl . '" data-pageTitle="' . $pageTitle . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuLink">' .
                                                '<div class="col-md-12 mainMenuItemCnt">' .
                                                '<i class="' . $icon . '" style="color: ' . $iconColor . '"></i>&nbsp;&nbsp;&nbsp;' . $text . '&nbsp;&nbsp;&nbsp;<i class="fa fa-chevron-down submenuIndicator" style="color: white"></i>' .
                                                '</div>' .
                                                '</a>';
                                        } else {
                                            $newItem = '<a href="' . $linkUrl . '" id="' . $linkId . '" data-externalApp="' . $externalApp . '" data-openMode="' . $openMode . '" data-linkUrl="' . $linkUrl . '" data-pageTitle="' . $pageTitle . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuLink">' .
                                                '<div class="col-md-12 mainMenuItemCnt">' .
                                                '<i class="' . $icon . '" style="color: ' . $iconColor . '"></i>&nbsp;&nbsp;&nbsp;' . $text .
                                                '</div>' .
                                                '</a>';
                                        }
                                    } */
                                    $text =  translate_string($text, $curr_lang, $link);
                                    //echo($text);
                                    $icon = checkFAIcon($icon);
                                    $newItem = buildMenuTag($linkUrl, $linkId, null, $openMode, $pageTitle, $externalApp, $icon, $iconColor, $text, true, 'menu');
                                }

                                if ((strpos($privileges, "'" . ($_SESSION['isPublic'] ? 'Public' : $_SESSION['loggedRole'])) !== false) && (($userType == 'any') || (($userType != 'any') && ($userType == $_SESSION['loggedType']))) && ($allowedOrgs == '*' || (strpos($allowedOrgs, "'" . $organizationSql) !== false) || $_SESSION['loggedRole'] == 'RootAdmin')) {
                                    echo $newItem;
                                }

                                $uname = isset($_SESSION['loggedUsername']) ? $_SESSION['loggedUsername'] : '';

                                $submenuQuery = "SELECT * FROM Dashboard.MainMenuSubmenus s LEFT JOIN Dashboard.MainMenuSubmenusUser u ON u.submenu=s.id WHERE menu = '$menuItemId' AND (user is NULL OR user='$uname') ORDER BY menuOrder ASC";
                                $r2 = mysqli_query($link, $submenuQuery);

                                if ($r2) {
                                    while ($row2 = mysqli_fetch_assoc($r2)) {
                                        $menuItemId2 = $row2['id'];
                                        $linkUrl2 = $row2['linkUrl'];

                                        if ($linkUrl2 == 'submenu') {
                                            $linkUrl2 = '#';
                                        }

                                        $linkId2 = $row2['linkId'];
                                        $icon2 = $row2['icon'];
                                        $text2 = $row2['text'];
                                        //
                                        $text2 = translate_string($text2, $curr_lang, $link);
                                        //
                                        $privileges2 = $row2['privileges'];
                                        $userType2 = $row2['userType'];
                                        $externalApp2 = $row2['externalApp'];
                                        $openMode2 = $row2['openMode'];
                                        $iconColor2 = $row2['iconColor'];
                                        $pageTitle2 = $row2['pageTitle'];
                                        $externalApp2 = $row2['externalApp'];
                                        $allowedOrgs2 = $row2['organizations'];

                                        if ($allowedOrgs2 == '*' || strpos($allowedOrgs2, "'" . $organizationSql) !== false || $_SESSION['loggedRole'] == 'RootAdmin') {
                                            /* if ($externalApp2 == 'yes') {
                                                if ($openMode2 == 'newTab') {
                                                    if ($_REQUEST['fromSubmenu'] == false || $_REQUEST['fromSubmenu'] != $linkId) {
                                                        $newItem = '<a href="' . $linkUrl2 . '" id="' . $linkId2 . '" data-fatherMenuId="' . $linkId . '" data-externalApp="' . $externalApp2 . '" data-openMode="' . $openMode2 . '" data-linkUrl="' . $linkUrl2 . '" data-pageTitle="' . $pageTitle2 . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuSubItemLink" target="_blank">' .
                                                            '<div class="col-md-12 mainMenuSubItemCnt" style="display: none">' .
                                                            '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="' . $icon2 . '" style="color: ' . $iconColor2 . '"></i>&nbsp;&nbsp;&nbsp;' . $text .
                                                            '</div>' .
                                                            '</a>';
                                                    } else {
                                                        $newItem = '<a href="' . $linkUrl2 . '" id="' . $linkId2 . '" data-fatherMenuId="' . $linkId . '" data-externalApp="' . $externalApp2 . '" data-openMode="' . $openMode2 . '" data-linkUrl="' . $linkUrl2 . '" data-pageTitle="' . $pageTitle2 . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuSubItemLink" target="_blank">' .
                                                            '<div class="col-md-12 mainMenuSubItemCnt">' .
                                                            '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="' . $icon2 . '" style="color: ' . $iconColor2 . '"></i>&nbsp;&nbsp;&nbsp;' . $text .
                                                            '</div>' .
                                                            '</a>';
                                                    }
                                                } else {
                                                    //CASO IFRAME
                                                    if (@$_REQUEST['fromSubmenu'] == false || @$_REQUEST['fromSubmenu'] != $linkId) {
                                                        $newItem = '<a href="' . $linkUrl2 . '" id="' . $linkId2 . '" data-fatherMenuId="' . $linkId . '" data-externalApp="' . $externalApp2 . '" data-openMode="' . $openMode2 . '" data-linkUrl="' . $linkUrl2 . '" data-pageTitle="' . $pageTitle2 . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuSubItemLink mainMenuIframeLink">' .
                                                            '<div class="col-md-12 mainMenuSubItemCnt" style="display: none">' .
                                                            '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="' . $icon2 . '" style="color: ' . $iconColor2 . '"></i>&nbsp;&nbsp;&nbsp;' . $text2 .
                                                            '</div>' .
                                                            '</a>';
                                                    } else {
                                                        $newItem = '<a href="' . $linkUrl2 . '" id="' . $linkId2 . '" data-fatherMenuId="' . $linkId . '" data-externalApp="' . $externalApp2 . '" data-openMode="' . $openMode2 . '" data-linkUrl="' . $linkUrl2 . '" data-pageTitle="' . $pageTitle2 . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuSubItemLink mainMenuIframeLink">' .
                                                            '<div class="col-md-12 mainMenuSubItemCnt">' .
                                                            '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="' . $icon2 . '" style="color: ' . $iconColor2 . '"></i>&nbsp;&nbsp;&nbsp;' . $text2 .
                                                            '</div>' .
                                                            '</a>';
                                                    }
                                                }
                                            } else {
                                                if (@$_REQUEST['fromSubmenu'] == false || @$_REQUEST['fromSubmenu'] != $linkId) {
                                                    $newItem = '<a href="' . $linkUrl2 . '" id="' . $linkId2 . '" data-fatherMenuId="' . $linkId . '" data-externalApp="' . $externalApp2 . '" data-openMode="' . $openMode2 . '" data-linkUrl="' . $linkUrl2 . '" data-pageTitle="' . $pageTitle2 . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuSubItemLink">' .
                                                        '<div class="col-md-12 mainMenuSubItemCnt" style="display: none">' .
                                                        '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="' . $icon2 . '" style="color: ' . $iconColor2 . '"></i>&nbsp;&nbsp;&nbsp;' . $text2 .
                                                        '</div>' .
                                                        '</a>';
                                                } else {
                                                    $newItem = '<a href="' . $linkUrl2 . '" id="' . $linkId2 . '" data-fatherMenuId="' . $linkId . '" data-externalApp="' . $externalApp2 . '" data-openMode="' . $openMode2 . '" data-linkUrl="' . $linkUrl2 . '" data-pageTitle="' . $pageTitle2 . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuSubItemLink">' .
                                                        '<div class="col-md-12 mainMenuSubItemCnt">' .
                                                        '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="' . $icon2 . '" style="color: ' . $iconColor2 . '"></i>&nbsp;&nbsp;&nbsp;' . $text2 .
                                                        '</div>' .
                                                        '</a>';
                                                }
                                            } */
                                            $isOpen = $_REQUEST['fromSubmenu'] == true && $_REQUEST['fromSubmenu'] == $linkId;
                                            $icon2 = checkFAIcon($icon2);
                                            $newItem = buildMenuTag($linkUrl2, $linkId2, $linkId, $openMode2, $pageTitle2, $externalApp2, $icon2, $iconColor2, $text2, $isOpen, 'menu');
                                        }

                                        if ((strpos($privileges2, "'" . ($_SESSION['isPublic'] ? 'Public' : $_SESSION['loggedRole'])) !== false) && (($userType == 'any') || (($userType != 'any') && ($userType == $_SESSION['loggedType']))) && ($allowedOrgs2 == '*' || (strpos($allowedOrgs2, "'" . $organizationSql) !== false) || $_SESSION['loggedRole'] == 'RootAdmin')) {
                                            echo $newItem;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                    mysqli_close($link);
                ?>
            </div>   
</div>
<style>
    .menuFilteredOut{display:none !important}
</style>
<script type='text/javascript'>
     
              
                
                
    $(document).ready(function () 
    {
        console.log('Entrato in Main Menu test');
        //var mainMenuScrollableCntHeight = parseInt($('#mainMenuCnt').outerHeight() - $('#headerClaimCnt').outerHeight() - $('#mainMenuCnt .mainMenuUsrCnt').outerHeight() - 30);
        //$('#mainMenuScrollableCnt').css("height", parseInt(mainMenuScrollableCntHeight + 0) + "px");
        //$('#mainMenuScrollableCnt').css("overflow-y", "auto");
        
        $('#mainMenuCnt a.mainMenuLink').attr('data-submenuVisible', 'false');
        $('#mainMenuCnt a.mainMenuSubItemLink').hide();
        
        $('#mainMenuUsrLoginCnt').hover(function(){
         //   $(this).css("background", "rgba(0, 162, 211, 1)");
            $(this).css("cursor", "pointer");
        //    $('#mainMenuUsrDetCnt').hide();
          //  if (document.getElementById("mainMenuUsrCnt").offsetWidth < 217) {
          //      $('#mainMenuUsrLogoutCnt').css("height", "90px !important");
          //  }
        //    $('#mainMenuUsrLogoutCnt').show();
        }, function(){
            $(this).css("background", "transparent");
            $(this).css("cursor", "normal");
            $('#mainMenuUsrLogoutCnt').hide();
            $('#mainMenuUsrDetCnt').show();
        });
        
        $('#mainMenuUsrLogoutBtn').click(function(){
<?php if(!$_SESSION['isPublic']) : ?>
            (function(){
               var i = document.createElement('iframe');
                i.style.display = 'none';
                i.onload = function() { i.parentNode.removeChild(i); };
                i.src = 'https://www.snap4city.org/drupal/user/logout';
                document.body.appendChild(i);
                //Logout Chat
                var ii = document.createElement('iframe');
                ii.style.display = 'none';
                ii.onload = function() { ii.parentNode.removeChild(ii); };
                ii.src = 'https://chat.snap4city.org/rocket-chat-rest-client/logout.php';
                document.body.appendChild(ii);
                })();
            setTimeout(function() {location.href = "logout.php";},500);
<?php else : ?>
            location.href = "ssoLogin.php";
<?php endif; ?>
        });

        $('#mainMenuUsrLoginBtn').click(function(){
            <?php if(!$_SESSION['isPublic']) : ?>
            (function(){
                var i = document.createElement('iframe');
                i.style.display = 'none';
                i.onload = function() { i.parentNode.removeChild(i); };
                i.src = 'https://www.snap4city.org/drupal/user/logout';
                document.body.appendChild(i);
                //Logout Chat
                var ii = document.createElement('iframe');
                ii.style.display = 'none';
                ii.onload = function() { ii.parentNode.removeChild(ii); };
                ii.src = 'https://chat.snap4city.org/rocket-chat-rest-client/logout.php';
                document.body.appendChild(ii);
            })();
            setTimeout(function() {location.href = "logout.php";},500);
            <?php else : ?>
            location.href = "ssoLogin.php";
            <?php endif; ?>
        });

        $('#headerClaimCnt').hover(function(){
            $(this).css("cursor", "pointer");
        });

        $('#headerClaimCnt').click(function(){
            var hostHomeUrl = window.location.hostname;
            if (hostHomeUrl.includes("localhost")) {
                hostHomeUrl = hostHomeUrl + "/dashboardSmartCity/";
            }
            window.open("//" + hostHomeUrl,"_self");
          //  window.open("https://www.snap4city.org","_self");
         //   window.open("https://www.snap4city.org/drupal/node/1");
        });
        
        $('#mainMenuCnt .mainMenuLink').click(function(event){
            event.preventDefault();
            var pageTitle = $(this).attr('data-pageTitle');
            var linkId = $(this).attr('id');
            var linkUrl = $(this).attr('data-linkUrl');
            
            if($(this).attr('data-linkUrl') === 'submenu')
            {
                $('#mainMenuCnt .mainMenuItemCnt').each(function(i){
                    $(this).removeClass('mainMenuItemCntActive');
                });
                $(this).find('div.mainMenuItemCnt').addClass("mainMenuItemCntActive");
                if($(this).attr('data-submenuVisible') === 'false')
                {
                    $(this).attr('data-submenuVisible', 'true');
                    $('.mainMenuSubItemCnt').css( "display", "block" );
                    $('#mainMenuCnt a.mainMenuSubItemLink[data-fatherMenuId=' + $(this).attr('id') + ']').show();
                    $(this).find('.submenuIndicator').removeClass('fa-chevron-down');
                    $(this).find('.submenuIndicator').addClass('fa-chevron-up');
                }
                else
                {
                    $(this).attr('data-submenuVisible', 'false');
                    $('#mainMenuCnt a.mainMenuSubItemLink[data-fatherMenuId=' + $(this).attr('id') + ']').hide();
                    $(this).find('.submenuIndicator').removeClass('fa-chevron-up');
                    $(this).find('.submenuIndicator').addClass('fa-chevron-down');
                }
            }
            else
            {
                $('#mainMenuCnt a.mainMenuSubItemLink').hide();
            
                $('#mainMenuCnt a.mainMenuSubItemLink').each(function(i){
                    $(this).attr('data-submenuVisible', 'false');
                });

                $('#mainMenuCnt .mainMenuItemCnt').each(function(i){
                    $(this).removeClass('mainMenuItemCntActive');
                });
                $(this).find('div.mainMenuItemCnt').addClass("mainMenuItemCntActive");
                window.open($(this).attr("href"), $(this).attr("target"));
            }
            
            //var mainMenuScrollableCntHeight = parseInt($('#mainMenuCnt').outerHeight() - $('#headerClaimCnt').outerHeight() - $('#mainMenuCnt .mainMenuUsrCnt').outerHeight() - 30);
            //$('#mainMenuScrollableCnt').css("height", parseInt(mainMenuScrollableCntHeight + 0) + "px");
            //$('#mainMenuScrollableCnt').css("overflow-y", "auto");
        });
        
        $('#mainMenuCnt .mainMenuSubItemLink').click(function(event){
            event.preventDefault();
            var pageTitle = $(this).attr('data-pageTitle');
            var linkId = $(this).attr('id');
            var linkUrl = $(this).attr('data-linkUrl');
            var submenuId = $(this).attr('data-fathermenuid');
            
            $('#mainMenuCnt a.mainMenuSubItemLink[data-fathermenuid=' + submenuId + '] .mainMenuSubItemCnt').removeClass('mainMenuItemCntActive');
            $(this).find('div.mainMenuSubItemCnt').addClass("mainMenuItemCntActive");
            window.open($(this).attr("href"), $(this).attr("target"));
        });
        
        //$(window).resize(function(){
            //var mainMenuScrollableCntHeight = parseInt($('#mainMenuCnt').outerHeight() - $('#headerClaimCnt').outerHeight() - $('#mainMenuCnt .mainMenuUsrCnt').outerHeight() - 30);
            //$('#mainMenuScrollableCnt').css("height", parseInt(mainMenuScrollableCntHeight + 0) + "px");
            //$('#mainMenuScrollableCnt').css("overflow-y", "auto");
        //});

        var searchTimeout
        $("#searchMenu").keyup(() => {
            clearTimeout(searchTimeout)
            let text =  $("#searchMenu").val().trim()
            searchTimeout = setTimeout(() => searchElement(text), 250)
        })
    });
    
    //

    function searchElement(text=""){
        $(".menuFilteredOut").removeClass("menuFilteredOut")
        $("#mainMenuScrollableCnt .mainMenuLink[data-submenuvisible=true]").click()
        if(text.trim() != ""){
            $("#mainMenuScrollableCnt .mainMenuLink[data-linkurl!=submenu] span").toArray().filter(x => !x.innerText.toLowerCase().includes(text.toLowerCase())).forEach(x => $(x).parents(".mainMenuLink").addClass("menuFilteredOut"))
            $("#mainMenuScrollableCnt .mainMenuLink[data-linkurl=submenu], #mainMenuScrollableCnt .mainMenuSubItemLink").addClass("menuFilteredOut")
            var toShowLinks = []
            $("#mainMenuScrollableCnt .mainMenuSubItemCnt span").toArray().filter(x => x.innerText.toLowerCase().includes(text.toLowerCase())).forEach(x => {
                let fatherID = $(x).parents(".mainMenuSubItemLink").removeClass("menuFilteredOut").attr("data-fathermenuid")
                let mainMenuLink = $(`#mainMenuScrollableCnt .mainMenuLink#${fatherID}[data-linkurl=submenu]`)[0]
                if(mainMenuLink != undefined)
                    toShowLinks.push(mainMenuLink)
            })
            $([... new Set(toShowLinks)]).click().removeClass("menuFilteredOut")
        }
        $(".mainMenuItemCntActive").removeClass("mainMenuItemCntActive")
    }
            
</script>    

