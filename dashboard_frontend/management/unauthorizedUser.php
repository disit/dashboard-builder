<?php
    /* Dashboard Builder.
   Copyright (C) 2016 DISIT Lab https://www.disit.org - University of Florence

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
if (!isset($_SESSION)) {
    session_start();
}

if ((!$_SESSION['isPublic'] && isset($_SESSION['newLayout']) && $_SESSION['newLayout'] === true) || ($_SESSION['isPublic'] && $_COOKIE['layout'] == "new_layout")) {

//    header("location: index.php?sessionExpired=true");
   header("location: ssoLogin.php");
?>

<!DOCTYPE HTML>
<html class="dark">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <!--<meta http-equiv="X-UA-Compatible" content="IE=edge">-->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Dashboard Management System</title>
    
    

    <!-- Bootstrap Core CSS -->
      <link href="../css/s4c-css/bootstrap/bootstrap.css" rel="stylesheet">
      <link href="../css/s4c-css/bootstrap/bootstrap-colorpicker.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles_gridster.css" type="text/css" />
    <link rel="stylesheet" type="text/css" href="../css/jquery.gridster.css">
    <!--<link rel="stylesheet" type="text/css" href="../css/new/jquery.gridster.css">-->
    <link rel="stylesheet" href="../css/style_widgets.css" type="text/css" />
    
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <!-- jQuery -->
    <script src="../js/jquery-1.10.1.min.js"></script>
    
    <!-- Bootstrap Core JavaScript -->
    <script src="../js/bootstrap.min.js"></script>

    <!-- Gridster -->
    <script src="../js/jquery.gridster.js" type="text/javascript" charset="utf-8"></script>

     <!-- Filestyle -->
    <script type="text/javascript" src="../js/filestyle/src/bootstrap-filestyle.min.js"></script>
    
    <!-- JQUERY UI -->
    <script src="../js/jqueryUi/jquery-ui.js"></script>
    

    <!-- Bootstrap colorpicker -->
    <script src="../js/bootstrap-colorpicker.min.js"></script>
    
    <!-- Modernizr -->
    <script src="../js/modernizr-custom.js"></script>
    
    <!-- Highcharts -->
    <script src="../js/highcharts/code/highcharts.js"></script>
    <script src="../js/highcharts/code/modules/exporting.js"></script>
    <script src="../js/highcharts/code/highcharts-more.js"></script>
    <script src="../js/highcharts/code/modules/solid-gauge.js"></script>
    <script src="../js/highcharts/code/highcharts-3d.js"></script>
    
    <!-- Bootstrap editable tables -->
    <!--<link href="https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>-->
    <link href="../bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet">
    <script src="../bootstrap3-editable/js/bootstrap-editable.js"></script>
    
    <!-- Font awesome icons -->
      <link rel="stylesheet" href="../css/s4c-css/fontawesome-free-6.2.0-web/css/all.min.css">
    
      <!-- Custom CSS -->
      <?php include "theme-switcher.php"?>
    
    <!-- TinyColors -->
    <script src="../js/tinyColor.js" type="text/javascript" charset="utf-8"></script>
    
    <script src="../js/widgetsCommonFunctions.js" type="text/javascript" charset="utf-8"></script>
    <script src="../js/dashboard_configdash.js" type="text/javascript" charset="utf-8"></script>
</head>     
<body> 
   <?php include "../cookie_banner/cookie-banner.php"; ?> 
    <div id="wrapper">
        <!-- Navigation -->
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.html">Dashboard Builder</a>
            </div>
        </nav>
    </div>   
    <br/><br/><br/><br/>
    <h1>Errore!</h1>
    <p>Utente e/o dashboard non identificabili</p>
</body>

<?php } else {
    include('../s4c-legacy-management/unauthorizedUser.php');
}
?>