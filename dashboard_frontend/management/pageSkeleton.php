<div class="container-fluid">
    <div class="row">
        <div class="hidden-xs hidden-sm col-md-2" id="mainMenuCnt">
            <div id="headerClaimCnt" class="col-md-12 centerWithFlex">Dashboard Management System</div>
            <div class="col-md-12 centerWithFlex mainMenuItemCnt">Overview</div>
            <div class="col-md-12 centerWithFlex mainMenuItemCnt">
                <!--<a href="../management/dashboard_mng.php" class="internalLink moduleLink" data-module="dashboards.php">Dashboards</a>-->
                <span class="moduleLink" data-module="dashboards.php" data-moduleName="Dashboards">Dashboards</span>
            </div>
            <div class="col-md-12 centerWithFlex mainMenuItemCnt">
                <a class="internalLink" href="../management/accountManagement.php" id="accountManagementLink">Account</a>
            </div>
            <div class="col-md-12 centerWithFlex mainMenuItemCnt">
                <a class="internalLink" href="../management/metrics_mng.php" id="link_metric_mng">Metrics</a>
            </div>
            <div class="col-md-12 centerWithFlex mainMenuItemCnt">
                <a class="internalLink" href="../management/widgets_mng.php" id="link_widgets_mng">Widgets</a>
            </div>
            <div class="col-md-12 centerWithFlex mainMenuItemCnt">
                <a class="internalLink" href="../management/dataSources_mng.php" id="link_sources_mng">Data sources</a>
            </div>
            <div class="col-md-12 centerWithFlex mainMenuItemCnt">
                <a class="internalLink" href="../management/usersManagement.php" id="link_user_register">Users</a>
            </div>
            <div class="col-md-12 centerWithFlex mainMenuItemCnt">
                <a class="internalLink" href="../management/poolsManagement.php?showManagementTab=false&selectedPoolId=-1" id="link_pools_management">Users pools</a>
            </div>
            <div class="col-md-12 centerWithFlex mainMenuItemCnt">
                <a href="<?php echo $notificatorLink?>" target="blank" class="internalLink">Notificator</a>
            </div>
        </div>
        <div class="col-xs-12 col-md-10" id="mainCnt">
            <div class="row">
                <div class="col-xs-10 col-md-12 centerWithFlex"  id="headerTitleCnt" style="background: yellow">Page title</div>
                <div class="col-xs-2 hidden-md hidden-lg centerWithFlex" id="headerMenuCnt" style="background: grey">Mob men</div>
            </div>
            <div class="row">
                <div class="col-xs-12"  id="mainContentCnt" style="background: green">
                    
                </div>
            </div>
        </div>
    </div>
</div>

<script type='text/javascript'>
    $(document).ready(function () 
    {
        $('span.moduleLink').click(function(){
            var moduleFile = $(this).attr("data-module");
            var moduleName = $(this).attr("data-moduleName");
            $('#headerTitleCnt').html(moduleName);
            $('#mainContentCnt').load(moduleFile);
        });
    });
</script> 