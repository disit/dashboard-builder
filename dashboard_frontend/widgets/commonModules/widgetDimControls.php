<div id="<?= $_REQUEST['name_w'] ?>_dimControls" class="widgetDimControls">
    <div class="row">
        <div class="col-xs-4 dimControlsCell centerWithFlex"></div>
        <div class="col-xs-4 dimControlsCell centerWithFlex">
            <i id="<?= $_REQUEST['name_w'] ?>_yMin" class="fa fa-angle-double-up widgetDimSingleControl"></i>
        </div>
        <div class="col-xs-4dimControlsCell centerWithFlex"></div>
    </div>
    <div class="row">
        <div class="col-xs-4 dimControlsCell centerWithFlex">
            <i id="<?= $_REQUEST['name_w'] ?>_xMin" class="fa fa-angle-double-left widgetDimSingleControl"></i>
        </div>
        <div class="col-xs-4 dimControlsCell centerWithFlex"></div>
        <div class="col-xs-4 dimControlsCell centerWithFlex">
            <i id="<?= $_REQUEST['name_w'] ?>_xPlus" class="fa fa-angle-double-right widgetDimSingleControl"></i>
        </div> 
    </div>
    <div class="row">
        <div class="col-xs-4 dimControlsCell centerWithFlex"></div>
        <div class="col-xs-4 dimControlsCell centerWithFlex">
            <i id="<?= $_REQUEST['name_w'] ?>_yPlus" class="fa fa-angle-double-down widgetDimSingleControl"></i>
        </div>
        <div class="col-xs-4 dimControlsCell centerWithFlex"></div> 
    </div>
    <div class="row" id="<?= $_REQUEST['name_w'] ?>_zoomRow" style="display: none">
        <div class="col-xs-4 dimControlsCell centerWithFlex">
            <i id="<?= $_REQUEST['name_w'] ?>_zoomOut" class="fa fa-search-minus widgetDimSingleControl zoomControl"></i>
        </div>
        <div class="col-xs-4 dimControlsCell centerWithFlex"></div>
        <div class="col-xs-4 dimControlsCell centerWithFlex">
            <i id="<?= $_REQUEST['name_w'] ?>_zoomIn" class="fa fa-search-plus widgetDimSingleControl zoomControl"></i>
        </div> 
    </div>    
</div>    

<script type='text/javascript'>
    $(document).ready(function()
    {
        var currentX, currentY, newX, newY = null;
        var currentZoom = "<?= $_REQUEST['zoomFactor'] ?>";
        
        function changeZoom()
        {
             var target = document.getElementById('<?= $_REQUEST['name_w'] ?>_iFrame');
             target.contentWindow.postMessage(currentZoom, '*');

             var formData = new FormData();
             formData.set('zoomFactorUpdated', currentZoom);
             formData.set('idWidget', '<?= $_REQUEST['Id'] ?>');
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
        
        if("<?= $_REQUEST['hostFile'] ?>" === "config")
        {
            $('#<?= $_REQUEST['name_w'] ?>_dimControls').show();
            
            if(("<?= $_REQUEST['name_w'] ?>".includes("widgetExternalContent"))||("<?= $_REQUEST['name_w'] ?>".includes("widgetGisWFS")))
            {
               $('#<?= $_REQUEST['name_w'] ?>_zoomRow').show();
            }
           
           $('#<?= $_REQUEST['name_w'] ?>_xMin').click(function(){
               $('.applicationCtxMenu').hide();
               currentX = parseInt($("#<?= $_REQUEST['name_w'] ?>").attr('data-sizex'));
               currentY = parseInt($("#<?= $_REQUEST['name_w'] ?>").attr('data-sizey'));
               newX = parseInt(currentX - 1);
               $("#gridsterUl").gridster().data('gridster').resize_widget($("#<?= $_REQUEST['name_w'] ?>"), newX);
               $("#<?= $_REQUEST['name_w'] ?>").trigger('customResizeEvent');
               
               $.ajax({
                    url: "../controllers/updateWidget.php",
                    data: {
                        action: "updateWidth",
                        widgetName: "<?= $_REQUEST['name_w'] ?>", 
                        newWidth: newX
                    },
                    type: "POST",
                    async: true,
                    dataType: 'json',
                    success: function(data) 
                    {
                        if(data.detail !== 'Ok')
                        {
                            $("#gridsterUl").gridster().data('gridster').resize_widget($("#<?= $_REQUEST['name_w'] ?>"), currentX);
                            $("#<?= $_REQUEST['name_w'] ?>").trigger('customResizeEvent');
                        }
                    },
                    error: function(errorData)
                    {
                        $("#gridsterUl").gridster().data('gridster').resize_widget($("#<?= $_REQUEST['name_w'] ?>"), currentX);
                        $("#<?= $_REQUEST['name_w'] ?>").trigger('customResizeEvent');
                    }
                });
           });
           
           $('#<?= $_REQUEST['name_w'] ?>_xPlus').click(function(){
               $('.applicationCtxMenu').hide();
               currentX = parseInt($("#<?= $_REQUEST['name_w'] ?>").attr('data-sizex'));
               currentY = parseInt($("#<?= $_REQUEST['name_w'] ?>").attr('data-sizey'));
               newX = parseInt(currentX + 1);
               $("#gridsterUl").gridster().data('gridster').resize_widget($("#<?= $_REQUEST['name_w'] ?>"), newX);
               $("#<?= $_REQUEST['name_w'] ?>").trigger('customResizeEvent');
               
               $.ajax({
                    url: "../controllers/updateWidget.php",
                    data: {
                        action: "updateWidth",
                        widgetName: "<?= $_REQUEST['name_w'] ?>", 
                        newWidth: newX
                    },
                    type: "POST",
                    async: true,
                    dataType: 'json',
                    success: function(data) 
                    {
                        if(data.detail !== 'Ok')
                        {
                            $("#gridsterUl").gridster().data('gridster').resize_widget($("#<?= $_REQUEST['name_w'] ?>"), currentX);
                            $("#<?= $_REQUEST['name_w'] ?>").trigger('customResizeEvent');
                        }
                    },
                    error: function(errorData)
                    {
                        $("#gridsterUl").gridster().data('gridster').resize_widget($("#<?= $_REQUEST['name_w'] ?>"), currentX);
                        $("#<?= $_REQUEST['name_w'] ?>").trigger('customResizeEvent');
                    }
                });
           });
           
           $('#<?= $_REQUEST['name_w'] ?>_yMin').click(function(){
               $('.applicationCtxMenu').hide();
               currentX = parseInt($("#<?= $_REQUEST['name_w'] ?>").attr('data-sizex'));
               currentY = parseInt($("#<?= $_REQUEST['name_w'] ?>").attr('data-sizey'));
               newY = parseInt(currentY - 1);
               $("#gridsterUl").gridster().data('gridster').resize_widget($("#<?= $_REQUEST['name_w'] ?>"), currentX, newY);
               $("#<?= $_REQUEST['name_w'] ?>").trigger('customResizeEvent');
               
               $.ajax({
                    url: "../controllers/updateWidget.php",
                    data: {
                        action: "updateHeight",
                        widgetName: "<?= $_REQUEST['name_w'] ?>", 
                        newHeight: newY
                    },
                    type: "POST",
                    async: true,
                    dataType: 'json',
                    success: function(data) 
                    {
                        if(data.detail !== 'Ok')
                        {
                            $("#gridsterUl").gridster().data('gridster').resize_widget($("#<?= $_REQUEST['name_w'] ?>"), currentX, currentY);
                            $("#<?= $_REQUEST['name_w'] ?>").trigger('customResizeEvent');
                        }
                    },
                    error: function(errorData)
                    {
                        $("#gridsterUl").gridster().data('gridster').resize_widget($("#<?= $_REQUEST['name_w'] ?>"), currentX);
                        $("#<?= $_REQUEST['name_w'] ?>").trigger('customResizeEvent');
                    }
                });
           });
           
           $('#<?= $_REQUEST['name_w'] ?>_yPlus').click(function(){
               $('.applicationCtxMenu').hide();
               currentX = parseInt($("#<?= $_REQUEST['name_w'] ?>").attr('data-sizex'));
               currentY = parseInt($("#<?= $_REQUEST['name_w'] ?>").attr('data-sizey'));
               newY = parseInt(currentY + 1);
               $("#gridsterUl").gridster().data('gridster').resize_widget($("#<?= $_REQUEST['name_w'] ?>"), currentX, newY);
               $("#<?= $_REQUEST['name_w'] ?>").trigger('customResizeEvent');
               
               $.ajax({
                    url: "../controllers/updateWidget.php",
                    data: {
                        action: "updateHeight",
                        widgetName: "<?= $_REQUEST['name_w'] ?>", 
                        newHeight: newY
                    },
                    type: "POST",
                    async: true,
                    dataType: 'json',
                    success: function(data) 
                    {
                        if(data.detail !== 'Ok')
                        {
                            $("#gridsterUl").gridster().data('gridster').resize_widget($("#<?= $_REQUEST['name_w'] ?>"), currentX, currentY);
                            $("#<?= $_REQUEST['name_w'] ?>").trigger('customResizeEvent');
                        }
                    },
                    error: function(errorData)
                    {
                        $("#gridsterUl").gridster().data('gridster').resize_widget($("#<?= $_REQUEST['name_w'] ?>"), currentX);
                        $("#<?= $_REQUEST['name_w'] ?>").trigger('customResizeEvent');
                    }
                });
           });
           
           $('#<?= $_REQUEST['name_w'] ?>_zoomOut').on('click', function () 
            {
                $('.applicationCtxMenu').hide();
                if(parseFloat(currentZoom - 0.1).toFixed(2) > 0.1)
                {
                    currentZoom = parseFloat(currentZoom - 0.05).toFixed(2);
                    changeZoom();
                }
                else
                {
                    alert("You have reached the minimum zoom factor");
                }
            });
            
            $('#<?= $_REQUEST['name_w'] ?>_zoomIn').on('click', function () 
            {
                $('.applicationCtxMenu').hide();
                currentZoom = (parseFloat(currentZoom) + parseFloat('0.05')).toFixed(2);
                changeZoom();
            });
        } 
    });
</script>    