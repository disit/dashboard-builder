<?php

/* Dashboard Builder.
  Copyright (C) 2018 DISIT Lab https://www.disit.org - University of Florence

  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; either version 2
  of the License, or (at your option) any later version.
  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.
  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA. */


include '../config.php';
require '../sso/autoload.php';

use Jumbojett\OpenIDConnectClient;

session_start();
ini_set("max_execution_time", 0);
error_reporting(E_ERROR);
//if (isset($_SESSION['loggedUsername'])) {
/* * ************** */
//if(isset($_SESSION['refreshToken'])) {
$oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
$oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));
$tkn = $oidc->refreshToken($_SESSION['refreshToken']);
$accessToken = $tkn->access_token;
$_SESSION['refreshToken'] = $tkn->refresh_token;

$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);
if (isset($_SESSION['loggedRole'])) {
    $role_session_active = $_SESSION['loggedRole'];
    if ($role_session_active == "RootAdmin") {
        session_start();
        $arr = Array();
        $service = $_REQUEST['service'];
//$service = 'list_menu';

        if ($service == 'list_menu') {
            //
            $sel_MainMenu = mysqli_real_escape_string($link, $_REQUEST['sel_MainMenu']);
            $sel_MainMenu = filter_var($sel_MainMenu, FILTER_SANITIZE_STRING);

            $sel_MainMenuSubmenus = mysqli_real_escape_string($link, $_REQUEST['sel_MainMenuSubmenus']);
            $sel_MainMenuSubmenus = filter_var($sel_MainMenuSubmenus, FILTER_SANITIZE_STRING);

            $sel_OrgMenu = mysqli_real_escape_string($link, $_REQUEST['sel_OrgMenu']);
            $sel_OrgMenu = filter_var($sel_OrgMenu, FILTER_SANITIZE_STRING);

            $sel_OrgMenuSubmenus = mysqli_real_escape_string($link, $_REQUEST['sel_OrgMenuSubmenus']);
            $sel_OrgMenuSubmenus = filter_var($sel_OrgMenuSubmenus, FILTER_SANITIZE_STRING);

            $sel_MobMainMenuSubmenus = mysqli_real_escape_string($link, $_REQUEST['sel_MobMainMenuSubmenus']);
            $sel_MobMainMenuSubmenus = filter_var($sel_MobMainMenuSubmenus, FILTER_SANITIZE_STRING);

            $sel_MobMainMenu = mysqli_real_escape_string($link, $_REQUEST['sel_MobMainMenu']);
            $sel_MobMainMenu = filter_var($sel_MobMainMenu, FILTER_SANITIZE_STRING);

            $select_all = mysqli_real_escape_string($link, $_REQUEST['select_all']);
            $select_all = filter_var($select_all, FILTER_SANITIZE_STRING);

            $domain = mysqli_real_escape_string($link, $_REQUEST['domain']);
            $domain = filter_var($domain, FILTER_SANITIZE_STRING);
            //sel_MainMenuSubmenus: sel_MainMenuSubmenus,
            //sel_OrgMenu: sel_OrgMenu,
            //sel_OrgMenuSubmenus: sel_OrgMenuSubmenus,
            //sel_MobMainMenuSubmenus: sel_MobMainMenuSubmenus,
            //sel_MobMainMenu: sel_MobMainMenu,
            //select_all: select_all
            $domain_condition = "";
            if ($domain == 'select_all') {
                $domain_condition = "";
            } else {
                $domain_condition = "   AND Domains.id='" . $domain . "'";
            }
            //$domain_condition = "";
            //
    $i = 0;
            //
            if (($select_all == 'true') || (($select_all == 'false') && ($sel_MainMenu == 'true'))) {
                //$query = "SELECT * FROM MainMenu;";
                $query = "SELECT MainMenu.*, Domains.claim  FROM MainMenu , Domains WHERE MainMenu.domain=Domains.id" . $domain_condition;
                $result = mysqli_query($link, $query) or die(mysqli_error($link));

                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        //print_r($row);
                        //$row['id'];
                        //
        $arr[$i]['id'] = $row['id'];
                        $arr[$i]['publicLinkUrl'] = rawurldecode($row['publicLinkUrl']);
                        $arr[$i]['linkId'] = rawurldecode($row['linkId']);
                        $arr[$i]['icon'] = rawurldecode($row['icon']);
                        $arr[$i]['text'] = rawurldecode($row['text']);
                        $arr[$i]['linkUrl'] = rawurldecode($row['linkUrl']);
                        $arr[$i]['submenu'] = '';
                        $arr[$i]['privileges'] = rawurldecode($row['privileges']);
                        $arr[$i]['userType'] = rawurldecode($row['userType']);
                        $arr[$i]['externalApp'] = rawurldecode($row['externalApp']);
                        $arr[$i]['openMode'] = rawurldecode($row['openMode']);
                        $arr[$i]['iconColor'] = rawurldecode($row['iconColor']);
                        $arr[$i]['pageTitle'] = rawurldecode($row['pageTitle']);
                        $arr[$i]['domain'] = rawurldecode($row['domain']);
                        $arr[$i]['menuOrder'] = rawurldecode($row['menuOrder']);
                        $arr[$i]['organizations'] = rawurldecode($row['organizations']);
                        $arr[$i]['typemenu'] = 'MainMenu';
                        $arr[$i]['claim'] = rawurldecode($row['claim']);
                        //
                        $i++;
// [id] => 1155 [linkUrl] => submenu [publicLinkUrl] => [linkId] => mainSetupLink [icon] => fa fa-cogs [text] => Settings [privileges] => ['RootAdmin','ToolAdmin'] [userType] => any [externalApp] => no [openMode] => submenu [iconColor] => #99ff99 [pageTitle] => Settings [domain] => 2 [menuOrder] => 19 [organizations] => ['Toscana', 'Firenze', 'Sardegna'. 'Helsinki', 'Antwerp', 'Garda Lake', 'DISIT', 'Other'] )
                    }
                }
            }


            if (($select_all == 'true') || (($select_all == 'false') && ($sel_MobMainMenu == 'true'))) {
                $query2 = "SELECT MobMainMenu.*, Domains.claim  FROM MobMainMenu , Domains WHERE MobMainMenu.domain=Domains.id" . $domain_condition;
                $result2 = mysqli_query($link, $query2) or die(mysqli_error($link));
                if ($result2) {
                    while ($row = mysqli_fetch_assoc($result2)) {
                        //print_r($row);
                        //$row['id'];
                        //
        $arr[$i]['id'] = $row['id'];
                        $arr[$i]['publicLinkUrl'] = rawurldecode($row['publicLinkUrl']);
                        $arr[$i]['linkId'] = rawurldecode($row['linkId']);
                        $arr[$i]['icon'] = rawurldecode($row['icon']);
                        $arr[$i]['text'] = rawurldecode($row['text']);
                        $arr[$i]['linkUrl'] = rawurldecode($row['linkUrl']);
                        $arr[$i]['submenu'] = '';
                        $arr[$i]['privileges'] = rawurldecode($row['privileges']);
                        $arr[$i]['userType'] = rawurldecode($row['userType']);
                        $arr[$i]['externalApp'] = rawurldecode($row['externalApp']);
                        $arr[$i]['openMode'] = rawurldecode($row['openMode']);
                        $arr[$i]['iconColor'] = rawurldecode($row['iconColor']);
                        $arr[$i]['pageTitle'] = rawurldecode($row['pageTitle']);
                        $arr[$i]['domain'] = rawurldecode($row['domain']);
                        $arr[$i]['menuOrder'] = rawurldecode($row['menuOrder']);
                        $arr[$i]['organizations'] = rawurldecode($row['organizations']);
                        $arr[$i]['typemenu'] = 'MobMainMenu';
                        $arr[$i]['claim'] = rawurldecode($row['claim']);
                        //
                        $i++;
// [id] => 1155 [linkUrl] => submenu [publicLinkUrl] => [linkId] => mainSetupLink [icon] => fa fa-cogs [text] => Settings [privileges] => ['RootAdmin','ToolAdmin'] [userType] => any [externalApp] => no [openMode] => submenu [iconColor] => #99ff99 [pageTitle] => Settings [domain] => 2 [menuOrder] => 19 [organizations] => ['Toscana', 'Firenze', 'Sardegna'. 'Helsinki', 'Antwerp', 'Garda Lake', 'DISIT', 'Other'] )
                    }
                }
            }
            //
            if (($select_all == 'true') || (($select_all == 'false') && ($sel_OrgMenu == 'true'))) {
                //if ($sel_OrgMenu == 'true') {
                $query3 = "SELECT OrgMenu.*,Domains.claim FROM OrgMenu, Domains WHERE OrgMenu.domain=Domains.id" . $domain_condition;
                $result3 = mysqli_query($link, $query3) or die(mysqli_error($link));
                if ($result3) {
                    while ($row = mysqli_fetch_assoc($result3)) {
                        $arr[$i]['id'] = $row['id'];
                        $arr[$i]['publicLinkUrl'] = rawurldecode($row['publicLinkUrl']);
                        $arr[$i]['linkId'] = rawurldecode($row['linkId']);
                        $arr[$i]['icon'] = rawurldecode($row['icon']);
                        $arr[$i]['text'] = rawurldecode($row['text']);
                        $arr[$i]['linkUrl'] = rawurldecode($row['linkUrl']);
                        $arr[$i]['iconColor'] = rawurldecode($row['iconColor']);
                        $arr[$i]['submenu'] = '';
                        $arr[$i]['privileges'] = rawurldecode($row['privileges']);
                        $arr[$i]['userType'] = rawurldecode($row['userType']);
                        $arr[$i]['externalApp'] = rawurldecode($row['externalApp']);
                        $arr[$i]['pageTitle'] = rawurldecode($row['pageTitle']);
                        $arr[$i]['domain'] = rawurldecode($row['domain']);
                        $arr[$i]['menuOrder'] = rawurldecode($row['menuOrder']);
                        $arr[$i]['organizations'] = rawurldecode($row['organizations']);
                        $arr[$i]['typemenu'] = 'OrgMenu';
                        $arr[$i]['openMode'] = rawurldecode($row['openMode']);
                        $arr[$i]['claim'] = rawurldecode($row['claim']);
                        //
                        $i++;
                        // [id] => 1155 [linkUrl] => submenu [publicLinkUrl] => [linkId] => mainSetupLink [icon] => fa fa-cogs [text] => Settings [privileges] => ['RootAdmin','ToolAdmin'] [userType] => any [externalApp] => no [openMode] => submenu [iconColor] => #99ff99 [pageTitle] => Settings [domain] => 2 [menuOrder] => 19 [organizations] => ['Toscana', 'Firenze', 'Sardegna'. 'Helsinki', 'Antwerp', 'Garda Lake', 'DISIT', 'Other'] )
                    }
                }
            }
            ///
            if (($select_all == 'true') || (($select_all == 'false') && ($sel_MainMenuSubmenus == 'true'))) {
                if ($domain_condition == '') {
                    $sub_query1 = "SELECT * FROM MainMenuSubmenus";
                    $sub_result1 = mysqli_query($link, $sub_query1) or die(mysqli_error($link));
                    if ($sub_result1) {
                        while ($row = mysqli_fetch_assoc($sub_result1)) {
                            //print_r($row);
                            //$row['id'];
                            //
                        $arr[$i]['id'] = $row['id'];
                            $arr[$i]['publicLinkUrl'] = '';
                            $arr[$i]['linkId'] = rawurldecode($row['linkId']);
                            $arr[$i]['icon'] = rawurldecode($row['icon']);
                            $arr[$i]['text'] = rawurldecode($row['text']);
                            $arr[$i]['linkUrl'] = rawurldecode($row['linkUrl']);
                            $arr[$i]['submenu'] = rawurldecode($row['menu']);
                            $arr[$i]['privileges'] = rawurldecode($row['privileges']);
                            $arr[$i]['userType'] = rawurldecode($row['userType']);
                            $arr[$i]['externalApp'] = rawurldecode($row['externalApp']);
                            $arr[$i]['openMode'] = rawurldecode($row['openMode']);
                            $arr[$i]['iconColor'] = rawurldecode($row['iconColor']);
                            $arr[$i]['pageTitle'] = rawurldecode($row['pageTitle']);
                            $arr[$i]['domain'] = '';
                            $arr[$i]['menuOrder'] = rawurldecode($row['menuOrder']);
                            $arr[$i]['organizations'] = rawurldecode($row['organizations']);
                            $arr[$i]['typemenu'] = 'MainMenuSubmenus';
                            $arr[$i]['claim'] = '';
                            //
                            $i++;
// [id] => 1155 [linkUrl] => submenu [publicLinkUrl] => [linkId] => mainSetupLink [icon] => fa fa-cogs [text] => Settings [privileges] => ['RootAdmin','ToolAdmin'] [userType] => any [externalApp] => no [openMode] => submenu [iconColor] => #99ff99 [pageTitle] => Settings [domain] => 2 [menuOrder] => 19 [organizations] => ['Toscana', 'Firenze', 'Sardegna'. 'Helsinki', 'Antwerp', 'Garda Lake', 'DISIT', 'Other'] )
                        }
                    }
                }
            }


            if (($select_all == 'true') || (($select_all == 'false') && ($sel_MobMainMenuSubmenus == 'true'))) {
                if ($domain_condition == '') {
                    $sub_query2 = "SELECT * FROM MobMainMenuSubmenus";
                    $sub_result2 = mysqli_query($link, $sub_query2) or die(mysqli_error($link));
                    if ($sub_result2) {
                        while ($row = mysqli_fetch_assoc($sub_result2)) {
                            //print_r($row);
                            //$row['id'];
                            //
                        $arr[$i]['id'] = $row['id'];
                            $arr[$i]['publicLinkUrl'] = '';
                            $arr[$i]['linkId'] = rawurldecode($row['linkId']);
                            $arr[$i]['icon'] = rawurldecode($row['icon']);
                            $arr[$i]['text'] = rawurldecode($row['text']);
                            $arr[$i]['linkUrl'] = rawurldecode($row['linkUrl']);
                            $arr[$i]['submenu'] = rawurldecode($row['menu']);
                            $arr[$i]['privileges'] = rawurldecode($row['privileges']);
                            $arr[$i]['userType'] = rawurldecode($row['userType']);
                            $arr[$i]['externalApp'] = rawurldecode($row['externalApp']);
                            $arr[$i]['openMode'] = rawurldecode($row['openMode']);
                            $arr[$i]['iconColor'] = rawurldecode($row['iconColor']);
                            $arr[$i]['pageTitle'] = rawurldecode($row['pageTitle']);
                            $arr[$i]['domain'] = '';
                            $arr[$i]['menuOrder'] = rawurldecode($row['menuOrder']);
                            $arr[$i]['organizations'] = rawurldecode($row['organizations']);
                            $arr[$i]['typemenu'] = 'MobMainMenuSubmenus';
                            $arr[$i]['claim'] = '';
                            //
                            $i++;
// [id] => 1155 [linkUrl] => submenu [publicLinkUrl] => [linkId] => mainSetupLink [icon] => fa fa-cogs [text] => Settings [privileges] => ['RootAdmin','ToolAdmin'] [userType] => any [externalApp] => no [openMode] => submenu [iconColor] => #99ff99 [pageTitle] => Settings [domain] => 2 [menuOrder] => 19 [organizations] => ['Toscana', 'Firenze', 'Sardegna'. 'Helsinki', 'Antwerp', 'Garda Lake', 'DISIT', 'Other'] )
                        }
                    }
                }
            }
            //

            if (($select_all == 'true') || (($select_all == 'false') && ($sel_OrgMenuSubmenus == 'true'))) {
                //if ($sel_OrgMenuSubmenus == 'true') {
                if ($domain_condition == '') {
                    $sub_query4 = "SELECT * FROM OrgMenuSubmenus";
                    $sub_result4 = mysqli_query($link, $sub_query4) or die(mysqli_error($link));
                    if ($sub_result4) {
                        while ($row = mysqli_fetch_assoc($sub_result4)) {

                            $arr[$i]['id'] = $row['id'];
                            $arr[$i]['publicLinkUrl'] = '';
                            $arr[$i]['linkId'] = rawurldecode($row['linkId']);
                            $arr[$i]['icon'] = rawurldecode($row['icon']);
                            $arr[$i]['text'] = rawurldecode($row['text']);
                            $arr[$i]['linkUrl'] = rawurldecode($row['linkUrl']);
                            $arr[$i]['submenu'] = rawurldecode($row['menu']);
                            $arr[$i]['privileges'] = rawurldecode($row['privileges']);
                            $arr[$i]['userType'] = rawurldecode($row['userType']);
                            $arr[$i]['externalApp'] = rawurldecode($row['externalApp']);
                            $arr[$i]['openMode'] = rawurldecode($row['openMode']);
                            $arr[$i]['iconColor'] = rawurldecode($row['iconColor']);
                            $arr[$i]['pageTitle'] = rawurldecode($row['pageTitle']);
                            $arr[$i]['domain'] = '';
                            $arr[$i]['menuOrder'] = rawurldecode($row['menuOrder']);
                            $arr[$i]['organizations'] = rawurldecode($row['organizations']);
                            $arr[$i]['typemenu'] = 'OrgMenuSubmenus';
                            $arr[$i]['claim'] = '';
                            //
                            $i++;
                        }
                    }
                }
            }
            ///
            echo json_encode($arr);
        }else if($service == 'list_menu_id'){
            //
             $typemenu = mysqli_real_escape_string($link, $_POST['type']);
            $typemenu = filter_var($typemenu, FILTER_SANITIZE_STRING);
            $typemenu = strip_tags($typemenu);
            //
            switch ($typemenu) {
                case 'MainMenuSubmenus':
                    $typemenu = 'MainMenu';
                    break;
                case 'MobMainMenuSubmenus':
                     $typemenu = 'MobMainMenu';
                    break;
                case 'OrgMenuSubmenus':
                     $typemenu = 'OrgMenu';
                    break;
            }
            //$typemenu = $_REQUEST('type');
            $query = 'SELECT id, text FROM '.$typemenu.' WHERE linkUrl="submenu" ORDER BY id';
             $result = mysqli_query($link, $query) or die(mysqli_error($link));
            $i = 0;
            while ($row = mysqli_fetch_assoc($result)) {
                    $arr[$i]['id'] = $row['id'];
                     $arr[$i]['text'] = $row['text'];
                //
                $i++;
            }
            //sort($arr);
            echo json_encode($arr);
            //
        } else if ($service == 'list_orgs') {
            //$query="SELECT DISTINCT organizations FROM mainmenu;";
            $query = "SELECT organizationName FROM Organizations;";
            $result = mysqli_query($link, $query) or die(mysqli_error($link));
            $i = 0;
            while ($row = mysqli_fetch_assoc($result)) {
                //print_r($row);
                //$row['id'];
                //
        $arr[$i] = $row['organizationName'];
                //
                $i++;
// [id] => 1155 [linkUrl] => submenu [publicLinkUrl] => [linkId] => mainSetupLink [icon] => fa fa-cogs [text] => Settings [privileges] => ['RootAdmin','ToolAdmin'] [userType] => any [externalApp] => no [openMode] => submenu [iconColor] => #99ff99 [pageTitle] => Settings [domain] => 2 [menuOrder] => 19 [organizations] => ['Toscana', 'Firenze', 'Sardegna'. 'Helsinki', 'Antwerp', 'Garda Lake', 'DISIT', 'Other'] )
            }
            ksort($arr);
            echo json_encode($arr);
            //echo($arr);
        } else if ($service == 'list_domains') {
            $query = "SELECT * FROM Domains";
            $result = mysqli_query($link, $query) or die(mysqli_error($link));
            $i = 0;

            while ($row = mysqli_fetch_assoc($result)) {
                //print_r($row);
                //$row['id'];
                //
        $arr[$i]['id'] = $row['id'];
                $arr[$i]['claim'] = $row['claim'];
                //
                $i++;

// [id] => 1155 [linkUrl] => submenu [publicLinkUrl] => [linkId] => mainSetupLink [icon] => fa fa-cogs [text] => Settings [privileges] => ['RootAdmin','ToolAdmin'] [userType] => any [externalApp] => no [openMode] => submenu [iconColor] => #99ff99 [pageTitle] => Settings [domain] => 2 [menuOrder] => 19 [organizations] => ['Toscana', 'Firenze', 'Sardegna'. 'Helsinki', 'Antwerp', 'Garda Lake', 'DISIT', 'Other'] )
            }
            sort($arr);
            echo json_encode($arr);
        } else if ($service == 'list_icons') {
            //$query = "SELECT DISTINCT count(icon) AS count, icon FROM MainMenu GROUP BY icon ORDER BY count DESC;";
            $query = "SELECT DISTINCT icon FROM MainMenu GROUP BY icon
                        UNION
                        SELECT DISTINCT icon FROM OrgMenu GROUP BY icon
                        UNION
                        SELECT DISTINCT icon FROM OrgMenuSubmenus GROUP BY icon
                        UNION
                        SELECT DISTINCT icon FROM MobMainMenu GROUP BY icon
                        UNION
                        SELECT DISTINCT icon FROM MobMainMenuSubmenus GROUP BY icon
                        UNION
                        SELECT DISTINCT icon FROM MainMenuSubmenus GROUP BY icon ORDER BY icon ASC
                        ";
            $result = mysqli_query($link, $query) or die(mysqli_error($link));
            $i = 0;
            while ($row = mysqli_fetch_assoc($result)) {
                //print_r($row);
                //$row['id'];
                //
        $arr[$i] = trim($row['icon']);
                //
                $i++;

// [id] => 1155 [linkUrl] => submenu [publicLinkUrl] => [linkId] => mainSetupLink [icon] => fa fa-cogs [text] => Settings [privileges] => ['RootAdmin','ToolAdmin'] [userType] => any [externalApp] => no [openMode] => submenu [iconColor] => #99ff99 [pageTitle] => Settings [domain] => 2 [menuOrder] => 19 [organizations] => ['Toscana', 'Firenze', 'Sardegna'. 'Helsinki', 'Antwerp', 'Garda Lake', 'DISIT', 'Other'] )
            }
            array_unique($arr);
            sort($arr);
            echo json_encode($arr);
        } else if ($service == 'create_menu') {
            $url = mysqli_real_escape_string($link, $_POST['url']);
            $url = filter_var($url, FILTER_SANITIZE_STRING);
            $url = strip_tags($url);

            $linkid = mysqli_real_escape_string($link, $_POST['linkid']);
            $linkid = filter_var($linkid, FILTER_SANITIZE_STRING);
            $linkid = strip_tags($linkid);

            $text = mysqli_real_escape_string($link, $_POST['text']);
            $text = filter_var($text, FILTER_SANITIZE_STRING);
            $text = strip_tags($text);

            $pagetitle = mysqli_real_escape_string($link, $_POST['pagetitle']);
            $pagetitle = filter_var($pagetitle, FILTER_SANITIZE_STRING);
            $pagetitle = strip_tags($pagetitle);

            $color = mysqli_real_escape_string($link, $_POST['color']);
            $color = filter_var($color, FILTER_SANITIZE_STRING);
            $color = strip_tags($color);

            $icon = mysqli_real_escape_string($link, $_POST['icon']);
            $icon = filter_var($icon, FILTER_SANITIZE_STRING);
            $icon = strip_tags($icon);

            $select_mode = mysqli_real_escape_string($link, $_POST['select_mode']);
            $select_mode = filter_var($select_mode, FILTER_SANITIZE_STRING);
            $select_mode = strip_tags($select_mode);


            $role = mysqli_real_escape_string($link, $_POST['role']);
            $role = filter_var($role, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
            $role = strip_tags($role);

            $org = mysqli_real_escape_string($link, $_POST['org']);
            $org = filter_var($org, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
            $org = strip_tags($org);

            //
            $menuOrder = mysqli_real_escape_string($link, $_POST['menuOrder']);
            $menuOrder = filter_var($menuOrder, FILTER_SANITIZE_STRING);
            $menuOrder = strip_tags($menuOrder);

            $externalApp = mysqli_real_escape_string($link, $_POST['externalApp']);
            $externalApp = filter_var($externalApp, FILTER_SANITIZE_STRING);
            $externalApp = strip_tags($externalApp);

            $select_type_creation = mysqli_real_escape_string($link, $_POST['select_type_creation']);
            $select_type_creation = filter_var($select_type_creation, FILTER_SANITIZE_STRING);
            $select_type_creation = strip_tags($select_type_creation);

            $publicLinkUrl = mysqli_real_escape_string($link, $_POST['publicLinkUrl']);
            $publicLinkUrl = filter_var($publicLinkUrl, FILTER_SANITIZE_STRING);
            $publicLinkUrl = strip_tags($publicLinkUrl);

            $domain = mysqli_real_escape_string($link, $_POST['domain']);
            $domain = filter_var($domain, FILTER_SANITIZE_STRING);
            $domain = strip_tags($domain);
            //$domain = 2;
            //$menuOrder = '';
            if ($icon === '') {
                $icon = null;
            }
            //$menu = '';
            if ($select_mode === 'submenu') {
                //$url = 'submenu';
                $publicLinkUrl = null;
            }
            //
            if (($select_type_creation == "MainMenuSubmenus") || ($select_type_creation == "MobMainMenuSubmenus") || ($select_type_creation == "OrgMenuSubmenus")) {
                $menu = mysqli_real_escape_string($link, $_POST['menu']);
                $menu = filter_var($menu, FILTER_SANITIZE_STRING);
                //
                echo($url);
                //
                $query = 'INSERT INTO ' . $select_type_creation . ' (menu, linkUrl,linkId, text, privileges, iconColor, pageTitle, menuOrder, organizations,icon, openMode, externalApp) VALUES("' . htmlspecialchars($menu) . '","' . htmlspecialchars($url) . '","' . htmlspecialchars($linkid) . '","' . htmlspecialchars($text) . '","' . htmlspecialchars($role) . '", "' . htmlspecialchars($color) . '", "' . htmlspecialchars($pagetitle) . '",' . htmlspecialchars($menuOrder) . ',"' . htmlspecialchars($org) . '", "' . htmlspecialchars($icon) . '", "' . htmlspecialchars($select_mode) . '", "' . htmlspecialchars($externalApp) . '");';
            } else if (($select_type_creation == "MainMenu") || ($select_type_creation == "MobMainMenu") || ($select_type_creation == "OrgMenu")) {

                $query = 'INSERT INTO ' . $select_type_creation . ' (publicLinkUrl,linkUrl,linkId, text, privileges, iconColor, pageTitle, organizations,icon, domain, openMode, externalApp, menuOrder) VALUES("' . htmlspecialchars($publicLinkUrl) . '","' . htmlspecialchars($url) . '","' . htmlspecialchars($linkid) . '","' . htmlspecialchars($text) . '","' . htmlspecialchars($role) . '", "' . htmlspecialchars($color) . '", "' . htmlspecialchars($pagetitle) . '","' . htmlspecialchars($org) . '", "' . htmlspecialchars($icon) . '", "' . htmlspecialchars($domain) . '", "' . htmlspecialchars($select_mode) . '", "' . htmlspecialchars($externalApp) . '","' . htmlspecialchars($menuOrder) . '");';
            } else {
                $query = "";
            }
            echo($query);
            $result = mysqli_query($link, $query) or die(mysqli_error($link));
            $result_mess = 'executed_query';
            if ($result) {
                $reslut_mess = 'OK';
            } else {
                $result_mess = 'No';
            }
            echo ($result_mess);
            //
        } elseif ($service == 'delete_menu') {
            $id = mysqli_real_escape_string($link, $_REQUEST['id']);
            $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
            $table = mysqli_real_escape_string($link, $_REQUEST['table']);
            $table = filter_var($table, FILTER_SANITIZE_SPECIAL_CHARS);

            if (($table == 'MainMenu') || ($table == 'MainMenuSubmenus') || ($table == 'OrgMenu') || ($table == 'OrgMenuSubmenus') || ($table == 'MobMainMenu') || ($table == 'MobMainMenuSubmenus')) {

                if (is_numeric($id)) {
                    $query = "DELETE FROM " . $table . " WHERE id=" . $id;
                    $result = mysqli_query($link, $query) or die(mysqli_error($link));
                    if ($result) {
                        echo('ok');
                    } else {
                        echo('error');
                    }
                }
            }
        } elseif ($service == 'copy_table') {
            //
            $table = mysqli_real_escape_string($link, $_REQUEST['table']);
            $table = filter_var($table, FILTER_SANITIZE_SPECIAL_CHARS);
            //
            // echo($table);

            if ($table == 'MobMainMenu') {

                $result['main'] = copy_table($link, 'MainMenu', 'MobMainMenu');
                $result['submenu'] = copy_table($link, 'MainMenuSubmenus', 'MobMainMenuSubmenus');
                echo json_encode($result);
                //
            } else {
                
            }
            //
        } elseif ($service == 'edit_menu') {

            $id = mysqli_real_escape_string($link, $_POST['id']);
            $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
            $id = strip_tags($id);

            $url_e = mysqli_real_escape_string($link, $_POST['url_e']);
            $url_e = filter_var($url_e, FILTER_SANITIZE_STRING);
            $url_e = strip_tags($url_e);

            $linkid_e = mysqli_real_escape_string($link, $_POST['linkid_e']);
            $linkid_e = filter_var($linkid_e, FILTER_SANITIZE_STRING);
            $linkid_e = strip_tags($linkid_e);

            $text_e = mysqli_real_escape_string($link, $_POST['text_e']);
            $text_e = filter_var($text_e, FILTER_SANITIZE_STRING);
            $text_e = strip_tags($text_e);

            $pagetitle_e = mysqli_real_escape_string($link, $_POST['pagetitle_e']);
            $pagetitle_e = filter_var($pagetitle_e, FILTER_SANITIZE_STRING);
            $pagetitle_e = strip_tags($pagetitle_e);

            $color_e = mysqli_real_escape_string($link, $_POST['color_e']);
            $color_e = filter_var($color_e, FILTER_SANITIZE_STRING);
            $color_e = strip_tags($color_e);

            $table = mysqli_real_escape_string($link, $_POST['table']);
            $table = filter_var($table, FILTER_SANITIZE_STRING);
            $table = strip_tags($table);

            $icon_e = mysqli_real_escape_string($link, $_POST['icon_e']);
            $icon_e = filter_var($icon_e, FILTER_SANITIZE_STRING);
            $icon_e = strip_tags($icon_e);

            $rol_arr = mysqli_real_escape_string($link, $_POST['rol_arr']);
            $rol_arr = filter_var($rol_arr, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
            $rol_arr = strip_tags($rol_arr);
            //$rol_arr = html_entity_decode($rol_arr);

            $org_arr = mysqli_real_escape_string($link, $_POST['org_arr']);
            $org_arr = filter_var($org_arr, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
            $org_arr = strip_tags($org_arr);
            //$org_arr = html_entity_decode($org_arr);

            $menuOrder = mysqli_real_escape_string($link, $_POST['menuOrder']);
            $menuOrder = filter_var($menuOrder, FILTER_SANITIZE_STRING);
            $menuOrder = strip_tags($menuOrder);

            $externalApp = mysqli_real_escape_string($link, $_POST['externalApp']);
            $externalApp = filter_var($externalApp, FILTER_SANITIZE_STRING);
            $externalApp = strip_tags($externalApp);

            $openmomde = mysqli_real_escape_string($link, $_POST['openmomde']);
            $openmomde = filter_var($openmomde, FILTER_SANITIZE_STRING);
            $openmomde = strip_tags($openmomde);

            $main_menu = mysqli_real_escape_string($link, $_POST['main_menu']);
            $main_menu = filter_var($main_menu, FILTER_SANITIZE_STRING);
            $main_menu = strip_tags($main_menu);

            $domain = mysqli_real_escape_string($link, $_POST['domain']);
            $domain = filter_var($domain, FILTER_SANITIZE_STRING);
            $domain = strip_tags($domain);

            if ($icon_e === '') {
                $icon_e = null;
            }

            if (($table == 'MainMenu') || ($table == 'OrgMenu') || ($table == 'MobMainMenu')) {
                if (is_numeric($id)) {
                    $query = 'UPDATE ' . htmlspecialchars($table) . '
                        SET linkUrl="' . htmlspecialchars($url_e) . '", 
                            linkId= "' . htmlspecialchars($linkid_e) . '",
                            text="' . htmlspecialchars($text_e) . '",
                            pagetitle= "' . htmlspecialchars($pagetitle_e) . '",
                            iconColor = "' . htmlspecialchars($color_e) . '",
                            organizations = "' . htmlspecialchars($org_arr) . '",
                            icon = "' . htmlspecialchars($icon_e) . '",
                            privileges="' . htmlspecialchars($rol_arr) . '",
                           externalApp="' . htmlspecialchars($externalApp) . '",
                           menuOrder =' . htmlspecialchars($menuOrder) . ' ,
                           openMode = "' . htmlspecialchars($openmomde) . '",
                           domain = ' . htmlspecialchars($domain) . '
                        WHERE id=' . $id . ' ';
                    echo($query);
                    $result = mysqli_query($link, $query) or die(mysqli_error($link));
                    //echo ($result);
                    $result_mess = 'executed_query';
                    if ($result) {
                        $result_mess = 'OK';
                    } else {
                        $result_mess = 'No';
                    }
                }
            } elseif (( ($table == 'MainMenuSubmenus') || ($table == 'OrgMenuSubmenus') || ($table == 'MobMainMenuSubmenus'))) {
                if (is_numeric($id)) {
                    $query = 'UPDATE ' . htmlspecialchars($table) . '
                        SET linkUrl="' . htmlspecialchars($url_e) . '", 
                            linkId= "' . htmlspecialchars($linkid_e) . '",
                            text="' . htmlspecialchars($text_e) . '",
                            pagetitle= "' . htmlspecialchars($pagetitle_e) . '",
                            iconColor = "' . htmlspecialchars($color_e) . '",
                            organizations = "' . htmlspecialchars($org_arr) . '",
                            icon = "' . htmlspecialchars($icon_e) . '",
                            privileges="' . htmlspecialchars($rol_arr) . '",
                           externalApp="' . htmlspecialchars($externalApp) . '",
                           menuOrder =' . htmlspecialchars($menuOrder) . ' ,
                           openMode = "' . htmlspecialchars($openmomde) . '",
                           menu = "' . htmlspecialchars($main_menu) . '"
                        WHERE id=' . $id . ' ';
                    echo($query);
                    $result = mysqli_query($link, $query) or die(mysqli_error($link));
                    //echo ($result);
                    $result_mess = 'executed_query';
                    if ($result) {
                        $result_mess = 'OK';
                    } else {
                        $result_mess = 'No';
                    }
                }
            } else {
                //nothing
            }
            echo ($result_mess);
        }
    }
}

//
function copy_table($link, $origin, $destination) {
    //
    $json_res = "";
    $json_res['origin'] = $origin;
    $json_res['destination'] = $destination;
    //
    $query_1 = "SELECT * FROM " . $destination;
    //
    $array_temp = array();
    $result_1 = mysqli_query($link, $query_1) or die(mysqli_error($link));
    $i = 0;
    while ($row = mysqli_fetch_assoc($result_1)) {
        $array_temp[$i] = $row;
        $i++;
    }
    $lun = $i;
    //
    //TRUCATE DESTINATION
    $query_t1 = "TRUNCATE TABLE " . $destination . ";";
    $result_t1 = mysqli_query($link, $query_t1) or die(mysqli_error($link));
    if ($result_t1) {
        //
        $json_res['Truncate_table ' + $destination] = 'OK';
        //
        $query_origin = "SELECT * FROM " . $origin;
        $result_origin = mysqli_query($link, $query_origin) or die(mysqli_error($link));
        $count_rows = 0;
        while ($row_orig = mysqli_fetch_assoc($result_origin)) {
            if ($destination === "MobMainMenuSubmenus") {
                $query = 'INSERT INTO ' . $destination . ' (id,menu, linkUrl,linkId, text, privileges, iconColor, pageTitle, menuOrder, organizations,icon, openMode, externalApp) VALUES("' . htmlspecialchars($row_orig['id']) . '",' . htmlspecialchars($row_orig['menu']) . ',"' . htmlspecialchars($row_orig['linkUrl']) . '","' . htmlspecialchars($row_orig['linkId']) . '","' . htmlspecialchars($row_orig['text']) . '","' . htmlspecialchars($row_orig['privileges']) . '","' . htmlspecialchars($row_orig['iconColor']) . '","' . htmlspecialchars($row_orig['pageTitle']) . '","' . htmlspecialchars($row_orig['menuOrder']) . '","' . htmlspecialchars($row_orig['organizations']) . '","' . htmlspecialchars($row_orig['icon']) . '","' . htmlspecialchars($row_orig['openMode']) . '","' . htmlspecialchars($row_orig['externalApp']) . '");';
                $result = mysqli_query($link, $query) or die(mysqli_error($link));
                //
                //
                if ($result) {
                    $count_rows ++;
                }
            } elseif ($destination === "OrgMenuSubmenus") {
                $query = 'INSERT INTO ' . $destination . ' (id,menu, linkUrl, publicLinkUrl,linkId, text, privileges, iconColor, pageTitle, menuOrder, organizations,icon, openMode, externalApp) VALUES("' . htmlspecialchars($row_orig['id']) . '",' . htmlspecialchars($row_orig['menu']) . ',"' . htmlspecialchars($row_orig['linkUrl']) . '","","' . htmlspecialchars($row_orig['linkId']) . '","' . htmlspecialchars($row_orig['text']) . '","' . htmlspecialchars($row_orig['privileges']) . '","' . htmlspecialchars($row_orig['iconColor']) . '","' . htmlspecialchars($row_orig['pageTitle']) . '","' . htmlspecialchars($row_orig['menuOrder']) . '","' . htmlspecialchars($row_orig['organizations']) . '","' . htmlspecialchars($row_orig['icon']) . '","' . htmlspecialchars($row_orig['openMode']) . '","' . htmlspecialchars($row_orig['externalApp']) . '");';
                $result = mysqli_query($link, $query) or die(mysqli_error($link));
                //
                //
                if ($result) {
                    $count_rows ++;
                }
            } elseif ($destination === "MobMainMenu") {
                //$query = 'INSERT INTO ' . $destination . ' (id, publicLinkUrl, linkUrl,linkId, text, privileges, iconColor, pageTitle, menuOrder, organizations,icon, openMode, externalApp) VALUES("' . htmlspecialchars($row_orig['id']) . '","' . htmlspecialchars($row_orig['publicLinkUrl']) . '","' . htmlspecialchars($row_orig['linkUrl']) . '","' . htmlspecialchars($row_orig['linkId']) . '","' . htmlspecialchars($row_orig['text']) . '","' . htmlspecialchars($row_orig['privileges']) . '","' . htmlspecialchars($row_orig['iconColor']) . '","' . htmlspecialchars($row_orig['pageTitle']) . '","' . htmlspecialchars($row_orig['menuOrder']) . '","' . htmlspecialchars($row_orig['organizations']) . '","' . htmlspecialchars($row_orig['icon']) . '","' . htmlspecialchars($row_orig['openMode']) . '","' . htmlspecialchars($row_orig['externalApp']) . '");';
                $query = 'INSERT INTO ' . $destination . ' (id, publicLinkUrl, linkUrl,linkId, text, privileges, iconColor, pageTitle, menuOrder, organizations,icon, openMode, externalApp, domain) VALUES("' . htmlspecialchars($row_orig['id']) . '","' . htmlspecialchars($row_orig['publicLinkUrl']) . '","' . htmlspecialchars($row_orig['linkUrl']) . '","' . htmlspecialchars($row_orig['linkId']) . '","' . htmlspecialchars($row_orig['text']) . '","' . htmlspecialchars($row_orig['privileges']) . '","' . htmlspecialchars($row_orig['iconColor']) . '","' . htmlspecialchars($row_orig['pageTitle']) . '","' . htmlspecialchars($row_orig['menuOrder']) . '","' . htmlspecialchars($row_orig['organizations']) . '","' . htmlspecialchars($row_orig['icon']) . '","' . htmlspecialchars($row_orig['openMode']) . '","' . htmlspecialchars($row_orig['externalApp']) . '","' . htmlspecialchars($row_orig['domain']) . '");';
                $result = mysqli_query($link, $query) or die(mysqli_error($link));
                //
                //
                if ($result) {
                    $count_rows ++;
                }
            } else if ($destination === "OrgMenu") {
                $query = 'INSERT INTO ' . $destination . ' (id, publicLinkUrl, linkUrl,linkId, text, privileges, iconColor, pageTitle, menuOrder, organizations,icon, openMode, externalApp, domain) VALUES("' . htmlspecialchars($row_orig['id']) . '","' . htmlspecialchars($row_orig['publicLinkUrl']) . '","' . htmlspecialchars($row_orig['linkUrl']) . '","' . htmlspecialchars($row_orig['linkId']) . '","' . htmlspecialchars($row_orig['text']) . '","' . htmlspecialchars($row_orig['privileges']) . '","' . htmlspecialchars($row_orig['iconColor']) . '","' . htmlspecialchars($row_orig['pageTitle']) . '","' . htmlspecialchars($row_orig['menuOrder']) . '","' . htmlspecialchars($row_orig['organizations']) . '","' . htmlspecialchars($row_orig['icon']) . '","' . htmlspecialchars($row_orig['openMode']) . '","' . htmlspecialchars($row_orig['externalApp']) . '","' . htmlspecialchars($row_orig['domain']) . '");';
                $result = mysqli_query($link, $query) or die(mysqli_error($link));
                //
                //
                if ($result) {
                    $count_rows ++;
                }
                //
            } else {
                $query = "";
            }
            //$z++;
        }
        $json_res['Copied Rows'] = $count_rows;
        //
    //
    } else {
        $json_res['Truncate_table ' + $destination] = 'ERROR';
        $count_rows = 0;

        for ($y = 0; $y < $lun; $y++) {
            if (($destination === "MobMainMenuSubmenus") || ($destination === "OrgMenuSubmenus")) {
                $query = 'INSERT INTO ' . $destination . ' (id,menu, linkUrl,linkId, text, privileges, iconColor, pageTitle, menuOrder, organizations,icon, openMode, externalApp) VALUES("' . htmlspecialchars($array_temp[$y]['id']) . '",' . htmlspecialchars($array_temp[$y]['menu']) . '","' . htmlspecialchars($array_temp[$y]['linkUrl']) . '","' . htmlspecialchars($array_temp[$y]['linkId']) . '","' . htmlspecialchars($array_temp[$y]['text']) . '","' . htmlspecialchars($array_temp[$y]['privileges']) . '","' . htmlspecialchars($array_temp[$y]['iconColor']) . '","' . htmlspecialchars($array_temp[$y]['pageTitle']) . '","' . htmlspecialchars($array_temp[$y]['menuOrder']) . '","' . htmlspecialchars($array_temp[$y]['organizations']) . '","' . htmlspecialchars($array_temp[$y]['icon']) . '","' . $array_temp[$y]['openMode'] . '","' . htmlspecialchars($array_temp[$y]['externalApp']) . '");';
                $result = mysqli_query($link, $query) or die(mysqli_error($link));
                if ($result) {
                    $count_rows ++;
                }
            } elseif (($destination === "MobMainMenu") || ($destination === "OrgMenu")) {
                $query = 'INSERT INTO ' . $destination . ' (id, publicLinkUrl, linkUrl,linkId, text, privileges, iconColor, pageTitle, menuOrder, organizations,icon, openMode, externalApp, domain) VALUES("' . htmlspecialchars($array_temp[$y]['id']) . '","' . htmlspecialchars($array_temp[$y]['publicLinkUrl']) . '","' . htmlspecialchars($array_temp[$y]['linkUrl']) . '","' . htmlspecialchars($array_temp[$y]['linkId']) . '","' . htmlspecialchars($array_temp[$y]['text']) . '","' . htmlspecialchars($array_temp[$y]['privileges']) . '","' . htmlspecialchars($array_temp[$y]['iconColor']) . '","' . htmlspecialchars($array_temp[$y]['pageTitle']) . '","' . htmlspecialchars($array_temp[$y]['menuOrder']) . '","' . htmlspecialchars($array_temp[$y]['organizations']) . '","' . htmlspecialchars($array_temp[$y]['icon']) . '","' . htmlspecialchars($array_temp[$y]['openMode']) . '","' . htmlspecialchars($array_temp[$y]['externalApp']) . '","' . htmlspecialchars($array_temp[$y]['domain']) . '");';
                $result = mysqli_query($link, $query) or die(mysqli_error($link));
                if ($result) {
                    $count_rows ++;
                }
                //
            } else {
                $query = "";
            }
        }
        //Inserimento dei vecchi dati 
    }
    $json_res['origin'] = $origin;
    $json_res['destination'] = $destination;
    $json_res['counted rows'] = $count_rows;
    //MEssage result
    if ($count_rows == $count_rows) {
        $json_res['result'] = 'success';
    } else {
        $json_res['result'] = 'not success';
    }
    return $json_res;
    //
}

?>