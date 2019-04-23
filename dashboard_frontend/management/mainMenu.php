<div class="hidden-xs hidden-sm col-md-2" id="mainMenuCnt">
        <div id="headerClaimCnt" class="col-md-12 centerWithFlex">
            <?php
                include 'config.php';

                error_reporting(E_ERROR | E_NOTICE);
                date_default_timezone_set('Europe/Rome');
                
                $link = mysqli_connect($host, $username, $password);
                mysqli_select_db($link, $dbname);
                
                $domainId = null;
                
                $currDom = $_SERVER['HTTP_HOST'];

                $domQ = "SELECT * FROM Dashboard.Domains WHERE domains LIKE '%$currDom%'";
                $r = mysqli_query($link, $domQ);

                if($r)
                {
                    if(mysqli_num_rows($r) > 0)
                    {
                        $row = mysqli_fetch_assoc($r);
                        $domainId = $row['id'];
                        echo $row['claim'];
                    }
                    else
                    {
                        echo 'DISIT';
                    }
                }
                else
                {
                    echo 'DISIT';
                }
            ?>
        </div>
            <div class="col-md-12 mainMenuUsrCnt">
                <div class="row">
<?php if(!$_SESSION['isPublic']) : ?>                  
                    <div class="col-md-12 centerWithFlex" id="mainMenuUsrCnt">
                        <?php if (($_SESSION['loggedOrganization'] == 'None' || $_SESSION['loggedOrganization'] == 'none')) {
                            if ($_SESSION['loggedUsername'] == 'roottooladmin1') {
                                echo "User: " . $_SESSION['loggedUsername'] . ", Org: " . $_SESSION['loggedOrganization'];
                            } else {
                                echo "User: " . $_SESSION['loggedUsername'] . ", Org: None";
                            }
                        } else {
                            echo "User: " . $_SESSION['loggedUsername'] . ", Org: " . $_SESSION['loggedOrganization'];
                        }?>
                    </div>
                    <div class="col-md-12 centerWithFlex" id="mainMenuUsrDetCnt">
                        <?php echo "Role: " . $_SESSION['loggedRole'] . ", Level: " . $_SESSION['loggedUserLevel']; ?>
                    </div>
                  <!--  <div class="col-md-12 centerWithFlex" id="mainMenuUsrLogoutCnt">
                        Logout<br/>
                    </div>  -->
                    <div class="col-xs-12 centerWithFlex" id="mainMenuUsrLogoutCnt">
                        <button type="button" id="mainMenuUsrLogoutBtn" class="editDashBtn">logout</button>
                    </div>
<?php else : ?>
                 <!--   <div class="col-md-12 centerWithFlex" id="mainMenuUsrLoginCnt">
                        <br/>Login<br/>
                    </div>  -->
                    <div class="col-xs-12 centerWithFlex" id="mainMenuUsrLogoutCnt">
                        <button type="button" id="mainMenuUsrLoginBtn" class="editDashBtn">login</button>
                        <script> </script>
                    </div>
<?php endif; ?>                  
                </div>
            </div>  

            <div id="mainMenuScrollableCnt"  class="col-md-12">
                 <?php
                    if(isset($_SESSION['loggedUsername'])) {
                        $ldapUsername = "cn=" . $_SESSION['loggedUsername'] . "," . $ldapBaseDN;
                        $ds = ldap_connect($ldapServer, $ldapPort);
                        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
                        $bind = ldap_bind($ds);
                        $organization = checkLdapOrganization($ds, $ldapUsername, $ldapBaseDN);
                        if (is_null($organization)) {
                            $organization = "None";
                            $organizationSql = "Other";
                        } else if ($organization == "") {
                            $organization = "None";
                            $organizationSql = "Other";
                        } else {
                            $organizationSql = $organization;
                        }
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

                            if(strpos($allowedOrgs, "'".$organizationSql) !== false || $_SESSION['loggedRole'] == 'RootAdmin') {
                                if ($externalApp == 'yes') {
                                    if ($openMode == 'newTab') {
                                        if ($linkUrl == 'submenu') {
                                            $newItem = '<a href="' . $linkUrl . '" id="' . $linkId . '" data-externalApp="' . $externalApp . '" data-openMode="' . $openMode . '" data-linkUrl="' . $linkUrl . '" data-pageTitle="' . $pageTitle . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuLink" target="_blank">' .
                                                '<div class="col-md-12 mainMenuItemCnt">' .
                                                '<i class="' . $icon . '" style="color: ' . $iconColor . '"></i>&nbsp;&nbsp;&nbsp;' . $text . '&nbsp;&nbsp;&nbsp;<i class="fa fa-caret-down submenuIndicator" style="color: white"></i>' .
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
                                                '<i class="' . $icon . '" style="color: ' . $iconColor . '"></i>&nbsp;&nbsp;&nbsp;' . $text . '&nbsp;&nbsp;&nbsp;<i class="fa fa-caret-down submenuIndicator" style="color: white"></i>' .
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
                                            '<i class="' . $icon . '" style="color: ' . $iconColor . '"></i>&nbsp;&nbsp;&nbsp;' . $text . '&nbsp;&nbsp;&nbsp;<i class="fa fa-caret-down submenuIndicator" style="color: white"></i>' .
                                            '</div>' .
                                            '</a>';
                                    } else {
                                        $newItem = '<a href="' . $linkUrl . '" id="' . $linkId . '" data-externalApp="' . $externalApp . '" data-openMode="' . $openMode . '" data-linkUrl="' . $linkUrl . '" data-pageTitle="' . $pageTitle . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuLink">' .
                                            '<div class="col-md-12 mainMenuItemCnt">' .
                                            '<i class="' . $icon . '" style="color: ' . $iconColor . '"></i>&nbsp;&nbsp;&nbsp;' . $text .
                                            '</div>' .
                                            '</a>';
                                    }
                                }
                            }

                            if((strpos($privileges, "'". ($_SESSION['isPublic'] ? 'Public' : $_SESSION['loggedRole'])) !== false)&&(($userType == 'any')||(($userType != 'any')&&($userType == $_SESSION['loggedType']))) && ((strpos($allowedOrgs, "'".$organizationSql) !== false) || $_SESSION['loggedRole'] == 'RootAdmin'))
                            {
                                echo $newItem;
                            }
                            
                            $uname = isset($_SESSION['loggedUsername']) ? $_SESSION['loggedUsername'] : '';

                            $submenuQuery = "SELECT * FROM Dashboard.MainMenuSubmenus s LEFT JOIN Dashboard.MainMenuSubmenusUser u ON u.submenu=s.id WHERE menu = '$menuItemId' AND (user is NULL OR user='$uname') ORDER BY menuOrder ASC";
                            $r2 = mysqli_query($link, $submenuQuery);

                            if($r2)
                            {
                                while($row2 = mysqli_fetch_assoc($r2))
                                {
                                    $menuItemId2 = $row2['id'];
                                    $linkUrl2 = $row2['linkUrl'];

                                    if($linkUrl2 == 'submenu')
                                    {
                                        $linkUrl2 = '#';
                                    }

                                    $linkId2 = $row2['linkId'];
                                    $icon2 = $row2['icon'];  
                                    $text2 = $row2['text'];
                                    $privileges2 = $row2['privileges'];      
                                    $userType2 = $row2['userType']; 
                                    $externalApp2 = $row2['externalApp'];
                                    $openMode2 = $row2['openMode'];
                                    $iconColor2 = $row2['iconColor'];
                                    $pageTitle2 = $row2['pageTitle'];
                                    $externalApp2 = $row2['externalApp'];
                                    $allowedOrgs2 = $row2['organizations'];

                                    if(strpos($allowedOrgs2, "'".$organizationSql) !== false || $_SESSION['loggedRole'] == 'RootAdmin') {
                                        if ($externalApp2 == 'yes') {
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
                                                if ($_REQUEST['fromSubmenu'] == false || $_REQUEST['fromSubmenu'] != $linkId) {
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
                                            if ($_REQUEST['fromSubmenu'] == false || $_REQUEST['fromSubmenu'] != $linkId) {
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
                                        }
                                    }

                                    if((strpos($privileges2, "'".($_SESSION['isPublic'] ? 'Public' : $_SESSION['loggedRole'])) !== false)&&(($userType == 'any')||(($userType != 'any')&&($userType == $_SESSION['loggedType']))) && ((strpos($allowedOrgs2, "'".$organizationSql) !== false) || $_SESSION['loggedRole'] == 'RootAdmin'))
                                    {
                                        echo $newItem;
                                    }
                                }
                            }
                        }
                    }
                    
                    mysqli_close($link);
                ?>
            </div>   
</div>


<script type='text/javascript'>
     
              
                
                
    $(document).ready(function () 
    {
        console.log('Entrato in Main Menu');
        var mainMenuScrollableCntHeight = parseInt($('#mainMenuCnt').outerHeight() - $('#headerClaimCnt').outerHeight() - $('#mainMenuCnt .mainMenuUsrCnt').outerHeight() - 30);
        $('#mainMenuScrollableCnt').css("height", parseInt(mainMenuScrollableCntHeight + 0) + "px");
        $('#mainMenuScrollableCnt').css("overflow-y", "auto");
        
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
                    $(this).find('.submenuIndicator').removeClass('fa-caret-down');
                    $(this).find('.submenuIndicator').addClass('fa-caret-up');
                }
                else
                {
                    $(this).attr('data-submenuVisible', 'false');
                    $('#mainMenuCnt a.mainMenuSubItemLink[data-fatherMenuId=' + $(this).attr('id') + ']').hide();
                    $(this).find('.submenuIndicator').removeClass('fa-caret-up');
                    $(this).find('.submenuIndicator').addClass('fa-caret-down');
                }
            }
            else
            {
                $('#mainMenuCnt a.mainMenuSubItemLink').hide();
            
                $('#mainMenuCnt a.mainMenuSubItemLink').each(function(i){
                    $(this).attr('data-submenuVisible', 'false');
                });
                switch($(this).attr('data-openMode'))
                {
                    case "iframe":
                        $('#mainMenuCnt .mainMenuItemCnt').each(function(i){
                            $(this).removeClass('mainMenuItemCntActive');
                        });
                        $(this).find('div.mainMenuItemCnt').addClass("mainMenuItemCntActive");
                        if($(this).attr('data-externalApp') === 'yes')
                        {
                            location.href = "iframeApp.php?linkUrl=" + encodeURIComponent(linkUrl) + "&linkId=" + linkId + "&pageTitle=" + pageTitle + "&fromSubmenu=false";
                        }
                        break;
                        
                    case "newTab":
                        var newTab = window.open($(this).attr('data-linkurl') + "?linkId=" + linkId + "&fromSubmenu=false", '_blank');
                        if(newTab) 
                        {
                            newTab.focus();
                        } 
                        else
                        {
                            alert('Please allow popups for this website');
                        }
                        break;
                        
                    case "samePage":
                        $('#mainMenuCnt .mainMenuItemCnt').each(function(i){
                            $(this).removeClass('mainMenuItemCntActive');
                        });
                        $(this).find('div.mainMenuItemCnt').addClass("mainMenuItemCntActive");
                      //  if (linkUrl.includes("../management/dashboards.php") || linkUrl.includes("../management/microApplications.php") || linkUrl.includes("../management/externalServices.php")) {
                        // GP COMMENT TEMPORARY
                            location.href = $(this).attr('data-linkurl') + "?linkId=" + linkId + "&fromSubmenu=false&pageTitle=" + pageTitle;
                      /*  } else {
                      // GP UNCOMMENT TEMPORARY
                            location.href = $(this).attr('data-linkurl') + "?linkId=" + linkId + "&fromSubmenu=false";
                        }*/
                        break;    
                }
            }
            
            var mainMenuScrollableCntHeight = parseInt($('#mainMenuCnt').outerHeight() - $('#headerClaimCnt').outerHeight() - $('#mainMenuCnt .mainMenuUsrCnt').outerHeight() - 30);
            $('#mainMenuScrollableCnt').css("height", parseInt(mainMenuScrollableCntHeight + 0) + "px");
            $('#mainMenuScrollableCnt').css("overflow-y", "auto");
        });
        
        $('#mainMenuCnt .mainMenuSubItemLink').click(function(event){
            event.preventDefault();
            var pageTitle = $(this).attr('data-pageTitle');
            var linkId = $(this).attr('id');
            var linkUrl = $(this).attr('data-linkUrl');
            var submenuId = $(this).attr('data-fathermenuid');
            
            switch($(this).attr('data-openMode'))
            {
                case "iframe":
                    $('#mainMenuCnt a.mainMenuSubItemLink[data-fathermenuid=' + submenuId + '] .mainMenuSubItemCnt').removeClass('mainMenuItemCntActive');
                    $(this).find('div.mainMenuSubItemCnt').addClass("mainMenuItemCntActive");
                    if($(this).attr('data-externalApp') === 'yes')
                    {
                        location.href = "iframeApp.php?linkUrl=" + encodeURIComponent(linkUrl) + "&linkId=" + linkId + "&pageTitle=" + pageTitle + "&fromSubmenu=" + submenuId;
                    }
                    break;

                case "newTab":
                    var newTab = window.open($(this).attr('data-linkurl') + "?linkId=" + linkId + "&fromSubmenu=" + submenuId, '_blank');
                    if(newTab) 
                    {
                        newTab.focus();
                    } 
                    else
                    {
                        alert('Please allow popups for this website');
                    }
                    break;

                case "samePage":
                    $('#mainMenuCnt a.mainMenuSubItemLink[data-fathermenuid=' + submenuId + '] .mainMenuSubItemCnt').removeClass('mainMenuItemCntActive');
                    $(this).find('div.mainMenuSubItemCnt').addClass("mainMenuItemCntActive");
                    //  location.href = $(this).attr('data-linkurl') + "?linkId=" + linkId + "&fromSubmenu=" + submenuId;
                  //  if (linkUrl.includes("../management/dashboards.php") || linkUrl.includes("../management/microApplications.php") || linkUrl.includes("../management/externalServices.php")) {
                    // GP COMMENT TEMPORARY
                        location.href = $(this).attr('data-linkurl') + "?linkId=" + linkId + "&fromSubmenu=" + submenuId + "&pageTitle=" + pageTitle;
                 //   } else {
                    // GP UNCOMMENT TEMPORARY
                 //       location.href = $(this).attr('data-linkurl') + "?linkId=" + linkId + "&fromSubmenu=" + submenuId;
                 //   }
                    break;    
            }
        });
        
        $(window).resize(function(){
            var mainMenuScrollableCntHeight = parseInt($('#mainMenuCnt').outerHeight() - $('#headerClaimCnt').outerHeight() - $('#mainMenuCnt .mainMenuUsrCnt').outerHeight() - 30);
            $('#mainMenuScrollableCnt').css("height", parseInt(mainMenuScrollableCntHeight + 0) + "px");
            $('#mainMenuScrollableCnt').css("overflow-y", "auto");
        });
    });
</script>    

