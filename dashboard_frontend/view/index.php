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

   header("Cache-Control: private, max-age=$cacheControlMaxAge");
   
   //Va studiata una soluzione, per ora tolto error reporting
   error_reporting(0);
   session_start();

   $dashId = escapeForJS(base64_decode($_REQUEST['iddasboard']));
   if (checkVarType($dashId, "integer") === false) {
   //     eventLog("Returned the following ERROR in index.php for dashId = ".$dashId.": ".$dashId." is not an integer as expected. USER = " . $_SESSION['loggedUsername'] . ". Exit from script.");
        eventLog("Returned the following ERROR in index.php for dashId = ".$dashId.": ".$dashId." is not an integer as expected. Exit from script.");
        exit();
   };


    $link = mysqli_connect($host, $username, $password);
    mysqli_select_db($link, $dbname);
    
    $query = "SELECT * FROM Dashboard.Config_dashboard WHERE Config_dashboard.Id = '$dashId'";
    $queryResult = mysqli_query($link, $query);
    
    if(isset($_REQUEST['embedPolicy']))
    {
        $embedPolicy = $_REQUEST['embedPolicy'];
    }
    else
    {
        $embedPolicy = 'manual';
    }
    
    if(isset($_REQUEST['autofit']))
    {
        $embedAutofit = $_REQUEST['autofit'];
    }
    else
    {
        $embedAutofit = 'no';
    }
    
    if(isset($_REQUEST['showHeader']))
    {
        $showHeaderEmbedded = $_REQUEST['showHeader'];
    }
    else
    {
        $showHeaderEmbedded = 'yes';
    }

    if($queryResult) 
    {
        $row = mysqli_fetch_array($queryResult);
        
        if($row['deleted'] === 'yes')
        {
            header("Location: dashboardNotAvailable.php");
            exit();
        }

        else
        {
            if(!isset($_SESSION['loggedUsername']))
            {
                //Se non è pubblica può andare avanti con codice standard, altrimenti gli viene chiesto di collegarsi
                if($row['visibility'] != 'public')
                {
                    $host='main.snap4city.org';
if(isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
  $host=$_SERVER['HTTP_X_FORWARDED_HOST'];
  if($host=='dashboard.km4city.org')
    $host.='/dashboardSmartCity';
}
                    header("Location: ../management/ssoLogin.php?redirect=https://$host/view/index.php?iddasboard=" . $_REQUEST['iddasboard']);
                    exit();
                }
            }

           if($queryResult->num_rows > 0) 
           {    
                $embeddable = $row['embeddable'];
                $authorizedPages = $row['authorizedPagesJson'];
           }
           else
           {
               $embeddable = 'no';
           }
        }
    }
    else
    {
        //$embeddable = 'no';
        header("Location: dashboardNotAvailable.php");
        exit();
    }
    
   mysqli_close($link);
   
   if(isset($_SERVER['HTTP_REFERER']))
   {
       if((strpos($_SERVER['HTTP_REFERER'], "http://".$appHost) !== false)||(strpos($_SERVER['HTTP_REFERER'], "https://".$appHost) !== false))
       {
           //Caso embed in una dashboard e previewer: in questo caso dev'essere sempre possibile fare l'embed
           $embeddable = 'yes';
       }
       else
       {
            //Caso embed in pagina esterna
            if($embeddable == "no")
            {
                header('X-Frame-Options: DENY');
            }
            else
            {
                if(($authorizedPages != '')&&($authorizedPages != null)&&($authorizedPages != 'NULL'))
                {
                    $authorizedPages = json_decode($authorizedPages);
                    $isAuthorized = false;
                    for($i = 0; $i < count($authorizedPages); $i++)
                    {
                        if(strpos($_SERVER['HTTP_REFERER'], $authorizedPages[$i]) !== false)
                        {
                            $isAuthorized = true;
                            break;
                        }
                    }

                    if(!$isAuthorized)
                    {
                        header('X-Frame-Options: DENY');
                    }
                }
                else
                {
                    header('X-Frame-Options: DENY');
                }
            }
        }
   } 
   else 
   {
       //Va studiata una soluzione, per ora tolto error reporting
       /*if(strpos($_SERVER['HTTP_REFERER'], $appUrl) !== false)
       {
           $embeddable = 'no';
       } */
   }
?>
<!DOCTYPE html>
<html lang="en">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Dashboard Management System</title>

    <!-- Bootstrap Core CSS -->
    <link href="../css/bootstrap.css" rel="stylesheet">
    
    <!-- Modernizr -->
    <script src="../js/modernizr-custom.js"></script>

    <!-- Custom CSS -->
    <link href="../css/dashboard.css?v=<?php echo time();?>" rel="stylesheet">
    <link href="../css/widgetHeader.css?v=<?php echo time();?>" rel="stylesheet">
    <link href="../css/dashboardView.css?v=<?php echo time();?>" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles_gridster.css" type="text/css" />
    <link href="../css/widgetCtxMenu.css?v=<?php echo time();?>" rel="stylesheet">
    <link rel="stylesheet" href="../css/style_widgets.css?v=<?php echo time();?>" type="text/css" />
    <link href="../css/widgetDimControls.css?v=<?php echo time();?>" rel="stylesheet">
    <link href="../css/chat.css?v=<?php echo time();?>" rel="stylesheet">
    
    <!-- Material icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <!-- jQuery -->
    <script src="../js/jquery-1.10.1.min.js"></script>
    
    <!-- JQUERY UI -->
    <script src="../js/jqueryUi/jquery-ui.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../js/bootstrap.min.js"></script>

    <!-- Gridster -->
    <link rel="stylesheet" type="text/css" href="../css/jquery.gridster.css">
    <script src="../js/jquery.gridsterMod.js" type="text/javascript" charset="utf-8"></script>
    <!--<link rel="stylesheet" type="text/css" href="../newGridster/dist/jquery.gridster.css">
    <script src="../newGridster/dist/jquery.gridster.js" type="text/javascript" charset="utf-8"></script>-->

    <!-- Highcharts --> 
    <script src="../js/highcharts/code/highcharts.js"></script>
    <script src="../js/highcharts/code/modules/exporting.js"></script>
    <script src="../js/highcharts/code/highcharts-more.js"></script>
    <script src="../js/highcharts/code/modules/solid-gauge.js"></script>
    <script src="../js/highcharts/code/highcharts-3d.js"></script>
    
    <!-- TinyColors -->
    <script src="../js/tinyColor.js" type="text/javascript" charset="utf-8"></script>
    
    <!-- Font awesome icons -->
    <link rel="stylesheet" href="../js/fontAwesome/css/font-awesome.min.css">
    
    <!-- Leaflet -->
    <!-- Versione locale: 1.3.1 --> 
    <link rel="stylesheet" href="../leafletCore/leaflet.css" />
    <script src="../leafletCore/leaflet.js"></script>
    <script src="../js/OMS-leaflet/oms.min.js"></script>    <!-- OverlappingMarkerSpider for Leaflet --!>
   
   <!-- Leaflet marker cluster plugin -->
   <link rel="stylesheet" href="../leaflet-markercluster/MarkerCluster.css" />
   <link rel="stylesheet" href="../leaflet-markercluster/MarkerCluster.Default.css" />
   <script src="../leaflet-markercluster/leaflet.markercluster-src.js" type="text/javascript" charset="utf-8"></script>
   
   <!-- Leaflet Wicket: libreria per parsare i file WKT --> 
   <script src="../wicket/wicket.js"></script> 
   <script src="../wicket/wicket-leaflet.js"></script>

    <!-- Leaflet Zoom Display -->
    <script src="../js/leaflet.zoomdisplay-src.js"></script>
    <link href="../css/leaflet.zoomdisplay.css" rel="stylesheet"/>
   
   <!-- Dot dot dot -->
   <script src="../dotdotdot/jquery.dotdotdot.js" type="text/javascript"></script>
   
    <!-- Bootstrap select -->
    <link href="../bootstrapSelect/css/bootstrap-select.css" rel="stylesheet"/>
    <script src="../bootstrapSelect/js/bootstrap-select.js"></script>
    
    <!-- Moment -->
    <script type="text/javascript" src="../moment/moment.js"></script>
    
    <!-- html2canvas -->
    <script type="text/javascript" src="../js/html2canvas.js"></script>
    
    <!-- Bootstrap datetimepicker -->
    <script src="../datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
    <link rel="stylesheet" href="../datetimepicker/build/css/bootstrap-datetimepicker.min.css">
    
    <!-- Weather icons -->
    <link rel="stylesheet" href="../img/meteoIcons/singleColor/css/weather-icons.css?v=<?php echo time();?>">
    
    <!-- Text fill -->
    <script src="../js/jquery.textfill.min.js"></script>
    
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
    
    <script src="../js/widgetsCommonFunctions.js?v=<?php echo time();?>" type="text/javascript" charset="utf-8"></script>
    <script src="../widgets/trafficEventsTypes.js?v=<?php echo time();?>" type="text/javascript" charset="utf-8"></script>
    <script src="../widgets/alarmTypes.js?v=<?php echo time();?>" type="text/javascript" charset="utf-8"></script>
    <script src="../widgets/fakeGeoJsons.js?v=<?php echo time();?>" type="text/javascript" charset="utf-8"></script>
    
    <!--OpenLayers -->
    <script src="ol/ol.js"></script>
    <link rel="stylesheet" href="ol/ol.css" />
    
<!-- Cristiano : Dynamic Routing -->
    <!--- Leaflet.drawer plugin -->
    <script src="../js/dynamic_routing/leaflet.draw.js"></script>
    <script src="../js/dynamic_routing/Leaflet.draw.drag-src.js"></script>
    <link rel="stylesheet" href="../css/dynamic_routing/leaflet.draw.css"/>
    <!-- Leaflet Control Geocoder -->
    <link rel="stylesheet" href="../css/dynamic_routing/Control.Geocoder.css" />
    <script src="../js/dynamic_routing/Control.Geocoder.js"></script>
    <!-- GH Leaflet Routing Machine plugin -->
    <link rel="stylesheet" href="../css/dynamic_routing/leaflet-routing-machine.css" />
  <!--  <script src="../js/dynamic_routing/leaflet-routing-machine.js"></script>    -->
    <script src="../js/dynamic_routing/corslite.min.js"></script>
<!-- End Cristiano -->

    <script type='text/javascript'>
        var array_metrics = new Array();
        var headerFontSize, headerModFontSize, subtitleFontSize, subtitleModFontSize, dashboardId, dashboardName, dashboardOrg, dashboardOrgKbUrl, logoFilename, logoLink,
            clockFontSizeMod, logoWidth, logoHeight, headerVisible = null;
    
        var dashboardZoomEventHandler = function(event)
        {
            document.body.style.zoom = event.data;
        };

        window.addEventListener('message', dashboardZoomEventHandler, false);    
        
        $(document).ready(function () 
        {
            var embedWidget, embedWidgetPolicy, headerVisible, wrapperWidth, dashBckImg, useBckImg, dashboardViewMode, gridster, gridsterCellW, gridsterCellH, widgetsContainerWidth, num_cols = null;
            var firstLoad = true;
            var loggedUserFirstAttempt = true;
            var myGpsActive, myGpsPeriod, myGpsInterval, globalDashboardTitle = null, backOverlayOpacity = null;
            var embedPreview = "<?php if(isset($_REQUEST['embedPreview'])){echo escapeForJS($_REQUEST['embedPreview']);}else{echo 'false';} ?>";
            var loggedUsername = "<?php echo $_SESSION['loggedUsername']; ?>";

            $.ajax({
                url: "../controllers/dashOrganizationProxy.php",
                data: {
                    dashId: <?php echo $dashId ?>,
                },
                type: "GET",
                async: true,
                dataType: 'json',
                success: function (data) {
                    if (data.params.organizations == "Antwerp" || data.params.organizations == "Helsinki") {
                        $('<script/>',{type:'text/javascript', src:'../js/dynamic_routing/leaflet-routing-machine-AntHel.js'}).appendTo('head');
                    } else {
                        $('<script/>',{type:'text/javascript', src:'../js/dynamic_routing/leaflet-routing-machine.js'}).appendTo('head');
                    }
                },
                error: function (errorData) {
                    console.log("Errore in reperimento Organizzaztion della dashboard di id = " + <?php echo $dashId ?> + "; ");
                    console.log(JSON.stringify(errorData));
                }
            });

            $("#chatContainer").css("top", $('#dashboardViewHeaderContainer').height());
            $("#chatContainer").css("left", $(window).width() - $('#chatContainer').width());
            //$("#chatContainer").css("left", $('#dashboardViewHeaderContainer').width() + $('#logos').width() - $('#chatContainer').width());
            
            $('#chatBtn').click(function(){
                if($(this).attr("data-status") === 'closed')
                {
                    $(this).attr("data-status", 'open');
                    $('#chatContainer').show();
                    console.log("Show");
                }
                else
                {
                    $(this).attr("data-status", 'closed');
                    $('#chatContainer').hide();
                    console.log("Hide");
                }
            });
            
            $('#chatBtnNew').click(function(){
                if($(this).attr("data-status") === 'closed')
                {
                    $(this).attr("data-status", 'open');
                    $('#chatContainer').show();
                    console.log("Show");
                }
                else
                {
                    $(this).attr("data-status", 'closed');
                    $('#chatContainer').hide();
                    console.log("Hide");
                }
            });
            //Caricamento della chat asincrono a valle di tutto il loading della dashboard 
          <?php
                    /*include '../config.php';
                    include "../rocket-chat-rest-client/RocketChatClient.php";
                    include "../rocket-chat-rest-client/RocketChatUser.php";
                    include "../rocket-chat-rest-client/RocketChatChannel.php";
                    define('REST_API_ROOT', '/api/v1/');
                    define('ROCKET_CHAT_INSTANCE', $chatBaseUrl);
                    $userId='Id';
                    $existChat='No';
                    $link = mysqli_connect($host, $username, $password);
                    mysqli_select_db($link, $dbname);
                    $query = "SELECT user,name_dashboard FROM Dashboard.Config_dashboard WHERE Config_dashboard.Id =".base64_decode($_REQUEST['iddasboard']);
                    $queryResult = mysqli_query($link, $query);
                    $row = mysqli_fetch_array($queryResult);
                    $userPro=$row["user"];
                    $idChat='id';
                    $nameDash=$row["name_dashboard"];
                    $nameChat=strtolower((str_replace(" ", "", $nameDash) . "-" . base64_decode($_REQUEST['iddasboard'])));
                    $admin = new \RocketChat\User();
                    $admin->login();
                    $channel = new \RocketChat\Channel('N');
                    $infoChannel = $channel->infoByName($nameChat);
                    if($infoChannel->success){
                    $existChat=$infoChannel->channel->name; 
                    }
                    if($userPro==$_SESSION['loggedUsername']){
                        $userChat=$admin->infoByUsername($_SESSION['loggedUsername']);
                        $userId=$userChat->user->_id;
                        $idChat=$infoChannel->channel->_id;
                    }*/
                    if(isset($_SESSION['loggedUsername'])){
                        $existChat='No';
                        $idChat='id';
                        $error='no';
                        $newMessage=0;
                        try  {
                            include '../config.php';
                            include "../rocket-chat-rest-client/RocketChatClient.php";
                            include "../rocket-chat-rest-client/RocketChatUser.php";
                            include "../rocket-chat-rest-client/RocketChatChannel.php";
                            define('REST_API_ROOT', '/api/v1/');
                            define('ROCKET_CHAT_INSTANCE', $chatBaseUrl);
                            $userId='Id';
                            $link = mysqli_connect($host, $username, $password);
                            mysqli_select_db($link, $dbname);
                            $query = "SELECT user,name_dashboard FROM Dashboard.Config_dashboard WHERE Config_dashboard.Id = '".base64_decode(escapeForSQL($_REQUEST['iddasboard'], $link))."'";
                            $queryResult = mysqli_query($link, $query);
                            $row = mysqli_fetch_array($queryResult);
                            $userPro=$row["user"];
                            $nameDash=$row["name_dashboard"];
                            $nameChat=strtolower((str_replace(" ", "", $nameDash) . "-" . base64_decode($_REQUEST['iddasboard'])));
                            $admin = new \RocketChat\User();
                            if($admin->login()){
                            $channel = new \RocketChat\Channel('N');
                            $userChat=$admin->infoByUsername($_SESSION['loggedUsername']);
                            $userId=$userChat->user->_id;
                             if ($_SESSION['loggedRole'] == "RootAdmin"){
                                $admin->setRole($userId);
                                }
                            
                            $nameChat=urldecode ($nameChat);
                            $nameChat = str_replace('à', 'a', $nameChat);
                            $nameChat = str_replace('è', 'e', $nameChat);
                            $nameChat = str_replace('é', 'e', $nameChat);
                            $nameChat = str_replace('ì', 'i', $nameChat);
                            $nameChat = str_replace('ò', 'o', $nameChat);
                            $nameChat = str_replace('ù', 'u', $nameChat);
                            $nameChat = str_replace('å', 'a', $nameChat);
                            $nameChat = str_replace('ë', 'e', $nameChat);
                            $nameChat = str_replace('ô', 'o', $nameChat);
                            $nameChat = str_replace('á', 'a', $nameChat);
                            $nameChat = str_replace('ç', 'c', $nameChat);
                            $nameChat = str_replace('ÿ', 'y', $nameChat);
                            $nameChat=preg_replace("/[^a-zA-Z0-9_-]/", "", $nameChat);
                            $infoChannel=$channel->infoUserChannel($nameChat,$userId);
                            if($infoChannel->joined){
                            //var_dump($existChat);
                            //var_dump(preg_replace("/[^a-zA-Z0-9_-]/", "", $existChat));
                            $infoChannelName = $channel->infoByName($nameChat);
                            $existChat=$infoChannelName->channel->name; 
                            $newMessage=$infoChannel->unreads;
                            }elseif ($_SESSION['loggedRole'] == "RootAdmin") {
                            $infoChannelName = $channel->infoByName($nameChat);
                            //var_dump($newMessage);
                            if($infoChannel->success){
                            $existChat=$infoChannelName->channel->name;     
                            $existChat=$nameChat; 
                            }
                            }
                            $admin->logout();
                                if($userPro==$_SESSION['loggedUsername']){
                                 $idChat=$infoChannelName->channel->_id;
                                } 
                                }
                                $error='Error Chat User';
                            }
                            catch (Exception $e) {
                            $error=$e->getMessage();
                        }
                    
                    
                    //}*/
                    ?>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                
                   setTimeout(function() {
                            /*if (<?php echo!isset($_SESSION['loggedUsername']); ?>){
                                  $('#chatBtn').attr('style', 'display: float');
                                  $('#chatIframeB').attr('src', 'http:\\www.google.it');
                            } else */if('<?php echo $existChat; ?>'!='No'&&'<?php echo $idChat; ?>'=='id'){
                                $('#chatIframeB').attr('style', 'height: 0px');
                                $('#chatIframe').attr('src', '<?php echo $chatBaseUrl; ?>/channel/<?php echo $existChat; ?>/?layout=embedded');
                                //
                                if('<?php echo $newMessage; ?>'=='0'){
                                    $('#chatBtn').attr('style', 'display: float');
                                
                                    }else{
                                $('#chatBtnNew').attr('style', 'display: float;color:red');
                                }
                            }else if ('<?php echo $existChat; ?>'!='No'&&'<?php echo $idChat; ?>'!='id'){
                                  
                                $('#chatIframeB').attr('style', 'height: 100px');
                                $('#chatIframeB').attr('src', 'chatFrame.php?nameChat=<?php echo $existChat; ?>&idDash=<?php echo base64_decode($_REQUEST['iddasboard']); ?>&idChat=<?php echo $idChat; ?>&idUserChat=<?php echo $userId; ?>');
                                $('#chatIframe').attr('src', '<?php echo $chatBaseUrl; ?>/channel/<?php echo $existChat; ?>/?layout=embedded');
                                $('#chatBtn').attr('style', 'display: float');
                                
                                }else if ('<?php echo $error; ?>'!='no'){
                                    console.log('Chat Error: <?php echo $error; ?>');
                                    }
                           }, 50);
                    <?php   
                       }
                    //}*/
                    ?>
            /* setTimeout(function() {
                $('#chatIframeC').attr('src', 'chatAdd.php?nameDash=' + $('#dashboardTitle span').text() + "-" + "<?= base64_decode($_REQUEST['iddasboard']) ?>" + "&idDash=<?= base64_decode($_REQUEST['iddasboard']) ?>");
                $('#chatIframe').attr('src', 'chatPage.php?nameDash=' + $('#dashboardTitle span').text() + "-" + "<?= base64_decode($_REQUEST['iddasboard']) ?>")
            }, 50);*/
            
           /* $('#chatIframe').on('load', function()
            {   
                try {
                var iframe = document.getElementById("chatIframe");
                $elmnt = iframe.contentWindow.document.getElementById("chatEx");
                } catch(e) {
                    console.log("OK");
                    $('#chatBtn').attr('style', 'display: float');
                }
                if ($elmnt == null) {
                    $('#chatBtn').attr('style', 'display: float');
                }
                
                      
            });*/
            // Fullscreen: passargli sempre il documentElement 
            $('#fullscreenButton').click(function(){
                if(document.documentElement.requestFullscreen) 
                {
                    document.documentElement.requestFullscreen();
                } 
                else if(document.documentElement.mozRequestFullScreen) 
                {
                    document.documentElement.mozRequestFullScreen();
                }
                else if(document.documentElement.webkitRequestFullScreen) 
                {
                    document.documentElement.webkitRequestFullScreen();
                } 
                else if(document.documentElement.msRequestFullscreen) 
                {
                    document.documentElement.msRequestFullscreen();
                }
                $('#fullscreenButton').hide();
                $('#restorescreenButton').show();
            });
            
            $('#restorescreenButton').click(function(){
                if(document.exitFullscreen) 
                {
                    document.exitFullscreen();
                } 
                else if(document.webkitExitFullscreen) 
                {
                    document.webkitExitFullscreen();
                } 
                else if(document.mozCancelFullScreen) 
                {
                    document.mozCancelFullScreen();
                } 
                else if(document.msExitFullscreen) 
                {
                    document.msExitFullscreen();
                }
                $('#restorescreenButton').hide();
                $('#fullscreenButton').show();
            });
            
            $(window).resize(function(){
                $('#clock').textfill({
                    maxFontPixels: 24
                });
                
                $('#fullscreenBtnContainer').textfill({
                    maxFontPixels: 32
                });
                
                $('#dashboardTitle').textfill({
                    maxFontPixels: -20
                });
                
                $('#dashboardSubtitle').textfill({
                    maxFontPixels: -20
                });
                
                //Ricalcolo del posizionamento della finestra della chat
                $("#chatContainer").css("top", $('#dashboardViewHeaderContainer').height());
                $("#chatContainer").css("left", $(window).width() - $('#chatContainer').width());
        
                switch(dashboardViewMode)
                {
                    case "fixed":
                        gridsterCellW = 76;
                        gridsterCellH = 38;
                        widgetsContainerWidth = num_cols*(gridsterCellW + 2);
                        break;
                        
                    case "smallResponsive":
                        if($(window).width() > 768)
                        {
                            gridsterCellW = 76;
                            gridsterCellH = 38;
                            widgetsContainerWidth = num_cols*(gridsterCellW + 2);
                        }
                        else
                        {
                            gridsterCellW = Math.floor(parseInt($('body').width()*0.98) / num_cols) - 2;
                            gridsterCellH = gridsterCellW/2;
                            widgetsContainerWidth = num_cols*(gridsterCellW + 2);
                        }
                        break;
                        
                    case "mediumResponsive":
                        if($(window).width() > 992)
                        {
                            gridsterCellW = 76;
                            gridsterCellH = 38;
                            widgetsContainerWidth = num_cols*(gridsterCellW + 2);
                        }
                        else
                        {
                            gridsterCellW = Math.floor(parseInt($('body').width()*0.98) / num_cols) - 2;
                            gridsterCellH = gridsterCellW/2;
                            widgetsContainerWidth = num_cols*(gridsterCellW + 2);
                        }
                        break;
                        
                    case "largeResponsive":
                        if($(window).width() > 1200)
                        {
                            gridsterCellW = 76;
                            gridsterCellH = 38;
                            widgetsContainerWidth = num_cols*(gridsterCellW + 2);
                        }
                        else
                        {
                            gridsterCellW = Math.floor(parseInt($('body').width()*0.98) / num_cols) - 2;
                            gridsterCellH = gridsterCellW/2;
                            widgetsContainerWidth = num_cols*(gridsterCellW + 2);
                        }
                        break;    
                        
                    case "alwaysResponsive":
                        gridsterCellW = Math.floor(parseInt($('body').width()*0.98) / num_cols) - 2;
                        gridsterCellH = gridsterCellW/2;
                        widgetsContainerWidth = num_cols*(gridsterCellW + 2);
                        break;
                        
                    default:
                        gridsterCellW = 76;
                        gridsterCellH = 38;
                        widgetsContainerWidth = num_cols*(gridsterCellW + 2);
                        break;    
                }
                
                $('#dashboardViewWidgetsContainer').css('width', widgetsContainerWidth + "px");
                $('div.footerLogos').css('margin-right', ($('body').width() - widgetsContainerWidth)/2);

                gridster.resize_widget_dimensions({
                    widget_base_dimensions: [gridsterCellW, gridsterCellH],
                    widget_margins: [1, 1]
                });
                                
                $('li.gs_w').trigger({
                    type: "resizeWidgets"
                }); 
            });
            
            //Definizioni di funzione
            function loadDashboard(dashboardParams, dashboardWidgets)
            {
                var minEmbedDim, autofitAlertFontSize;
                
                globalDashboardTitle = dashboardParams.title_header;
                dashBckImg = dashboardParams.bckImgFilename;
                useBckImg = dashboardParams.useBckImg;
                backOverlayOpacity = dashboardParams.backOverlayOpacity;
                
                if((dashBckImg !== null)&&(useBckImg === 'yes'))
                {
                    $('#dashBckCnt').css('background-image', 'url("../img/dashBackgrounds/dashboard' + "<?= base64_decode($_GET['iddasboard']) ?>" + '/' + dashBckImg + '")');
                    $('#dashBckOverlay').show();
                    $('#dashBckOverlay').css('background-color', 'rgba(0,0,0,' + backOverlayOpacity + ')');
                } 
                else
                {
                    $('#dashBckOverlay').hide();
                }

                if( navigator.userAgent.match(/Android/i)
                    || navigator.userAgent.match(/webOS/i)
                    || navigator.userAgent.match(/iPhone/i)
                    || navigator.userAgent.match(/iPad/i)
                    || navigator.userAgent.match(/iPod/i)
                    || navigator.userAgent.match(/BlackBerry/i)
                    || navigator.userAgent.match(/Windows Phone/i)) {

                    $('#dashBckCnt').css('width', '100%');
                    $('#dashBckCnt').css('height', '100%');

                }

                if('<?php echo $embeddable; ?>' === 'yes')
                {
                    if(window.self !== window.top)
                    {
                        if(('<?php echo escapeForJS($embedPolicy); ?>' === 'auto')||(('<?php echo escapeForJS($embedPolicy); ?>' !== 'auto')&&('<?php echo escapeForJS($embedAutofit); ?>' === 'yes')))
                        {
                            $('#autofitAlert').css("width", $(window).width());
                            $('#autofitAlert').css("height", $(window).height());
                            $('#autofitAlertMsgContainer').css("height", $(window).height()*0.45);
                            $('#autofitAlertIconContainer').css("height", $(window).height()*0.55);
                            
                            if($(window).height() < $(window).width())
                            {
                                minEmbedDim = $(window).height();
                            }
                            else
                            {
                                minEmbedDim = $(window).width();
                            }
                            
                            if((minEmbedDim > 0) && (minEmbedDim < 300))
                            {
                                autofitAlertFontSize = 16;
                            }
                            else
                            {
                                if((minEmbedDim >= 300) && (minEmbedDim < 600))
                                {
                                    autofitAlertFontSize = 24;
                                }
                                else
                                {
                                    if((minEmbedDim >= 600) && (minEmbedDim < 900))
                                    {
                                        autofitAlertFontSize = 32;
                                    }
                                    else
                                    {
                                        autofitAlertFontSize = 36;
                                    }
                                }
                            }
                            
                            $('#autofitAlertMsgContainer').css("font-size", autofitAlertFontSize + "px");
                            $('#autofitAlertIconContainer i.fa-spin').css("font-size", autofitAlertFontSize*2 + "px");
                            
                            $('#autofitAlert').show();
                        }
                    }
                }
                
                $('body').removeClass("dashboardViewBodyAuth");
                
                dashboardId = <?= escapeForJS(base64_decode($_GET['iddasboard'])) ?>;
                dashboardName = dashboardParams.name_dashboard;
                dashboardOrg = dashboardParams.organizations;
                logoFilename = dashboardParams.logoFilename;
                logoLink = dashboardParams.logoLink;
                headerVisible = dashboardParams.headerVisible;
                dashboardViewMode = dashboardParams.viewMode;
                $("#headerLogoImg").css("display", "none");
                $("#dashboardViewHeaderContainer").css("background-color", dashboardParams.color_header);

                $.ajax({
                    url: "../controllers/getOrganizationParameters.php",
                    data: {
                        action: "getSpecificOrgParameters",
                        param: dashboardOrg
                    },
                    type: "GET",
                    async: true,
                    dataType: 'json',
                    success: function (data) {
                     /*   var orgLatLng = data.orgGpsCentreLatLng;
                        microAppLat = orgLatLng.split(",")[0].trim();
                        microAppLng = orgLatLng.split(",")[1].trim();
                        $('#iotApplicationsIframe').attr('src', url + '&coordinates='+microAppLat+';'+microAppLng+'&lang=ita&maxDistance=0.3&maxResults=150');  */
                        dashboardOrgKbUrl = data.orgKbUrl;
                    },
                    error: function (errorData) {
                        console.log("Errore in reperimento parametri Org specifica: ");
                        console.log(JSON.stringify(errorData));
                    }
                });

                //Sfondo
                $("body").css("background-color", dashboardParams.external_frame_color);
                $("#dashboardViewWidgetsContainer").css("background-color", dashboardParams.color_background);
                var headerFontColor = dashboardParams.headerFontColor;
                var headerFontSize = dashboardParams.headerFontSize;
                
                $("#dashboardTitle").css("color", headerFontColor);
                //$('#chatBtn').css("color", $('#dashboardTitle').css('color'));
                $("#dashboardTitle span").text(dashboardParams.title_header);
                $("#clock").css("color", headerFontColor);
                $('#fullscreenBtnContainer').css("color", headerFontColor);
                
                $('#clock').textfill({
                    maxFontPixels: -20
                });
                
                $('#fullscreenBtnContainer').textfill({
                    maxFontPixels: 32
                });

                var whiteSpaceRegex = '^[ t]+';
                if((dashboardParams.subtitle_header === "") || (dashboardParams.subtitle_header === null) ||(typeof dashboardParams.subtitle_header === 'undefined') ||(dashboardParams.subtitle_header.match(whiteSpaceRegex)))
                {
                    $("#dashboardTitle").css("height", "100%");
                    $("#dashboardSubtitle").css("display", "none");
                }
                else
                {
                    $("#dashboardTitle").css("height", "70%");
                    $("#dashboardSubtitle").css("height", "30%");
                    $("#dashboardSubtitle").css("display", "flex");
                    $("#dashboardSubtitle").css("color", headerFontColor);
                    $("#dashboardSubtitle span").text(dashboardParams.subtitle_header);
                }
                
                $('#dashboardTitle').textfill({
                    maxFontPixels: -20
                });
                
                $('#dashboardSubtitle').textfill({
                    maxFontPixels: -20
                });

                if(logoFilename !== null)
                {
                    $("#headerLogoImg").prop("src", "../img/dashLogos/dashboard" + dashboardId + "/" + logoFilename);
                    $("#headerLogoImg").prop("alt", "Dashboard logo");
                    $("#headerLogoImg").show();
                    if((logoLink !== null) && (logoLink !== ''))
                    {
                       var logoImage = $('#headerLogoImg');
                       var logoLinkElement = $('<a href="' + logoLink + '" target="_blank" class="pippo">'); 
                       logoImage.wrap(logoLinkElement); 
                    }
                }

                num_cols = dashboardParams.num_columns;
                
                switch(dashboardViewMode)
                {
                    case "fixed":
                        gridsterCellW = 76;
                        gridsterCellH = 38;
                        widgetsContainerWidth = num_cols*(gridsterCellW + 2);
                        break;
                        
                    case "smallResponsive":
                        if($(window).width() > 768)
                        {
                            gridsterCellW = 76;
                            gridsterCellH = 38;
                            widgetsContainerWidth = num_cols*(gridsterCellW + 2);
                        }
                        else
                        {
                            gridsterCellW = Math.floor(parseInt($('body').width()*0.98) / num_cols) - 2;
                            gridsterCellH = gridsterCellW/2;
                            widgetsContainerWidth = num_cols*(gridsterCellW + 2);
                        }
                        break;
                        
                    case "mediumResponsive":
                        if($(window).width() > 992)
                        {
                            gridsterCellW = 76;
                            gridsterCellH = 38;
                            widgetsContainerWidth = num_cols*(gridsterCellW + 2);
                        }
                        else
                        {
                            gridsterCellW = Math.floor(parseInt($('body').width()*0.98) / num_cols) - 2;
                            gridsterCellH = gridsterCellW/2;
                            widgetsContainerWidth = num_cols*(gridsterCellW + 2);
                        }
                        break;
                        
                    case "largeResponsive":
                        if($(window).width() > 1200)
                        {
                            gridsterCellW = 76;
                            gridsterCellH = 38;
                            widgetsContainerWidth = num_cols*(gridsterCellW + 2);
                        }
                        else
                        {
                            gridsterCellW = Math.floor(parseInt($('body').width()*0.98) / num_cols) - 2;
                            gridsterCellH = gridsterCellW/2;
                            widgetsContainerWidth = num_cols*(gridsterCellW + 2);
                        }
                        break;     
                        
                    case "alwaysResponsive":
                        gridsterCellW = Math.floor(parseInt($('body').width()*0.98) / num_cols) - 2;
                        gridsterCellH = gridsterCellW/2;
                        widgetsContainerWidth = num_cols*(gridsterCellW + 2);
                        break;
                        
                    default:
                        gridsterCellW = 76;
                        gridsterCellH = 38;
                        widgetsContainerWidth = num_cols*(gridsterCellW + 2);
                        break;    
                }
                
                $('#dashboardViewWidgetsContainer').css('width', widgetsContainerWidth + "px");
                $('div.footerLogos').css('margin-right', ($('body').width() - widgetsContainerWidth)/2);
                
               if(window.self === window.top)
               {
                    //Controllo mostrare/nascondere header su view principale
                    if(headerVisible === '1')
                    {
                       $("#dashboardViewHeaderContainer").show();
                       $('#dashboardViewWidgetsContainer').css('margin-top', ($('#dashboardViewHeaderContainer').height() + 15) + "px");
                    }
                    else
                    {
                       $("#dashboardViewHeaderContainer").hide();
                       $('#dashboardViewWidgetsContainer').css('margin-top', "0px");
                    } 
               }
               else
               {
                    //Controllo mostrare/nascondere header in modalità embedded
                    if('<?php echo escapeForJS($embedPolicy); ?>' === 'auto')
                    {
                        $("#dashboardViewHeaderContainer").hide();
                        $("#dashboardViewHeaderContainer").css("margin-bottom", "0px");
                    }
                    else
                    {
                        if('<?php echo escapeForJS($showHeaderEmbedded); ?>' === 'no')
                        {
                            $("#dashboardViewHeaderContainer").hide();
                            $("#dashboardViewHeaderContainer").css("margin-bottom", "0px");
                        }
                        else
                        {
                            $("#dashboardViewHeaderContainer").show();
                            $('#dashboardViewWidgetsContainer').css('margin-top', ($('#dashboardViewHeaderContainer').height() + 15) + "px");
                        }
                    }
                    $("#logos a.footerLogo").hide();
                    $("#logos #embedAutoLogoContainer").show();
               }
               
                gridster = $("#gridsterUl").gridster({
                    widget_base_dimensions: [gridsterCellW, gridsterCellH],
                    widget_margins: [1, 1],
                    min_cols: num_cols,
                    max_size_x: 100,
                    max_rows: 100,
                    extra_rows: 100,
                    draggable: {ignore_dragging: false},
                    serialize_params: function ($w, wgd){
                        return {
                            id: $w.attr('id'),
                            col: wgd.col,
                            row: wgd.row,
                            size_x: wgd.size_x,
                            size_y: wgd.size_y
                        };
                    }
                }).data('gridster').disable();//Fine creazione Gridster
                
                for(var i = 0; i < dashboardWidgets.length; i++)
                {
                    var time = 0;
                    if(dashboardWidgets[i]['temporal_range_w'] === "Mensile") 
                    {
                        time = "30/DAY";
                    } 
                    else if (dashboardWidgets[i]['temporal_range_w'] === "Annuale") 
                    {
                        time = "365/DAY";
                    } 
                    else if (dashboardWidgets[i]['temporal_range_w'] === "Settimanale") 
                    {
                        time = "7/DAY";
                    } 
                    else if (dashboardWidgets[i]['temporal_range_w'] === "Giornaliera") 
                    {
                        time = "1/DAY";
                    } 
                    else if (dashboardWidgets[i]['temporal_range_w'] === "4 Ore") 
                    {
                        time = "4/HOUR";
                    } 
                    else if (dashboardWidgets[i]['temporal_range_w'] === "12 Ore") 
                    {
                        time = "12/HOUR";
                    }
                    var widget = ['<li data-widgetType="' + dashboardWidgets[i]['type_w'] + '" data-widgetId="' + dashboardWidgets[i]['Id'] + '" id="' + dashboardWidgets[i]['name_w'] + '"></li>', dashboardWidgets[i]['size_columns'], dashboardWidgets[i]['size_rows'], dashboardWidgets[i]['n_column'], dashboardWidgets[i]['n_row']];

                    gridster.add_widget.apply(gridster, widget);
                    
                    if(('<?php echo $embeddable; ?>' === 'yes')&&(window.self !== window.top))
                    {
                        embedWidget = true;
                    }
                    else
                    {
                        embedWidget = false;
                    }
                    embedWidgetPolicy = '<?php echo escapeForJS($embedPolicy); ?>';
                    
                    dashboardWidgets[i].time = time;
                    dashboardWidgets[i].embedWidget = embedWidget;
                    dashboardWidgets[i].embedWidgetPolicy = embedWidgetPolicy;
                    dashboardWidgets[i].hostFile = 'index';
                    $("li#" + dashboardWidgets[i]['name_w']).css('border', '1px solid ' + dashboardWidgets[i].borderColor);
                    
                    $("#gridsterUl").find("li#" + dashboardWidgets[i]['name_w']).load("../widgets/" + encodeURIComponent(dashboardWidgets[i]['type_w']) + ".php", dashboardWidgets[i]);

                }//Fine del secondo for
                
                
                if(('<?php echo $embeddable; ?>' === 'yes')&&(window.self !== window.top))
                {
                    if('<?php echo escapeForJS($embedPolicy); ?>' === 'auto')
                    {
                        //Cambia logo se embedded in sito diverso dal dashboard manager
                        if(!document.referrer.includes(window.self.location.host)||((embedPreview === 'true')&&(document.referrer.includes(window.self.location.host))))
                        {
                            $('#page-wrapper div.container-fluid div.footerLogos').hide();
                            $('#page-wrapper #embedAutoLogoContainer').css("width", $('#wrapper-dashboard').css("width"));
                            $('#page-wrapper div.container-fluid div.footerLogos').hide();
                            $('#page-wrapper #embedAutoLogoContainer').css("background-color", $('#container-widgets').css("background-color"));
                            $('#page-wrapper #embedAutoLogoContainer').css("display", "flex");
                            $('#page-wrapper #embedAutoLogoContainer').css("align-items", "flex-start");
                            $('#page-wrapper #embedAutoLogoContainer').css("justify-content", "flex-start");
                            $('#page-wrapper #embedAutoLogoContainer').css("margin-left", "10px");
                        }
                        
                        $('#wrapper-dashboard').css("width", $('#wrapper-dashboard').width() - 40);
                        $('#page-wrapper div.container-fluid').css('padding-left', '0px');
                        $('#page-wrapper div.container-fluid').css('padding-right', '0px');
                        
                        var widthRatio, heightRatio, iframeW, iframeH, iframeCase = null;
                        
                        //Il timeout serve per consentire a Gridster il caricamento degli widget, purtroppo Gridster non innesca eventi in tal senso
                        setTimeout(function(){
                            if($(window).width() < $('#wrapper-dashboard').width())
                            {
                                iframeW = '0';
                            }
                            else
                            {
                               iframeW = '1';
                            }

                            if($(window).height() < $('#wrapper-dashboard').height())
                            {
                                iframeH = '0';
                            }
                            else
                            {
                                iframeH = '1';
                            }

                            iframeCase = iframeW + iframeH;
                            
                            //console.log("iframeCase: " + iframeCase);
                            
                            switch(iframeCase)
                            {
                                case '00':
                                    widthRatio = parseInt($(window).width() + 17) / $('#wrapper-dashboard').width();
                                    heightRatio = parseInt($(window).height() + 17) / $('#wrapper-dashboard').height();
                                    $('body').css('overflow', 'hidden');

                                    $('#wrapper-dashboard').css("-ms-transform-origin", '0 0');
                                    $('#wrapper-dashboard').css("-webkit-transform-origin", '0 0');
                                    $('#wrapper-dashboard').css("-moz-transform-origin", '0 0');
                                    $('#wrapper-dashboard').css("transform-origin", '0 0');
                                    $('#wrapper-dashboard').css('-ms-transform', 'scale(' + widthRatio + ', ' + heightRatio + ')');
                                    $('#wrapper-dashboard').css('-webkit-transform', 'scale(' + widthRatio + ', ' + heightRatio + ')');
                                    $('#wrapper-dashboard').css('-moz-transform', 'scale(' + widthRatio + ', ' + heightRatio + ')');
                                    $('#wrapper-dashboard').css('transform', 'scale(' + widthRatio + ', ' + heightRatio + ')');
                                    break;
                                    
                                case '01':
                                    widthRatio = parseInt($(window).width() + 0) / $('#wrapper-dashboard').width();
                                    heightRatio = parseInt($(window).height() + 17) / $('#wrapper-dashboard').height();
                                    $('body').css('overflow', 'hidden');

                                    $('#wrapper-dashboard').css("-ms-transform-origin", '0 0');
                                    $('#wrapper-dashboard').css("-webkit-transform-origin", '0 0');
                                    $('#wrapper-dashboard').css("-moz-transform-origin", '0 0');
                                    $('#wrapper-dashboard').css("transform-origin", '0 0');
                                    $('#wrapper-dashboard').css('-ms-transform', 'scale(' + widthRatio + ', ' + heightRatio + ')');
                                    $('#wrapper-dashboard').css('-webkit-transform', 'scale(' + widthRatio + ', ' + heightRatio + ')');
                                    $('#wrapper-dashboard').css('-moz-transform', 'scale(' + widthRatio + ', ' + heightRatio + ')');
                                    $('#wrapper-dashboard').css('transform', 'scale(' + widthRatio + ', ' + heightRatio + ')');
                                    break;    

                                case '10':
                                    widthRatio = parseInt($(window).width() + 17) / $('#wrapper-dashboard').width();
                                    heightRatio = parseInt($(window).height() + 0) / $('#wrapper-dashboard').height();
                                    $('body').css('overflow', 'hidden');
                                    var gapX = parseInt(($(window).width() - $('#wrapper-dashboard').width())/2);
                                    $('#wrapper-dashboard').css("-ms-transform-origin", '0 0');
                                    $('#wrapper-dashboard').css("-webkit-transform-origin", '0 0');
                                    $('#wrapper-dashboard').css("-moz-transform-origin", '0 0');
                                    $('#wrapper-dashboard').css("transform-origin", '0 0');
                                    $('#wrapper-dashboard').css('-ms-transform', 'translateX(-' + gapX + 'px) scale(' + widthRatio + ', ' + heightRatio + ')');
                                    $('#wrapper-dashboard').css('-webkit-transform', 'translateX(-' + gapX + 'px) scale(' + widthRatio + ', ' + heightRatio + ')');
                                    $('#wrapper-dashboard').css('-moz-transform', 'translateX(-' + gapX + 'px) scale(' + widthRatio + ', ' + heightRatio + ')');
                                    $('#wrapper-dashboard').css('transform', 'translateX(-' + gapX + 'px) scale(' + widthRatio + ', ' + heightRatio + ')');
                                    break;
                                    
                                case '11':
                                    widthRatio = parseInt($(window).width() + 0) / $('#wrapper-dashboard').width();
                                    heightRatio = parseInt($(window).height() - 5) / $('#wrapper-dashboard').height();
                                    $('body').css('overflow', 'hidden');
                                    var gapX = parseInt(($(window).width() - $('#wrapper-dashboard').width())/2);
                                    $('#wrapper-dashboard').css("-ms-transform-origin", '0 0');
                                    $('#wrapper-dashboard').css("-webkit-transform-origin", '0 0');
                                    $('#wrapper-dashboard').css("-moz-transform-origin", '0 0');
                                    $('#wrapper-dashboard').css("transform-origin", '0 0');
                                    $('#wrapper-dashboard').css('-ms-transform', 'translateX(-' + gapX + 'px) scale(' + widthRatio + ', ' + heightRatio + ')');
                                    $('#wrapper-dashboard').css('-webkit-transform', 'translateX(-' + gapX + 'px) scale(' + widthRatio + ', ' + heightRatio + ')');
                                    $('#wrapper-dashboard').css('-moz-transform', 'translateX(-' + gapX + 'px) scale(' + widthRatio + ', ' + heightRatio + ')');
                                    $('#wrapper-dashboard').css('transform', 'translateX(-' + gapX + 'px) scale(' + widthRatio + ', ' + heightRatio + ')');
                                    break;    
                            }
                            $('#autofitAlert').hide();
                        }, 2500);
                    }
                    else
                    {
                        //Cambia logo se embedded in sito diverso dal dashboard manager
                        if(!document.referrer.includes(window.self.location.host)||((embedPreview === 'true')&&(document.referrer.includes(window.self.location.host))))
                        {
                            $('#page-wrapper #embedAutoLogoContainer').css("width", $('#container-widgets').css("width"));
                            $('#page-wrapper div.container-fluid div.footerLogos').hide();
                            $('#page-wrapper #embedAutoLogoContainer').css("background-color", $('#container-widgets').css("background-color"));
                            $('#page-wrapper #embedAutoLogoContainer').css("display", "flex");
                            $('#page-wrapper #embedAutoLogoContainer').css("align-items", "flex-start");
                            $('#page-wrapper #embedAutoLogoContainer').css("justify-content", "flex-start");
                            $('#page-wrapper #embedAutoLogoContainer').css("margin-left", "10px");
                        }
                        
                        //Autofit in modalità manuale
                        if('<?php echo escapeForJS($embedAutofit); ?>' === 'yes')
                        {
                            $('#wrapper-dashboard').css("width", $('#wrapper-dashboard').width() - 40);
                            $('#page-wrapper div.container-fluid').css('padding-left', '0px');
                            $('#page-wrapper div.container-fluid').css('padding-right', '0px');

                            var widthRatio, heightRatio, iframeW, iframeH, iframeCase = null;

                            //Il timeout serve per consentire a Gridster il caricamento degli widget, purtroppo Gridster non innesca eventi in tal senso
                            setTimeout(function(){
                                if($(window).width() < $('#wrapper-dashboard').width())
                                {
                                    iframeW = '0';
                                }
                                else
                                {
                                   iframeW = '1';
                                }

                                if($(window).height() < $('#wrapper-dashboard').height())
                                {
                                    iframeH = '0';
                                }
                                else
                                {
                                    iframeH = '1';
                                }

                                iframeCase = iframeW + iframeH;

                                switch(iframeCase)
                                {
                                    case '00':
                                        widthRatio = parseInt($(window).width() + 17) / $('#wrapper-dashboard').width();
                                        heightRatio = parseInt($(window).height() + 17) / $('#wrapper-dashboard').height();
                                        $('body').css('overflow', 'hidden');

                                        $('#wrapper-dashboard').css("-ms-transform-origin", '0 0');
                                        $('#wrapper-dashboard').css("-webkit-transform-origin", '0 0');
                                        $('#wrapper-dashboard').css("-moz-transform-origin", '0 0');
                                        $('#wrapper-dashboard').css("transform-origin", '0 0');
                                        $('#wrapper-dashboard').css('-ms-transform', 'scale(' + widthRatio + ', ' + heightRatio + ')');
                                        $('#wrapper-dashboard').css('-webkit-transform', 'scale(' + widthRatio + ', ' + heightRatio + ')');
                                        $('#wrapper-dashboard').css('-moz-transform', 'scale(' + widthRatio + ', ' + heightRatio + ')');
                                        $('#wrapper-dashboard').css('transform', 'scale(' + widthRatio + ', ' + heightRatio + ')');
                                        break;
                                        
                                    case '01':
                                        widthRatio = parseInt($(window).width() + 0) / $('#wrapper-dashboard').width();
                                        heightRatio = parseInt($(window).height() + 17) / $('#wrapper-dashboard').height();
                                        $('body').css('overflow', 'hidden');

                                        $('#wrapper-dashboard').css("-ms-transform-origin", '0 0');
                                        $('#wrapper-dashboard').css("-webkit-transform-origin", '0 0');
                                        $('#wrapper-dashboard').css("-moz-transform-origin", '0 0');
                                        $('#wrapper-dashboard').css("transform-origin", '0 0');
                                        $('#wrapper-dashboard').css('-ms-transform', 'scale(' + widthRatio + ', ' + heightRatio + ')');
                                        $('#wrapper-dashboard').css('-webkit-transform', 'scale(' + widthRatio + ', ' + heightRatio + ')');
                                        $('#wrapper-dashboard').css('-moz-transform', 'scale(' + widthRatio + ', ' + heightRatio + ')');
                                        $('#wrapper-dashboard').css('transform', 'scale(' + widthRatio + ', ' + heightRatio + ')');
                                        break;    

                                    case '10':
                                        widthRatio = parseInt($(window).width() + 17) / $('#wrapper-dashboard').width();
                                        heightRatio = parseInt($(window).height() + 0) / $('#wrapper-dashboard').height();
                                        $('body').css('overflow', 'hidden');
                                        var gapX = parseInt(($(window).width() - $('#wrapper-dashboard').width())/2);
                                        $('#wrapper-dashboard').css("-ms-transform-origin", '0 0');
                                        $('#wrapper-dashboard').css("-webkit-transform-origin", '0 0');
                                        $('#wrapper-dashboard').css("-moz-transform-origin", '0 0');
                                        $('#wrapper-dashboard').css("transform-origin", '0 0');
                                        $('#wrapper-dashboard').css('-ms-transform', 'translateX(-' + gapX + 'px) scale(' + widthRatio + ', ' + heightRatio + ')');
                                        $('#wrapper-dashboard').css('-webkit-transform', 'translateX(-' + gapX + 'px) scale(' + widthRatio + ', ' + heightRatio + ')');
                                        $('#wrapper-dashboard').css('-moz-transform', 'translateX(-' + gapX + 'px) scale(' + widthRatio + ', ' + heightRatio + ')');
                                        $('#wrapper-dashboard').css('transform', 'translateX(-' + gapX + 'px) scale(' + widthRatio + ', ' + heightRatio + ')');
                                        break;

                                    case '11':
                                        widthRatio = parseInt($(window).width() + 0) / $('#wrapper-dashboard').width();
                                        heightRatio = parseInt($(window).height() + 0) / $('#wrapper-dashboard').height();
                                        $('body').css('overflow', 'hidden');
                                        var gapX = parseInt(($(window).width() - $('#wrapper-dashboard').width())/2);
                                        $('#wrapper-dashboard').css("-ms-transform-origin", '0 0');
                                        $('#wrapper-dashboard').css("-webkit-transform-origin", '0 0');
                                        $('#wrapper-dashboard').css("-moz-transform-origin", '0 0');
                                        $('#wrapper-dashboard').css("transform-origin", '0 0');
                                        $('#wrapper-dashboard').css('-ms-transform', 'translateX(-' + gapX + 'px) scale(' + widthRatio + ', ' + heightRatio + ')');
                                        $('#wrapper-dashboard').css('-webkit-transform', 'translateX(-' + gapX + 'px) scale(' + widthRatio + ', ' + heightRatio + ')');
                                        $('#wrapper-dashboard').css('-moz-transform', 'translateX(-' + gapX + 'px) scale(' + widthRatio + ', ' + heightRatio + ')');
                                        $('#wrapper-dashboard').css('transform', 'translateX(-' + gapX + 'px) scale(' + widthRatio + ', ' + heightRatio + ')');
                                        break;    
                                }
                                $('#autofitAlert').hide();
                            }, 2500); 
                        }
                    }
                }
                else
                {
                    function sendCurrentPosition(position)
                    {
                        console.log("sendCurrentPosition OK");

                        $.ajax({
                            url: "../management/nrSendGpsProxy.php",
                            type: "POST",
                            data: {
                                httpRelativeUrl: encodeURI(globalDashboardTitle),
                                dashboardTitle: encodeURI(globalDashboardTitle),
                                gpsData: JSON.stringify({
                                    latitude: position.coords.latitude,
                                    longitude: position.coords.longitude,
                                    accuracy:  position.coords.accuracy,
                                    altitude:  position.coords.altitude,
                                    altitudeAccuracy:  position.coords.altitudeAccuracy,
                                    heading:  position.coords.heading,
                                    speed:  position.coords.speed
                                })
                            },
                            async: true,
                            dataType: 'json',
                            success: function(data)
                            {
                                console.log("Data sent to test GPS OK");
                                console.log(JSON.stringify(data));
                            },
                            error: function(errorData)
                            {
                                console.log("Data sent to test GPS KO");
                                console.log(JSON.stringify(errorData));
                            }
                        });
                    }
                    
                    function sendCurrentPositionError(obj)
                    {
                        console.log("Get current position KO: " + obj.message);
                    }
                    
                    <?php
                        $genFileContent = parse_ini_file("../conf/environment.ini");
                        $nodeEmittersApiContent = parse_ini_file("../conf/nodeEmittersApi.ini");
                        $myGpsActive = $nodeEmittersApiContent["gpsActive"][$genFileContent['environment']['value']];
                        $myGpsPeriod = $nodeEmittersApiContent["gpsPeriod"][$genFileContent['environment']['value']];
                        echo 'myGpsActive = "' . $myGpsActive . '";';
                        echo 'myGpsPeriod = ' . $myGpsPeriod . ';';
                    ?>
                                                                                                                    
                    if(myGpsActive === 'yes')
                    {
                        myGpsInterval = setInterval(function(){
                            if(navigator.geolocation) 
                            {
                                navigator.geolocation.getCurrentPosition(sendCurrentPosition, sendCurrentPositionError);
                            } 
                            else 
                            { 
                                //console.log("Navigator not available");
                            }
                        }, parseInt(parseInt(myGpsPeriod)*1000))
                    }
                    else
                    {
                        //console.log("Navigator not active");
                    }
                }
                
            }
            
            function authUser()
            {
                $.ajax({
                    url: "../management/getDashboardData.php",
                    //Lasciare il vecchio refuso "iddasboard" per non cambiare i link
                    data: 
                    { 
                        dashboardId: <?= escapeForJS(base64_decode($_GET['iddasboard'])) ?>,
                        username: $("#username").val(),
                        password: $("#password").val(),
                        loggedUserFirstAttempt: loggedUserFirstAttempt
                    },
                    type: "GET",
                    async: true,//LASCIARLA ASINCRONA.
                    dataType: 'json',
                    success: function (response) 
                    {  
                        switch(response.visibility)
                        {
                            case 'public':
                                $('body').removeClass("dashboardViewBodyAuth");
                                $('#authFormDarkBackground').hide();
                                $('#authFormContainer').hide();
                                $("#dashboardViewMainContainer").show();
                                loadDashboard(response.dashboardParams, response.dashboardWidgets);
                                break;

                            case 'author': case 'restrict':    
                                $('body').addClass("dashboardViewBodyAuth");
                                $('#authFormDarkBackground').show();
                                $('#authFormContainer').show();
                                switch(response.detail)
                                {
                                    /*case "credentialsMissing":
                                        $("#dashboardViewMainContainer").hide();
                                        if(firstLoad === false)
                                        {
                                            $("#authFormMessage").html("Credentials missing");
                                        }
                                        else
                                        {
                                            $("#authFormMessage").html("");
                                            firstLoad = false;
                                        }
                                        $("#authBtn").click(authUser);
                                        break;
                                        
                                    case "checkUserQueryKo":
                                        //Fallimento query controllo presenza utente
                                        $("#dashboardViewMainContainer").hide();
                                        $("#authFormMessage").html("Failure during DB query to check user: please try again");
                                        $("#authBtn").click(authUser);
                                        break;
                                        
                                    case "checkLoggedUserQueryKo":
                                        //Fallimento query controllo presenza utente
                                        $("#dashboardViewMainContainer").hide();
                                        $("#authFormMessage").html("Failure during DB query to check user logged to main application: please try again");
                                        $("#authBtn").click(authUser);
                                        break;
                                        
                                    case "checkLoggedViewUserQueryKo":
                                        //Fallimento query controllo presenza utente
                                        $("#dashboardViewMainContainer").hide();
                                        $("#authFormMessage").html("Failure during DB query to check user logged to dashboard view: please try again");
                                        $("#authBtn").click(authUser);
                                        break;     
                                        
                                    case "userNotRegistered":
                                        $("#dashboardViewMainContainer").hide();
                                        $("#authFormMessage").html("User not registered or wrong username / password");
                                        $("#authBtn").click(authUser);
                                        break;*/
                                        
                                    case "Ok": 
                                        $('body').removeClass("dashboardViewBodyAuth");
                                        $('#authFormDarkBackground').hide();
                                        $('#authFormContainer').hide();
                                        $("#dashboardViewMainContainer").show();
                                        $('#hiddenUsername').val($("#username").val());
                                        loadDashboard(response.dashboardParams, response.dashboardWidgets);
                                        
                                        if(response.context === "View")
                                        {
                                            $("#viewLogoutBtn").show();
                                            $("#viewLogoutBtn").click(function(){
                                                event.preventDefault();
                                                $("#logoutViewModal").modal('show');
                                            });
                                        }
                                    break;
                                    
                                    default:
                                        /*$("#dashboardViewMainContainer").hide();
                                        $("#authFormMessage").html("User not allowed to see this dashboard");
                                        $('body').addClass("dashboardViewBodyAuth");
                                        $('#authFormDarkBackground').show();
                                        $('#authFormContainer').show();
                                        $("#authBtn").click(authUser); */
                                        location.href = "../management/viewLogout.php?dashboardId=<?= escapeForJS($_REQUEST['iddasboard'])?>";
                                        break;

                                    /*case "Ko": 
                                        $("#dashboardViewMainContainer").hide();
                                        $("#authFormMessage").html("User not allowed to see this dashboard");
                                        $('body').addClass("dashboardViewBodyAuth");
                                        $('#authFormDarkBackground').show();
                                        $('#authFormContainer').show();
                                        $("#authBtn").click(authUser);        
                                        break;

                                    case "loggedUserKo": 
                                        loggedUserFirstAttempt = false;
                                        $("#dashboardViewMainContainer").hide();
                                        $("#authFormMessage").html("Logged user not allowed to see this dashboard");
                                        $('body').addClass("dashboardViewBodyAuth");
                                        $('#authFormDarkBackground').show();
                                        $('#authFormContainer').show();
                                        $("#authBtn").click(authUser);        
                                        break;

                                    case "loggedViewUserKo": 
                                        loggedUserFirstAttempt = false;
                                        $("#dashboardViewMainContainer").hide();
                                        $("#authFormMessage").html("User logged to dashboard view not allowed to see this dashboard");
                                        $('body').addClass("dashboardViewBodyAuth");
                                        $('#authFormDarkBackground').show();
                                        $('#authFormContainer').show();
                                        $("#authBtn").click(authUser);        
                                        break;  */
                                }
                        }
                    },
                    error: function (data)
                    {
                        $("#dashboardViewMainContainer").hide();
                        $("#authFormContainer").hide();
                        $("#getVisibilityError").show();
                        console.log("Error: " + JSON.stringify(data));
                    }
                }); 
            }
            //Fine definizioni di funzione
            
            //Main
            authUser();
            myVar = setInterval("updateFunction()", 60*1000);    // Firing access count every 1 MINUTE

        });

        function updateFunction(){

            $.getJSON('../controllers/dashDailyAccessController.php?updateHour=true',
                {
                    dashId: <?= escapeForJS(base64_decode($_GET['iddasboard'])) ?>
                },
                function (data) {

                });

        }

    </script>
</head>
<body>
    <?php include "../management/sessionExpiringPopup.php" ?>
    <div id="dashBckCnt">
       <div id="dashBckOverlay">
        
       </div>             
    </div>
    <div id="dashboardViewMainContainer" class="container-fluid">
        <nav id="dashboardViewHeaderContainer" class="navbar navbar-fixed-top" role="navigation">
            <div id="fullscreenBtnContainer" data-status="normal">
                <span>
                    <i id="fullscreenButton" class="fa fa-window-maximize"></i>
                    <i id="restorescreenButton" class="fa fa-window-restore"></i>            
                </span>
            </div>
            <div id="dashboardViewTitleAndSubtitleContainer">
                <div id="dashboardTitle">
                    <span contenteditable="false"></span>
                </div>
                <div id="dashboardSubtitle">
                    <span></span>
                </div>            
            </div>
            <div id="headerLogo">
                <img id="headerLogoImg"/>
               <i class="fa fa-comment-o" id="chatBtn" data-status="closed" style="display: none"></i> <!-- style="display: none !important" -->
               <i class="fa fa-comment" id="chatBtnNew" data-status="closed" style="display: none"></i>
                <!--<i class="fas fa-bullhorn" id="chatBtn" data-status="closed" style="display: none"></i>-->
                <!--<i class="fas fa-bullhorn" style="font-size:48px;display: none" id="chatBtnNew" data-status="closed"></i>-->
            </div>
            <div id="clock">
                <span id="tick2"><?php include('../widgets/time.php'); ?></span>
            </div>
        </nav>
        
        <div id="dashboardViewWidgetsContainer" class="gridster">
            <ul id="gridsterUl"></ul>            
        </div>

        <hr id="horizontalFooterLine" style="height:1px;width:75%;border:none;color:#333;background-color:#333;margin-bottom:-10px;" />
        <div class="footerNavRow">
            <div id="firstColumnFooter" class="footerNavColumn">
                <!-- empty -->
            </div>
            <div id="footerPolicyId" class="footerNavColumn">
                <ul class="menu nav">
                    <li class="footerNavMenu"><a href="https://www.snap4city.org/drupal/node/49" target="_blank" style="font-size:13px;color:black;font-weight: bold;" title="">Privacy Policy</a></li>
                    <li class="footerNavMenu"><a href="https://www.snap4city.org/drupal/node/48" target="_blank" style="font-size:13px;color:black;font-weight: bold;" title="">Cookies Policy</a></li>
                    <li class="footerNavMenu"><a href="https://www.snap4city.org/drupal/legal" target="_blank" style="font-size:13px;color:black;font-weight: bold;" title="">Terms and Conditions</a></li>
                    <li class="footerNavMenu"><a href="https://www.snap4city.org/drupal/contact" target="_blank" style="font-size:13px;color:black;font-weight: bold;" title="">Contact us</a></li>
                </ul>
            </div>
            <div id="footerLogoId" class="footerNavColumn">
                <div style="width:68%;float:right;">
                <a title="Disit" href="https://www.snap4city.org" target="_new" class="footerLogo"><img src="https://dashboard.km4city.org/img/applicationLogos/disitLogoTransparent.png" alt="Mountains" style="width:100%"></a>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            // Get URL
            var url = window.location.href;
            // Get DIV
            var msg0 = document.getElementById('horizontalFooterLine');
            var msg1 = document.getElementById('firstColumnFooter');
            var msg2 = document.getElementById('footerPolicyId');
            var msg3 = document.getElementById('footerLogoId');
            // Check if URL contains the keyword
            if( url.search('embedPolicy=') > 0 ) {
                // Display the message
                msg0.style.display = "none";
           //     msg1.style.display = "none";
                msg2.style.display = "none";
            //    msg3.style.margin = "auto";
            //    msg3.style.width = "50%";

            }
        </script>

    </div>
    
    <div id="chatContainer" data-status="closed">
        <iframe id="chatIframeB" class="chatIframe" scrolling="no"></iframe>
        <iframe id="chatIframe" class="chatIframe"></iframe>
    </div>

    <div id="authFormDarkBackground">
        <div class="row">
            <div class="col-xs-12 centerWithFlex" id="loginMainTitle">Dashboard Management System</div>
        </div>
        
        <div class="row">
            <div id="authFormContainer" class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-4 col-md-offset-4">
                <div class="col-xs-12" id="loginFormTitle" style="margin-top: 15px">
                   Restricted access dashboard
                </div>
<?php /*              
                <form id="authForm" class="form-signin" role="form" method="post" action="">
                    <div class="col-xs-12" id="loginFormBody">
                        <div class="col-xs-12 modalCell">
                            <div class="modalFieldCnt">
                                <input type="text" class="modalInputTxt" id="username" name="username" required> 
                            </div>
                            <div class="modalFieldLabelCnt">Username</div>
                        </div>
                        <div class="col-xs-12 modalCell">
                            <div class="modalFieldCnt">
                                <input type="password" class="modalInputTxt" id="password" name="password" required> 
                            </div>
                            <div class="modalFieldLabelCnt">Password</div>
                        </div>
                        <div class="col-xs-12 modalCell">
                            <div id="authFormMessage"></div>
                        </div>
                        <input type="hidden" id="hiddenUsername" name="hiddenUsername"> 
                    </div>
                <div class="col-xs-12 centerWithFlex" id="loginFormFooter" style="margin-bottom: 15px">
                    <button type="reset" id="loginCancelBtn" class="btn cancelBtn" data-dismiss="modal">Reset</button>
                    <button type="button" id="authBtn" name="login" class="btn confirmBtn internalLink">Login</button>
                </div>
                </form>*/?>
            </div>
        </div>
    </div> 
    
    <!-- MODALI -->
    <!-- Modale informazioni widget -->
    <div class="modal fade" id="widgetInfoModal" tabindex="-1" role="dialog" aria-labelledby="widgetInfoModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content modalContentWizardForm"> 
                <div class="modalHeader centerWithFlex">
                    <div class="col-xs-10 col-xs-offset-1"></div>
                    <div class="col-xs-1">
                        <button type="button" class="compactMenuCancelBtn" id="widgetInfoModalCancelBtnView" data-dismiss="modal"><i class="fa fa-remove"></i></button>             
                    </div>
                </div>
            
                <div id="widgetInfoModalBodyView" class="modal-body modalBody">
                    
                </div>
            </div>    <!-- Fine modal content -->
        </div> <!-- Fine modal dialog -->
    </div>
    <!-- Fine modale informazioni widget -->
    
    
    <!-- Modale di conferma logout dashboard -->
    <div class="modal fade" id="logoutViewModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modalHeader centerWithFlex">
              Close this dashboard
            </div>
            <div id="delDsModalBody" class="modal-body modalBody">
                <div class="row" id="logoutViewModalMsg">
                    <div class="col-xs-12 modalCell">
                        <div class="modalDelMsg col-xs-12 centerWithFlex">
                            Do you want to confirm logout from this dashboard? 
                        </div>
                        <div class="modalDelObjName col-xs-12 centerWithFlex" id="delDsName"></div> 
                    </div>
                </div>
                <div class="row" id="logoutViewModalOk">
                    <div class="col-xs-12 centerWithFlex">Logout correctly executed</div>
                    <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-up" style="font-size:36px"></i></div>
                </div>
                <div class="row" id="logoutViewModalKo">
                    <div class="col-xs-12 centerWithFlex">Logout not possibile, please try again</div>
                    <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-down" style="font-size:36px"></i></div>
                </div>
            </div>
            <div id="logoutViewModalFooter" class="modal-footer">
              <button type="button" id="discardLogoutBtn" class="btn cancelBtn" data-dismiss="modal">Cancel</button>
              <button type="button" id="confirmLogoutBtn" class="btn confirmBtn internalLink">Confirm</button>
            </div>
          </div>
        </div>
    </div>
    
    <!-- Modale impossibilità di apertura link in nuovo tab per widgetExternalContent -->
    <div class="modal fade" tabindex="-1" id="newTabLinkOpenImpossibile" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document"> 
            <div class="modal-content">
                <div class="modal-header centerWithFlex">
                    <h4 class="modal-title">External content</h4>
                </div>
                <div class="modal-body">
                    <div id="newTabLinkOpenImpossibileMsg"></div>
                    <div id="newTabLinkOpenImpossibileIcon">
                         <i class="fa fa-frown-o"></i>           
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modale cambio stato evacuation plan -->
        <div class="modal fade" tabindex="-1" id="modalChangePlanStatus" role="dialog" aria-labelledby="myModalLabel">
            <div id="modalChangePlanStatusDialog" class="modal-dialog modal-lg" role="document"> 
                <div class="modal-content">
                    <div id="modalChangePlanStatusModalTitle" class="modal-header centerWithFlex">
                        evacuation plan status management
                    </div>
                    <div id="modalChangePlanStatusMain" class="modal-body container-fluid">
                        <div class="row">
                            <div class="col-sm-6 centerWithFlex modalChangePlanStatusLabel">
                                plan identifier
                            </div> 
                            <div class="col-sm-6 centerWithFlex modalChangePlanStatusLabel">
                                current approval status
                            </div>
                        </div>
                        <div class="row">
                           <div class="col-sm-6 centerWithFlex" id="modalChangePlanStatusTitle" ></div> 
                           <div class="col-sm-4 col-sm-offset-1 centerWithFlex" id="modalChangePlanStatusStatus"></div>
                        </div>
                       
                        <div class="row">
                           <div class="col-sm-6 col-sm-offset-3 centerWithFlex modalChangePlanStatusLabel">
                               new approval status 
                           </div> 
                        </div>
                        <div class="row">
                           <div class="col-sm-4 col-sm-offset-4 centerWithFlex">
                               <select class="form-control" id="modalChangePlanStatusSelect" name="modalChangePlanStatusSelect" required></select> 
                           </div> 
                        </div>
                    </div>
                    <div id="modalChangePlanStatusWait" class="modal-body container-fluid">
                        <div class="row">
                            <div class="col-sm-6 col-sm-offset-3 centerWithFlex">
                                updating status, please wait
                            </div> 
                        </div>
                        <div class="row">
                            <div class="col-sm-6 col-sm-offset-3 centerWithFlex">
                                <i class="fa fa-spinner fa-spin" style="font-size:84px"></i>
                            </div> 
                        </div>
                    </div>
                    <div id="modalChangePlanStatusOk" class="modal-body container-fluid">
                        <div class="row">
                            <div class="col-sm-10 col-sm-offset-1 centerWithFlex">
                                status successfully updated
                            </div> 
                        </div>
                        <div class="row">
                            <div class="col-sm-6 col-sm-offset-3 centerWithFlex">
                                <i class="fa fa-thumbs-o-up" style="font-size:84px"></i>
                            </div> 
                        </div>
                    </div>
                    <div id="modalChangePlanStatusKo" class="modal-body container-fluid">
                        <div class="row">
                            <div class="col-sm-12 centerWithFlex">
                                error while trying to send new status to server, please try again
                            </div> 
                        </div>
                        <div class="row">
                            <div class="col-sm-6 col-sm-offset-3 centerWithFlex">
                                <i class="fa fa-thumbs-down" style="font-size:84px"></i>
                            </div> 
                        </div>
                    </div>
                    
                    <input type="hidden" id="modalChangePlanStatusPlanId" />
                    <input type="hidden" id="modalChangePlanStatusCurrentStatus" />
                   
                    <div id="modalChangePlanStatusFooter" class="modal-footer centerWithFlex">
                       <button type="button" class="btn btn-secondary" id="modalChangePlanStatusCancelBtn">cancel</button>
                       <button type="button" class="btn btn-primary" id="modalChangePlanStatusConfirmBtn">confirm</button>
                    </div>
                </div>
            </div>
        </div>
    
    <div id="getVisibilityError">
        <div id="wrapper">
            <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="index.html">Dashboard management system</a>
                </div>
            </nav>
        </div>   
        <br/><br/><br/><br/>
        <h1>Error!</h1>
        <p>Error while trying to get dashboard visibility: please try again</p>
    </div>
     
    <div id="autofitAlert">
        <div class="row">
            <div id="autofitAlertMsgContainer" class="col-xs-12">
               Auto refit in progress, please wait                    
            </div>                     
        </div>
        <div class="row">
            <div id="autofitAlertIconContainer" class="col-xs-12">
               <i class="fa fa-circle-o-notch fa-spin"></i>                    
            </div>                     
        </div>                        
    </div>
</body>
</html>
<?php
    //Query $iddasboard=NzU2
$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);
$queryAccess = "SELECT * FROM Dashboard.IdDashDailyAccess WHERE IdDashboard = $dashId ORDER BY date DESC;";
$resultAccess = mysqli_query($link, $queryAccess);
$currentDate = date("Y-m-d");
//$currentDate = '2018-07-21';
$nameDash = $row['name_dashboard'];

//if($resultAccess) {
    if (mysqli_num_rows($resultAccess) > 0) {

        $rowAcc = mysqli_fetch_array($resultAccess);
        if ($rowAcc['date'] === $currentDate) {     // CHECK ON LAST DATE
        // $dashboardWidgets = [];
            $queryUpdate = "UPDATE Dashboard.IdDashDailyAccess SET nAccessPerDay = nAccessPerDay + 1 WHERE IdDashboard = $dashId AND date = '$currentDate';";
            $resultUpdate = mysqli_query($link, $queryUpdate);

        } else {
            // insert in mysql
            $queryInsert = "INSERT INTO Dashboard.IdDashDailyAccess " .
                "(IdDashboard, date, nAccessPerDay, nMinutesPerDay) VALUES ('$dashId', '$currentDate', 1, 0) ON DUPLICATE KEY UPDATE nAccessPerDay = nAccessPerDay + 1;";
            $resultInsert = mysqli_query($link, $queryInsert);
        }
    } else {
        // insert in mysql
        $queryInsert = "INSERT INTO Dashboard.IdDashDailyAccess " .
            "(IdDashboard, date, nAccessPerDay, nMinutesPerDay) VALUES ('$dashId', '$currentDate', 1, 0) ON DUPLICATE KEY UPDATE nAccessPerDay = nAccessPerDay + 1;";
        $resultInsert = mysqli_query($link, $queryInsert);
    }
//}

?>
