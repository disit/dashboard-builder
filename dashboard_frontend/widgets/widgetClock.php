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
   include('../config.php');
   header("Cache-Control: private, max-age=$cacheControlMaxAge"); 
?>

<script type='text/javascript'>
    $(document).ready(function <?= $_REQUEST['name_w'] ?>(firstLoad) 
    {
        <?php
            $titlePatterns = array();
            $titlePatterns[0] = '/_/';
            $titlePatterns[1] = '/\'/';
            $replacements = array();
            $replacements[0] = ' ';
            $replacements[1] = '&apos;';
            $title = $_REQUEST['title_w'];
            $link = mysqli_connect($host, $username, $password);
            if (checkWidgetNameInDashboard($link, $_REQUEST['name_w'], $_REQUEST['id_dashboard']) === false) {
                eventLog("Returned the following ERROR in widgetClock.php for the widget ".escapeForHTML($_REQUEST['name_w'])." is not instantiated or allowed in this dashboard.");
                exit();
            }
        ?>
        var hostFile = "<?= escapeForJS($_REQUEST['hostFile']) ?>";
        var widgetName = "<?= $_REQUEST['name_w'] ?>";
        var widgetContentColor = "<?= escapeForJS($_REQUEST['color_w']) ?>";
        var widgetHeaderColor = "<?= escapeForJS($_REQUEST['frame_color_w']) ?>";
        var widgetHeaderFontColor = "<?= escapeForJS($_REQUEST['headerFontColor']) ?>";
        var widgetProperties, styleParameters, clockData, clockFont = null;
        var fontSize = "<?= escapeForJS($_REQUEST['fontSize']) ?>";
        var fontColor = "<?= escapeForJS($_REQUEST['fontColor']) ?>";
        var elToEmpty = $("#<?= $_REQUEST['name_w'] ?>_chartContainer");
        var embedWidget = <?= $_REQUEST['embedWidget']=='true'?'true':'false' ?>;
        var embedWidgetPolicy = '<?= escapeForJS($_REQUEST['embedWidgetPolicy']) ?>';
        var headerHeight = 25;
        var showTitle = "<?= escapeForJS($_REQUEST['showTitle']) ?>";
		var hasTimer = "<?= escapeForJS($_REQUEST['hasTimer']) ?>";
		var showHeader = null;
        elToEmpty.css("font-family", "Verdana");
        var firstShowClock = true;
        
        if(((embedWidget === true)&&(embedWidgetPolicy === 'auto'))||((embedWidget === true)&&(embedWidgetPolicy === 'manual')&&(showTitle === "no"))||((embedWidget === false)&&(showTitle === "no")))
		{
				showHeader = false;
		}
		else
		{
				showHeader = true;
		}  
        
        //Specifiche per questo widget
        
        //Definizioni di funzione specifiche del widget
        function updateTime() 
         {
            var now = new Date();
            var days = new Array();
            var months = new Array();

            days[0] = "Sun";
            days[1] = "Mon";
            days[2] = "Tue";
            days[3] = "Wed";
            days[4] = "Thu";
            days[5] = "Fri";
            days[6] = "Sat";

            months[0] = "Jan";
            months[1] = "Feb";
            months[2] = "Mar";
            months[3] = "Apr";
            months[4] = "May";
            months[5] = "Jun";
            months[6] = "Jul";
            months[7] = "Aug";
            months[8] = "Sep";
            months[9] = "Oct";
            months[10] = "Nov";
            months[11] = "Dec";

            //var day = days[now.getDay()];
            var day = now.getDate();
            //var month = months[now.getMonth()];
            var month = parseInt(now.getMonth() + 1);
            var hours = now.getHours();
            var minutes = now.getMinutes();
            var seconds = now.getSeconds();
            
            if(day <= 9)
            {
               day = "0" + day;
            }
            
            if(month <= 9)
            {
               month = "0" + month;
            }
            
            if(hours <= 9)
            {
               hours = "0" + hours;
            }

            if(minutes <= 9)
            {
               minutes = "0" + minutes;
            }

            if(seconds <= 9)
            {
               seconds = "0" + seconds;
            }
            
            var date = day + "/" + month + "/" + now.getFullYear();
            var time = hours + ":" + minutes + ":" + seconds;
            $("#<?= $_REQUEST['name_w'] ?>_chartContainer").css("font-size", fontSize + "px");
            $("#<?= $_REQUEST['name_w'] ?>_chartContainer").css("color", fontColor);
            
            if(clockFont === 'lcd')
            {
               $("#<?= $_REQUEST['name_w'] ?>_chartContainer").css("font-family", "Digital");
            }
            else
            {
               $("#<?= $_REQUEST['name_w'] ?>_chartContainer").css("font-family", "Verdana");
            }
            
            switch(clockData)
            {
               case "date":
                  if(firstShowClock)
                  {
                     $("#<?= $_REQUEST['name_w'] ?>_chartContainer").empty();
                     $("#<?= $_REQUEST['name_w'] ?>_chartContainer").html(date);
                     $("#<?= $_REQUEST['name_w'] ?>_chartContainer").addClass("centerWithFlex");
                     firstShowClock = false;
                  }
                  else
                  {
                     $("#<?= $_REQUEST['name_w'] ?>_chartContainer").html(date);
                  }
                  break;
                  
               case "time":
                  if(firstShowClock)
                  {
                     $("#<?= $_REQUEST['name_w'] ?>_chartContainer").empty();
                     $("#<?= $_REQUEST['name_w'] ?>_chartContainer").html(time);
                     $("#<?= $_REQUEST['name_w'] ?>_chartContainer").addClass("centerWithFlex");
                     firstShowClock = false;
                  }
                  else
                  {
                     $("#<?= $_REQUEST['name_w'] ?>_chartContainer").html(time);
                  }
                  break;
                  
               case "dateTime":
                  if(firstShowClock)
                  {
                     $("#<?= $_REQUEST['name_w'] ?>_chartContainer").removeClass("centerWithFlex");
                     $("#<?= $_REQUEST['name_w'] ?>_chartContainer").empty();
                     $("#<?= $_REQUEST['name_w'] ?>_chartContainer").append('<div class="clockDate">' + date + '</div>');
                     $("#<?= $_REQUEST['name_w'] ?>_chartContainer").append('<div class="clockTime">' + time + '</div>');
                     firstShowClock = false;
                  }
                  else
                  {
                     $("#<?= $_REQUEST['name_w'] ?>_chartContainer div.clockDate").html(date);
                     $("#<?= $_REQUEST['name_w'] ?>_chartContainer div.clockTime").html(time);
                  }
                  break;   
            }
         }
        
        //Restituisce il JSON delle info se presente, altrimenti NULL
        function getStyleParameters()
        {
            var styleParameters = null;
            if(jQuery.parseJSON(widgetProperties.param.styleParameters !== null))
            {
                styleParameters = jQuery.parseJSON(widgetProperties.param.styleParameters); 
            }
            
            return styleParameters;
        }
        
        function resizeWidget()
        {
            setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
        }
        //Fine definizioni di funzione 
        
        setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
        $('#<?= $_REQUEST['name_w'] ?>_div').parents('li.gs_w').off('resizeWidgets');
        $('#<?= $_REQUEST['name_w'] ?>_div').parents('li.gs_w').on('resizeWidgets', resizeWidget);
        setupLoadingPanel(widgetName, widgetContentColor, firstLoad);

        widgetProperties = getWidgetProperties(widgetName);
        
        if((widgetProperties !== null) && (widgetProperties !== ''))
        {
            //Inizio eventuale codice ad hoc basato sulle proprietà del widget
            $("#<?= $_REQUEST['name_w'] ?>_chartContainer").css("font-weight", "bold");
            styleParameters = getStyleParameters();
            clockData = styleParameters.clockData;
            clockFont = styleParameters.clockFont;
            showWidgetContent(widgetName);
            setInterval(updateTime, 1000); 
        }
        else
        {
            console.log("Errore in caricamento proprietà widget");
        }
		
	$('#<?= $_REQUEST['name_w'] ?>_countdownContainerDiv').remove();
        
        $(document).off('resizeHighchart_' + widgetName);
        $(document).on('resizeHighchart_' + widgetName, function(event) 
        {
            showHeader = event.showHeader;
        });
        
        $("#<?= $_REQUEST['name_w'] ?>").on('customResizeEvent', function(event){
            resizeWidget();
        });
});//Fine document ready 
</script>

<div class="widget" id="<?= $_REQUEST['name_w'] ?>_div">
    <div class='ui-widget-content'>
	    <?php include '../widgets/widgetHeader.php'; ?>
	   <?php include '../widgets/widgetCtxMenu.php'; ?>
        
        <div id="<?= $_REQUEST['name_w'] ?>_loading" class="loadingDiv">
            <div class="loadingTextDiv">
                <p>Loading data, please wait</p>
            </div>
            <div class ="loadingIconDiv">
                <i class='fa fa-spinner fa-spin'></i>
            </div>
        </div>
        
        <div id="<?= $_REQUEST['name_w'] ?>_content" class="content">
            <?php include '../widgets/commonModules/widgetDimControls.php'; ?>	 
            <p id="<?= $_REQUEST['name_w'] ?>_noDataAlert" style='text-align: center; font-size: 18px; display:none'>Nessun dato disponibile</p>
            <div id="<?= $_REQUEST['name_w'] ?>_chartContainer" class="chartContainer">
            </div>
        </div>
    </div>	
</div> 
