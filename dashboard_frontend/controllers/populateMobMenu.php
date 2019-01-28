<?php
include '../config.php'; 

error_reporting(E_ERROR);
date_default_timezone_set('Europe/Rome');

$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);

$q = "SELECT * FROM Dashboard.MainMenuSubmenus";
echo $q . "<br>";
$r = mysqli_query($link, $q);

if($r)
{
    while($row = mysqli_fetch_assoc($r))
    {
        $id = $row['id'];
        $menu = $row['menu'];
        $linkUrl = $row['linkUrl']; 
        $linkId = $row['linkId'];
        $icon = $row['icon']; 
        $text = $row['text']; 
        $privileges = $row['privileges']; 
        $userType = $row['userType']; 
        $externalApp = $row['externalApp']; 
        $openMode = $row['openMode']; 
        $iconColor = $row['iconColor'];
        $pageTitle = $row['pageTitle'];
        
        $q2 = "INSERT INTO Dashboard.MobMainMenuSubmenus(id, menu, linkUrl, linkId, icon, text, privileges, userType, externalApp, openMode, iconColor, pageTitle) VALUES('$id', '$menu', '$linkUrl', '$linkId', '$icon', '$text', 'NULL', '$userType', '$externalApp', '$openMode', '$iconColor', '$pageTitle')";
        echo $q2 . "<br>";
        $r2 = mysqli_query($link, $q2);
    }
}
