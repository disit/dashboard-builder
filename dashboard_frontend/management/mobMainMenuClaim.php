<?php
    include 'config.php';

    error_reporting(E_ERROR | E_NOTICE);
    date_default_timezone_set('Europe/Rome');

    $link = mysqli_connect($host, $username, $password);
    mysqli_select_db($link, $dbname);

    $currDom = $_SERVER['HTTP_HOST'];

    $domQ = "SELECT * FROM Dashboard.Domains WHERE domains LIKE '%$currDom%'";
    $r = mysqli_query($link, $domQ);

    if($r)
    {
        if(mysqli_num_rows($r) > 0)
        {
            $row = mysqli_fetch_assoc($r);
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
?>
        
