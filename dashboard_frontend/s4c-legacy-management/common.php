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

function buildMenuTag($linkUrl, $linkId, $parentLinkId, $openMode, $pageTitle, $externalApp, $icon, $iconColor, $text, $isOpen)
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

function get_access_token($token_endpoint, $username, $password, $client_id){

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$token_endpoint);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,
        "username=".$username."&password=".$password."&scope=openid&grant_type=password&client_id=".$client_id);
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
    if ($host=='localhost') {
        header("Location: ../management/ssoLogin.php?redirect=http://$host" . $_SERVER['REQUEST_URI']);
    } else {
        header("Location: ../management/ssoLogin.php?redirect=https://$host" . $_SERVER['REQUEST_URI']);
    }
    exit();
}