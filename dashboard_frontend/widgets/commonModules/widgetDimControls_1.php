<div id="<?= $name_w ?>_dimControls" class="widgetDimControls">
    <div class="row">
        <div class="col-xs-4 dimControlsCell centerWithFlex"></div>
        <div class="col-xs-4 dimControlsCell centerWithFlex">
            <i id="<?= $name_w ?>_yMin" class="fa fa-angle-double-up widgetDimSingleControl"></i>
        </div>
        <div class="col-xs-4dimControlsCell centerWithFlex"></div>
    </div>
    <div class="row">
        <div class="col-xs-4 dimControlsCell centerWithFlex">
            <i id="<?= $name_w ?>_xMin" class="fa fa-angle-double-left widgetDimSingleControl"></i>
        </div>
        <div class="col-xs-4 dimControlsCell centerWithFlex"></div>
        <div class="col-xs-4 dimControlsCell centerWithFlex">
            <i id="<?= $name_w ?>_xPlus" class="fa fa-angle-double-right widgetDimSingleControl"></i>
        </div> 
    </div>
    <div class="row">
        <div class="col-xs-4 dimControlsCell centerWithFlex"></div>
        <div class="col-xs-4 dimControlsCell centerWithFlex">
            <i id="<?= $name_w ?>_yPlus" class="fa fa-angle-double-down widgetDimSingleControl"></i>
        </div>
        <div class="col-xs-4 dimControlsCell centerWithFlex"></div> 
    </div>
    <div class="row" id="<?= $name_w ?>_zoomRow" style="display: none">
        <div class="col-xs-4 dimControlsCell centerWithFlex">
            <i id="<?= $name_w ?>_zoomOut" class="fa fa-search-minus widgetDimSingleControl zoomControl"></i>
        </div>
        <div class="col-xs-4 dimControlsCell centerWithFlex"></div>
        <div class="col-xs-4 dimControlsCell centerWithFlex">
            <i id="<?= $name_w ?>_zoomIn" class="fa fa-search-plus widgetDimSingleControl zoomControl"></i>
        </div> 
    </div>    
</div>    

<script type='text/javascript'>
    $(document).ready(function()
    {
        var currentX, currentY, newX, newY = null;
        var currentZoom = "<?= $zoomFactor1 ?>";
        
        function changeZoom()
        {
             var target = document.getElementById('<?= $name_w ?>_iFrame');
             target.contentWindow.postMessage(currentZoom, '*');

             var formData = new FormData();
             formData.set('zoomFactorUpdated', currentZoom);
             formData.set('idWidget', '<?= $Id1 ?>');
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
        
        if("<?= $hostfile ?>" === "config")
        {
            $('#<?= $name_w ?>_dimControls').show();
            
            if("<?= $name_w ?>".includes("widgetExternalContent"))
            {
               $('#<?= $name_w ?>_zoomRow').show();
            }
           
           $('#<?= $name_w ?>_xMin').click(function(){
               $('.applicationCtxMenu').hide();
               currentX = parseInt($("#<?= $name_w ?>").attr('data-sizex'));
               currentY = parseInt($("#<?= $name_w ?>").attr('data-sizey'));
               newX = parseInt(currentX - 1);
               $("#gridsterUl").gridster().data('gridster').resize_widget($("#<?= $name_w ?>"), newX);
               $("#<?= $name_w ?>").trigger('customResizeEvent');
               
               $.ajax({
                    url: "../controllers/updateWidget.php",
                    data: {
                        action: "updateWidth",
                        widgetName: "<?= $name_w ?>", 
                        newWidth: newX
                    },
                    type: "POST",
                    async: true,
                    dataType: 'json',
                    success: function(data) 
                    {
                        if(data.detail !== 'Ok')
                        {
                            $("#gridsterUl").gridster().data('gridster').resize_widget($("#<?= $name_w ?>"), currentX);
                            $("#<?= $name_w ?>").trigger('customResizeEvent');
                        }
                    },
                    error: function(errorData)
                    {
                        $("#gridsterUl").gridster().data('gridster').resize_widget($("#<?= $name_w ?>"), currentX);
                        $("#<?= $name_w ?>").trigger('customResizeEvent');
                    }
                });
           });
           
           $('#<?= $name_w ?>_xPlus').click(function(){
               $('.applicationCtxMenu').hide();
               currentX = parseInt($("#<?= $name_w ?>").attr('data-sizex'));
               currentY = parseInt($("#<?= $name_w ?>").attr('data-sizey'));
               newX = parseInt(currentX + 1);
               $("#gridsterUl").gridster().data('gridster').resize_widget($("#<?= $name_w ?>"), newX);
               $("#<?= $name_w ?>").trigger('customResizeEvent');
               
               $.ajax({
                    url: "../controllers/updateWidget.php",
                    data: {
                        action: "updateWidth",
                        widgetName: "<?= $name_w ?>", 
                        newWidth: newX
                    },
                    type: "POST",
                    async: true,
                    dataType: 'json',
                    success: function(data) 
                    {
                        if(data.detail !== 'Ok')
                        {
                            $("#gridsterUl").gridster().data('gridster').resize_widget($("#<?= $name_w ?>"), currentX);
                            $("#<?= $name_w ?>").trigger('customResizeEvent');
                        }
                    },
                    error: function(errorData)
                    {
                        $("#gridsterUl").gridster().data('gridster').resize_widget($("#<?= $name_w ?>"), currentX);
                        $("#<?= $name_w ?>").trigger('customResizeEvent');
                    }
                });
           });
           
           $('#<?= $name_w ?>_yMin').click(function(){
               $('.applicationCtxMenu').hide();
               currentX = parseInt($("#<?= $name_w ?>").attr('data-sizex'));
               currentY = parseInt($("#<?= $name_w ?>").attr('data-sizey'));
               newY = parseInt(currentY - 1);
               $("#gridsterUl").gridster().data('gridster').resize_widget($("#<?= $name_w ?>"), currentX, newY);
               $("#<?= $name_w ?>").trigger('customResizeEvent');
               
               $.ajax({
                    url: "../controllers/updateWidget.php",
                    data: {
                        action: "updateHeight",
                        widgetName: "<?= $name_w ?>", 
                        newHeight: newY
                    },
                    type: "POST",
                    async: true,
                    dataType: 'json',
                    success: function(data) 
                    {
                        if(data.detail !== 'Ok')
                        {
                            $("#gridsterUl").gridster().data('gridster').resize_widget($("#<?= $name_w ?>"), currentX, currentY);
                            $("#<?= $name_w ?>").trigger('customResizeEvent');
                        }
                    },
                    error: function(errorData)
                    {
                        $("#gridsterUl").gridster().data('gridster').resize_widget($("#<?= $name_w ?>"), currentX);
                        $("#<?= $name_w ?>").trigger('customResizeEvent');
                    }
                });
           });
           
           $('#<?= $name_w ?>_yPlus').click(function(){
               $('.applicationCtxMenu').hide();
               currentX = parseInt($("#<?= $name_w ?>").attr('data-sizex'));
               currentY = parseInt($("#<?= $name_w ?>").attr('data-sizey'));
               newY = parseInt(currentY + 1);
               $("#gridsterUl").gridster().data('gridster').resize_widget($("#<?= $name_w ?>"), currentX, newY);
               $("#<?= $name_w ?>").trigger('customResizeEvent');
               
               $.ajax({
                    url: "../controllers/updateWidget.php",
                    data: {
                        action: "updateHeight",
                        widgetName: "<?= $name_w ?>", 
                        newHeight: newY
                    },
                    type: "POST",
                    async: true,
                    dataType: 'json',
                    success: function(data) 
                    {
                        if(data.detail !== 'Ok')
                        {
                            $("#gridsterUl").gridster().data('gridster').resize_widget($("#<?= $name_w ?>"), currentX, currentY);
                            $("#<?= $name_w ?>").trigger('customResizeEvent');
                        }
                    },
                    error: function(errorData)
                    {
                        $("#gridsterUl").gridster().data('gridster').resize_widget($("#<?= $name_w ?>"), currentX);
                        $("#<?= $name_w ?>").trigger('customResizeEvent');
                    }
                });
           });
           
           $('#<?= $name_w ?>_zoomOut').on('click', function () 
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
            
            $('#<?= $name_w ?>_zoomIn').on('click', function () 
            {
                $('.applicationCtxMenu').hide();
                currentZoom = (parseFloat(currentZoom) + parseFloat('0.05')).toFixed(2);
                changeZoom();
            });
        } 
    });
</script>    