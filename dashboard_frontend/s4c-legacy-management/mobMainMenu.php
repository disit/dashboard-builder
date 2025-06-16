<i id="mobMainMenuBtn" data-shown="false" class="fa fa-navicon"></i>

<?php
    $currDom = $_SERVER['HTTP_HOST'];

    $domQ = "SELECT * FROM Dashboard.Domains WHERE domains LIKE '%$currDom%'";
    $r = mysqli_query($link, $domQ);

    if($r)
    {
        if(mysqli_num_rows($r) > 0)
        {
            $row = mysqli_fetch_assoc($r);
            $domainId = $row['id'];
        }
    }
                  
                $curr_lang ="";
              /* if (isset($_SESSION['lang'])){
                  $curr_lang =$_SESSION['lang']; 
               }*/
                $flagicon ='';
                //
                if(strpos($localizationsRoles, "Public")){
                        if (isset($_REQUEST['lang'])){
                           $curr_lang =$_REQUEST['lang']; 
                        }
                  }
                //
                  $curr_lang = selectLanguage($localizations);
                if (($curr_lang !== '')&&($curr_lang !== null)){
                    $flagicon ='../img/flagicons/'.$curr_lang.'.png';
                }else{
                    $flagicon ='../img/flagicons/en_US.png';
                }
?>

<div id="mobMainMenuCnt">
    <div id="mobMainMenuPortraitCnt">
        <div class="row">
<?php if(!$_SESSION['isPublic']) : ?>
            <div class="col-xs-12 centerWithFlex" id="mobMainMenuUsrCnt">
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
            <div class="col-xs-12 centerWithFlex" id="mobMainMenuUsrDetCnt">
                <i class="fa fa-lock" style="font-size: 20px; color: rgba(0, 162, 211, 1)"></i>&nbsp;<?php echo "Role: " . $_SESSION['loggedRole'] . ", Level: " . $_SESSION['loggedUserLevel']; ?>
            </div>
            <div class="col-xs-12 centerWithFlex" id="mobMainMenuUsrLogoutCnt">
                <button type="button" id="mobMainMenuUsrLogoutBtn" class="editDashBtn">logout</button>  
            </div>
                                                    <?php 
  if ((strpos($localizationsRoles, $_SESSION['loggedRole']))) {
    echo('<a href="#" id="mobMainMenuSelectLanguageBtn" style="font-size: 10px;"><img src="'.$flagicon.'" id="flagicon" alt="'.$_SESSION['lang'].'" style="padding:5px; height: 24px;  width: 31px;"></a>');
}
?>
   
<?php else : ?>
            <div class="col-xs-12 centerWithFlex" id="mobMainMenuUsrLogoutCnt">
                <button type="button" id="mobMainMenuUsrLoginBtn" class="editDashBtn">login</button>
                                                                <?php 
  if ((strpos($localizationsRoles, 'Public'))) {
    echo('<a href="#" id="mobMainMenuSelectLanguageBtn" style="font-size: 10px;"><img src="'.$flagicon.'" id="flagicon" alt="'.$curr_lang.'" style="padding:5px; height: 24px;  width: 31px;"></a>');
}
?>
            </div>
<?php endif; ?>         
        </div>
        <div class="col-xs-12 centerWithFlex">
            <input id="mobSearchMenu" type="text" placeholder="Search element" style="color:black; font-size:medium; margin-bottom:2.5%" autocomplete="off">
        </div>
        <hr id="porHr">
        
        <?php
        //    include 'config.php';
            if(!$_SESSION['isPublic']) {
              $ldapUsername = "cn=" . $_SESSION['loggedUsername'] . "," . $ldapBaseDN;
              $ds = ldap_connect($ldapServer, $ldapPort);
              ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
              if($ldapAdminDN)
                  $bind = ldap_bind($ds, $ldapAdminDN, $ldapAdminPwd);
              else
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
            }

            error_reporting(E_ERROR);
            date_default_timezone_set('Europe/Rome');

            $link = mysqli_connect($host, $username, $password);
            mysqli_select_db($link, $dbname);

            $menuQuery = "SELECT * FROM Dashboard.MobMainMenu WHERE domain = $domainId ORDER BY menuOrder ASC";
            $r = mysqli_query($link, $menuQuery);

            if($r)
            {
                if((mysqli_num_rows($r)%2) != 0)
                {
                    $addFiller = true;
                }
                else
                {
                    $addFiller = false;
                }
                
                while($row = mysqli_fetch_assoc($r))
                {
                    $menuItemId = $row['id'];
                    $linkUrl = ($_SESSION['isPublic'] && $row['publicLinkUrl']!=null && $row['publicLinkUrl']!='NULL' ? $row['publicLinkUrl'] : $row['linkUrl']);
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
                    //
                    $text = translate_string($text, $curr_lang, $link);
                    //

                    if($allowedOrgs=='*' || strpos($allowedOrgs, "'".$organizationSql) !== false || $_SESSION['loggedRole'] == 'RootAdmin') {
                        if($externalApp == 'yes')
                        {
                            if($openMode == 'newTab')
                            {
                                if($linkUrl == 'submenu')
                                {
                                    $newItem =  '<a href="' . $linkUrl . '" id="' . $linkId . '" data-externalApp="' . $externalApp . '" data-openMode="' . $openMode . '" data-linkUrl="' . $linkUrl . '" data-pageTitle="' . $pageTitle . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuLink" target="_blank">' .
                                                '<div class="col-xs-6 mainMenuItemCnt">' .
                                                    '<i class="' . $icon . '" style="color: ' . $iconColor . '"></i>&nbsp;&nbsp;&nbsp;' . $text . '&nbsp;&nbsp;&nbsp;<i class="fa fa-caret-down submenuIndicator" style="color: white"></i>' .
                                                '</div>' .
                                            '</a>';
                                }
                                else
                                {
                                    $newItem =  '<a href="' . $linkUrl . '" id="' . $linkId . '" data-externalApp="' . $externalApp . '" data-openMode="' . $openMode . '" data-linkUrl="' . $linkUrl . '" data-pageTitle="' . $pageTitle . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuLink" target="_blank">' .
                                                '<div class="col-xs-6 mainMenuItemCnt">' .
                                                    '<i class="' . $icon . '" style="color: ' . $iconColor . '"></i>&nbsp;&nbsp;&nbsp;' . $text .
                                                '</div>' .
                                            '</a>';
                                }
                            }
                            else
                            {
                                //CASO IFRAME
                                if($linkUrl == 'submenu')
                                {
                                    $newItem =  '<a href="' . $linkUrl . '" id="' . $linkId . '" data-externalApp="' . $externalApp . '" data-openMode="' . $openMode . '" data-linkUrl="' . $linkUrl . '" data-pageTitle="' . $pageTitle . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuLink mainMenuIframeLink">' .
                                                    '<div class="col-xs-6 mainMenuItemCnt">' .
                                                        '<i class="' . $icon . '" style="color: ' . $iconColor . '"></i>&nbsp;&nbsp;&nbsp;' . $text . '&nbsp;&nbsp;&nbsp;<i class="fa fa-caret-down submenuIndicator" style="color: white"></i>' .
                                                    '</div>' .
                                                '</a>';
                                }
                                else
                                {
                                    $newItem =  '<a href="' . $linkUrl . '" id="' . $linkId . '" data-externalApp="' . $externalApp . '" data-openMode="' . $openMode . '" data-linkUrl="' . $linkUrl . '" data-pageTitle="' . $pageTitle . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuLink mainMenuIframeLink">' .
                                                    '<div class="col-xs-6 mainMenuItemCnt">' .
                                                        '<i class="' . $icon . '" style="color: ' . $iconColor . '"></i>&nbsp;&nbsp;&nbsp;' . $text .
                                                    '</div>' .
                                                '</a>';
                                }
                            }
                        }
                        else
                        {
                            if($linkUrl == 'submenu')
                            {
                                $newItem =  '<a href="' . $linkUrl . '" id="' . $linkId . '" data-externalApp="' . $externalApp . '" data-openMode="' . $openMode . '" data-linkUrl="' . $linkUrl . '" data-pageTitle="' . $pageTitle . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuLink">' .
                                                '<div class="col-xs-6 mainMenuItemCnt">' .
                                                    '<i class="' . $icon . '" style="color: ' . $iconColor . '"></i>&nbsp;&nbsp;&nbsp;' . $text . '&nbsp;&nbsp;&nbsp;<i class="fa fa-caret-down submenuIndicator" style="color: white"></i>' .
                                                '</div>' .
                                            '</a>';
                            }
                            else
                            {
                                $newItem =  '<a href="' . $linkUrl . '" id="' . $linkId . '" data-externalApp="' . $externalApp . '" data-openMode="' . $openMode . '" data-linkUrl="' . $linkUrl . '" data-pageTitle="' . $pageTitle . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuLink">' .
                                                '<div class="col-xs-6 mainMenuItemCnt">' .
                                                    '<i class="' . $icon . '" style="color: ' . $iconColor . '"></i>&nbsp;&nbsp;&nbsp;' . $text .
                                                '</div>' .
                                            '</a>';
                            }
                        }
                    }

                    if((strpos($privileges, "'".($_SESSION['isPublic'] ? 'Public' : $_SESSION['loggedRole'])) !== false)&&(($userType == 'any')||(($userType != 'any') && ($userType == $_SESSION['loggedType']))) && ($allowedOrgs=='*' || (strpos($allowedOrgs, "'".$organizationSql) !== false) || $_SESSION['loggedRole'] == 'RootAdmin'))
                    {
                        echo $newItem;
                    }
                }//Fine popolamento main menu
                
                if($addFiller)
                {
                    $newItem = '<div class="col-xs-6 mainMenuItemCnt"></div>';
                    echo $newItem;
                    //Comment
                }
                
                $uname = isset($_SESSION['loggedUsername']) ? $_SESSION['loggedUsername'] : '';

                $submenuQuery = "SELECT * FROM Dashboard.MobMainMenuSubmenus s LEFT JOIN Dashboard.MainMenuSubmenusUser u ON u.submenu=s.id WHERE menu IN (SELECT id FROM Dashboard.MobMainMenu WHERE domain = $domainId) AND (user is NULL OR user='$uname') ORDER BY menu, menuOrder ASC";
                $r2 = mysqli_query($link, $submenuQuery);

                if($r2)
                {
                    while($row2 = mysqli_fetch_assoc($r2))
                    {
                        $menuItemId2 = $row2['id'];
                        $linkUrl2 = $row2['linkUrl'];
                        $fatherMenuDbId = $row2['menu'];

                        if($linkUrl2 == 'submenu')
                        {
                            $linkUrl2 = '#';
                        }
                        
                        $q3 = "SELECT * FROM Dashboard.MobMainMenu WHERE id = $fatherMenuDbId LIMIT 1";
                        $r3 = mysqli_query($link, $q3);
                        
                        $row3 = mysqli_fetch_assoc($r3);
                        $fatherMenuId = $row3['linkId']; 
                        
                        $linkId2 = $row2['linkId'];
                        $icon2 = $row2['icon'];  
                        $text2 = $row2['text'];
                         $text2 = translate_string($text2, $curr_lang, $link);
                        $privileges2 = $row2['privileges'];      
                        $userType2 = $row2['userType']; 
                        $externalApp2 = $row2['externalApp'];
                        $openMode2 = $row2['openMode'];
                        $iconColor2 = $row2['iconColor'];
                        $pageTitle2 = $row2['pageTitle'];
                        $externalApp2 = $row2['externalApp'];
                        $allowedOrgs2 = $row2['organizations'];
                        if ($fatherMenuDbId == 1059 || $allowedOrgs2 != "['Firenze', 'Helsinki', 'Antwerp', 'Disit', 'Other']") {
                            $stopFlag = 1;
                        }

                        if($allowedOrgs2=='*' || strpos($allowedOrgs2, "'".$organizationSql) !== false || $_SESSION['loggedRole'] == 'RootAdmin') {
                            if($externalApp2 == 'yes')
                            {
                                if($openMode2 == 'newTab')
                                {
                                    $newItem =  '<a href="' . $linkUrl2 . '" id="' . $linkId2 . '" data-fatherMenuId="' . $fatherMenuId . '" data-externalApp="' . $externalApp2 . '" data-openMode="' . $openMode2 . '" data-linkUrl="' . $linkUrl2 . '" data-pageTitle="' . $pageTitle2 . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuSubItemLink" target="_blank">' .
                                                    '<div class="col-xs-6 mainMenuSubItemCnt">' .
                                                        '<i class="' . $icon2 . '" style="color: ' . $iconColor2 . '"></i>&nbsp;&nbsp;&nbsp;' . $text .
                                                    '</div>' .
                                                '</a>';
                                }
                                else
                                {
                                    //CASO IFRAME
                                    $newItem =  '<a href="' . $linkUrl2 . '" id="' . $linkId2 . '" data-fatherMenuId="' . $fatherMenuId . '" data-externalApp="' . $externalApp2 . '" data-openMode="' . $openMode2 . '" data-linkUrl="' . $linkUrl2 . '" data-pageTitle="' . $pageTitle2 . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuSubItemLink mainMenuIframeLink">' .
                                                    '<div class="col-xs-6 mainMenuSubItemCnt">' .
                                                        '<i class="' . $icon2 . '" style="color: ' . $iconColor2 . '"></i>&nbsp;&nbsp;&nbsp;' . $text2 .
                                                    '</div>' .
                                                '</a>';
                                }
                            }
                            else
                            {
                                $newItem =  '<a href="' . $linkUrl2 . '" id="' . $linkId2 . '" data-fatherMenuId="' . $fatherMenuId . '" data-externalApp="' . $externalApp2 . '" data-openMode="' . $openMode2 . '" data-linkUrl="' . $linkUrl2 . '" data-pageTitle="' . $pageTitle2 . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuSubItemLink">' .
                                                '<div class="col-xs-6 mainMenuSubItemCnt">' .
                                                    '<i class="' . $icon2 . '" style="color: ' . $iconColor2 . '"></i>&nbsp;&nbsp;&nbsp;' . $text2 .
                                                '</div>' .
                                            '</a>';
                            }
                        }

                        if((strpos($privileges2, "'".@($_SESSION['isPublic'] ? 'Public' : $_SESSION['loggedRole']."'")) !== false) && (($userType == 'any')||(($userType != 'any')&&($userType == $_SESSION['loggedType']))) && ($allowedOrgs2=='*' || (strpos($allowedOrgs2, "'".$organizationSql) !== false || $_SESSION['loggedRole'] == 'RootAdmin')))
                        {
                            echo $newItem;
                        }
                    }
                    
                    
                }
            }
        ?>
        
    </div><!-- Fine portrait container -->
 
    <div id="mobMainMenuLandCnt">
         <div class="row">
<?php if(!$_SESSION['isPublic']) : ?>
            <div class="col-xs-5 centerWithFlex" id="mobMainMenuUsrCnt">
                <?php echo "User: " . $_SESSION['loggedUsername'] . ", Org: " . @$_SESSION['loggedOrganization']; ?>
            </div>
            <div class="col-xs-5 centerWithFlex" id="mobMainMenuUsrDetCnt">
                <i class="fa fa-lock" style="font-size: 20px; color: rgba(0, 162, 211, 1)"></i>&nbsp;<?php echo "Role: " . $_SESSION['loggedRole'] . ", Level: " . $_SESSION['loggedUserLevel']; ?>
            </div>
            <div class="col-xs-2 centerWithFlex" id="mobMainMenuUsrLogoutCnt">
                <button type="button" id="mobMainMenuUsrLogoutBtn" class="editDashBtn">logout</button>
            </div>
<?php else : ?>
            <div class="col-xs-12 centerWithFlex" id="mobMainMenuUsrLogoutCnt">
                <button type="button" id="mobMainMenuUsrLoginBtn" class="editDashBtn">login</button>
            </div>
<?php endif; ?>         
       </div>
        <hr id="landHr">
        
        <?php
            include 'config.php';

            error_reporting(E_ERROR);
            date_default_timezone_set('Europe/Rome');

            if($_SESSION['loggedRole']!='Public') { //CHECK
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
            }

            $link = mysqli_connect($host, $username, $password);
            mysqli_select_db($link, $dbname);

            // $menuQuery = "SELECT * FROM Dashboard.MobMainMenu ORDER BY id ASC";
            $menuQuery = "SELECT * FROM Dashboard.MobMainMenu WHERE domain = $domainId ORDER BY menuOrder ASC";

            $r = mysqli_query($link, $menuQuery);

            if($r)
            {
                if((mysqli_num_rows($r)%3) != 0)
                {
                    $addFiller = true;
                }
                else
                {
                    $addFiller = false;
                }
                
                while($row = mysqli_fetch_assoc($r))
                {
                    $menuItemId = $row['id'];
                    $linkUrl = ($_SESSION['isPublic'] && $row['publicLinkUrl']!=null && $row['publicLinkUrl']!='NULL' ? $row['publicLinkUrl'] : $row['linkUrl']);
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
                    $text =  translate_string($text, $curr_lang, $link);
                    // if($allowedOrgs=='*' || strpos($allowedOrgs, "'".$organizationSql) !== false) {
                    if($allowedOrgs=='*' || strpos($allowedOrgs, "'".$organizationSql) !== false || $_SESSION['loggedRole'] == 'RootAdmin') {
                        if ($externalApp == 'yes') {
                            if ($openMode == 'newTab') {
                                if ($linkUrl == 'submenu') {
                                    $newItem = '<a href="' . $linkUrl . '" id="' . $linkId . '" data-externalApp="' . $externalApp . '" data-openMode="' . $openMode . '" data-linkUrl="' . $linkUrl . '" data-pageTitle="' . $pageTitle . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuLink" target="_blank">' .
                                        '<div class="col-xs-4 mainMenuItemCnt">' .
                                        '<i class="' . $icon . '" style="color: ' . $iconColor . '"></i>&nbsp;&nbsp;&nbsp;' . $text . '&nbsp;&nbsp;&nbsp;<i class="fa fa-caret-down submenuIndicator" style="color: white"></i>' .
                                        '</div>' .
                                        '</a>';
                                } else {
                                    $newItem = '<a href="' . $linkUrl . '" id="' . $linkId . '" data-externalApp="' . $externalApp . '" data-openMode="' . $openMode . '" data-linkUrl="' . $linkUrl . '" data-pageTitle="' . $pageTitle . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuLink" target="_blank">' .
                                        '<div class="col-xs-4 mainMenuItemCnt">' .
                                        '<i class="' . $icon . '" style="color: ' . $iconColor . '"></i>&nbsp;&nbsp;&nbsp;' . $text .
                                        '</div>' .
                                        '</a>';
                                }
                            } else {
                                //CASO IFRAME
                                if ($linkUrl == 'submenu') {
                                    $newItem = '<a href="' . $linkUrl . '" id="' . $linkId . '" data-externalApp="' . $externalApp . '" data-openMode="' . $openMode . '" data-linkUrl="' . $linkUrl . '" data-pageTitle="' . $pageTitle . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuLink mainMenuIframeLink">' .
                                        '<div class="col-xs-4 mainMenuItemCnt">' .
                                        '<i class="' . $icon . '" style="color: ' . $iconColor . '"></i>&nbsp;&nbsp;&nbsp;' . $text . '&nbsp;&nbsp;&nbsp;<i class="fa fa-caret-down submenuIndicator" style="color: white"></i>' .
                                        '</div>' .
                                        '</a>';
                                } else {
                                    $newItem = '<a href="' . $linkUrl . '" id="' . $linkId . '" data-externalApp="' . $externalApp . '" data-openMode="' . $openMode . '" data-linkUrl="' . $linkUrl . '" data-pageTitle="' . $pageTitle . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuLink mainMenuIframeLink">' .
                                        '<div class="col-xs-4 mainMenuItemCnt">' .
                                        '<i class="' . $icon . '" style="color: ' . $iconColor . '"></i>&nbsp;&nbsp;&nbsp;' . $text .
                                        '</div>' .
                                        '</a>';
                                }
                            }
                        } else {
                            if ($linkUrl == 'submenu') {
                                $newItem = '<a href="' . $linkUrl . '" id="' . $linkId . '" data-externalApp="' . $externalApp . '" data-openMode="' . $openMode . '" data-linkUrl="' . $linkUrl . '" data-pageTitle="' . $pageTitle . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuLink">' .
                                    '<div class="col-xs-4 mainMenuItemCnt">' .
                                    '<i class="' . $icon . '" style="color: ' . $iconColor . '"></i>&nbsp;&nbsp;&nbsp;' . $text . '&nbsp;&nbsp;&nbsp;<i class="fa fa-caret-down submenuIndicator" style="color: white"></i>' .
                                    '</div>' .
                                    '</a>';
                            } else {
                                $newItem = '<a href="' . $linkUrl . '" id="' . $linkId . '" data-externalApp="' . $externalApp . '" data-openMode="' . $openMode . '" data-linkUrl="' . $linkUrl . '" data-pageTitle="' . $pageTitle . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuLink">' .
                                    '<div class="col-xs-4 mainMenuItemCnt">' .
                                    '<i class="' . $icon . '" style="color: ' . $iconColor . '"></i>&nbsp;&nbsp;&nbsp;' . $text .
                                    '</div>' .
                                    '</a>';
                            }
                        }
                    }

                    // if((strpos($privileges, "'".$_SESSION['loggedRole']) !== false)&&(($userType == 'any')||(($userType != 'any') && ($userType == $_SESSION['loggedType'])))  && ($allowedOrgs=='*' || strpos($allowedOrgs, "'".$organizationSql) !== false))
                    if((strpos($privileges, "'".($_SESSION['isPublic'] ? 'Public' : $_SESSION['loggedRole'])) !== false)&&(($userType == 'any')||(($userType != 'any') && ($userType == $_SESSION['loggedType']))) && ($allowedOrgs=='*' || (strpos($allowedOrgs, "'".$organizationSql) !== false) || $_SESSION['loggedRole'] == 'RootAdmin'))
                    {
                        echo $newItem;
                    }
                }//Fine popolamento main menu
                
                if($addFiller)
                {
                    $newItem = '<div class="col-xs-4 mainMenuItemCnt"></div>';
                    echo $newItem;     
                }

                // $submenuQuery = "SELECT * FROM Dashboard.MobMainMenuSubmenus ORDER BY id, menu ASC";
                $uname = isset($_SESSION['loggedUsername']) ? $_SESSION['loggedUsername'] : '';

                $submenuQuery = "SELECT * FROM Dashboard.MobMainMenuSubmenus s LEFT JOIN Dashboard.MainMenuSubmenusUser u ON u.submenu=s.id WHERE menu IN (SELECT id FROM Dashboard.MobMainMenu WHERE domain = $domainId) AND (user is NULL OR user='$uname') ORDER BY menu, menuOrder ASC";
                $r2 = mysqli_query($link, $submenuQuery);

                if($r2)
                {
                    while($row2 = mysqli_fetch_assoc($r2))
                    {
                        $menuItemId2 = $row2['id'];
                        $linkUrl2 = $row2['linkUrl'];
                        $fatherMenuDbId = $row2['menu'];

                        if($linkUrl2 == 'submenu')
                        {
                            $linkUrl2 = '#';
                        }
                        
                        $q3 = "SELECT * FROM Dashboard.MobMainMenu WHERE id = $fatherMenuDbId LIMIT 1";
                        $r3 = mysqli_query($link, $q3);
                        
                        $row3 = mysqli_fetch_assoc($r3);
                        $fatherMenuId = $row3['linkId']; 
                        
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
                        //
                        $text2 = translate_string($text2, $curr_lang, $link);

                        // if($allowedOrgs2=='*' || strpos($allowedOrgs2, "'".$organizationSql) !== false) {
                        if($allowedOrgs2=='*' || strpos($allowedOrgs2, "'".$organizationSql) !== false || $_SESSION['loggedRole'] == 'RootAdmin') {
                            if ($externalApp2 == 'yes') {
                                if ($openMode2 == 'newTab') {
                                    $newItem = '<a href="' . $linkUrl2 . '" id="' . $linkId2 . '" data-fatherMenuId="' . $fatherMenuId . '" data-externalApp="' . $externalApp2 . '" data-openMode="' . $openMode2 . '" data-linkUrl="' . $linkUrl2 . '" data-pageTitle="' . $pageTitle2 . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuSubItemLink" target="_blank">' .
                                        '<div class="col-xs-4 mainMenuSubItemCnt">' .
                                        '<i class="' . $icon2 . '" style="color: ' . $iconColor2 . '"></i>&nbsp;&nbsp;&nbsp;' . $text .
                                        '</div>' .
                                        '</a>';
                                } else {
                                    //CASO IFRAME
                                    $newItem = '<a href="' . $linkUrl2 . '" id="' . $linkId2 . '" data-fatherMenuId="' . $fatherMenuId . '" data-externalApp="' . $externalApp2 . '" data-openMode="' . $openMode2 . '" data-linkUrl="' . $linkUrl2 . '" data-pageTitle="' . $pageTitle2 . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuSubItemLink mainMenuIframeLink">' .
                                        '<div class="col-xs-4 mainMenuSubItemCnt">' .
                                        '<i class="' . $icon2 . '" style="color: ' . $iconColor2 . '"></i>&nbsp;&nbsp;&nbsp;' . $text2 .
                                        '</div>' .
                                        '</a>';
                                }
                            } else {
                                $newItem = '<a href="' . $linkUrl2 . '" id="' . $linkId2 . '" data-fatherMenuId="' . $fatherMenuId . '" data-externalApp="' . $externalApp2 . '" data-openMode="' . $openMode2 . '" data-linkUrl="' . $linkUrl2 . '" data-pageTitle="' . $pageTitle2 . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuSubItemLink">' .
                                    '<div class="col-xs-4 mainMenuSubItemCnt">' .
                                    '<i class="' . $icon2 . '" style="color: ' . $iconColor2 . '"></i>&nbsp;&nbsp;&nbsp;' . $text2 .
                                    '</div>' .
                                    '</a>';
                            }
                        }

                        // if((strpos($privileges2, "'".($_SESSION['isPublic'] ? 'Public' : $_SESSION['loggedRole'])) !== false) && (($userType == 'any')||(($userType != 'any')&&($userType == $_SESSION['loggedType'])))  && ($allowedOrgs2=='*' || strpos($allowedOrgs2, "'".$organizationSql) !== false))
                        if((strpos($privileges2, "'".@($_SESSION['isPublic'] ? 'Public' : $_SESSION['loggedRole']."'")) !== false) && (($userType == 'any')||(($userType != 'any')&&($userType == $_SESSION['loggedType']))) && ($allowedOrgs2=='*' || (strpos($allowedOrgs2, "'".$organizationSql) !== false || $_SESSION['loggedRole'] == 'RootAdmin')))
                        {
                            echo $newItem;
                        }
                    }
                }
            }
        ?>
    </div>  
</div>
<style>
    .mobMenuFilteredOut{display:none !important}
</style>
<script type='text/javascript'>
    $(document).ready(function () 
    {

        var mobSearchTimeout
        $("#mobSearchMenu").keyup(() => {
            clearTimeout(mobSearchTimeout)
            let text =  $("#mobSearchMenu").val().trim()
            mobSearchTimeout = setTimeout(() => mobSearchElement(text), 250)
        })

        $('#mobMainMenuCnt').css("top", parseInt($('#mobHeaderClaimCnt').height() + $('#headerMenuCnt').height()) + "px");
        
        $( window ).on( "orientationchange", function( event ) {
            if($('#mobMainMenuCnt').is(':visible'))
            {
                if($(window).width() < $(window).height())
                {
                    $('#mobMainMenuPortraitCnt').hide();
                    $('#mobMainMenuLandCnt').show();
                }
                else
                {
                    $('#mobMainMenuLandCnt').hide();
                    $('#mobMainMenuPortraitCnt').show();
                }
            }
        });

        $('#mobHeaderClaimCnt').hover(function(){
            $(this).css("cursor", "pointer");
        });

        $('#mobHeaderClaimCnt').click(function(){
            var hostHomeUrl = window.location.hostname;
            if (hostHomeUrl.includes("localhost")) {
                hostHomeUrl = hostHomeUrl + "/dashboardSmartCity/";
            }
            window.open("//" + hostHomeUrl,"_self");
          //  window.open("https://www.snap4city.org/drupal/node/1");
        });

        $('#mobMainMenuBtn').click(function(){
            if($('#mobMainMenuBtn').attr("data-shown") === "false")
            {
                $('#mobMainMenuCnt').show();
                if($(window).width() < $(window).height())
                {
                    $('#mobMainMenuLandCnt').hide();
                    $('#mobMainMenuPortraitCnt').show();
                }
                else
                {
                    $('#mobMainMenuPortraitCnt').hide();
                    $('#mobMainMenuLandCnt').show();
                }
                
                $('#mobMainMenuBtn').attr("data-shown", "true");
                setTimeout(function(){
                    $('#mobMainMenuCnt').css("opacity", "1");
                }, 50);
            }
            else
            {
                $('#mobMainMenuCnt').css("opacity", "0");
                $('#mobMainMenuBtn').attr("data-shown", "false");
                setTimeout(function(){
                    $('#mobMainMenuCnt').hide();
                }, 350);
            }
        });
        var logoutLogin = function() {
<?php if($_SESSION['isPublic']) : ?>
            location.href = "ssoLogin.php";
<?php else : ?>
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
<?php endif; ?>          
        }
        $('#mobMainMenuPortraitCnt #mobMainMenuUsrLogoutBtn').click(logoutLogin);
        
        $('#mobMainMenuLandCnt #mobMainMenuUsrLogoutBtn').click(logoutLogin);

        $('#mobMainMenuPortraitCnt #mobMainMenuUsrLoginBtn').click(logoutLogin);

        $('#mobMainMenuLandCnt #mobMainMenuUsrLoginBtn').click(logoutLogin);
        
        $('#mobMainMenuPortraitCnt a.mainMenuLink').attr('data-submenuVisible', 'false');
        $('#mobMainMenuPortraitCnt a.mainMenuSubItemLink').hide();
        $('#mobMainMenuLandCnt a.mainMenuLink').attr('data-submenuVisible', 'false');
        $('#mobMainMenuLandCnt a.mainMenuSubItemLink').hide();
        
        $('#mobMainMenuPortraitCnt .mainMenuLink').click(function(event)
        {
            event.preventDefault();
            var pageTitle = $(this).attr('data-pageTitle');
            var linkId = $(this).attr('id');
            var linkUrl = $(this).attr('data-linkUrl');
            
            if($(this).attr('data-linkUrl') === 'submenu')
            {
                $('#mobMainMenuPortraitCnt .mainMenuItemCnt').each(function(i){
                    $(this).removeClass('mainMenuItemCntActive');
                });
                $(this).find('div.mainMenuItemCnt').addClass("mainMenuItemCntActive");
                if($(this).attr('data-submenuVisible') === 'false')
                {
                    $(this).attr('data-submenuVisible', 'true');
                    $('.mainMenuSubItemCnt').css( "display", "block" );
                    $('#mobMainMenuPortraitCnt a.mainMenuSubItemLink[data-fatherMenuId=' + $(this).attr('id') + ']').show();
                    $(this).find('.submenuIndicator').removeClass('fa-caret-down');
                    $(this).find('.submenuIndicator').addClass('fa-caret-up');
                }
                else
                {
                    $(this).attr('data-submenuVisible', 'false');
                    $('#mobMainMenuPortraitCnt a.mainMenuSubItemLink[data-fatherMenuId=' + $(this).attr('id') + ']').hide();
                    $(this).find('.submenuIndicator').removeClass('fa-caret-up');
                    $(this).find('.submenuIndicator').addClass('fa-caret-down');
                }
            }
            else
            {
                $('#mobMainMenuPortraitCnt a.mainMenuSubItemLink').hide();
            
                $('#mobMainMenuPortraitCnt a.mainMenuSubItemLink').each(function(i){
                    $(this).attr('data-submenuVisible', 'false');
                });
                switch($(this).attr('data-openMode'))
                {
                    case "iframe":
                        $('#mobMainMenuPortraitCnt .mainMenuItemCnt').each(function(i){
                            $(this).removeClass('mainMenuItemCntActive');
                        });
                        $(this).find('div.mainMenuItemCnt').addClass("mainMenuItemCntActive");
                        if($(this).attr('data-externalApp') === 'yes')
                        {
                            console.log("Link id iframe: " + linkId);
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
                        $('#mobMainMenuPortraitCnt .mainMenuItemCnt').each(function(i){
                            $(this).removeClass('mainMenuItemCntActive');
                        });
                        
                        console.log("Link id samePage: " + linkId);
                        
                        $(this).find('div.mainMenuItemCnt').addClass("mainMenuItemCntActive");
                        // GP COMMENT TEMPORARY
                        location.href = $(this).attr('data-linkurl') + "?linkId=" + linkId + "&fromSubmenu=false&pageTitle=" + pageTitle;
                        // GP UNCOMMENT TEMPORARY
                     //   location.href = $(this).attr('data-linkurl') + "?linkId=" + linkId + "&fromSubmenu=false";
                        break;    
                }
            }
            
        });
        
        $('#mobMainMenuPortraitCnt .mainMenuSubItemLink').click(function(event){
            event.preventDefault();
            var pageTitle = $(this).attr('data-pageTitle');
            var linkId = $(this).attr('id');
            var linkUrl = $(this).attr('data-linkUrl');
            var submenuId = $(this).attr('data-fathermenuid');
            
            switch($(this).attr('data-openMode'))
            {
                case "iframe":
                    $('#mobMainMenuPortraitCnt a.mainMenuSubItemLink[data-fathermenuid=' + submenuId + '] .mainMenuSubItemCnt').removeClass('mainMenuItemCntActive');
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
                    $('#mobMainMenuPortraitCnt a.mainMenuSubItemLink[data-fathermenuid=' + submenuId + '] .mainMenuSubItemCnt').removeClass('mainMenuItemCntActive');
                    $(this).find('div.mainMenuSubItemCnt').addClass("mainMenuItemCntActive");
                    // GP COMMENT TEMPORARY
                    location.href = $(this).attr('data-linkurl') + "?linkId=" + linkId + "&fromSubmenu=" + submenuId + "&pageTitle=" + pageTitle;
                    // GP UNCOMMENT TEMPORARY
                //    location.href = $(this).attr('data-linkurl') + "?linkId=" + linkId + "&fromSubmenu=" + submenuId;
                    break;    
            }
        });
        
        $('#mobMainMenuLandCnt .mainMenuLink').click(function(event)
        {
            event.preventDefault();
            var pageTitle = $(this).attr('data-pageTitle');
            var linkId = $(this).attr('id');
            var linkUrl = $(this).attr('data-linkUrl');
            
            if($(this).attr('data-linkUrl') === 'submenu')
            {
                $('#mobMainMenuLandCnt .mainMenuItemCnt').each(function(i){
                    $(this).removeClass('mainMenuItemCntActive');
                });
                $(this).find('div.mainMenuItemCnt').addClass("mainMenuItemCntActive");
                if($(this).attr('data-submenuVisible') === 'false')
                {
                    $(this).attr('data-submenuVisible', 'true');
                    $('.mainMenuSubItemCnt').css( "display", "block" );
                    $('#mobMainMenuLandCnt a.mainMenuSubItemLink[data-fatherMenuId=' + $(this).attr('id') + ']').show();
                    $(this).find('.submenuIndicator').removeClass('fa-caret-down');
                    $(this).find('.submenuIndicator').addClass('fa-caret-up');
                }
                else
                {
                    $(this).attr('data-submenuVisible', 'false');
                    $('#mobMainMenuLandCnt a.mainMenuSubItemLink[data-fatherMenuId=' + $(this).attr('id') + ']').hide();
                    $(this).find('.submenuIndicator').removeClass('fa-caret-up');
                    $(this).find('.submenuIndicator').addClass('fa-caret-down');
                }
            }
            else
            {
                $('#mobMainMenuLandCnt a.mainMenuSubItemLink').hide();
            
                $('#mobMainMenuLandCnt a.mainMenuSubItemLink').each(function(i){
                    $(this).attr('data-submenuVisible', 'false');
                });
                switch($(this).attr('data-openMode'))
                {
                    case "iframe":
                        $('#mobMainMenuLandCnt .mainMenuItemCnt').each(function(i){
                            $(this).removeClass('mainMenuItemCntActive');
                        });
                        $(this).find('div.mainMenuItemCnt').addClass("mainMenuItemCntActive");
                        if($(this).attr('data-externalApp') === 'yes')
                        {
                            console.log("Link id iframe: " + linkId);
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
                        $('#mobMainMenuLandCnt .mainMenuItemCnt').each(function(i){
                            $(this).removeClass('mainMenuItemCntActive');
                        });
                        
                        console.log("Link id samePage: " + linkId);
                        
                        $(this).find('div.mainMenuItemCnt').addClass("mainMenuItemCntActive");
                        // GP COMMENT TEMPORARY
                        location.href = $(this).attr('data-linkurl') + "?linkId=" + linkId + "&fromSubmenu=false&pageTitle=" + pageTitle;
                        // GP UNCOMMENT TEMPORARY
                     //   location.href = $(this).attr('data-linkurl') + "?linkId=" + linkId + "&fromSubmenu=false";
                        break;    
                }
            }
            
        });
        
        $('#mobMainMenuLandCnt .mainMenuSubItemLink').click(function(event){
            event.preventDefault();
            var pageTitle = $(this).attr('data-pageTitle');
            var linkId = $(this).attr('id');
            var linkUrl = $(this).attr('data-linkUrl');
            var submenuId = $(this).attr('data-fathermenuid');
            
            switch($(this).attr('data-openMode'))
            {
                case "iframe":
                    $('#mobMainMenuLandCnt a.mainMenuSubItemLink[data-fathermenuid=' + submenuId + '] .mainMenuSubItemCnt').removeClass('mainMenuItemCntActive');
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
                    $('#mobMainMenuLandCnt a.mainMenuSubItemLink[data-fathermenuid=' + submenuId + '] .mainMenuSubItemCnt').removeClass('mainMenuItemCntActive');
                    $(this).find('div.mainMenuSubItemCnt').addClass("mainMenuItemCntActive");
                    // GP UNCOMMENT TEMPORARY
                    location.href = $(this).attr('data-linkurl') + "?linkId=" + linkId + "&fromSubmenu=" + submenuId + "&pageTitle=" + pageTitle;
                    // GP UNCOMMENT TEMPORARY
                    //   location.href = $(this).attr('data-linkurl') + "?linkId=" + linkId + "&fromSubmenu=" + submenuId;
                    break;    
            }
        });
    });
    
        
    //
    $('#mobMainMenuSelectLanguageBtn').click(function () {
            //alert("Confir#mainMenuSelectLanguageBtnmed");
            $(".select_lang").prop("checked", false);
            //
            //Ajax su translate//
            $('#mobMenutranslate-modal').modal('show'); 
            
            //
        });
        
        /*function select_lang(lang){
            //$(".select_lang").prop("checked", false);
            
            $.ajax({
                async: true,
                type: 'POST',
                url: 'setlocale.php',
                data: {
                    lang: lang
                },
                success: function (data) {
                    //alert(lang);
                    location.reload();
                    }
                });
            }*/
                                            
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

            function mobSearchElement(text=""){
                $(".mobMenuFilteredOut").removeClass("mobMenuFilteredOut")
                $("#mobMainMenuPortraitCnt .mainMenuLink[data-submenuvisible=true]").click()
                if(text.trim() != ""){
                    $("#mobMainMenuPortraitCnt .mainMenuLink[data-linkurl!=submenu] div").toArray().filter(x => !x.innerText.toLowerCase().includes(text.toLowerCase())).forEach(x => $(x).parents(".mainMenuLink").addClass("mobMenuFilteredOut"))
                    $("#mobMainMenuPortraitCnt .mainMenuLink[data-linkurl=submenu], #mobMainMenuPortraitCnt .mainMenuSubItemLink").addClass("mobMenuFilteredOut")
                    var toShowLinks = []
                    $("#mobMainMenuPortraitCnt .mainMenuSubItemCnt").toArray().filter(x => x.innerText.toLowerCase().includes(text.toLowerCase())).forEach(x => {
                        let fatherID = $(x).parents(".mainMenuSubItemLink").removeClass("mobMenuFilteredOut").attr("data-fathermenuid")
                        let mainMenuLink = $(`#mobMainMenuPortraitCnt .mainMenuLink#${fatherID}[data-linkurl=submenu]`)[0]
                        if(mainMenuLink != undefined)
                            toShowLinks.push(mainMenuLink)
                    })
                    $([... new Set(toShowLinks)]).click().removeClass("mobMenuFilteredOut")
                }
                $("div.mainMenuItemCntActive").removeClass("mainMenuItemCntActive")
            }
            
</script>    

<?php 
if ((strpos($localizationsRoles, $_SESSION['loggedRole']))||(strpos($localizationsRoles, 'Public'))) {
   $obj = json_decode($localizations, true);
   $languages = $obj['languages'];
   $tot_leng = count($languages);
   if ($tot_leng > 0){
       echo ('<div id="mobMenutranslate-modal" class="modal fade bd-example-modal-sm" role="dialog" style="z-index: 20050 !important;">
        <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
        <div class="modal-body">
            <button type="button" class="close" data-dismiss="modal">&times;</button>');
     for($i=0; $i<$tot_leng; $i++){
         $lang = $obj['languages'][$i];
         echo('<ul><input class="form-check-input select_lang" type="checkbox" onclick="select_lang(\''.$lang['code'].'\',\''.$_SESSION['loggedRole'].'\')"> <img src="../img/flagicons/'.$lang['code'].'.png" alt="alternatetext" style="padding:5px; height: 24px; width: 31px;"> <span style="color: black; font-size: 14px;">'.$lang['lang'].'</span></ul>');
     }  
      echo ('</div>                      
    </div>
  </div>
    </div>');
   }
}

?>