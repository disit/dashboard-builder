<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->

<?php

include_once('../config.php');

if (!isset($_SESSION)) {
    session_start();
}

if ((!$_SESSION['isPublic'] && isset($_SESSION['newLayout']) && $_SESSION['newLayout'] === true) || ($_SESSION['isPublic'] && $_COOKIE['layout'] == "new_layout")) {

?>

<html class="dark">
    <head>
        <meta charset="UTF-8">
        
        <link href="../css/chatIframe.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="../css/chat.css" type="text/css" />
        
    </head>
    <body>
        <?php include "../cookie_banner/cookie-banner.php"; ?>
        <?php
        $error = $_REQUEST['error'];

        if ($error != 'no') {
            echo "<div><font color='#eeeeee' size='3em'>Chat Error:</br>" . $_REQUEST['error'] . "</br></font>";
        } else {
            echo "<div><font color='#eeeeee' size='3em'>SuperUser Chat Error</br></br></font>";
        } echo '</div>';
        ?>

    </body>
</html>

<?php } else {
    include('../s4c-legacy-management/chatFrameError.php');
}
?>