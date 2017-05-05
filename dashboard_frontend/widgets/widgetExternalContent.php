<?php
/* Dashboard Builder.
   Copyright (C) 2016 DISIT Lab http://www.disit.org - University of Florence

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
?>
<script type='text/javascript'>
    $(document).ready(function iframe(firstLoad) 
    {
        <?php
            $titlePatterns = array();
            $titlePatterns[0] = '/_/';
            $titlePatterns[1] = '/\'/';
            $replacements = array();
            $replacements[0] = ' ';
            $replacements[1] = '&apos;';
            $title = $_GET['title'];
        ?> 
                
        var hostFile = "<?= $_GET['hostFile'] ?>";
        var widgetName = "<?= $_GET['name'] ?>";
        var divContainer = $("#<?= $_GET['name'] ?>_content");
        var widgetContentColor = "<?= $_GET['color'] ?>";
        var widgetHeaderColor = "<?= $_GET['frame_color'] ?>";
        var widgetHeaderFontColor = "<?= $_GET['headerFontColor'] ?>";
        var nome_wid = "<?= $_GET['name'] ?>_div";
        var linkElement = $('#<?= $_GET['name'] ?>_link_w');
        var color = '<?= $_GET['color'] ?>';
        var fontSize = "<?= $_GET['fontSize'] ?>";
        var fontColor = "<?= $_GET['fontColor'] ?>";
        var timeToReload = <?= $_GET['freq'] ?>;
        var metricId = "<?= $_GET['metric'] ?>";
        var idWidget = "<?= $_GET['idWidget'] ?>";
        var zoomControlsColor = "<?= $_GET['zoomControlsColor'] ?>";
        var leftWrapper = "0px";
        var zoomTarget = "body";
        var currentZoom = "<?= $_GET['zoomFactor'] ?>";
        var currentScaleX = "<?= $_GET['scaleX'] ?>";
        var currentScaleY = "<?= $_GET['scaleY'] ?>";
        var controlsPosition = "<?= $_GET['controlsPosition'] ?>";
        var showTitle = "<?= $_GET['showTitle'] ?>";
        var controlsVisibility = "<?= $_GET['controlsVisibility'] ?>";
        var sizeX = "<?= $_GET['sizeX'] ?>";
        var sizeY = "<?= $_GET['sizeY'] ?>";
        var colore_frame = "<?= $_GET['frame_color'] ?>";
        var url = "<?= $_GET['link_w'] ?>";
        var numCols = "<?= $_GET['numCols'] ?>";
        var wrapperW = $('#<?= $_GET['name'] ?>_div').outerWidth();
        var widgetPropertiesString, widgetProperties, thresholdObject, infoJson, styleParameters, metricType, metricData, pattern, totValues, shownValues, 
            descriptions, udm, threshold, thresholdEval, stopsArray, delta, deltaPerc, seriesObj, dataObj, pieObj, legendLength,
            rangeMin, rangeMax, widgetParameters, mapQuery, topWrapper, originalWidth, originalHeight, height, zoomDisplayTimeout, wrapperH = null;
        
        
        //Definizioni di funzione specifiche del widget
        /*Restituisce il JSON delle soglie se presente, altrimenti NULL*/
        function getThresholdsJson()
        {
            var thresholdsJson = null;
            if(jQuery.parseJSON(widgetProperties.param.parameters !== null))
            {
                thresholdsJson = widgetProperties.param.parameters; 
            }
            
            return thresholdsJson;
        }
        
        /*Restituisce il JSON delle info se presente, altrimenti NULL*/
        function getInfoJson()
        {
            var infoJson = null;
            if(jQuery.parseJSON(widgetProperties.param.infoJson !== null))
            {
                infoJson = jQuery.parseJSON(widgetProperties.param.infoJson); 
            }
            
            return infoJson;
        }
        
        /*Restituisce il JSON delle info se presente, altrimenti NULL*/
        function getStyleParameters()
        {
            var styleParameters = null;
            if(jQuery.parseJSON(widgetProperties.param.styleParameters !== null))
            {
                styleParameters = jQuery.parseJSON(widgetProperties.param.styleParameters); 
            }
            
            return styleParameters;
        }
        
        function changeDimX(op)
        {
            $("#<?= $_GET['name'] ?>_div").parent().attr("data-sizex", sizeX);
            var width = null;
            switch(op)
            {
                case "+":
                    width = parseInt($('#<?= $_GET['name'] ?>_div').outerWidth() + 76);
                    break;
                    
                case "-":
                    width = parseInt($('#<?= $_GET['name'] ?>_div').outerWidth() - 76);
                    break;
            }
            
            $('#<?= $_GET['name'] ?>_content').css("width", width + "px");
    
            var formData = new FormData();
            formData.set('widthUpdated', sizeX);
            formData.set('idWidget', idWidget);
            $.ajax({
                url: "process-form.php",
                data: formData,
                async: true,
                processData: false,
                contentType: false,  
                type: 'POST',
                success: function (msg) 
                {
                },
                error: function()
                {
                    console.log("Errore in chiamata PHP per scrittura zoom factor");
                }
            });
        }
        
        function changeDimY(op)
        {
            $("#<?= $_GET['name'] ?>_div").parent().attr("data-sizey", sizeY);
            var height = null;
            switch(op)
            {
                case "+":
                    height = parseInt($('#<?= $_GET['name'] ?>_div').outerHeight() + 38);
                    break;
                    
                case "-":
                    height = parseInt($('#<?= $_GET['name'] ?>_div').outerHeight() - 38);
                    break;
            }
            
            $('#<?= $_GET['name'] ?>_content').css("height", height + "px");
    
    
            var formData = new FormData();
            formData.set('heightUpdated', sizeY);
            formData.set('idWidget', idWidget);
            $.ajax({
                url: "process-form.php",
                data: formData,
                async: true,
                processData: false,
                contentType: false,  
                type: 'POST',
                success: function (msg) 
                {
                    
                },
                error: function()
                {
                    console.log("Errore in chiamata PHP per scrittura zoom factor");
                }
            }); 
        }
        
        function changeZoom()
        {
            var target = document.getElementById('<?= $_GET['name'] ?>_iFrame');
            target.contentWindow.postMessage(currentZoom, '*');
            
            var formData = new FormData();
            formData.set('zoomFactorUpdated', currentZoom);
            formData.set('idWidget', idWidget);
            $.ajax({
                url: "process-form.php",
                data: formData,
                async: true,
                processData: false,
                contentType: false,  
                type: 'POST',
                success: function (msg) 
                {
                },
                error: function()
                {
                    console.log("Errore in chiamata PHP per scrittura zoom factor");
                }
            });  
        }
        
        function updateZoomControlsPosition()
        {
            switch (controlsPosition)
            {
                case 'topleft':
                    $('#<?= $_GET['name'] ?>_zoomControls').css("left", "1%");
                    $('#<?= $_GET['name'] ?>_zoomControls').css("top", "2%");
                    break;

                case 'topCenter':
                    $('#<?= $_GET['name'] ?>_zoomControls').css("left", "50%");
                    $('#<?= $_GET['name'] ?>_zoomControls').css("top", "2%");
                    $('#<?= $_GET['name'] ?>_zoomControls').css("transform", "translateX(-50%");
                    break;

                case 'topRight':
                    $('#<?= $_GET['name'] ?>_zoomControls').css("left", "80%");
                    $('#<?= $_GET['name'] ?>_zoomControls').css("top", "2%");
                    break;

                case 'middleRight':
                    $('#<?= $_GET['name'] ?>_zoomControls').css("left", "80%");
                    $('#<?= $_GET['name'] ?>_zoomControls').css("top", "50%");
                    break;

                case 'bottomRight':
                    $('#<?= $_GET['name'] ?>_zoomControls').css("left", "80%");
                    $('#<?= $_GET['name'] ?>_zoomControls').css("top", "78%");
                    break;

                case 'bottomMiddle':
                    $('#<?= $_GET['name'] ?>_zoomControls').css("left", "50%");
                    $('#<?= $_GET['name'] ?>_zoomControls').css("top", "78%");
                    $('#<?= $_GET['name'] ?>_zoomControls').css("transform", "translateX(-50%");
                    break;

                case 'bottomLeft':
                    $('#<?= $_GET['name'] ?>_zoomControls').css("left", "1%");
                    $('#<?= $_GET['name'] ?>_zoomControls').css("top", "78%");
                    break;

                case 'middleLeft':
                    $('#<?= $_GET['name'] ?>_zoomControls').css("left", "1%");
                    $('#<?= $_GET['name'] ?>_zoomControls').css("top", "50%");
                    break;

                default:
                    $('#<?= $_GET['name'] ?>_zoomControls').css("left", "1%");
                    $('#<?= $_GET['name'] ?>_zoomControls').css("top", "2%");
                    break;
            }
        }
        
        //Va aggiornata con showWidgetContent
        function iframeLoaded(event)
        {
            $('#<?= $_GET['name'] ?>_loading').css("display", "none");
            $('#<?= $_GET['name'] ?>_wrapper').css("width", "100%");
            $("#<?= $_GET['name'] ?>_wrapper").css("height", height);
            
            if((controlsVisibility === 'alwaysVisible') && (hostFile === 'config'))
            {
                $('#<?= $_GET['name'] ?>_zoomControls').css("display", "block");
                updateZoomControlsPosition();
            }
            var target = document.getElementById('<?= $_GET['name'] ?>_iFrame');
            target.contentWindow.postMessage(currentZoom, '*');
            
            $("#<?= $_GET['name'] ?>_content").contents().find("body").css("transform-origin", "0% 0%");
            
            if(firstLoad !== false)
            {
                $('#<?= $_GET['name'] ?>_loading').css("display", "none");
                $('#<?= $_GET['name'] ?>_wrapper').css("display", "block");
            }
        }
        //Fine definizioni di funzione 
        
        setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor);
        if(firstLoad === false)
        {
            showWidgetContent(widgetName);
        }
        else
        {
            setupLoadingPanel(widgetName, widgetContentColor, firstLoad);
        }
        $("#<?= $_GET['name'] ?>_titleDiv").html("<?= preg_replace($titlePatterns, $replacements, $title) ?>");
        widgetProperties = getWidgetProperties(widgetName);
        
        if((widgetProperties !== null) && (widgetProperties !== 'undefined'))
        {
            //Inizio eventuale codice ad hoc basato sulle proprietà del widget
            styleParameters = getStyleParameters();//Restituisce null finché non si usa il campo per questo widget
            //Fine eventuale codice ad hoc basato sulle proprietà del widget
            
            //Inizio eventuale codice ad hoc basato sui dati della metrica
            if((hostFile === "index") && (showTitle === "no"))
            {
                $('#<?= $_GET['name'] ?>_header').css("display", "none");
                height = parseInt($("#<?= $_GET['name'] ?>_div").prop("offsetHeight"));
                wrapperH = parseInt($('#<?= $_GET['name'] ?>_div').outerHeight());
                topWrapper = "0px";
            }
            else
            {
                $('#<?= $_GET['name'] ?>_header').css("display", "block");
                height = parseInt($("#<?= $_GET['name'] ?>_div").prop("offsetHeight") - 25);
                wrapperH = parseInt($('#<?= $_GET['name'] ?>_div').outerHeight() - 25);
                topWrapper = "25px";
            }

            $('#<?= $_GET['name'] ?>_content').css("width", wrapperW + "px");
            $('#<?= $_GET['name'] ?>_content').css("height", wrapperH + "px");
            if(firstLoad !== false)
            {
                showWidgetContent(widgetName);
            }
            else
            {
                elToEmpty.empty();
            }
            
            $("#<?= $_GET['name'] ?>_iFrame").load(iframeLoaded);
            $('#<?= $_GET['name'] ?>_iFrame').attr("src", url);
            
            $('#<?= $_GET['name'] ?>_xPlus').on('click', function () 
            {
                clearTimeout(zoomDisplayTimeout);
                sizeX = parseInt(parseInt(sizeX) + 1);
                changeDimX("+");
                $("#<?= $_GET['name'] ?>_zoomDisplay").html("");
                $("#<?= $_GET['name'] ?>_zoomDisplay").html("width<br/>" + sizeX);
                $("#<?= $_GET['name'] ?>_zoomDisplay").css("visibility", "visible");

                updateZoomControlsPosition();

                zoomDisplayTimeout = setTimeout(function(){
                    $("#<?= $_GET['name'] ?>_zoomDisplay").css("visibility", "hidden");
                }, 300);
            });

            $('#<?= $_GET['name'] ?>_xMin').on('click', function () 
            {
                clearTimeout(zoomDisplayTimeout);
                sizeX = parseInt(parseInt(sizeX) - 1);
                changeDimX("-");
                $("#<?= $_GET['name'] ?>_zoomDisplay").html("");
                $("#<?= $_GET['name'] ?>_zoomDisplay").html("width<br/>" + sizeX);
                $("#<?= $_GET['name'] ?>_zoomDisplay").css("visibility", "visible");

                updateZoomControlsPosition();

                zoomDisplayTimeout = setTimeout(function(){
                    $("#<?= $_GET['name'] ?>_zoomDisplay").css("visibility", "hidden");
                }, 300);
            });

            $('#<?= $_GET['name'] ?>_yPlus').on('click', function () 
            {
                clearTimeout(zoomDisplayTimeout);
                sizeY = parseInt(parseInt(sizeY) + 1);
                changeDimY("+");

                updateZoomControlsPosition();

                $("#<?= $_GET['name'] ?>_zoomDisplay").html("");
                $("#<?= $_GET['name'] ?>_zoomDisplay").html("height<br/>" + sizeY);
                $("#<?= $_GET['name'] ?>_zoomDisplay").css("visibility", "visible");

                zoomDisplayTimeout = setTimeout(function(){
                    $("#<?= $_GET['name'] ?>_zoomDisplay").css("visibility", "hidden");
                }, 300);
            });

            $('#<?= $_GET['name'] ?>_yMin').on('click', function () 
            {
                clearTimeout(zoomDisplayTimeout);
                sizeY = parseInt(parseInt(sizeY) - 1);
                changeDimY("-");

                $("#<?= $_GET['name'] ?>_zoomDisplay").html("");
                $("#<?= $_GET['name'] ?>_zoomDisplay").html("height<br/>" + sizeY);
                $("#<?= $_GET['name'] ?>_zoomDisplay").css("visibility", "visible");

                updateZoomControlsPosition();

                zoomDisplayTimeout = setTimeout(function(){
                    $("#<?= $_GET['name'] ?>_zoomDisplay").css("visibility", "hidden");
                }, 300);
            });

            $('#<?= $_GET['name'] ?>_zoomIn').on('click', function () 
            {
                clearTimeout(zoomDisplayTimeout);
                currentZoom = (parseFloat(currentZoom) + parseFloat('0.05')).toFixed(2);
                changeZoom();
                var percentZoom = parseInt(currentZoom*100);
                $("#<?= $_GET['name'] ?>_zoomDisplay").html("");
                $("#<?= $_GET['name'] ?>_zoomDisplay").html("zoom<br/>" + percentZoom + "%");
                $("#<?= $_GET['name'] ?>_zoomDisplay").css("visibility", "visible");

                zoomDisplayTimeout = setTimeout(function(){
                    $("#<?= $_GET['name'] ?>_zoomDisplay").css("visibility", "hidden");
                }, 300);
            });

            $('#<?= $_GET['name'] ?>_zoomOut').on('click', function () 
            {
                if(parseFloat(currentZoom - 0.1).toFixed(2) > 0.1)
                {
                    clearTimeout(zoomDisplayTimeout);
                    currentZoom = parseFloat(currentZoom - 0.05).toFixed(2);
                    changeZoom();
                    var percentZoom = parseInt(currentZoom*100);
                    $("#<?= $_GET['name'] ?>_zoomDisplay").html("");
                    $("#<?= $_GET['name'] ?>_zoomDisplay").html("zoom<br/>" + percentZoom + "%");
                    $("#<?= $_GET['name'] ?>_zoomDisplay").css("visibility", "visible");
                    zoomDisplayTimeout = setTimeout(function(){
                        $("#<?= $_GET['name'] ?>_zoomDisplay").css("visibility", "hidden");
                    }, 300);
                }
                else
                {
                    alert("You have reached the minimum zoom factor");
                }
            });
        }    
        else
        {
            alert("Error while loading widget properties");
        }
    });//Fine document ready 
</script>

<div class="widget" id="<?= $_GET['name'] ?>_div">
    <div class='ui-widget-content'>
        <div id='<?= $_GET['name'] ?>_header' class="widgetHeader">
            <div id="<?= $_GET['name'] ?>_infoButtonDiv" class="infoButtonContainer">
                <a id ="info_modal" href="#" class="info_source"><img id="source_<?= $_GET['name'] ?>" src="../management/img/info.png" class="source_button"></a>
            </div>    
            <div id="<?= $_GET['name'] ?>_titleDiv" class="titleDiv"></div>
            <div id="<?= $_GET['name'] ?>_buttonsDiv" class="buttonsContainer">
                <a class="icon-cfg-widget" href="#"><span class="glyphicon glyphicon-cog glyphicon-modify-widget" aria-hidden="true"></span></a>
                <a class="icon-remove-widget" href="#"><span class="glyphicon glyphicon-remove glyphicon-modify-widget" aria-hidden="true"></span></a>
            </div>
        </div>
        
        <div id="<?= $_GET['name'] ?>_loading" class="loadingDiv">
            <div class="loadingTextDiv">
                <p>Loading data, please wait</p>
            </div>
            <div class ="loadingIconDiv">
                <i class='fa fa-spinner fa-spin'></i>
            </div>
        </div>
        
        <div id="<?= $_GET['name'] ?>_content" class="content">
            <p id="<?= $_GET['name'] ?>_noDataAlert" style='text-align: center; font-size: 18px; display:none'>Nessun dato disponibile</p>
            <div id="<?= $_GET['name'] ?>_wrapper" class="iframeWrapper">
                <div id="<?= $_GET['name'] ?>_zoomControls" class="iframeZoomControls">
                    <div id="<?= $_GET['name'] ?>_dimDiv" class="zoomControlsRow">
                        <div class="zoomControlsLabelDiv">
                            width
                        </div>
                        <div class="zoomControlsButtonsDiv">
                            <i id="<?= $_GET['name'] ?>_xMin" class="fa fa-minus-square-o"></i>
                            <i id="<?= $_GET['name'] ?>_xPlus" class="fa fa-plus-square-o"></i>
                        </div>
                    </div>
                    <div id="<?= $_GET['name'] ?>_dimDiv" class="zoomControlsRow">
                        <div class="zoomControlsLabelDiv">
                            height
                        </div>
                        <div class="zoomControlsButtonsDiv">
                            <i id="<?= $_GET['name'] ?>_yMin" class="fa fa-minus-square-o"></i>
                            <i id="<?= $_GET['name'] ?>_yPlus" class="fa fa-plus-square-o"></i>
                        </div>
                    </div>
                    <div id="<?= $_GET['name'] ?>_zoomDiv" class="zoomControlsRow">
                        <div class="zoomControlsLabelDiv">
                            zoom    
                        </div>
                        <div class="zoomControlsButtonsDiv">
                            <i id="<?= $_GET['name'] ?>_zoomOut" class="fa fa-minus-square-o"></i> 
                            <i id="<?= $_GET['name'] ?>_zoomIn" class="fa fa-plus-square-o"></i>
                        </div>
                    </div>
                </div>
                <div id="<?= $_GET['name'] ?>_zoomDisplay" class="zoomDisplay"></div>
                <iframe id="<?= $_GET['name'] ?>_iFrame" class="iFrame"></iframe>
            </div>
        </div>
    </div>	
</div> 