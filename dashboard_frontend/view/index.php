<?php 
    /* Dashboard Builder.
   Copyright (C) 2018 DISIT Lab https://www.disit.org - University of Florence

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
   include '../config.php';

   header("Cache-Control: private, max-age=$cacheControlMaxAge");
   
   //Va studiata una soluzione, per ora tolto error reporting
   error_reporting(0);
   session_start();

   $dashId = escapeForJS(base64_decode($_REQUEST['iddasboard']));
   if (checkVarType($dashId, "integer") === false) {
   //     eventLog("Returned the following ERROR in index.php for dashId = ".$dashId.": ".$dashId." is not an integer as expected. USER = " . $_SESSION['loggedUsername'] . ". Exit from script.");
   //     eventLog("Returned the following ERROR in index.php for dashId = ".$dashId.": ".$dashId." is not an integer as expected. Exit from script.");
        eventLog("Returned the following ERROR in index.php for dashId = ".$dashId.": ".$dashId." is not an integer as expected. Exit from script. HTTP REFERER: ".$_SERVER['HTTP_REFERER'] . "; Request URI: " . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . ". Request Method: " . $_SERVER['REQUEST_METHOD']);
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

    if(isset($_REQUEST['showFooter']))
    {
        $showFooterEmbedded = $_REQUEST['showFooter'];
    }
    else
    {
        $showFooterEmbedded = 'yes';
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
                    redirect_on_login();
                }
            }

           if($queryResult->num_rows > 0) 
           {
                $dashOrg = $row['organizations'];
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
    <link href="../css/dashboard_configdash.css?v=<?php echo time();?>" rel="stylesheet">
    
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
 <!--   <script src="../js/highcharts/code/highcharts.js"></script>
    <script src="../js/highcharts/code/modules/exporting.js"></script>
    <script src="../js/highcharts/code/highcharts-more.js"></script>
    <script src="../js/highcharts/code/modules/solid-gauge.js"></script>
    <script src="../js/highcharts/code/highcharts-3d.js"></script>
    <script src="../js/highcharts-8.0.0/code/highcharts.js"></script>
    <script src="../js/highcharts-8.0.0/code/modules/exporting.js"></script>
    <script src="../js/highcharts-8.0.0/code/highcharts-more.js"></script>
    <script src="../js/highcharts-8.0.0/code/modules/parallel-coordinates.js"></script>
    <script src="../js/highcharts-8.0.0/code/modules/solid-gauge.js"></script>
    <script src="../js/highcharts-8.0.0/code/highcharts-3d.js"></script>  -->

    <script src="../js/highcharts-9/code/highcharts.js"></script>
    <script src="../js/highcharts-9/code/modules/exporting.js"></script>
    <script src="../js/highcharts-9/code/highcharts-more.js"></script>
    <script src="../js/highcharts-9/code/modules/parallel-coordinates.js"></script>
    <script src="../js/highcharts-9/code/modules/solid-gauge.js"></script>
    <script src="../js/highcharts-9/code/highcharts-3d.js"></script>
    <script src="../js/highcharts-9/code/modules/streamgraph.js"></script>
    
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

	<!-- MS> WidgetSelectorTech is based on Fancytree -->	
	<link href="../js/skin-win8/ui.fancytree.css" rel="stylesheet">
	<script src="../js/fancytree/jquery.fancytree.ui-deps.js"></script>
	<script src="../js/fancytree/jquery.fancytree.js"></script>
	<!-- <MS -->

    <!-- Hijrah Date -->
    <script type="text/javascript" src="../js/hijrah-date.js"></script>
  <!--  <script src="https://rawgithub.com/miladjafary/highcharts-plugins/master/js/jalali.js"></script>    -->
    <script src="../js/highcharts-localization.js"></script>

    <!-- New WS -->
   <script src="https://www.snap4city.org/synoptics/socket.io/socket.io.js"></script>

    <!-- DataTables -->
    <script type="text/javascript" charset="utf8" src="../js/DataTables/datatables.js"></script>
    <link rel="stylesheet" type="text/css" href="../js/DataTables/datatables.css">
    <script type="text/javascript" charset="utf8" src="../js/DataTables/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript" charset="utf8" src="../js/DataTables/dataTables.responsive.min.js"></script>
    <script type="text/javascript" charset="utf8" src="../js/DataTables/responsive.bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../css/DataTables/dataTables.bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/DataTables/responsive.bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/DataTables/jquery.dataTables.min.css">
    <script type="text/javascript" charset="utf8" src="../js/DataTables/Select-1.2.5/js/dataTables.select.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../js/DataTables/Select-1.2.5/css/select.dataTables.min.css">
    <script type="text/javascript" charset="utf8" src="../js/DataTables/dataTables.scrollResize.min.js"></script>

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
            var scaleFactorFlag, extra_rows_gridster, max_rows_gridster = null;
            var scaleFactorW = 1;
            var scaleFactorH = 1;
            /*  var newScaledGridsterCellW = 15;
              var newScaledGridsterCellH = 7; */
            var newScaledGridsterCellW = 26;
            var newScaledGridsterCellH = 13;
            var widgetMargins = [1, 1];
            var infoMsgPopupFlag = false;
            var infoMsgText = null;

            $('#open_BIMenu').hide();
            $('#orgMenu').hide();
            $('#orgMenuCnt a.mainMenuLink').attr('data-submenuVisible', 'false');
            $('#orgMenuCnt a.orgMenuSubItemLink').hide();
            $('.linkgen').css('text-decoration', 'none');

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

           // $('#orgMenuButton').off('click');
            $('#orgMenuButton').click(function(){
               // alert("Organization Menu Coming Soon !");
                if($('#orgMenu').attr('data-shown') === 'true')
                {
                    $('#orgMenu').hide();
                    $('#orgMenu').attr('data-shown', 'false');
                }
                else
                {
                    $('#orgMenu').show();
                    $('#orgMenu').attr('data-shown', 'true');
                }
            });

            $('#orgMenu .quitRow').off('click');
            $('#orgMenu .quitRow').click(function()
            {
                $('#orgMenu .fullCtxSubmenu').each(function(i){
                    $(this).attr('data-clicked', 'false');
                });
                $('.widgetSubmenu').hide();
                $('.fullCtxMenu').hide();
                $('.applicationCtxMenu').hide();
                $('.fullCtxSubmenu').hide();
                $('#orgMenu').hide();
                $(".fullCtxMenuRow").css('color', 'rgb(51, 64, 69)');
                $(".fullCtxMenuRow").css('background-color', 'transparent');
                $(".fullCtxMenuRow").attr("data-selected", "false");
                $('#orgMenu').attr('data-shown', 'false');

                // Close Org Sub-Menus
                $('#orgMenu a.mainMenuLink').attr('data-submenuVisible', 'false');
                $('.orgMenuSubItemCnt a.orgMenuSubItemLink').hide();
                $('.orgMenuSubItemCnt').css( "display", "none" );
                $('.submenuIndicator').removeClass('fa-caret-up');
                $('.submenuIndicator').addClass('fa-caret-down');
            });

        /*    $('#orgMenu .fullCtxMenuRow').hover(function(){
                if($(this).attr("data-selected") === "false")
                {
                    $(this).css('color', 'white');
                    $(this).css('background-color', 'rgba(0, 162, 211, 1)');
                }
            }, function(){
                if($(this).attr("data-selected") === "false")
                {
                    $(this).css('color', 'rgb(51, 64, 69)');
                    $(this).css('background-color', 'transparent');
                }
            });*/

          //  $('#orgMenuCnt .mainMenuLink').click(function(event){
            $('.mainMenuLink').click(function(event){
         //  $('.linkgen').click(function(event){
                event.preventDefault();
                var pageTitle = $(this).attr('data-pageTitle');
                var linkId = $(this).attr('id');
                var linkUrl = $(this).attr('data-linkUrl');

                if($(this).attr('data-linkUrl') === 'submenu')
                {
                    $('#orgMenuCnt .orgMenuItemCnt').each(function(i){
                        $(this).removeClass('orgMenuItemCntActive');
                    });
                    $(this).find('div.orgMenuItemCnt').addClass("orgMenuItemCntActive");
                    if($(this).attr('data-submenuVisible') === 'false')
                    {
                        $(this).attr('data-submenuVisible', 'true');
                     //   $('.orgMenuSubItemCnt').css( "display", "block" );
                        $('.orgMenuSubItemCnt[data-fatherMenuIdDiv='+ $(this).attr('id') + ']').css( "display", "block" );
                    //    $(this).find('.orgMenuSubItemCnt').css( "display", "block" ); // data-fatherMenuIdDiv
                        $('.orgMenuSubItemCnt a.orgMenuSubItemLink[data-fatherMenuId=' + $(this).attr('id') + ']').show();
                        $(this).find('.submenuIndicator').removeClass('fa-caret-down');
                        $(this).find('.submenuIndicator').addClass('fa-caret-up');
                    }
                    else
                    {
                        $(this).attr('data-submenuVisible', 'false');
                        $('.orgMenuSubItemCnt a.orgMenuSubItemLink[data-fatherMenuId=' + $(this).attr('id') + ']').hide();
                    //    $('.orgMenuSubItemCnt').css( "display", "none" );
                        $('.orgMenuSubItemCnt[data-fatherMenuIdDiv='+ $(this).attr('id') + ']').css( "display", "none" );
                     //   $(this).find('.orgMenuSubItemCnt').css( "display", "none" );
                        $(this).find('.submenuIndicator').removeClass('fa-caret-up');
                        $(this).find('.submenuIndicator').addClass('fa-caret-down');
                    }
                }
                else
                {
                    $('#orgMenuCnt a.orgMenuSubItemLink').hide();

                    $('#orgMenuCnt a.orgMenuSubItemLink').each(function(i){
                        $(this).attr('data-submenuVisible', 'false');
                    });
                    switch($(this).attr('data-openMode'))
                    {
                        case "iframe":
                            $('#orgMenuCnt .orgMenuItemCnt').each(function(i){
                                $(this).removeClass('orgMenuItemCntActive');
                            });
                            $(this).find('div.orgMenuItemCnt').addClass("orgMenuItemCntActive");
                            if($(this).attr('data-externalApp') === 'yes')
                            {
                             //   location.href = "iframeApp.php?linkUrl=" + encodeURIComponent(linkUrl) + "&linkId=" + linkId + "&pageTitle=" + pageTitle + "&fromSubmenu=false";
                                location.href = "iframeApp.php?linkUrl=" + encodeURIComponent(linkUrl);
                            }
                            break;

                        case "newTab":
                         //   var newTab = window.open($(this).attr('data-linkurl') + "?linkId=" + linkId + "&fromSubmenu=false", '_blank');
                            var newTab = window.open($(this).attr('data-linkurl'));
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
                            $('#orgMenuCnt .orgMenuItemCnt').each(function(i){
                                $(this).removeClass('orgMenuItemCntActive');
                            });
                            $(this).find('div.orgMenuItemCnt').addClass("orgMenuItemCntActive");
                         //   location.href = $(this).attr('data-linkurl') + "?linkId=" + linkId + "&fromSubmenu=false&pageTitle=" + pageTitle;
                            location.href = $(this).attr('data-linkurl');
                            break;
                    }
                }

                var mainMenuScrollableCntHeight = parseInt($('#orgMenuCnt').outerHeight() - $('#headerClaimCnt').outerHeight() - $('#orgMenuCnt .mainMenuUsrCnt').outerHeight() - 30);
                $('#mainMenuScrollableCnt').css("height", parseInt(mainMenuScrollableCntHeight + 0) + "px");
                $('#mainMenuScrollableCnt').css("overflow-y", "auto");
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
                        if (scaleFactorFlag == null) {
                            gridsterCellW = 76;
                            gridsterCellH = 38;
                            widgetsContainerWidth = num_cols * (gridsterCellW + 2);
                        } else if (scaleFactorFlag == 'yes') {
                            // MOD GRID
                            gridsterCellW = newScaledGridsterCellW;
                            gridsterCellH = newScaledGridsterCellH;
                            widgetsContainerWidth = dashboardParams.width;
                        }

                        break;
                        
                    case "smallResponsive":
                        if($(window).width() > 768)
                        {
                            if (scaleFactorFlag == null) {
                                gridsterCellW = 76;
                                gridsterCellH = 38;
                                widgetsContainerWidth = num_cols * (gridsterCellW + 2);
                            } else if (scaleFactorFlag == 'yes') {
                                // MOD GRID
                                gridsterCellW = newScaledGridsterCellW;
                                gridsterCellH = newScaledGridsterCellH;
                                widgetsContainerWidth = dashboardParams.width;
                            }
                        }
                        else
                        {
                            if (scaleFactorFlag == null) {
                                gridsterCellW = Math.floor(parseInt($('body').width() * 0.98) / num_cols) - 2;
                                gridsterCellH = gridsterCellW / 2;
                                widgetsContainerWidth = num_cols * (gridsterCellW + 2);
                            } else {
                                gridsterCellW = Math.floor(parseInt($('body').width()*0.98) / num_cols);
                                gridsterCellH = gridsterCellW/2;
                                //    widgetsContainerWidth = dashboardParams.width;
                                widgetsContainerWidth = num_cols * (gridsterCellW);
                            }
                        }
                        break;
                        
                    case "mediumResponsive":
                        if($(window).width() > 992)
                        {
                            if (scaleFactorFlag == null) {
                                gridsterCellW = 76;
                                gridsterCellH = 38;
                                widgetsContainerWidth = num_cols * (gridsterCellW + 2);
                            } else if (scaleFactorFlag == 'yes') {
                                // MOD GRID
                                gridsterCellW = newScaledGridsterCellW;
                                gridsterCellH = newScaledGridsterCellH;
                                widgetsContainerWidth = dashboardParams.width;
                            }
                        }
                        else
                        {
                            if (scaleFactorFlag == null) {
                                gridsterCellW = Math.floor(parseInt($('body').width() * 0.98) / num_cols) - 2;
                                gridsterCellH = gridsterCellW / 2;
                                widgetsContainerWidth = num_cols * (gridsterCellW + 2);
                            } else if (scaleFactorFlag == 'yes') {
                                gridsterCellW = Math.floor(parseInt($('body').width() * 0.98) / num_cols);
                                gridsterCellH = gridsterCellW / 2;
                                widgetsContainerWidth = num_cols * (gridsterCellW);
                            }
                        }
                        break;
                        
                    case "largeResponsive":
                        if($(window).width() > 1200)
                        {
                            if (scaleFactorFlag == null) {
                                gridsterCellW = 76;
                                gridsterCellH = 38;
                                widgetsContainerWidth = num_cols * (gridsterCellW + 2);
                            } else if (scaleFactorFlag == 'yes') {
                                // MOD GRID
                                gridsterCellW = newScaledGridsterCellW;
                                gridsterCellH = newScaledGridsterCellH;
                                widgetsContainerWidth = dashboardParams.width;
                            }
                        }
                        else
                        {
                            if (scaleFactorFlag == null) {
                                gridsterCellW = Math.floor(parseInt($('body').width() * 0.98) / num_cols) - 2;
                                gridsterCellH = gridsterCellW / 2;
                                widgetsContainerWidth = num_cols * (gridsterCellW + 2);
                            } else if (scaleFactorFlag == 'yes') {
                                gridsterCellW = Math.floor(parseInt($('body').width() * 0.98) / num_cols);
                                gridsterCellH = gridsterCellW / 2;
                                widgetsContainerWidth = num_cols * (gridsterCellW);
                            }
                        }
                        break;    
                        
                    case "alwaysResponsive":
                        if (scaleFactorFlag == null) {
                            gridsterCellW = Math.floor(parseInt($('body').width()*0.98) / num_cols) - 2;
                            gridsterCellH = gridsterCellW/2;
                            widgetsContainerWidth = num_cols * (gridsterCellW + 2);
                        } else {
                            gridsterCellW = Math.floor(parseInt($('body').width()*0.98) / num_cols);
                            gridsterCellH = gridsterCellW/2;
                            //    widgetsContainerWidth = dashboardParams.width;
                            widgetsContainerWidth = num_cols * (gridsterCellW);
                        }
                        break;
                        
                    default:
                        if (scaleFactorFlag == null) {
                            gridsterCellW = 76;
                            gridsterCellH = 38;
                            widgetsContainerWidth = num_cols * (gridsterCellW + 2);
                        } else if (scaleFactorFlag == 'yes') {
                            // MOD GRID
                            gridsterCellW = newScaledGridsterCellW;
                            gridsterCellH = newScaledGridsterCellH;
                            widgetsContainerWidth = dashboardParams.width;
                        }
                        break;    
                }
                
                $('#dashboardViewWidgetsContainer').css('width', widgetsContainerWidth + "px");
                $('div.footerLogos').css('margin-right', ($('body').width() - widgetsContainerWidth)/2);

                gridster.resize_widget_dimensions({
                    widget_base_dimensions: [gridsterCellW, gridsterCellH],
                //    widget_margins: [1, 1]
                    widget_margins: widgetMargins
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
                infoMsgPopupFlag = dashboardParams.infoMsgPopup;
                infoMsgText = dashboardParams.infoMsgText;

                if (infoMsgPopupFlag == "yes" && infoMsgText != null && infoMsgText != '') {
                    $("#msgInfoModal").modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                //    $('#msgInfoModal').css("vertical-align", "middle");
                 //   $('#msgInfoModal .modalHeader').html(infoMsgText);
                    $('#msgInfoModalBodyView').css('text-align','center');
                    $('#msgInfoModalBodyView').html(infoMsgText);
                  //  $('#msgInfoModalText').val(infoMsgText);

                    $('#msgInfoModal .modal-dialog').css("vertical-align", "middle")

                    $('#msgInfoModal').modal('show');
                }
                
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
                        if (scaleFactorFlag == null) {
                            gridsterCellW = 76;
                            gridsterCellH = 38;
                            widgetsContainerWidth = num_cols * (gridsterCellW + 2);
                        } else if (scaleFactorFlag == 'yes') {
                            // MOD GRID
                            gridsterCellW = newScaledGridsterCellW;
                            gridsterCellH = newScaledGridsterCellH;
                            widgetsContainerWidth = dashboardParams.width;
                        }

                        break;
                        
                    case "smallResponsive":
                        if($(window).width() > 768)
                        {
                            if (scaleFactorFlag == null) {
                                gridsterCellW = 76;
                                gridsterCellH = 38;
                                widgetsContainerWidth = num_cols * (gridsterCellW + 2);
                            } else if (scaleFactorFlag == 'yes') {
                                // MOD GRID
                                gridsterCellW = newScaledGridsterCellW;
                                gridsterCellH = newScaledGridsterCellH;
                                widgetsContainerWidth = dashboardParams.width;
                            }
                        }
                        else
                        {
                            if (scaleFactorFlag == null) {
                                gridsterCellW = Math.floor(parseInt($('body').width() * 0.98) / num_cols) - 2;
                                gridsterCellH = gridsterCellW / 2;
                                widgetsContainerWidth = num_cols * (gridsterCellW + 2);
                            } else if (scaleFactorFlag == 'yes') {
                                gridsterCellW = Math.floor(parseInt($('body').width() * 0.98) / num_cols);
                                gridsterCellH = gridsterCellW / 2;
                                widgetsContainerWidth = num_cols * (gridsterCellW);
                            }
                        }
                        break;
                        
                    case "mediumResponsive":
                        if($(window).width() > 992)
                        {
                            if (scaleFactorFlag == null) {
                                gridsterCellW = 76;
                                gridsterCellH = 38;
                                widgetsContainerWidth = num_cols * (gridsterCellW + 2);
                            } else if (scaleFactorFlag == 'yes') {
                                // MOD GRID
                                gridsterCellW = newScaledGridsterCellW;
                                gridsterCellH = newScaledGridsterCellH;
                                widgetsContainerWidth = dashboardParams.width;
                            }
                        }
                        else
                        {
                            if (scaleFactorFlag == null) {
                                gridsterCellW = Math.floor(parseInt($('body').width() * 0.98) / num_cols) - 2;
                                gridsterCellH = gridsterCellW / 2;
                                widgetsContainerWidth = num_cols * (gridsterCellW + 2);
                            } else if (scaleFactorFlag == 'yes') {
                                gridsterCellW = Math.floor(parseInt($('body').width() * 0.98) / num_cols);
                                gridsterCellH = gridsterCellW / 2;
                                widgetsContainerWidth = num_cols * (gridsterCellW);
                            }
                        }
                        break;
                        
                    case "largeResponsive":
                        if($(window).width() > 1200)
                        {
                            if (scaleFactorFlag == null) {
                                gridsterCellW = 76;
                                gridsterCellH = 38;
                                widgetsContainerWidth = num_cols * (gridsterCellW + 2);
                            } else if (scaleFactorFlag == 'yes') {
                                // MOD GRID
                                gridsterCellW = newScaledGridsterCellW;
                                gridsterCellH = newScaledGridsterCellH;
                                widgetsContainerWidth = dashboardParams.width;
                            }
                        }
                        else
                        {
                            if (scaleFactorFlag == null) {
                                gridsterCellW = Math.floor(parseInt($('body').width() * 0.98) / num_cols) - 2;
                                gridsterCellH = gridsterCellW / 2;
                                widgetsContainerWidth = num_cols * (gridsterCellW + 2);
                            } else if (scaleFactorFlag == 'yes') {
                                gridsterCellW = Math.floor(parseInt($('body').width() * 0.98) / num_cols);
                                gridsterCellH = gridsterCellW / 2;
                                widgetsContainerWidth = num_cols * (gridsterCellW);
                            }
                        }
                        break;     
                        
                    case "alwaysResponsive":
                        if (scaleFactorFlag == null) {
                            gridsterCellW = Math.floor(parseInt($('body').width() * 0.98) / num_cols) - 2;
                            gridsterCellH = gridsterCellW / 2;
                            widgetsContainerWidth = num_cols * (gridsterCellW + 2);
                        } else {
                            gridsterCellW = Math.floor(parseInt($('body').width()*0.98) / num_cols);
                            gridsterCellH = gridsterCellW/2;
                        //    widgetsContainerWidth = dashboardParams.width;
                            widgetsContainerWidth = num_cols * (gridsterCellW);
                        }
                        break;
                        
                    default:
                        if (scaleFactorFlag == null) {
                            gridsterCellW = 76;
                            gridsterCellH = 38;
                            widgetsContainerWidth = num_cols * (gridsterCellW + 2);
                        } else if (scaleFactorFlag == 'yes') {
                            // MOD GRID
                            gridsterCellW = newScaledGridsterCellW;
                            gridsterCellH = newScaledGridsterCellH;
                            widgetsContainerWidth = dashboardParams.width;
                        }
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

                        if('<?php echo escapeForJS($showFooterEmbedded); ?>' === 'no')
                        {
                            $(".footerNavRow").hide();
                            $("#horizontalFooterLine").hide();
                         //   $("#dashboardViewHeaderContainer").css("margin-bottom", "0px");
                        }
                        else
                        {
                            $(".footerNavRow").show();
                            $("#horizontalFooterLine").show();
                         //   $('#dashboardViewWidgetsContainer').css('margin-top', ($('#dashboardViewHeaderContainer').height() + 15) + "px");
                        }
                    }
                    $("#logos a.footerLogo").hide();
                    $("#logos #embedAutoLogoContainer").show();
               }

                if (scaleFactorFlag == null) {
                    widgetMargins = [1, 1];
                    extra_rows_gridster = 100;
                    max_rows_gridster = 100;
                } else if (scaleFactorFlag == 'yes') {
                    widgetMargins = [0, 0];
                    extra_rows_gridster = 100 * scaleFactorH;
                    max_rows_gridster = 100 * scaleFactorH;
                }

                gridster = $("#gridsterUl").gridster({
                    widget_base_dimensions: [gridsterCellW, gridsterCellH],
                 //   widget_margins: [1, 1],
                    widget_margins: widgetMargins,
                    min_cols: num_cols,
                    max_size_x: 200,
                //    max_rows: 100,
                    max_rows: max_rows_gridster,
                //    extra_rows: 100,
                    extra_rows: extra_rows_gridster,
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
                localStorage.clear();
                for(var i = 0; i < dashboardWidgets.length; i++)
                {
                    var time = 0;
                    if(dashboardWidgets[i]['temporal_range_w'] === "Mensile") 
                    {
                        time = "30/DAY";
                    }
                    else if (dashboardWidgets[i]['temporal_range_w'] === "Semestrale")
                    {
                        time = "180/DAY";
                    }
                    else if (dashboardWidgets[i]['temporal_range_w'] === "Annuale")
                    {
                        time = "365/DAY";
                    }
                    else if (dashboardWidgets[i]['temporal_range_w'] === "2 Anni")
                    {
                        time = "730/DAY";
                    }
                    else if (dashboardWidgets[i]['temporal_range_w'] === "10 Anni")
                    {
                        time = "3650/DAY";
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
                        if (checkBIDash(response.dashboardWidgets)) {
                            $('#open_BIMenu').show();
                        }
                        scaleFactorFlag = response.dashboardParams.scaleFactor;
                        if (scaleFactorFlag == "yes") {
                            scaleFactorW = 78 / newScaledGridsterCellW;
                            scaleFactorH = 39 / newScaledGridsterCellH;
                        //    scaleFactorH = scaleFactorW;
                            //    dashboardParams.num_columns = Math.round(scaleFactorW * dashboardParams.num_columns);
                        //    response.dashboardParams.num_columns = Math.round(scaleFactorW * (response.dashboardParams.num_columns) + scaleFactorW);
                        } else if (scaleFactorFlag == null) {
                            scaleFactorW = 1;
                            scaleFactorH = 1;
                        }
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
  <?php include "../cookie_banner/cookie-banner.php"; ?>
    <?php include "../management/sessionExpiringPopup.php" ?>
    <div id="dashBckCnt">
       <div id="dashBckOverlay">
        
       </div>             
    </div>
    <div id="dashboardViewMainContainer" class="container-fluid">
        <nav id="dashboardViewHeaderContainer" class="navbar navbar-fixed-top" role="navigation">
            <div id="fullscreenBtnContainer" data-status="normal">
                <span id="spanCnt">
                    <i id="fullscreenButton" class="fa fa-window-maximize"></i>
                    <i id="restorescreenButton" class="fa fa-window-restore"></i>
                            <?php

                            $link = mysqli_connect($host, $username, $password);
                            mysqli_select_db($link, $dbname);

                            $orgMenuVisibilityQuery = "SELECT * FROM Dashboard.Config_dashboard WHERE id = '$dashId';";
                            $r = mysqli_query($link, $orgMenuVisibilityQuery);

                            if($r) {
                                while ($row = mysqli_fetch_assoc($r)) {

                                    $orgMenuVisible = $row['orgMenuVisible'];

                                    $orgMenuIconHidden = '<i id="orgMenuButton" class="fa fa-bars" style="display:none"></i>';
                                    $orgMenuIconShown = '<i id="orgMenuButton" class="fa fa-bars"></i>';
                                    if ($orgMenuVisible == 'yes') {
                                        echo($orgMenuIconShown);
                                    } else {
                                        echo($orgMenuIconHidden);
                                    }

                                }
                            }

                            $domainId = null;

                            $currDom = $_SERVER['HTTP_HOST'];

                            $domQ = "SELECT * FROM Dashboard.Domains WHERE domains LIKE '%$currDom%'";
                            $r = mysqli_query($link, $domQ);

                            if($r)
                            {
                                if(mysqli_num_rows($r) > 0)
                                {
                                    $row = mysqli_fetch_assoc($r);
                                    $domainId = $row['id'];
                                 //   echo $row['claim'];
                                }
                                else
                                {
                                 //   echo 'DISIT';
                                }
                            }
                            else
                            {
                             //   echo 'DISIT';
                            }

                       /*     if(isset($_SESSION['loggedOrganization'])) {
                                $organization = $_SESSION['loggedOrganization'];
                                $organizationSql = $organization;
                            } else {
                                $organization = "None";
                                $organizationSql = "Other";
                            }   */

                            $organizationSql = $dashOrg;

                            $newDivItem = '<div id=orgMenuCnt"><div id="orgMenu" data-shown="false" class="applicationCtxMenu fullCtxMenu container-fluid dashboardCtxMenu">';
                            echo($newDivItem);

                            $menuQuery = "SELECT * FROM Dashboard.OrgMenu WHERE domain = $domainId ORDER BY menuOrder ASC";
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

                                    if($allowedOrgs=='*' || strpos($allowedOrgs, "'".$organizationSql) !== false) {
                                        if ($externalApp == 'yes') {
                                            if ($openMode == 'newTab') {
                                                if ($linkUrl == 'submenu') {
                                                    $newItem =  '<div class="row" data-selected="false">' .
                                                        '<div class="col-md-12 orgMenuItemCnt">' .
                                                        '<a href="' . $linkUrl . '" id="' . $linkId . '" data-externalApp="' . $externalApp . '" data-openMode="' . $openMode . '" data-linkUrl="' . $linkUrl . '" data-pageTitle="' . $pageTitle . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuLink" target="_blank">' .
                                                        '<i class="' . $icon . '" style="color: ' . $iconColor . '"></i>&nbsp;&nbsp;&nbsp;' . $text . '&nbsp;&nbsp;&nbsp;<i class="fa fa-caret-down submenuIndicator" style="color: rgb(51, 64, 69)"></i>' .
                                                        '</a>'.
                                                        '</div>' .
                                                        '</div>';

                                                   /* $newItem = '<a href="' . $linkUrl . '" id="' . $linkId . '" data-externalApp="' . $externalApp . '" data-openMode="' . $openMode . '" data-linkUrl="' . $linkUrl . '" data-pageTitle="' . $pageTitle . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuLink" target="_blank">' .
                                                        '<div class="col-md-12 mainMenuItemCnt">' .
                                                        '<i class="' . $icon . '" style="color: ' . $iconColor . '"></i>&nbsp;&nbsp;&nbsp;' . $text . '&nbsp;&nbsp;&nbsp;<i class="fa fa-caret-down submenuIndicator" style="color: white"></i>' .
                                                        '</div>' .
                                                        '</a>';*/
                                                } else {
                                                    $newItem =  '<div class="row" data-selected="false">' .
                                                        '<div class="col-md-12 orgMenuItemCnt">' .
                                                        '<a href="' . $linkUrl . '" id="' . $linkId . '" data-externalApp="' . $externalApp . '" data-openMode="' . $openMode . '" data-linkUrl="' . $linkUrl . '" data-pageTitle="' . $pageTitle . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuLink" target="_blank">' .
                                                        '<i class="' . $icon . '" style="color: ' . $iconColor . '"></i>&nbsp;&nbsp;&nbsp;' . $text .
                                                        '</a>'.
                                                        '</div>' .
                                                        '</div>';

                                                /*    $newItem = '<a href="' . $linkUrl . '" id="' . $linkId . '" data-externalApp="' . $externalApp . '" data-openMode="' . $openMode . '" data-linkUrl="' . $linkUrl . '" data-pageTitle="' . $pageTitle . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuLink mainMenuIframeLink">' .
                                                        '<div class="col-md-12 mainMenuItemCnt">' .
                                                        '<i class="' . $icon . '" style="color: ' . $iconColor . '"></i>&nbsp;&nbsp;&nbsp;' . $text .
                                                        '</div>' .
                                                        '</a>';*/
                                                }
                                            } else {
                                                //CASO IFRAME
                                                if ($linkUrl == 'submenu') {
                                                    $newItem =  '<div class="row" data-selected="false">' .
                                                        '<div class="col-md-12 orgMenuItemCnt">' .
                                                        '<a href="' . $linkUrl . '" id="' . $linkId . '" data-externalApp="' . $externalApp . '" data-openMode="' . $openMode . '" data-linkUrl="' . $linkUrl . '" data-pageTitle="' . $pageTitle . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuLink mainMenuIframeLink">' .
                                                        '<i class="' . $icon . '" style="color: ' . $iconColor . '"></i>&nbsp;&nbsp;&nbsp;' . $text . '&nbsp;&nbsp;&nbsp;<i class="fa fa-caret-down submenuIndicator" style="color: rgb(51, 64, 69)"></i>' .
                                                        '</a>'.
                                                        '</div>' .
                                                        '</div>';

                                                /*    $newItem = '<a href="' . $linkUrl . '" id="' . $linkId . '" data-externalApp="' . $externalApp . '" data-openMode="' . $openMode . '" data-linkUrl="' . $linkUrl . '" data-pageTitle="' . $pageTitle . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuLink mainMenuIframeLink">' .
                                                        '<div class="col-md-12 mainMenuItemCnt">' .
                                                        '<i class="' . $icon . '" style="color: ' . $iconColor . '"></i>&nbsp;&nbsp;&nbsp;' . $text . '&nbsp;&nbsp;&nbsp;<i class="fa fa-caret-down submenuIndicator" style="color: white"></i>' .
                                                        '</div>' .
                                                        '</a>';*/
                                                } else {
                                                    $newItem =  '<div class="row" data-selected="false">' .
                                                        '<div class="col-md-12 orgMenuItemCnt">' .
                                                        '<a href="' . $linkUrl . '" id="' . $linkId . '" data-externalApp="' . $externalApp . '" data-openMode="' . $openMode . '" data-linkUrl="' . $linkUrl . '" data-pageTitle="' . $pageTitle . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuLink mainMenuIframeLink">' .
                                                        '<i class="' . $icon . '" style="color: ' . $iconColor . '"></i>&nbsp;&nbsp;&nbsp;' . $text .
                                                        '</a>'.
                                                        '</div>' .
                                                        '</div>';

                                                /*    $newItem = '<a href="' . $linkUrl . '" id="' . $linkId . '" data-externalApp="' . $externalApp . '" data-openMode="' . $openMode . '" data-linkUrl="' . $linkUrl . '" data-pageTitle="' . $pageTitle . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuLink mainMenuIframeLink">' .
                                                        '<div class="col-md-12 mainMenuItemCnt">' .
                                                        '<i class="' . $icon . '" style="color: ' . $iconColor . '"></i>&nbsp;&nbsp;&nbsp;' . $text .
                                                        '</div>' .
                                                        '</a>';*/
                                                }
                                            }
                                        } else {
                                            if ($linkUrl == 'submenu') {
                                                $newItem =  '<div class="row" data-selected="false">' .
                                                    '<div class="col-md-12 orgMenuItemCnt">' .
                                                    '<a href="' . $linkUrl . '" id="' . $linkId . '" data-externalApp="' . $externalApp . '" data-openMode="' . $openMode . '" data-linkUrl="' . $linkUrl . '" data-pageTitle="' . $pageTitle . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuLink">' .
                                                    '<i class="' . $icon . '" style="color: ' . $iconColor . '"></i>&nbsp;&nbsp;&nbsp;' . $text . '&nbsp;&nbsp;&nbsp;<i class="fa fa-caret-down submenuIndicator" style="color: rgb(51, 64, 69)"></i>' .
                                                    '</a>'.
                                                    '</div>' .
                                                    '</div>';

                                            /*    $newItem = '<a href="' . $linkUrl . '" id="' . $linkId . '" data-externalApp="' . $externalApp . '" data-openMode="' . $openMode . '" data-linkUrl="' . $linkUrl . '" data-pageTitle="' . $pageTitle . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuLink">' .
                                                    '<div class="col-md-12 mainMenuItemCnt">' .
                                                    '<i class="' . $icon . '" style="color: ' . $iconColor . '"></i>&nbsp;&nbsp;&nbsp;' . $text . '&nbsp;&nbsp;&nbsp;<i class="fa fa-caret-down submenuIndicator" style="color: white"></i>' .
                                                    '</div>' .
                                                    '</a>';*/
                                            } else {
                                                $newItem =  '<div class="row" data-selected="false">' .
                                                    '<div class="col-md-12 orgMenuItemCnt">' .
                                                    '<a href="' . $linkUrl . '" id="' . $linkId . '" data-externalApp="' . $externalApp . '" data-openMode="' . $openMode . '" data-linkUrl="' . $linkUrl . '" data-pageTitle="' . $pageTitle . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuLink">' .
                                                    '<i class="' . $icon . '" style="color: ' . $iconColor . '"></i>&nbsp;&nbsp;&nbsp;' . $text .
                                                    '</a>'.
                                                    '</div>' .
                                                    '</div>';

                                            /*    $newItem = '<a href="' . $linkUrl . '" id="' . $linkId . '" data-externalApp="' . $externalApp . '" data-openMode="' . $openMode . '" data-linkUrl="' . $linkUrl . '" data-pageTitle="' . $pageTitle . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuLink">' .
                                                    '<div class="col-md-12 mainMenuItemCnt">' .
                                                    '<i class="' . $icon . '" style="color: ' . $iconColor . '"></i>&nbsp;&nbsp;&nbsp;' . $text .
                                                    '</div>' .
                                                    '</a>';*/
                                            }
                                        }
                                    }

                                    if((strpos($privileges, "'". ($_SESSION['isPublic'] ? 'Public' : $_SESSION['loggedRole'])) !== false)&&(($userType == 'any')||(($userType != 'any')&&($userType == $_SESSION['loggedType']))) && ($allowedOrgs=='*' || (strpos($allowedOrgs, "'".$organizationSql) !== false)))
                                    {
                                        echo $newItem;
                                    }

                                    $uname = isset($_SESSION['loggedUsername']) ? $_SESSION['loggedUsername'] : '';

                                    $submenuQuery = "SELECT * FROM Dashboard.OrgMenuSubmenus s LEFT JOIN Dashboard.MainMenuSubmenusUser u ON u.submenu=s.id WHERE menu = '$menuItemId' AND (user is NULL OR user='$uname') ORDER BY menuOrder ASC";
                                    $r2 = mysqli_query($link, $submenuQuery);

                                    if($r2)
                                    {
                                        while($row2 = mysqli_fetch_assoc($r2))
                                        {
                                            $menuItemId2 = $row2['id'];
                                            $linkUrl2 = $row2['linkUrl'];

                                            if($linkUrl2 == 'submenu')
                                            {
                                                $linkUrl2 = '#';
                                            }

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

                                            if($allowedOrgs2=='*' || strpos($allowedOrgs2, "'".$organizationSql) !== false || $_SESSION['loggedRole'] == 'RootAdmin') {
                                                if ($externalApp2 == 'yes') {
                                                    if ($openMode2 == 'newTab') {
                                                        if ($_REQUEST['fromSubmenu'] == false || $_REQUEST['fromSubmenu'] != $linkId) {
                                                            $newItem =  '<div class="row" data-selected="false">' .
                                                                '<div class="col-md-12 orgMenuSubItemCnt" data-fatherMenuIdDiv="' . $linkId .'" style="display: none">' .
                                                                '<a href="' . $linkUrl2 . '" id="' . $linkId2 . '" style="text-decoration:none;padding-left:15px" data-fatherMenuId="' . $linkId . '" data-externalApp="' . $externalApp2 . '" data-openMode="' . $openMode2 . '" data-linkUrl="' . $linkUrl2 . '" data-pageTitle="' . $pageTitle2 . '" data-submenuVisible="false" class="internalLink moduleLink orgMenuSubItemLink" target="_blank">' .
                                                                '<i class="' . $icon2 . '" style="color: ' . $iconColor2 . '"></i>&nbsp;&nbsp;&nbsp;' . $text2 .
                                                                '</a>'.
                                                                '</div>' .
                                                                '</div>';

                                                        /*    $newItem = '<a href="' . $linkUrl2 . '" id="' . $linkId2 . '" data-fatherMenuId="' . $linkId . '" data-externalApp="' . $externalApp2 . '" data-openMode="' . $openMode2 . '" data-linkUrl="' . $linkUrl2 . '" data-pageTitle="' . $pageTitle2 . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuSubItemLink" target="_blank">' .
                                                                '<div class="col-md-12 mainMenuSubItemCnt" style="display: none">' .
                                                                '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="' . $icon2 . '" style="color: ' . $iconColor2 . '"></i>&nbsp;&nbsp;&nbsp;' . $text .
                                                                '</div>' .
                                                                '</a>';*/
                                                        } else {
                                                            $newItem =  '<div class="row" data-selected="false">' .
                                                                '<div class="col-md-12 orgMenuSubItemCnt" data-fatherMenuIdDiv="' . $linkId .'" style="display: none">' .
                                                                '<a href="' . $linkUrl2 . '" id="' . $linkId2 . '" style="text-decoration:none;padding-left:15px" data-fatherMenuId="' . $linkId . '" data-externalApp="' . $externalApp2 . '" data-openMode="' . $openMode2 . '" data-linkUrl="' . $linkUrl2 . '" data-pageTitle="' . $pageTitle2 . '" data-submenuVisible="false" class="internalLink moduleLink orgMenuSubItemLink" target="_blank">' .
                                                                '<i class="' . $icon2 . '" style="color: ' . $iconColor2 . '"></i>&nbsp;&nbsp;&nbsp;' . $text .
                                                                '</a>'.
                                                                '</div>' .
                                                                '</div>';

                                                        /*    $newItem = '<a href="' . $linkUrl2 . '" id="' . $linkId2 . '" data-fatherMenuId="' . $linkId . '" data-externalApp="' . $externalApp2 . '" data-openMode="' . $openMode2 . '" data-linkUrl="' . $linkUrl2 . '" data-pageTitle="' . $pageTitle2 . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuSubItemLink" target="_blank">' .
                                                                '<div class="col-md-12 mainMenuSubItemCnt">' .
                                                                '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="' . $icon2 . '" style="color: ' . $iconColor2 . '"></i>&nbsp;&nbsp;&nbsp;' . $text .
                                                                '</div>' .
                                                                '</a>';*/
                                                        }
                                                    } else {
                                                        //CASO IFRAME
                                                        if ($_REQUEST['fromSubmenu'] == false || $_REQUEST['fromSubmenu'] != $linkId) {
                                                            $newItem =  '<div class="row" data-selected="false">' .
                                                                '<div class="col-md-12 orgMenuSubItemCnt" data-fatherMenuIdDiv="' . $linkId .'" style="display: none">' .
                                                                '<a href="' . $linkUrl2 . '" id="' . $linkId2 . '" style="text-decoration:none;padding-left:15px" data-fatherMenuId="' . $linkId . '" data-externalApp="' . $externalApp2 . '" data-openMode="' . $openMode2 . '" data-linkUrl="' . $linkUrl2 . '" data-pageTitle="' . $pageTitle2 . '" data-submenuVisible="false" class="internalLink moduleLink orgMenuSubItemLink mainMenuIframeLink">' .
                                                                '<i class="' . $icon2 . '" style="color: ' . $iconColor2 . '"></i>&nbsp;&nbsp;&nbsp;' . $text2 .
                                                                '</a>'.
                                                                '</div>' .
                                                                '</div>';

                                                        /*    $newItem = '<a href="' . $linkUrl2 . '" id="' . $linkId2 . '" data-fatherMenuId="' . $linkId . '" data-externalApp="' . $externalApp2 . '" data-openMode="' . $openMode2 . '" data-linkUrl="' . $linkUrl2 . '" data-pageTitle="' . $pageTitle2 . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuSubItemLink mainMenuIframeLink">' .
                                                                '<div class="col-md-12 mainMenuSubItemCnt" style="display: none">' .
                                                                '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="' . $icon2 . '" style="color: ' . $iconColor2 . '"></i>&nbsp;&nbsp;&nbsp;' . $text2 .
                                                                '</div>' .
                                                                '</a>';*/
                                                        } else {
                                                            $newItem =  '<div class="row" data-selected="false">' .
                                                                '<div class="col-md-12 orgMenuSubItemCnt" data-fatherMenuIdDiv="' . $linkId .'" style="display: none">' .
                                                                '<a href="' . $linkUrl2 . '" id="' . $linkId2 . '" style="text-decoration:none;padding-left:15px" data-fatherMenuId="' . $linkId . '" data-externalApp="' . $externalApp2 . '" data-openMode="' . $openMode2 . '" data-linkUrl="' . $linkUrl2 . '" data-pageTitle="' . $pageTitle2 . '" data-submenuVisible="false" class="internalLink moduleLink orgMenuSubItemLink mainMenuIframeLink">' .
                                                                '<i class="' . $icon2 . '" style="color: ' . $iconColor2 . '"></i>&nbsp;&nbsp;&nbsp;' . $text2 .
                                                                '</a>'.
                                                                '</div>' .
                                                                '</div>';

                                                        /*    $newItem = '<a href="' . $linkUrl2 . '" id="' . $linkId2 . '" data-fatherMenuId="' . $linkId . '" data-externalApp="' . $externalApp2 . '" data-openMode="' . $openMode2 . '" data-linkUrl="' . $linkUrl2 . '" data-pageTitle="' . $pageTitle2 . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuSubItemLink mainMenuIframeLink">' .
                                                                '<div class="col-md-12 mainMenuSubItemCnt">' .
                                                                '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="' . $icon2 . '" style="color: ' . $iconColor2 . '"></i>&nbsp;&nbsp;&nbsp;' . $text2 .
                                                                '</div>' .
                                                                '</a>';*/
                                                        }
                                                    }
                                                } else {
                                                    if ($_REQUEST['fromSubmenu'] == false || $_REQUEST['fromSubmenu'] != $linkId) {
                                                        $newItem =  '<div class="row" data-selected="false">' .
                                                            '<div class="col-md-12 orgMenuSubItemCnt" data-fatherMenuIdDiv="' . $linkId .'" style="display: none">' .
                                                            '<a href="' . $linkUrl2 . '" id="' . $linkId2 . '" style="text-decoration:none;padding-left:15px" data-fatherMenuId="' . $linkId . '" data-externalApp="' . $externalApp2 . '" data-openMode="' . $openMode2 . '" data-linkUrl="' . $linkUrl2 . '" data-pageTitle="' . $pageTitle2 . '" data-submenuVisible="false" class="internalLink moduleLink orgMenuSubItemLink">' .
                                                            '<i class="' . $icon2 . '" style="color: ' . $iconColor2 . '"></i>&nbsp;&nbsp;&nbsp;' . $text2 .
                                                            '</a>'.
                                                            '</div>' .
                                                            '</div>';

                                                     /*   $newItem = '<a href="' . $linkUrl2 . '" id="' . $linkId2 . '" data-fatherMenuId="' . $linkId . '" data-externalApp="' . $externalApp2 . '" data-openMode="' . $openMode2 . '" data-linkUrl="' . $linkUrl2 . '" data-pageTitle="' . $pageTitle2 . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuSubItemLink">' .
                                                            '<div class="col-md-12 mainMenuSubItemCnt" style="display: none">' .
                                                            '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="' . $icon2 . '" style="color: ' . $iconColor2 . '"></i>&nbsp;&nbsp;&nbsp;' . $text2 .
                                                            '</div>' .
                                                            '</a>';*/
                                                    } else {
                                                        $newItem =  '<div class="row" data-selected="false">' .
                                                            '<div class="col-md-12 orgMenuSubItemCnt" data-fatherMenuIdDiv="' . $linkId .'" style="display: none">' .
                                                            '<a href="' . $linkUrl2 . '" id="' . $linkId2 . '" style="text-decoration:none;padding-left:15px" data-fatherMenuId="' . $linkId . '" data-externalApp="' . $externalApp2 . '" data-openMode="' . $openMode2 . '" data-linkUrl="' . $linkUrl2 . '" data-pageTitle="' . $pageTitle2 . '" data-submenuVisible="false" class="internalLink moduleLink orgMenuSubItemLink">' .
                                                            '<i class="' . $icon2 . '" style="color: ' . $iconColor2 . '"></i>&nbsp;&nbsp;&nbsp;' . $text2 .
                                                            '</a>'.
                                                            '</div>' .
                                                            '</div>';

                                                     /*   $newItem = '<a href="' . $linkUrl2 . '" id="' . $linkId2 . '" data-fatherMenuId="' . $linkId . '" data-externalApp="' . $externalApp2 . '" data-openMode="' . $openMode2 . '" data-linkUrl="' . $linkUrl2 . '" data-pageTitle="' . $pageTitle2 . '" data-submenuVisible="false" class="internalLink moduleLink mainMenuSubItemLink">' .
                                                            '<div class="col-md-12 mainMenuSubItemCnt">' .
                                                            '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="' . $icon2 . '" style="color: ' . $iconColor2 . '"></i>&nbsp;&nbsp;&nbsp;' . $text2 .
                                                            '</div>' .
                                                            '</a>';*/
                                                    }
                                                }
                                            }

                                            if((strpos($privileges2, "'".($_SESSION['isPublic'] ? 'Public' : $_SESSION['loggedRole'])) !== false)&&(($userType == 'any')||(($userType != 'any')&&($userType == $_SESSION['loggedType']))) && ($allowedOrgs2=='*' || (strpos($allowedOrgs2, "'".$organizationSql) !== false) || $_SESSION['loggedRole'] == 'RootAdmin'))
                                            {
                                                echo $newItem;
                                            }
                                        }
                                    }
                                }
                            }

                            mysqli_close($link);
                            ?>

                            <div class="row fullCtxMenuRow quitRow" data-selected="false">
                                <div class="col-md-12 orgMenuItemCnt">
                                    <i class="fa fa-mail-reply"></i>&nbsp;&nbsp;&nbsp;Quit&nbsp;&nbsp;&nbsp;
                                  <!--  <div class="col-xs-2 fullCtxMenuIcon centerWithFlex"><i class="fa fa-mail-reply"></i></div>
                                    <div class="col-xs-10 fullCtxMenuTxt">Quit</div>    -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <i id="open_BIMenu" class="fa fa-history"></i>
                    <script  type="text/javascript">

                        $('#spanCnt').append('<div id="BIMenuCnt" class="applicationCtxMenu fullCtxMenu container-fluid dashboardCtxMenu" style="display: block;"></div>');
                        $('#BIMenuCnt').hide();
                        $('#open_BIMenu').on("click", function(){
                            $('#BIMenuCnt').show();
                        });
                        $('#BIMenuCnt').append('<div id="quit" class="col-md-12 orgMenuSubItemCnt">Quit</div>');
                        $( "#quit" ).mouseover(function() {
                            $('#quit').css('cursor', 'pointer');
                        });
                        $('#quit').on("click", function(){
                            $('#BIMenuCnt').hide();
                        });
                        $('#BIMenuCnt').append('<div id="start" class="col-md-12 orgMenuSubItemCnt">Start</div>');
                        $( "#start" ).mouseover(function() {
                            $('#start').css('cursor', 'pointer');
                        });
                        $('#start').on("click", function(){
                            var widgets = JSON.parse(localStorage.getItem("widgets"));
                            for(var w in widgets){
                                if(widgets[w] != null){
                                    $('body').trigger({
                                        type: "resetContent_"+widgets[w]
                                    });
                                }
                            }
                        });
                    </script>
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

        <?php include('footer.html');?>

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

    <!-- Modale informazioni dashboard -->
  <!--  <div class="modal fade" id="msgInfoModal" tabindex="-1" role="dialog" aria-labelledby="msgInfoModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content modalContentWizardForm">
                <div class="modalHeader centerWithFlex">
                    <div class="col-xs-10 col-xs-offset-1"></div>
                </div>

                <div id="msgInfoModalBodyView" class="modal-body modalBody">

                </div>

                <div id="msgInfoModalFooter" class="modal-footer">
                    <div class="compactMenuBtns row centerWithFlex">
                        <button type="button" class="compactMenuCancelBtn" id="msgInfoModalCancelBtnView" data-dismiss="modal" style="margin-right: 3px;">OK</button>
                    </div>
                    <div class="compactMenuMsg centerWithFlex">

                    </div>
                </div>

            </div>    <!-- Fine modal content -->
        </div> <!-- Fine modal dialog -->
    </div>
    <!-- Fine modale informazioni dashboard -->

    <!-- Modale informazioni dashboard 2 -->
    <div class="modal fade" id="msgInfoModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modalHeader centerWithFlex">
                    Dashboard Info Message
                </div>
                <div id="msgInfoModalBodyView" class="modal-body modalBody">

                </div>
                <div id="msgInfoModalFooter" class="modal-footer">
                  <!--  <button type="button" id="discardLogoutBtn" class="btn cancelBtn" data-dismiss="modal">Cancel</button>  -->
                    <button type="button" id="confirmLogoutBtn" class="btn confirmBtn internalLink" data-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Fine modale informazioni dashboard 2 -->

    
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
    <!--
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
    </div> -->
     
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
mysqli_close($link);

?>
