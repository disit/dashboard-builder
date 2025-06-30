<div class="hidden-xs hidden-sm col-md-2" id="mainMenuCnt">
        <div id="headerClaimCnt" class="col-md-12 centerWithFlex">
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
                        echo $row['claim'];
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
        <div class="col-md-12 centerWithFlex" style="padding-top: 12px;">
            <button id="switchLayout">
                Switch To New Layout (Beta)
            </button>
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
                        <?php 
  if (strpos($localizationsRoles, $_SESSION['loggedRole'])) {
    echo('<a href="#" id="mainMenuSelectLanguageBtn" style="font-size: 10px;"><img src="'.$flagicon.'" id="flagicon" alt="'.$_SESSION['lang'].'" style="padding:5px; height: 24px;  width: 31px;"></a>');
}
?>
                        
                    </div>
<?php else : ?>                 
                 <!--   <div class="col-md-12 centerWithFlex" id="mainMenuUsrLoginCnt">
                        <br/>Login<br/>
                    </div>  -->
                    <div class="col-xs-12 centerWithFlex" id="mainMenuUsrLogoutCnt">
                        <button type="button" id="mainMenuUsrLoginBtn" class="editDashBtn">login</button>
                        <?php 
                       //  if(strpos($localizationsRoles, "Public")){
                          if ($localizationEnabled){
     echo('<a href="#" id="mainMenuSelectLanguageBtn" style="font-size: 10px;"><img src="'.$flagicon.'" id="flagicon" alt="'.$curr_lang.'" style="padding:5px; height: 24px;  width: 31px;"></a>');
     }
     ?>
                        <script> </script>
                    </div>
<?php endif; ?>                  
                </div>
            </div>  
            <div id="searchMenuDiv" class="col-md-12 centerWithFlex">
                <input id="searchMenu" type="text" placeholder="Search element" style="color:black; margin-bottom:5%" autocomplete="off">
            </div>
            <div id="mainMenuScrollableCnt"  class="col-md-12">
                 <?php

                     function buildMenuTagLegacy($linkUrl, $linkId, $parentLinkId, $openMode, $pageTitle, $externalApp, $icon, $iconColor, $text, $isOpen)
                     {
                         $isIframe = $externalApp == 'yes' && $openMode == "iframe";
                         $isNewTab = $openMode == "newTab";
                         $classes = "internalLink moduleLink";
                         $target = "_self";

                         $isSubmenu = $parentLinkId != null && trim($parentLinkId) != '';
                         if ($isSubmenu) {
                             $classes = "{$classes} mainMenuSubItemLink";
                         } else {
                             $classes = "{$classes} mainMenuLink";
                         }

                         $fromParent = $isSubmenu ? $parentLinkId : "false";
                         $url = addQueryParamsToUrl(
                             buildPlatformUrl($linkUrl, $pageTitle, $openMode),
                             [
                                 "linkId" => $linkId,
                                 "fromSubmenu" => $fromParent
                             ]
                         );
                         if ($isIframe == true) {
                             $classes = "{$classes} mainMenuIframeLink";
                         } elseif ($isNewTab) {
                             $target = "_blank";
                         }

                         $prop = [
                             "href" => $url,
                             "id" => $linkId,
                             "data-externalApp" => $externalApp,
                             "data-openMode" => $openMode,
                             "data-linkUrl" => $linkUrl,
                             "data-pageTitle" => $pageTitle,
                             "data-submenuVisible" => "false",
                             "class" => $classes,
                             "target" => $target
                         ];

                         if ($isSubmenu) {
                             $prop["data-fatherMenuId"] = $parentLinkId;
                         }

                         $attr = implode(" ", array_map(function ($k, $v) {
                             return "{$k}=\"{$v}\"";
                         }, array_keys($prop), $prop));

                         $containerClass = $isSubmenu ? "mainMenuSubItemCnt" : "mainMenuItemCnt";
                         $displayStyle = $isOpen ? "" : 'style="display: none"';
                         $subMenuCaret = $linkUrl == "submenu" ? '<i class="fa fa-caret-down submenuIndicator" style="color: white"></i>' : "";
                         return <<<EOT
    <a $attr>
        <div class="col-md-12 $containerClass" $displayStyle>
            <i class="$icon" style="color: $iconColor"></i><span>$text</span>$subMenuCaret
        </div>
    </a>
EOT;
                     }

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

                            if($allowedOrgs=='*' || strpos($allowedOrgs, "'".$organizationSql) !== false || $_SESSION['loggedRole'] == 'RootAdmin') {
                                /* if ($externalApp == 'yes') {
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
                                } */
                               $text =  translate_string($text, $curr_lang, $link);
                                //echo($text);
                                $newItem = buildMenuTagLegacy($linkUrl,$linkId,null,$openMode,$pageTitle,$externalApp,$icon,$iconColor,$text,true, 'menu');
                            }

                            if((strpos($privileges, "'". ($_SESSION['isPublic'] ? 'Public' : $_SESSION['loggedRole'])) !== false)&&(($userType == 'any')||(($userType != 'any')&&($userType == $_SESSION['loggedType']))) && ($allowedOrgs=='*' || (strpos($allowedOrgs, "'".$organizationSql) !== false) || $_SESSION['loggedRole'] == 'RootAdmin'))
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
                                    //
                                    $text2 =  translate_string($text2, $curr_lang, $link);
                                    //
                                    $privileges2 = $row2['privileges'];      
                                    $userType2 = $row2['userType']; 
                                    $externalApp2 = $row2['externalApp'];
                                    $openMode2 = $row2['openMode'];
                                    $iconColor2 = $row2['iconColor'];
                                    $pageTitle2 = $row2['pageTitle'];
                                    $externalApp2 = $row2['externalApp'];
                                    $allowedOrgs2 = $row2['organizations'];

                                    if($allowedOrgs2=='*' || strpos($allowedOrgs2, "'".$organizationSql) !== false || $_SESSION['loggedRole'] == 'RootAdmin') {
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
                                        $newItem = buildMenuTagLegacy($linkUrl2,$linkId2,$linkId,$openMode2,$pageTitle2,$externalApp2,$icon2,$iconColor2,$text2,$isOpen, 'menu');
                                    }

                                    if((strpos($privileges2, "'".($_SESSION['isPublic'] ? 'Public' : $_SESSION['loggedRole'])) !== false)&&(($userType == 'any')||(($userType != 'any')&&($userType == $_SESSION['loggedType']))) && ($allowedOrgs2=='*' || (strpos($allowedOrgs2, "'".$organizationSql) !== false) || $_SESSION['loggedRole'] == 'RootAdmin'))
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

<?php 
//if (strpos($localizationsRoles, $_SESSION['loggedRole'])) {
 if ((strpos($localizationsRoles, $_SESSION['loggedRole']))||  ($localizationEnabled)) {
     $control_role = 'true';
   $obj = json_decode($localizations, true);
   $languages = $obj['languages'];
   $tot_leng = count($languages);
   if ($tot_leng > 0){
       echo ('<div id="translate-modal" class="modal fade bd-example-modal-sm" role="dialog">
        <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
        <div class="modal-body">
            <button type="button" class="close" data-dismiss="modal">&times;</button>');
     for($i=0; $i<$tot_leng; $i++){
         $lang = $obj['languages'][$i];
         echo('<input class="form-check-input select_lang" type="checkbox" value="col-md-12 mainMenuItemCnt mainMenuItemCntActive" onclick="select_lang(\''.$lang['code'].'\',\''.$_SESSION['loggedRole'].'\')"> <img src="../img/flagicons/'.$lang['code'].'.png" alt="alternatetext" style="padding:5px; height: 24px; width: 31px;"> <span>'.$lang['lang'].'</span><br />');
     }                   
      echo ('</div>                      
    </div>
  </div>
    </div>');
}
}
?>
<style>
    .menuFilteredOut{display:none !important}
</style>
<script type='text/javascript'>
     
              
                
                
    $(document).ready(function () 
    {
        console.log('Entrato in Main Menu');
        // Vecchio calcolo altezza
        // var mainMenuScrollableCntHeight = parseInt($('#mainMenuCnt').outerHeight() - $('#headerClaimCnt').outerHeight() - $('#mainMenuCnt .mainMenuUsrCnt').outerHeight() - $('#searchMenuDiv').outerHeight() - 30);
        // Calcolo dinamico
        var mainMenuScrollableCntHeight = parseInt($('#mainMenuCnt').outerHeight() - 30 - $("#mainMenuCnt").children().not("#mainMenuScrollableCnt").toArray().reduce((tot, x) => tot + $(x).outerHeight(), 0));
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

        $('#switchLayout').click(function(){
            location.href = "index.php?switchNewLayout=true";
            // location.href = "../switchLayout.php?switchNewLayout=true";
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

                $('#mainMenuCnt .mainMenuItemCnt').each(function(i){
                    $(this).removeClass('mainMenuItemCntActive');
                });
                $(this).find('div.mainMenuItemCnt').addClass("mainMenuItemCntActive");
                window.open($(this).attr("href"), $(this).attr("target"));
            }
            
            // var mainMenuScrollableCntHeight = parseInt($('#mainMenuCnt').outerHeight() - $('#headerClaimCnt').outerHeight() - $('#mainMenuCnt .mainMenuUsrCnt').outerHeight() - 30);
            // $('#mainMenuScrollableCnt').css("height", parseInt(mainMenuScrollableCntHeight + 0) + "px");
            // $('#mainMenuScrollableCnt').css("overflow-y", "auto");
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
        
        $(window).resize(function(){
            // var mainMenuScrollableCntHeight = parseInt($('#mainMenuCnt').outerHeight() - $('#headerClaimCnt').outerHeight() - $('#mainMenuCnt .mainMenuUsrCnt').outerHeight() - 30);
            // Calcolo dinamico
            var mainMenuScrollableCntHeight = parseInt($('#mainMenuCnt').outerHeight() - 30 - $("#mainMenuCnt").children().not("#mainMenuScrollableCnt").toArray().reduce((tot, x) => tot + $(x).outerHeight(), 0));
            $('#mainMenuScrollableCnt').css("height", parseInt(mainMenuScrollableCntHeight + 0) + "px");
            $('#mainMenuScrollableCnt').css("overflow-y", "auto");
        });

        var searchTimeout
        $("#searchMenu").keyup(() => {
            clearTimeout(searchTimeout)
            let text =  $("#searchMenu").val().trim()
            searchTimeout = setTimeout(() => searchElement(text), 250)
        })
    });
    
    //
    $('#mainMenuSelectLanguageBtn').click(function () {
            //alert("Confirmed");
            $(".select_lang").prop("checked", false);
            //
            //Ajax su translate//
            $('#translate-modal').modal('show'); 
            
            //
        });
        
        function select_lang(lang, role){
            //$(".select_lang").prop("checked", false);
            
            $.ajax({
                async: true,
                type: 'POST',
                url: 'setlocale.php',
                data: {
                    lang: lang
                },
                success: function (data) {
                    //alert(role);
                    //location.reload();
                    if ((role === null)||(role === "")||(role === undefined)){
                        //location.search = location.search.replace(/lang=[^&$]*/i, 'lang='+lang);
                        //window.location.search = jQuery.query.set("lang", lang);
                     //window.location.search += '&lang='+lang;
                     // window.location.search;
                     // Construct URLSearchParams object instance from current URL querystring.
                    var queryParams = new URLSearchParams(window.location.search);
                    // Set new or modify existing parameter value. 
                    queryParams.set("lang",lang);
                    // Replace current querystring with the new one.
                    history.replaceState(null, null, "?"+queryParams.toString());
                    location.reload();
                }else{
                    location.reload();
                }
                    }
                });
            }

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
            $("div.mainMenuItemCntActive").removeClass("mainMenuItemCntActive")
        }
            
</script>    

