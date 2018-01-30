<i id="mobMainMenuBtn" data-shown="false" class="fa fa-navicon"></i>

<div id="mobMainMenuCnt">
    <div id="mobMainMenuPortraitCnt">
        <div class="row">
            <div class="col-xs-12 centerWithFlex" id="mobMainMenuIconCnt">
                <img src="../img/mainMenuIcons/user.ico" />
            </div>
            <div class="col-xs-12 centerWithFlex" id="mobMainMenuUsrCnt">
                <?php echo $_SESSION['loggedUsername']; ?>
            </div>
            <div class="col-xs-12 centerWithFlex" id="mobMainMenuUsrDetCnt">
                <?php echo $_SESSION['loggedRole'] . " | " . $_SESSION['loggedType']; ?>
            </div>
            <div class="col-xs-12 centerWithFlex" id="mobMainMenuUsrLogoutCnt">
                <button type="button" id="mobMainMenuUsrLogoutBtn" class="editDashBtn">logout</button>
            </div>
        </div>
        <hr>
        <?php
        if(isset($_SESSION['loggedRole'])&&isset($_SESSION['loggedType']))
        {
            if($_SESSION['loggedRole'] == "ToolAdmin")
            {
        ?>
                <a href="../management/setup.php" id="setupLink" class="internalLink moduleLink">
                    <div class="col-xs-12 mobMainMenuItemCnt">
                        <i class="fa fa-cogs"></i>&nbsp;&nbsp;&nbsp;Setup
                    </div>
                </a>
        <?php        
                }
            }
        ?> 
        <a href="../management/dashboards.php" id="dashboardsLink" class="internalLink moduleLink">
            <div class="col-xs-12 mobMainMenuItemCnt">
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
                        <div class="col-xs-12 mobMainMenuItemCnt">
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
                        <div class="col-xs-12 mobMainMenuItemCnt">
                            <i class="fa fa-server"></i>&nbsp;&nbsp;&nbsp;Metrics
                        </div>
                    </a>
                    <a class="internalLink moduleLink" href="../management/widgets.php" id="link_widgets_mng">
                        <div class="col-xs-12 mobMainMenuItemCnt">
                            <i class="fa fa-area-chart"></i>&nbsp;&nbsp;&nbsp;Widgets
                        </div>
                    </a>
                    <a class="internalLink moduleLink" href="../management/datasources.php" id="link_sources_mng">
                        <div class="col-xs-12 mobMainMenuItemCnt">
                            <i class="fa fa-database"></i>&nbsp;&nbsp;&nbsp;Data sources
                        </div>
                    </a>
                    <a class="internalLink moduleLink" href="../management/users.php" id="link_user_register">
                        <div class="col-xs-12 mobMainMenuItemCnt">
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
                    <div class="col-xs-12 mobMainMenuItemCnt">
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
            <div class="col-xs-12 mobMainMenuItemCnt">
                <i class="fa fa-bell"></i>&nbsp;&nbsp;&nbsp;Notificator
            </div>
        </a>
    </div>
    
    <div id="mobMainMenuLandCnt">
        <div class="row">
            <div class="col-xs-4 centerWithFlex" id="mobMainMenuUsrCnt">
                <img src="../img/mainMenuIcons/user.ico" />&nbsp;&nbsp;<?php echo $_SESSION['loggedUsername']; ?>
            </div>
            <div class="col-xs-4 centerWithFlex" id="mobMainMenuUsrDetCnt">
                <?php echo $_SESSION['loggedRole'] . " | " . $_SESSION['loggedType']; ?>
            </div>
            <div class="col-xs-4 centerWithFlex" id="mobMainMenuUsrLogoutCnt">
                <button type="button" id="mobMainMenuUsrLogoutBtn" class="editDashBtn">logout</button>
            </div>
        </div>
       
        <a href="../management/setup.php" id="setupLink" class="internalLink moduleLink">
            <div class="col-xs-4 mobMainMenuItemCnt">
                <i class="fa fa-cogs"></i>&nbsp;&nbsp;&nbsp;Setup
            </div>
        </a>
        <a href="../management/dashboards.php" id="dashboardsLink" class="internalLink moduleLink">
            <div class="col-xs-4 mobMainMenuItemCnt">
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
                        <div class="col-xs-4 mobMainMenuItemCnt">
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
                        <div class="col-xs-4 mobMainMenuItemCnt">
                            <i class="fa fa-server"></i>&nbsp;&nbsp;&nbsp;Metrics
                        </div>
                    </a>
                    <a class="internalLink moduleLink" href="../management/widgets.php" id="link_widgets_mng">
                        <div class="col-xs-4 mobMainMenuItemCnt">
                            <i class="fa fa-area-chart"></i>&nbsp;&nbsp;&nbsp;Widgets
                        </div>
                    </a>
                    <a class="internalLink moduleLink" href="../management/datasources.php" id="link_sources_mng">
                        <div class="col-xs-4 mobMainMenuItemCnt">
                            <i class="fa fa-database"></i>&nbsp;&nbsp;&nbsp;Data sources
                        </div>
                    </a>
                    <a class="internalLink moduleLink" href="../management/users.php" id="link_user_register">
                        <div class="col-xs-4 mobMainMenuItemCnt">
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
                    <div class="col-xs-4 mobMainMenuItemCnt">
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
            <div class="col-xs-4 mobMainMenuItemCnt">
                <i class="fa fa-bell"></i>&nbsp;&nbsp;&nbsp;Notificator
            </div>
        </a>
    </div>  
</div>

<script type='text/javascript'>
    $(document).ready(function () 
    {
        $('#mobMainMenuCnt').css("top", parseInt($('#mobHeaderClaimCnt').height() + $('#headerMenuCnt').height()) + "px");
        
        $( window ).on( "orientationchange", function( event ) {
            if($('#mobMainMenuCnt').is(':visible'))
            {
                if($(window).width() < $(window).height())
                {
                    $('#mobMainMenuPortraitCnt').hide();
                    $('#mobMainMenuLandCnt').show();
                }
                else
                {
                    $('#mobMainMenuLandCnt').hide();
                    $('#mobMainMenuPortraitCnt').show();
                }
            }
        });
        
        $('#mobMainMenuBtn').parent().click(function(){
            if($('#mobMainMenuBtn').attr("data-shown") === "false")
            {
                $('#mobMainMenuCnt').show();
                if($(window).width() < $(window).height())
                {
                    $('#mobMainMenuLandCnt').hide();
                    $('#mobMainMenuPortraitCnt').show();
                }
                else
                {
                    $('#mobMainMenuPortraitCnt').hide();
                    $('#mobMainMenuLandCnt').show();
                }
                
                
                $('#mobMainMenuBtn').attr("data-shown", "true");
                setTimeout(function(){
                    $('#mobMainMenuCnt').css("opacity", "1");
                }, 50);
            }
            else
            {
                
                $('#mobMainMenuCnt').css("opacity", "0");
                $('#mobMainMenuBtn').attr("data-shown", "false");
                setTimeout(function(){
                    $('#mobMainMenuCnt').hide();
                }, 350);
            }
        });
        
        
        $('#mobMainMenuPortraitCnt #mobMainMenuUsrLogoutBtn').click(function(){
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
        
        $('#mobMainMenuLandCnt #mobMainMenuUsrLogoutBtn').click(function(){
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

