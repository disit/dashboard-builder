<div class="hidden-xs hidden-sm col-md-2" id="mainMenuCnt">
    <div id="headerClaimCnt" class="col-md-12 centerWithFlex">Dashboard Management System</div>
    <div class="col-md-12 mainMenuUsrCnt">
        <div class="row">
            <div class="col-md-12 centerWithFlex" id="mainMenuIconCnt">
                <img src="../img/mainMenuIcons/user.ico" />
            </div>
            <div class="col-md-12 centerWithFlex" id="mainMenuUsrCnt">
                <?php echo $_SESSION['loggedUsername']; ?>
            </div>
            <div class="col-md-12 centerWithFlex" id="mainMenuUsrDetCnt">
                <?php echo $_SESSION['loggedRole'] . " | " . $_SESSION['loggedType']; ?>
            </div>
            <div class="col-md-12 centerWithFlex" id="mainMenuUsrLogoutCnt">
                Logout
            </div>
        </div>
    </div>
    
    
    <?php
        if(isset($_SESSION['loggedRole'])&&isset($_SESSION['loggedType']))
        {
            if($_SESSION['loggedRole'] == "ToolAdmin")
            {
    ?>
                <a href="../management/setup.php" id="setupLink" class="internalLink moduleLink">
                    <div class="col-md-12 mainMenuItemCnt">
                        <i class="fa fa-cogs"></i>&nbsp;&nbsp;&nbsp;Setup
                    </div>
                </a>
    
    <?php        
            }
        }
    ?> 
    <a href="../management/dashboards.php" id="dashboardsLink" class="internalLink moduleLink">
        <div class="col-md-12 mainMenuItemCnt">
            <i class="fa fa-dashboard"></i>&nbsp;&nbsp;&nbsp;Dashboards
        </div>
    </a>
    
    <?php
        if(isset($_SESSION['loggedRole'])&&isset($_SESSION['loggedType']))
        {
            if($_SESSION['loggedType'] == "local")
            {
    ?>
                <a class="internalLink moduleLink" href="../management/account.php" id="accountManagementLink">
                    <div class="col-md-12 mainMenuItemCnt">
                        <i class="fa fa-user"></i>&nbsp;&nbsp;&nbsp;Account
                    </div>
                </a>
    <?php        
            }
    ?>  
    
    <?php
            if($_SESSION['loggedRole'] == "ToolAdmin")
            {
    ?>
                <a class="internalLink moduleLink" href="../management/metrics.php" id="link_metric_mng">    
                    <div class="col-md-12 mainMenuItemCnt">
                        <i class="fa fa-server"></i>&nbsp;&nbsp;&nbsp;Metrics
                    </div>
                </a>
                <a class="internalLink moduleLink" href="../management/widgets.php" id="link_widgets_mng">
                    <div class="col-md-12 mainMenuItemCnt">
                        <i class="fa fa-area-chart"></i>&nbsp;&nbsp;&nbsp;Widgets
                    </div>
                </a>
                <a class="internalLink moduleLink" href="../management/datasources.php" id="link_sources_mng">
                    <div class="col-md-12 mainMenuItemCnt">
                        <i class="fa fa-database"></i>&nbsp;&nbsp;&nbsp;Data sources
                    </div>
                </a>
                <a class="internalLink moduleLink" href="../management/users.php" id="link_user_register">
                    <div class="col-md-12 mainMenuItemCnt">
                        <i class="fa fa-group"></i>&nbsp;&nbsp;&nbsp;Users
                    </div>
                </a> 
    <?php        
            }
    ?>
    
    <?php
            if(($_SESSION['loggedRole'] == "ToolAdmin") || ($_SESSION['loggedRole'] == "AreaManager"))
            {
    ?>
            <a class="internalLink moduleLink" href="../management/pools.php?showManagementTab=false&selectedPoolId=-1" id="link_pools_management">
                <div class="col-md-12 mainMenuItemCnt">
                    <i class="fa fa-object-ungroup"></i>&nbsp;&nbsp;&nbsp;Users pools
                </div>
            </a>
    <?php        
            }
    ?>
     
    <?php        
        }
    ?>    
    
    
    <a href="<?php echo $notificatorLink?>" id="notificatorLink" target="blank" class="internalLink moduleLink">
        <div class="col-md-12 mainMenuItemCnt">
            <i class="fa fa-bell"></i>&nbsp;&nbsp;&nbsp;Notificator
        </div>
    </a>    
</div>

<script type='text/javascript'>
    $(document).ready(function () 
    {
        $('div.mainMenuUsrCnt').hover(function(){
            $(this).css("background", "rgba(0, 162, 211, 1)");
            $(this).css("cursor", "pointer");
            $('#mainMenuUsrDetCnt').hide();
            $('#mainMenuUsrLogoutCnt').show();
        }, function(){
            $(this).css("background", "transparent");
            $(this).css("cursor", "normal");
            $('#mainMenuUsrLogoutCnt').hide();
            $('#mainMenuUsrDetCnt').show();
        });
        
        $('div.mainMenuUsrCnt').click(function(){
            location.href = "logout.php";
            /*$.ajax({
                url: "iframeProxy.php",
                action: "notificatorRemoteLogout",
                async: true,
                success: function()
                {

                },
                error: function(errorData)
                {
                    console.log("Remote logout from Notificator failed");
                    console.log(JSON.stringify(errorData));
                },
                complete: function()
                {
                    location.href = "logout.php";
                }
            });*/
        });
    });
</script>    

