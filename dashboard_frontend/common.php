<?php
/* Dashboard Builder.
   Copyright (C) 2017 DISIT Lab https://www.disit.org - University of Florence

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

/*function buildMenuTagLegacy($linkUrl, $linkId, $parentLinkId, $openMode, $pageTitle, $externalApp, $icon, $iconColor, $text, $isOpen)
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
}   */

function buildMenuTag($linkUrl, $linkId, $parentLinkId, $openMode, $pageTitle, $externalApp, $icon, $iconColor, $text, $isOpen, $context)
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

    if (strcmp($context, 'smallBtnMySnap') == 0 || strcmp($context, 'smallBtnTour') == 0 || strcmp($context, 'btnPublicDash') == 0) {
        $prop = [
            "href" => $url,
            "id" => $linkId,
            "data-externalApp" => $externalApp,
            "data-openMode" => $openMode,
            "data-linkUrl" => $linkUrl,
            "data-pageTitle" => $pageTitle,
            "data-submenuVisible" => "false",
            "target" => $target
        ];
    } else {
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
    }

    if ($isSubmenu) {
        $prop["data-fatherMenuId"] = $parentLinkId;
    }

    $attr = implode(" ", array_map(function ($k, $v) {
        return "{$k}=\"{$v}\"";
    }, array_keys($prop), $prop));

    $containerClass = $isSubmenu ? "mainMenuSubItemCnt" : "mainMenuItemCnt";
    $displayStyle = $isOpen ? "" : 'style="display: none"';
    $subMenuCaret = $linkUrl == "submenu" ? '<i class="fa fa-chevron-down submenuIndicator"></i>' : "";
    if (strcmp($context, 'menu') == 0)
        return menuHeredoc($attr, $displayStyle, $containerClass, $iconColor, $icon, $text, $subMenuCaret);
    if (strcmp($context, 'userProfile') == 0)
        return userHeredoc($attr, $text, $subMenuCaret, $icon);
    if (strcmp($context, 'smallBtnMySnap') == 0 || strcmp($context, 'smallBtnTour') == 0 || strcmp($context, 'btnPublicDash') == 0) {
        return smallBtnHeredoc($attr, $text, $subMenuCaret, $context);
    }
}

function menuHeredoc($attr, $displayStyle, $containerClass, $iconColor, $icon, $text, $subMenuCaret)  {
    return <<<EOT
<a $attr $displayStyle>
    <div class="$containerClass" $displayStyle>
        <div class="icon-cont" style="background: $iconColor">
            <i class="$icon"></i>
        </div>
          <span>$text</span>$subMenuCaret
    </div>
</a>
EOT;
}

function userHeredoc($attr, $text, $subMenuCaret, $icon) {
    return <<<EOT
        <a $attr>
            <!-- <i class="$icon"></i   -->
            <span>$text</span>$subMenuCaret
        </a>
EOT;
}

function smallBtnHeredoc($attr, $text, $subMenuCaret, $context) {
    if (strcmp($context, 'smallBtnMySnap') == 0) {
        $attr = $attr . ' class="myS4C-btn" alt="My Snap4City"';
    }
    if (strcmp($context, 'smallBtnTour') == 0) {
        $attr = $attr . ' class="tour-btn" alt="My Snap4City"';
    }
    if (strcmp($context, 'btnPublicDash') == 0) {
        $attr = $attr . ' class="dash-public-btn" alt="Public Dashboards"';
    }
    return <<<EOT
        <a $attr>
            <span>$text</span>$subMenuCaret
        </a>
EOT;
}

function buildMenu($link, $domainId, $linkId, $context, $curr_lang) {

    if(isset($_SESSION['loggedOrganization'])) {
        $organization = $_SESSION['loggedOrganization'];
        $organizationSql = $organization;
    } else {
        $organization = "None";
        $organizationSql = "Other";
    }

    if (!$link) {
        $link = mysqli_connect($host, $username, $password);
        mysqli_select_db($link, $dbname);
        $this_mysql_conn = true;
    }

    $menuQuery = "SELECT * FROM Dashboard.MainMenu WHERE domain = $domainId AND linkId = '$linkId' ORDER BY menuOrder ASC";
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

            if (strcmp($linkId, "userprofileLink") != 0) {

                if($allowedOrgs=='*' || strpos($allowedOrgs, "'".$organizationSql) !== false || $_SESSION['loggedRole'] == 'RootAdmin') {
                    $text =  translate_string($text, $curr_lang, $link);
                    if (strcmp($linkId, "snap4cityPortalLink") == 0) {
                        $newItem = buildMenuTag($linkUrl, $linkId, null, $openMode, $pageTitle, $externalApp, $icon, $iconColor, $text, true, 'smallBtnMySnap');
                    }
                    if (strcmp($linkId, "resettour") == 0) {
                        $newItem = buildMenuTag($linkUrl, $linkId, null, $openMode, $pageTitle, $externalApp, $icon, $iconColor, $text, true, 'smallBtnTour');
                    }
                    if (strcmp($linkId, "dashboardsLink") == 0) {
                        $newItem = buildMenuTag($linkUrl, $linkId, null, $openMode, $pageTitle, $externalApp, $icon, $iconColor, $text, true, 'btnPublicDash');
                    }
                }

                if ((strpos($privileges, "'" . ($_SESSION['isPublic'] ? 'Public' : $_SESSION['loggedRole'])) !== false) && (($userType == 'any') || (($userType != 'any') && ($userType == $_SESSION['loggedType']))) && ($allowedOrgs == '*' || (strpos($allowedOrgs, "'" . $organizationSql) !== false) || $_SESSION['loggedRole'] == 'RootAdmin')) {
                    echo $newItem;
                }

            }

            if (strcmp($linkId, "userprofileLink") == 0) {
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
                        $text2 = "  " . $text2;
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
                            $isOpen = $_REQUEST['fromSubmenu'] == true && $_REQUEST['fromSubmenu'] == $linkId;
                            if (strcmp($context, "userProfile") == 0)
                                $newItem = '<li>' . buildMenuTag($linkUrl2, $linkId2, $linkId, $openMode2, $pageTitle2, $externalApp2, $icon2, $iconColor2, $text2, $isOpen, 'userProfile') . '</li>';
                        }

                        if ((strpos($privileges2, "'" . ($_SESSION['isPublic'] ? 'Public' : $_SESSION['loggedRole'])) !== false) && (($userType == 'any') || (($userType != 'any') && ($userType == $_SESSION['loggedType']))) && ($allowedOrgs2 == '*' || (strpos($allowedOrgs2, "'" . $organizationSql) !== false) || $_SESSION['loggedRole'] == 'RootAdmin')) {
                            echo $newItem;
                        }
                    }
                }
            }
        }
    }
    if ($link && $this_mysql_conn) {
        mysqli_close($link);
    }

}

function buildPlatformUrl($url, $pageTitle, $openMode)
{
    $result = $url;
    if ($openMode == "iframe") {
        $result = rawurlencode($result);
        $result = "iframeApp.php?linkUrl={$result}&pageTitle={$pageTitle}";
    } elseif ($openMode == "samePage") {
        if (strpos($result, '?') !== false) {
            $result = "{$result}&pageTitle={$pageTitle}";
        } else {
            $result = "{$result}?pageTitle={$pageTitle}";
        }
    }

    return $result;
}

function addQueryParamsToUrl($url, $queryParams)
{
    $paramsString = http_build_query($queryParams);

    $urlHasParams = parse_url($url, PHP_URL_QUERY) != '';
    if ($urlHasParams) {
        return "{$url}&{$paramsString}";
    }

    return "{$url}?{$paramsString}";
}

function checkLdapMembership($connection, $userDn, $tool, $baseDn)
{
    $result = ldap_search($connection, $baseDn, '(&(objectClass=posixGroup)(memberUid=' . $userDn . '))');
    $entries = ldap_get_entries($connection, $result);
    foreach ($entries as $key => $value) 
    {
        if(is_numeric($key)) 
        {
           if($value["cn"]["0"] == $tool) 
           {
              return true;
           }
        }
    }

    return false;
 }

function checkLdapRole($connection, $userDn, $role, $baseDn) 
 {
    $result = ldap_search($connection, $baseDn, '(&(objectClass=organizationalRole)(cn=' . $role . ')(roleOccupant=' . $userDn . '))');
    $entries = ldap_get_entries($connection, $result);
    foreach ($entries as $key => $value) 
    {
       if(is_numeric($key)) 
       { 
          if($value["cn"]["0"] == $role) 
          {
             return true;
          }
       }
    }

    return false;
}

function checkLdapOrganization($connection, $userDn, $baseDn)
{
    $result = ldap_search($connection, $baseDn, '(&(objectClass=organizationalUnit)(l=' . $userDn . '))');
  //  $result = ldap_search($connection, $baseDn, '(&(objectClass=organizationalUnit))');
    $entries = ldap_get_entries($connection, $result);
    $orgsArray = [];
    foreach ($entries as $key => $value)
    {
        if(is_numeric($key))
        {
            if($value["ou"]["0"] != '' )
            {
                array_push($orgsArray, $value["ou"]["0"]);
            }
        }
    }
    
    if (sizeof($orgsArray) > 1 || sizeof($orgsArray)==0) {
        return "DISIT";
    } else {
        return $orgsArray[0];
    }
  //  return "";
}

function checkLdapGroup($connection, $userDn, $baseDn, $userOrg)
{
//    $result = ldap_search($connection, $baseDn, '(&(objectClass=groupOfNames)(member=' . $userDn . ')(objectClass=organizationalUnit)(ou=' . $userOrg . '))');
    $result = ldap_search($connection, $baseDn, '(&(objectClass=groupOfNames)(member=' . $userDn . '))');
    $entries = ldap_get_entries($connection, $result);
    $groupsArray = [];
    foreach ($entries as $key => $value)
    {
        if(is_numeric($key))
        {
            if($value["ou"]["0"] != '' )
            {
                array_push($groupsArray, $value["ou"]["0"]);
            }
        }
    }

    if (sizeof($groupsArray) > 1) {
        return $groupsArray;
    } else {
        return "";
    }
    //  return "";
}

function checkLdapGroupUsers($connection, $userDn, $baseDn, $userOrg)
{
    $result = ldap_search($connection, $baseDn, '(&(objectClass=groupOfNames)(member='. $userOrg . '))');
    //  $result = ldap_search($connection, $baseDn, '(&(objectClass=organizationalUnit))');
    $entries = ldap_get_entries($connection, $result);
    $orgsArray = [];
    foreach ($entries as $key => $value)
    {
        if(is_numeric($key))
        {
            if($value["ou"]["0"] != '' )
            {
                array_push($orgsArray, $value["ou"]["0"]);
            }
        }
    }

    if (sizeof($orgsArray) > 1) {
        return "Other";
    } else {
        return $orgsArray[0];
    }
    //  return "";
}

function checkUserLevel($username, $host, $usernameDb, $passwordDb)
{
    $usrLevel = "none";
    $linkNew = new mysqli($host, $usernameDb, $passwordDb, "profiledb");
    //  $result = ldap_search($connection, $baseDn, '(&(objectClass=organizationalUnit))');

    $query = "SELECT * FROM iot.roles_levels WHERE username = '$username'";
    $r = mysqli_query($linkNew, $query);

    if($r)
    {
        if(mysqli_num_rows($r) > 0)
        {
            $row = mysqli_fetch_assoc($r);
            $usrLevel = $row['level'];
            return $usrLevel;
        }
        else
        {
            return $usrLevel;
        }
    }
    else
    {
        return "none";
    }

    return "";
}

function checkSession($role, $redirect = '') {
  $goodSession=true;
  if(isset($_SESSION['refreshToken'])) {
      $parts = explode(".", $_SESSION['refreshToken']);
      $claims =  json_decode(base64_decode($parts[1]));
      if(isset($claims->exp) && $claims->exp < time()) {
        //refresh token expired
        $goodSession=false;
      }
  } else if(isset($_SESSION['loggedRole'])) {
    $goodSession=false;
  }
  if(!$goodSession) {
      $f=fopen("/tmp/checkSession.log","a");
      if(isset($claims->exp))
        fwrite($f,date('c')." invalid session, expired refresh token, exp: ".($claims->exp-time())." sub: $claims->sub tkn: ".$_SESSION['refreshToken']."\n");
      else
        fwrite($f,date('c')." invalid session, no refresh token for ".$_SESSION['loggedUsername']."/".$_SESSION['loggedRole']."--- SESSION: ".var_export($_SESSION,true)." \n");
      fclose($f);    
  }
  if(!isset($_SESSION['loggedRole']) || !$goodSession) {
    $_SESSION = array();
    $_SESSION['isPublic'] = true;
    $curRole = 'Public';
    //$_SESSION['loggedRole'] = "Public";
    //$_SESSION['loggedUsername']  = "";
    //$_SESSION['loggedType']  = "-";
    //$_SESSION['loggedOrganization']  = "none";
    //$_SESSION['loggedUserLevel']  = "-";      
    $_SESSION['sessionEndTime'] = time()+24*3600;
  } else {
    $_SESSION['isPublic'] = false;
    $curRole = $_SESSION['loggedRole'];
  }
  
  //redirect to login or the specified redirect if the current role level is lower than the minimum required role level or if the session is expired
  $roles=array('Public','Observer','Manager','AreaManager','ToolAdmin','RootAdmin');
  if(!$goodSession || array_search($curRole, $roles) < array_search($role, $roles) ) {
    if(!$redirect) {
      $redirect = 'ssoLogin.php';
    }
    header("Location: $redirect");
    exit();
  }
}

function checkDashboardId($link, $id, $user = NULL) {
  if(isset($_SESSION['loggedRole']) && $_SESSION['loggedRole']=='RootAdmin')
    return true;
  
  if(!isset($user)) {
    $user = $_SESSION['loggedUsername'];
  }
  $r = mysqli_query($link, "SELECT Id FROM Dashboard.Config_dashboard WHERE Id = '$id' AND user='$user'");
  if($r && mysqli_num_rows($r)>0)
    return true;
  return false;
}

function checkWidgetName($link, $wname, $user = NULL) {
  if(isset($_SESSION['loggedRole']) && $_SESSION['loggedRole']=='RootAdmin')
    return true;
  
  if(!isset($user)) {
    $user = $_SESSION['loggedUsername'];
  }
  $result = mysqli_query($link, "SELECT COUNT(*) FROM Dashboard.Config_widget_dashboard w JOIN Dashboard.Config_dashboard d ON w.id_dashboard=d.Id WHERE user='".$_SESSION['loggedUsername']."' and name_w = '$wname'");
  if(mysqli_fetch_array($result)[0]>0) {
      return true;
  }
  return false;
}

function escapeForJS($varToBeEscaped) {
    $varEscaped1 =  preg_replace("/<\\/?script[^>]*>/i", "", $varToBeEscaped);
    $varEescaped = addslashes($varEscaped1);
    return addslashes($varEescaped);
}

function escapeForHTML($varToBeEscaped) {
    return htmlspecialchars($varToBeEscaped);
}

function escapeForSQL($varToBeEscaped, $db) {
    if (is_numeric($varToBeEscaped)) {
        return $varToBeEscaped;
    } else {
        return mysqli_real_escape_string($db, $varToBeEscaped);
    }
}

function checkVarType($varToBeChecked, $expectedType) {
    $filter = "";
    $validatedVar = "false";
    if (strcmp($expectedType, 'integer') == 0 || strcmp($expectedType, 'int') == 0) {
        $validatedVar = filter_var($varToBeChecked, FILTER_VALIDATE_INT);
    } else if (strcmp($expectedType, 'float') == 0) {
        $validatedVar = filter_var($varToBeChecked, FILTER_VALIDATE_FLOAT);
    } else if (strcmp($expectedType, 'boolean') == 0) {
        $validatedVar = filter_var($varToBeChecked, FILTER_VALIDATE_BOOLEAN);
    } else if (strcmp($expectedType, 'url') == 0) {
        $validatedVar = filter_var($varToBeChecked, FILTER_VALIDATE_URL);
    } else if (strcmp($expectedType, 'ip') == 0) {
        $validatedVar = filter_var($varToBeChecked, FILTER_VALIDATE_IP);
    } else if (strcmp($expectedType, 'email') == 0) {
        $validatedVar = filter_var($varToBeChecked, FILTER_VALIDATE_EMAIL);
    }
    return $validatedVar;
}

function checkWidgetNameInDashboard($link, $widgetName, $dashId) {
    if (checkVarType($dashId, "integer") === false) {
        eventLog("Returned the following ERROR in common.php when checking var type for for dashboard_id = ".$dashId.": ".$dashId." is not an integer as expected. Exit from script.");
        exit();
    };
    $query = "SELECT * FROM Dashboard.Config_widget_dashboard WHERE id_dashboard = '" . $dashId . "' AND name_w = '" . escapeForSQL($widgetName, $link) . "';";
    $r = mysqli_query($link, $query);

    if($r)
    {
        if(mysqli_num_rows($r) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    else
    {
        return false;
    }
}

function checkAlphaNum($entry) {
    if (ctype_alnum($entry)) {
        return true;
    } else {
        return false;
    }
}

function checkAlphaNumAndSpaces($entry) {
    if (ctype_alnum(str_replace(' ', '', $entry))) {
        return true;
    } else {
        return false;
    }
}

function getUserIP()
{
    // Get real visitor IP behind CloudFlare network
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
        $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if(filter_var($client, FILTER_VALIDATE_IP))
    {
        $ip = $client;
    }
    elseif(filter_var($forward, FILTER_VALIDATE_IP))
    {
        $ip = $forward;
    }
    else
    {
        $ip = $remote;
    }

    return $ip;
}

function eventLog($msgArray)
{
    $string="";
    // $logData['event_datetime']='['.date('D Y-m-d h:i:s A').'] [client '.$_SERVER['REMOTE_ADDR'].']';
    $logData['event_datetime']='['.date('D Y-m-d h:i:s A').'] [client '.getUserIP().'--'.$_SERVER['HTTP_USER_AGENT'].']';
    if(is_array($msgArray))
    {
        foreach($msgArray as $msg)
            $string.=$logData['event_datetime']." ".$msg."rn";
    }
    else
    {
        $string.=$logData['event_datetime']." ".$msgArray."\r\n";
    }

    $stCurLogFileName='log_'.date('Ymd').'.txt';
    $fHandler=fopen("../logs/".$stCurLogFileName,'a+');
    fwrite($fHandler,$string);
    fclose($fHandler);
}

function eventLogReq($msgArray)
{
    $string="";
    $logData['event_datetime']='['.date('D Y-m-d h:i:s A').'] [client '.$_SERVER['REMOTE_ADDR'].']';
    if(is_array($msgArray))
    {
        foreach($msgArray as $key => $value) {
            $string .= $logData['event_datetime'] . " [" . $key . "] = " . $value . "\r\n";
        }
    }
    else
    {
        $string.=$logData['event_datetime']." ".$msgArray."\r\n";
    }

    $stCurLogFileName='log_'.date('Ymd').'.txt';
    $fHandler=fopen("../logs/".$stCurLogFileName,'a+');
    fwrite($fHandler,$string);
    fclose($fHandler);
}

function sanitizeTitle($title)
{
    $titlePatterns = array();
    $titlePatterns[0] = '/_/';
    $titlePatterns[1] = '/\'/';
    $replacements = array();
    $replacements[0] = ' ';
    $replacements[1] = '&apos;';
    $title = preg_replace($titlePatterns, $replacements, $title);
    $new_title =  filter_var(html_entity_decode($title, ENT_QUOTES|ENT_HTML5), FILTER_SANITIZE_STRING);
    return $new_title;
}

function sanitizeString($reqParameter) {
    $sanitizedParam = $reqParameter;
    if (sanitizePostString($reqParameter) == null) {
        $sanitizedParam = sanitizeGetString($reqParameter);
    } else {
        $sanitizedParam = sanitizePostString($reqParameter);
    }
    return $sanitizedParam;
}

function sanitizeInt($reqParameter) {
    $sanitizedParam = $reqParameter;
    if (sanitizePostInt($reqParameter) == null) {
        $sanitizedParam = sanitizeGetInt($reqParameter);
    } else {
        $sanitizedParam = sanitizePostInt($reqParameter);
    }
    return $sanitizedParam;
}

function sanitizeFloat($reqParameter) {
    $sanitizedParam = $reqParameter;
    if (sanitizePostFloat($reqParameter) == null) {
        $sanitizedParam = sanitizeGetFloat($reqParameter);
    } else {
        $sanitizedParam = sanitizePostFloat($reqParameter);
    }
    return $sanitizedParam;
}

function sanitizePostString($var) {
    return filter_input(INPUT_POST, $var, FILTER_SANITIZE_STRING);
}

function sanitizeGetString($var) {
    return filter_input(INPUT_GET, $var, FILTER_SANITIZE_STRING);
}

function sanitizePostInt($var) {
    return filter_input(INPUT_POST, $var, FILTER_SANITIZE_NUMBER_INT);
}

function sanitizeGetInt($var) {
    return filter_input(INPUT_GET, $var, FILTER_SANITIZE_NUMBER_INT);
}

function sanitizePostFloat($var) {
    return filter_input(INPUT_POST, $var, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
}

function sanitizeGetFloat($var) {
    return filter_input(INPUT_GET, $var, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
}

function sanitizeJson($jsonVar) {
  /*  $titlePatterns = array();
    $titlePatterns[0] = '/_/';
    $titlePatterns[1] = '/\'/';
    $titlePatterns[2] = '/<\/?script>/';
    $replacements = array();
    $replacements[0] = ' ';
    $replacements[1] = '&apos;';
    $replacements[2] = '';*/
 //   $jsonVarSanitized = preg_replace($titlePatterns, $replacements, $jsonVar);
 //   $jsonVarSanitized =  preg_replace('/<\/?script>/', '', $jsonVar);
    $jsonVarSanitized = strip_tags($jsonVar);
 //   $jsonVarSanitized = html_entity_decode(filter_var($jsonVar, FILTER_SANITIZE_STRING));
    return $jsonVarSanitized;
}

function sanitizeJsonRelaxed($jsonVar) {

    $jsonVarSanitized =  preg_replace("/<\\/?script[^>]*>/", "", $jsonVar);
    return $jsonVarSanitized;
}

function sanitizeJsonRelaxed2($jsonVar) {
    $js =  addslashes(json_encode(json_decode(stripslashes($jsonVar))));
    $jsonVarSanitized =  preg_replace("/<\\/?script[^>]*>/", "", $js);
    return $jsonVarSanitized;
}

function encryptOSSL($string, $secret_key, $secret_iv, $encrypt_method) {
    $output = false;

    // hash
    $key = hash('sha256', $secret_key);

    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
    $output = base64_encode($output);


    return $output;
}

function decryptOSSL($string, $secret_key, $secret_iv, $encrypt_method) {
    $output = false;

    // hash
    $key = hash('sha256', $secret_key);

    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);

    return $output;
}

function serializeToJsonString($obj)
{
    return addslashes(json_encode($obj, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK));
}

function get_access_token($token_endpoint, $username, $password, $client_id, $client_secret = ""){

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$token_endpoint);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,
        "username=".$username."&password=".$password."&grant_type=password&client_id=".$client_id.($client_secret ? "&client_secret=$client_secret" : ""));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $curl_response = curl_exec($ch);
    curl_close($ch);
    return json_decode($curl_response)->access_token;

}

function redirect_on_login() {
    //$host='www.snap4city.org';
    $host=$_SERVER['HTTP_HOST'];
    if(isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
        $host=$_SERVER['HTTP_X_FORWARDED_HOST'];
    }
    if ($host=='localhost' || strpos($GLOBALS['appUrl'], 'http:') === 0) {
        header("Location: ../management/ssoLogin.php?redirect=http://$host" . $_SERVER['REQUEST_URI']);
    } else {
        header("Location: ../management/ssoLogin.php?redirect=https://$host" . $_SERVER['REQUEST_URI']);
    }
    exit();
}

function checkFAIcon($ico) {
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
}

function checkHost($link, $kbHost) {
    $query = "SELECT DISTINCT kbUrl FROM Dashboard.Organizations;";
    $r = mysqli_query($link, $query);

    if($r) {
        while ($row = mysqli_fetch_assoc($r)) {
            if (stripos($kbHost, $row['kbUrl']) !== false) {
                return true;
            } else {
                $stopFLag = 1;
            }
        }
        return false;
    } else {
        return false;
    }
}

function console_log($output, $with_script_tags = true) {
    $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) .
        ');';
    if ($with_script_tags) {
        $js_code = '<script>' . $js_code . '</script>';
    }
    echo $js_code;
}

function decodeHTML($html) {
    $htmlStringWithoutPTags = strip_tags($html);
    $map = array(
        '<br />' => '\n',
    //    '&nbsp;&nbsp;&nbsp;&nbsp;' => '\t',
        '&nbsp;' => ' ',
        '&quot;' => '"',
        '&#39;' => "'",
        '&amp;' => '&',
        '&lt;' => '<',
        '&gt;' => '>'
    );

    return str_replace(array_keys($map), array_values($map), $htmlStringWithoutPTags);
}

function json_validator($data) {
    if (!empty($data)) {
        return is_string($data) &&
        is_array(json_decode($data, true)) ? true : false;
    }
    return false;
}

function decodeNbsp($html) {
    $htmlStringWithoutPTags = strip_tags($html);
    return str_replace("&nbsp;", " ", $htmlStringWithoutPTags);
}