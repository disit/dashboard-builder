<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <link href="../css/chatIframe.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="../css/chat.css" type="text/css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    </head>
    <body>

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
