<?php
    include '../config.php';
    
    $link = mysqli_connect($host, $username, $password);
    mysqli_select_db($link, $dbname);
    error_reporting(E_ERROR | E_NOTICE);
    /*
    $lastUsedColors = null;
    $dashId = $_REQUEST['id_dashboard'];
    $q = "SELECT * FROM Dashboard.Config_dashboard WHERE Id = '$dashId'";
    $r = mysqli_query($link, $q);

    if($r) 
    {
        $row = mysqli_fetch_assoc($r);
        $lastUsedColors = json_decode($row['lastUsedColors']);
    } 
     * 
     * 
     * */                     
?>


<!-- Main context menu btn -->
<div id="<?= $name_w ?>_widgetCtxMenuBtnCnt" class="widgetCtxMenuBtnCnt centerWithFlex">
	<i id="<?= $name_w ?>_widgetCtxMenuBtn" class="widgetCtxMenuBtn fa fa-caret-square-o-down" data-status="normal"></i>
</div>

<!-- Main context menu -->
<div id="<?= $name_w ?>_widgetCtxMenu" data-widgetName="<?= $name_w ?>" data-shown="false" class="applicationCtxMenu fullCtxMenu container-fluid widgetCtxMenu">
	<div class="row fullCtxMenuRow headerVisibility" data-selected="false" data-index="0">
            <div class="col-xs-2 fullCtxMenuIcon centerWithFlex"><i class="fa"></i></div>
            <div class="col-xs-10 fullCtxMenuTxt">Hide header</div>
	</div>
	<div class="row fullCtxMenuRow headerColorRow hasSubmenu" data-selected="false" data-index="1" data-boundTo="<?= $name_w ?>_headerColorSubmenu">
            <div class="col-xs-2 fullCtxMenuIcon centerWithFlex"><i class="fa fa-paint-brush"></i></div>
            <div class="col-xs-10 fullCtxMenuTxt">Header color</div>
	</div>
	<div id="<?= $name_w ?>_headerColorSubmenu" data-clicked="false" data-boundTo="headerColorRow" class="fullCtxMenu fullCtxSubmenu dashboardCtxMenu widgetSubmenu container-fluid">
		<div class="row">
                   <div class="col-xs-12 centerWithFlex submenuLabel">Palette</div> 
		   <div id="<?= $name_w ?>_headerColorPicker" class="col-xs-12 centerWithFlex"></div>
		</div>
		<div class="row">
                    <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(255, 255, 255, 0)">
                        <div class="transQuadWhite"></div>
                        <div class="transQuadGrey"></div>
                        <div class="transQuadGrey"></div>
                        <div class="transQuadWhite"></div> 
                    </div>
                    <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(255, 255, 255, 1)"></div>
                    <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(255, 217, 0, 1)"></div>
                    <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(255, 153, 51, 1)"></div>
                    <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(255, 51, 0, 1)"></div>
                    <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(204, 0, 0, 1)"></div>
                    <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(102, 255, 51, 1)"></div>
                    <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(0, 204, 0, 1)"></div>
                    <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(0, 255, 255, 1)"></div>
                    <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(51, 204, 255, 1)"></div>
                    <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(0, 153, 204, 1)"></div>
                    <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(0, 0, 0, 1)"></div>
		</div>
                <div class="row lastUsedColorsRow">
                    <div class="col-xs-12 centerWithFlex submenuLabel">Last used</div> 
                    <?php
                        for($i = 0; $i < count($lastUsedColors); $i++)
                        {
                            echo '<div class="col-xs-1 ctxMenuPaletteColor" data-color="' . $lastUsedColors[$i] . '"></div>';
                        }
                    ?>
		</div>
		<div class="row contextMenuBtnsRow">
                    <div class="col-xs-4 centerWithFlex">
                        <button type="button" class="contextMenuQuitBtn" id="<?= $name_w ?>_headerColorQuitBtn">Quit</button>
                    </div>
                    <div class="col-xs-4 centerWithFlex">
                        <button type="button" class="contextMenuCancelBtn" id="<?= $name_w ?>_headerColorCancelBtn">Undo</button>
                    </div>
                    <div class="col-xs-4 centerWithFlex">
                        <button type="button" class="contextMenuConfirmBtn" id="<?= $name_w ?>_headerColorConfirmBtn">Apply</button>
                    </div>
		</div>
		<div class="row contextMenuMsgRow">
			<div class="col-xs-12 centerWithFlex"></div>
		</div>
	</div>
	
	<div class="row fullCtxMenuRow titleColorRow hasSubmenu" data-selected="false" data-index="2" data-boundTo="<?= $name_w ?>_titleColorSubmenu">
		<div class="col-xs-2 fullCtxMenuIcon centerWithFlex"><i class="fa fa-paint-brush"></i></div>
		<div class="col-xs-10 fullCtxMenuTxt">Title color</div>
	</div>
	<div id="<?= $name_w ?>_titleColorSubmenu" data-clicked="false" data-boundTo="titleColorRow" class="fullCtxMenu fullCtxSubmenu dashboardCtxMenu widgetSubmenu container-fluid">
		<div class="row">
                    <div class="col-xs-12 centerWithFlex submenuLabel">Palette</div>
                    <div id="<?= $name_w ?>_titleColorPicker" class="col-xs-12 centerWithFlex"></div>
		</div>
		<div class="row">
                    <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(255, 255, 255, 0)">
                        <div class="transQuadWhite"></div>
                        <div class="transQuadGrey"></div>
                        <div class="transQuadGrey"></div>
                        <div class="transQuadWhite"></div> 
                    </div>
                    <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(255, 255, 255, 1)"></div>
                    <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(255, 217, 0, 1)"></div>
                    <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(255, 153, 51, 1)"></div>
                    <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(255, 51, 0, 1)"></div>
                    <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(204, 0, 0, 1)"></div>
                    <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(102, 255, 51, 1)"></div>
                    <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(0, 204, 0, 1)"></div>
                    <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(0, 255, 255, 1)"></div>
                    <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(51, 204, 255, 1)"></div>
                    <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(0, 153, 204, 1)"></div>
                    <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(0, 0, 0, 1)"></div>
		</div>
                <div class="row lastUsedColorsRow">
                    <div class="col-xs-12 centerWithFlex submenuLabel">Last used</div>
                    <?php
                        for($i = 0; $i < count($lastUsedColors); $i++)
                        {
                            echo '<div class="col-xs-1 ctxMenuPaletteColor" data-color="' . $lastUsedColors[$i] . '"></div>';
                        }
                    ?>
		</div>
		<div class="row contextMenuBtnsRow">
                    <div class="col-xs-4 centerWithFlex">
                        <button type="button" class="contextMenuQuitBtn" id="<?= $name_w ?>_titleColorQuitBtn">Quit</button>
                    </div>
                    <div class="col-xs-4 centerWithFlex">
                        <button type="button" class="contextMenuCancelBtn" id="<?= $name_w ?>_titleColorCancelBtn">Undo</button>
                    </div>
                    <div class="col-xs-4 centerWithFlex">
                        <button type="button" class="contextMenuConfirmBtn" id="<?= $name_w ?>_titleColorConfirmBtn">Apply</button>
                    </div>
		</div>
		<div class="row contextMenuMsgRow">
                    <div class="col-xs-12 centerWithFlex"></div>
		</div>
	</div>
	
        <div class="row fullCtxMenuRow backgroundColorRow hasSubmenu" data-selected="false" data-index="3" data-boundTo="<?= $name_w ?>_backgroundColorSubmenu">
            <div class="col-xs-2 fullCtxMenuIcon centerWithFlex"><i class="fa fa-paint-brush"></i></div>
            <div class="col-xs-10 fullCtxMenuTxt">Background color</div>
	</div>
	<div id="<?= $name_w ?>_backgroundColorSubmenu" data-clicked="false" data-boundTo="backgroundColorRow" class="fullCtxMenu fullCtxSubmenu dashboardCtxMenu widgetSubmenu container-fluid">
            <div class="row">
                <div class="col-xs-12 centerWithFlex submenuLabel">Palette</div>
                <div id="<?= $name_w ?>_backgroundColorPicker" class="col-xs-12 centerWithFlex"></div>
            </div>
            <div class="row">
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(255, 255, 255, 0)">
                    <div class="transQuadWhite"></div>
                    <div class="transQuadGrey"></div>
                    <div class="transQuadGrey"></div>
                    <div class="transQuadWhite"></div> 
                </div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(255, 255, 255, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(255, 217, 0, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(255, 153, 51, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(255, 51, 0, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(204, 0, 0, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(102, 255, 51, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(0, 204, 0, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(0, 255, 255, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(51, 204, 255, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(0, 153, 204, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(0, 0, 0, 1)"></div>
            </div>
            <div class="row lastUsedColorsRow">
                <div class="col-xs-12 centerWithFlex submenuLabel">Last used</div>
                <?php
                    for($i = 0; $i < count($lastUsedColors); $i++)
                    {
                        echo '<div class="col-xs-1 ctxMenuPaletteColor" data-color="' . $lastUsedColors[$i] . '"></div>';
                    }
                ?>
            </div>
            <div class="row contextMenuBtnsRow">
                <div class="col-xs-4 centerWithFlex">
                    <button type="button" class="contextMenuQuitBtn" id="<?= $name_w ?>_backgroundColorQuitBtn">Quit</button>
                </div>
                <div class="col-xs-4 centerWithFlex">
                    <button type="button" class="contextMenuCancelBtn" id="<?= $name_w ?>_backgroundColorCancelBtn">Undo</button>
                </div>
                <div class="col-xs-4 centerWithFlex">
                    <button type="button" class="contextMenuConfirmBtn" id="<?= $name_w ?>_backgroundColorConfirmBtn">Apply</button>
                </div>
            </div>
            <div class="row contextMenuMsgRow">
                <div class="col-xs-12 centerWithFlex"></div>
            </div>
	</div>
    
        <div id="<?= $name_w ?>_chartColorMenuItem" data-selected="false" class="row fullCtxMenuRow chartColorRow hasSubmenu" data-index="4" data-boundTo="<?= $name_w ?>_chartColorSubmenu">
            <div class="col-xs-2 fullCtxMenuIcon centerWithFlex"><i class="fa fa-paint-brush"></i></div>
            <div class="col-xs-10 fullCtxMenuTxt">Chart color</div>
	</div>
	<div id="<?= $name_w ?>_chartColorSubmenu" data-clicked="false" data-boundTo="chartColorRow" class="fullCtxMenu fullCtxSubmenu dashboardCtxMenu widgetSubmenu container-fluid">
            <div class="row">
                <div class="col-xs-12 centerWithFlex submenuLabel">Palette</div>
                <div id="<?= $name_w ?>_chartColorPicker" class="col-xs-12 centerWithFlex"></div>
            </div>
            <div class="row">
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(255, 255, 255, 0)">
                    <div class="transQuadWhite"></div>
                    <div class="transQuadGrey"></div>
                    <div class="transQuadGrey"></div>
                    <div class="transQuadWhite"></div> 
                </div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(255, 255, 255, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(255, 217, 0, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(255, 153, 51, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(255, 51, 0, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(204, 0, 0, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(102, 255, 51, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(0, 204, 0, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(0, 255, 255, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(51, 204, 255, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(0, 153, 204, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(0, 0, 0, 1)"></div>
            </div>
            <div class="row lastUsedColorsRow">
                <div class="col-xs-12 centerWithFlex submenuLabel">Last used</div>
                <?php
                    for($i = 0; $i < count($lastUsedColors); $i++)
                    {
                        echo '<div class="col-xs-1 ctxMenuPaletteColor" data-color="' . $lastUsedColors[$i] . '"></div>';
                    }
                ?>
            </div>
            <div class="row contextMenuBtnsRow">
                <div class="col-xs-4 centerWithFlex">
                    <button type="button" class="contextMenuQuitBtn" id="<?= $name_w ?>_chartColorQuitBtn">Quit</button>
                </div>
                <div class="col-xs-4 centerWithFlex">
                    <button type="button" class="contextMenuCancelBtn" id="<?= $name_w ?>_chartColorCancelBtn">Undo</button>
                </div>
                <div class="col-xs-4 centerWithFlex">
                    <button type="button" class="contextMenuConfirmBtn" id="<?= $name_w ?>_chartColorConfirmBtn">Apply</button>
                </div>
            </div>
            <div class="row contextMenuMsgRow">
                    <div class="col-xs-12 centerWithFlex"></div>
            </div>
	</div>
        
        <div id="<?= $name_w ?>_chartAxesColorMenuItem" data-selected="false" class="row fullCtxMenuRow chartAxesColor hasSubmenu" data-index="5" data-boundTo="<?= $name_w ?>_chartAxesColorSubmenu">
            <div class="col-xs-2 fullCtxMenuIcon centerWithFlex"><i class="fa fa-paint-brush"></i></div>
            <div class="col-xs-10 fullCtxMenuTxt">Axes color</div>
	</div>
	<div id="<?= $name_w ?>_chartAxesColorSubmenu" data-clicked="false" data-boundTo="chartAxesColor" class="fullCtxMenu fullCtxSubmenu dashboardCtxMenu widgetSubmenu container-fluid">
            <div class="row">
                <div class="col-xs-12 centerWithFlex submenuLabel">Palette</div>
                <div id="<?= $name_w ?>_chartAxesColorPicker" class="col-xs-12 centerWithFlex"></div>
            </div>
            <div class="row">
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(255, 255, 255, 0)">
                    <div class="transQuadWhite"></div>
                    <div class="transQuadGrey"></div>
                    <div class="transQuadGrey"></div>
                    <div class="transQuadWhite"></div> 
                </div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(255, 255, 255, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(255, 217, 0, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(255, 153, 51, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(255, 51, 0, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(204, 0, 0, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(102, 255, 51, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(0, 204, 0, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(0, 255, 255, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(51, 204, 255, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(0, 153, 204, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(0, 0, 0, 1)"></div>
            </div>
            <div class="row lastUsedColorsRow">
                <div class="col-xs-12 centerWithFlex submenuLabel">Last used</div>
                <?php
                    for($i = 0; $i < count($lastUsedColors); $i++)
                    {
                        echo '<div class="col-xs-1 ctxMenuPaletteColor" data-color="' . $lastUsedColors[$i] . '"></div>';
                    }
                ?>
            </div>
            <div class="row contextMenuBtnsRow">
                <div class="col-xs-4 centerWithFlex">
                    <button type="button" class="contextMenuQuitBtn" id="<?= $name_w ?>_chartAxesColorQuitBtn">Quit</button>
                </div>
                <div class="col-xs-4 centerWithFlex">
                    <button type="button" class="contextMenuCancelBtn" id="<?= $name_w ?>_chartAxesColorCancelBtn">Undo</button>
                </div>
                <div class="col-xs-4 centerWithFlex">
                    <button type="button" class="contextMenuConfirmBtn" id="<?= $name_w ?>_chartAxesColorConfirmBtn">Apply</button>
                </div>
            </div>
            <div class="row contextMenuMsgRow">
                    <div class="col-xs-12 centerWithFlex"></div>
            </div>
	</div>
    
        <div id="<?= $name_w ?>_chartPlaneColorMenuItem" data-selected="false" class="row fullCtxMenuRow chartPlaneColor hasSubmenu" data-index="6" data-boundTo="<?= $name_w ?>_chartPlaneColorSubmenu">
            <div class="col-xs-2 fullCtxMenuIcon centerWithFlex"><i class="fa fa-paint-brush"></i></div>
            <div class="col-xs-10 fullCtxMenuTxt">Plane lines color</div>
	</div>
	<div id="<?= $name_w ?>_chartPlaneColorSubmenu" data-clicked="false" data-boundTo="chartPlaneColor" class="fullCtxMenu fullCtxSubmenu dashboardCtxMenu widgetSubmenu container-fluid">
            <div class="row">
                <div class="col-xs-12 centerWithFlex submenuLabel">Palette</div>
                <div id="<?= $name_w ?>_chartPlaneColorPicker" class="col-xs-12 centerWithFlex"></div>
            </div>
            <div class="row">
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(255, 255, 255, 0)">
                    <div class="transQuadWhite"></div>
                    <div class="transQuadGrey"></div>
                    <div class="transQuadGrey"></div>
                    <div class="transQuadWhite"></div> 
                </div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(255, 255, 255, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(255, 217, 0, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(255, 153, 51, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(255, 51, 0, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(204, 0, 0, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(102, 255, 51, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(0, 204, 0, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(0, 255, 255, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(51, 204, 255, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(0, 153, 204, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(0, 0, 0, 1)"></div>
            </div>
            <div class="row lastUsedColorsRow">
                <div class="col-xs-12 centerWithFlex submenuLabel">Last used</div>
                <?php
                    for($i = 0; $i < count($lastUsedColors); $i++)
                    {
                        echo '<div class="col-xs-1 ctxMenuPaletteColor" data-color="' . $lastUsedColors[$i] . '"></div>';
                    }
                ?>
            </div>
            <div class="row contextMenuBtnsRow">
                <div class="col-xs-4 centerWithFlex">
                    <button type="button" class="contextMenuQuitBtn" id="<?= $name_w ?>_chartPlaneColorQuitBtn">Quit</button>
                </div>
                <div class="col-xs-4 centerWithFlex">
                    <button type="button" class="contextMenuCancelBtn" id="<?= $name_w ?>_chartPlaneColorCancelBtn">Undo</button>
                </div>
                <div class="col-xs-4 centerWithFlex">
                    <button type="button" class="contextMenuConfirmBtn" id="<?= $name_w ?>_chartPlaneColorConfirmBtn">Apply</button>
                </div>
            </div>
            <div class="row contextMenuMsgRow">
                    <div class="col-xs-12 centerWithFlex"></div>
            </div>
	</div>
    
        <div id="<?= $name_w ?>_chartLabelsColorMenuItem" data-selected="false" class="row fullCtxMenuRow chartLabelsColor hasSubmenu" data-index="7" data-boundTo="<?= $name_w ?>_chartLabelsColorSubmenu">
            <div class="col-xs-2 fullCtxMenuIcon centerWithFlex"><i class="fa fa-paint-brush"></i></div>
            <div class="col-xs-10 fullCtxMenuTxt">Chart labels color</div>
	</div>
	<div id="<?= $name_w ?>_chartLabelsColorSubmenu" data-clicked="false" data-boundTo="chartLabelsColor" class="fullCtxMenu fullCtxSubmenu dashboardCtxMenu widgetSubmenu container-fluid">
            <div class="row">
                <div class="col-xs-12 centerWithFlex submenuLabel">Palette</div>
                <div id="<?= $name_w ?>_chartLabelsColorPicker" class="col-xs-12 centerWithFlex"></div>
            </div>
            <div class="row">
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(255, 255, 255, 0)">
                    <div class="transQuadWhite"></div>
                    <div class="transQuadGrey"></div>
                    <div class="transQuadGrey"></div>
                    <div class="transQuadWhite"></div> 
                </div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(255, 255, 255, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(255, 217, 0, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(255, 153, 51, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(255, 51, 0, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(204, 0, 0, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(102, 255, 51, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(0, 204, 0, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(0, 255, 255, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(51, 204, 255, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(0, 153, 204, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(0, 0, 0, 1)"></div>
            </div>
            <div class="row lastUsedColorsRow">
                <div class="col-xs-12 centerWithFlex submenuLabel">Last used</div>
                <?php
                    for($i = 0; $i < count($lastUsedColors); $i++)
                    {
                        echo '<div class="col-xs-1 ctxMenuPaletteColor" data-color="' . $lastUsedColors[$i] . '"></div>';
                    }
                ?>
            </div>
            <div class="row contextMenuBtnsRow">
                <div class="col-xs-4 centerWithFlex">
                    <button type="button" class="contextMenuQuitBtn" id="<?= $name_w ?>_chartLabelsColorQuitBtn">Quit</button>
                </div>
                <div class="col-xs-4 centerWithFlex">
                    <button type="button" class="contextMenuCancelBtn" id="<?= $name_w ?>_chartLabelsColorCancelBtn">Undo</button>
                </div>
                <div class="col-xs-4 centerWithFlex">
                    <button type="button" class="contextMenuConfirmBtn" id="<?= $name_w ?>_chartLabelsColorConfirmBtn">Apply</button>
                </div>
            </div>
            <div class="row contextMenuMsgRow">
                <div class="col-xs-12 centerWithFlex"></div>
            </div>
	</div>
    
        <!--<div id="<?= $name_w ?>_dataLabelsFontSizeMenuItem" class="row fullCtxMenuRow dataLabelsFontSizeRow hasSubmenu" data-index="2" data-boundTo="<?= $name_w ?>_dataLabelsFontSizeSubmenu">
            <div class="col-xs-2 fullCtxMenuIcon centerWithFlex"><i class="fa fa-text-height"></i></div>
            <div class="col-xs-10 fullCtxMenuTxt">Data font size</div>
	</div>
	<div id="<?= $name_w ?>_dataLabelsFontSizeSubmenu" data-clicked="false" data-boundTo="dataLabelsFontSizeRow" class="fullCtxMenu fullCtxSubmenu dashboardCtxMenu fontSizeSubmenu widgetSubmenu container-fluid">
            <div class="row">
                <div class="col-xs-3">
                    <i id="<?= $name_w ?>_dataLabelsFontSizeMinus" class="fa fa-minus-circle sizeControl"></i>
                </div>
                <div class="col-xs-6">
                    <input type="text" id="<?= $name_w ?>_dataLabelsFontSize" class="submenuFontSize">
                </div>
                <div class="col-xs-3">
                    <i id="<?= $name_w ?>_dataLabelsFontSizePlus" class="fa fa-plus-circle sizeControl"></i>
                </div>
            </div>
        </div>-->
        
    
        <div id="<?= $name_w ?>_borderMenuItem" data-selected="false" class="row fullCtxMenuRow borderRow hasSubmenu" data-index="8" data-boundTo="<?= $name_w ?>_borderSubmenu">
            <div class="col-xs-2 fullCtxMenuIcon centerWithFlex"><i class="fa fa-paint-brush"></i></div>
            <div class="col-xs-10 fullCtxMenuTxt">Border color</div>
	</div>
        <div id="<?= $name_w ?>_borderSubmenu" data-clicked="false" data-boundTo="borderRow" class="fullCtxMenu fullCtxSubmenu dashboardCtxMenu widgetSubmenu container-fluid">
            <div class="row">
                <div class="col-xs-12 centerWithFlex submenuLabel">Palette</div>
                <div id="<?= $name_w ?>_borderColorPicker" class="col-xs-12 centerWithFlex"></div>
            </div>
            <div class="row">
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(255, 255, 255, 0)">
                    <div class="transQuadWhite"></div>
                    <div class="transQuadGrey"></div>
                    <div class="transQuadGrey"></div>
                    <div class="transQuadWhite"></div> 
                </div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(255, 255, 255, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(255, 217, 0, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(255, 153, 51, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(255, 51, 0, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(204, 0, 0, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(102, 255, 51, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(0, 204, 0, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(0, 255, 255, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(51, 204, 255, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(0, 153, 204, 1)"></div>
                <div class="col-xs-1 ctxMenuPaletteColor" data-color="rgba(0, 0, 0, 1)"></div>
            </div>
            <div class="row lastUsedColorsRow">
                <div class="col-xs-12 centerWithFlex submenuLabel">Last used</div>
                <?php
                    for($i = 0; $i < count($lastUsedColors); $i++)
                    {
                        echo '<div class="col-xs-1 ctxMenuPaletteColor" data-color="' . $lastUsedColors[$i] . '"></div>';
                    }
                ?>
            </div>
            <div class="row contextMenuBtnsRow">
                <div class="col-xs-4 centerWithFlex">
                    <button type="button" class="contextMenuQuitBtn" id="<?= $name_w ?>_borderColorQuitBtn">Quit</button>
                </div>
                <div class="col-xs-4 centerWithFlex">
                    <button type="button" class="contextMenuCancelBtn" id="<?= $name_w ?>_borderCancelBtn">Undo</button>
                </div>
                <div class="col-xs-4 centerWithFlex">
                    <button type="button" class="contextMenuConfirmBtn" id="<?= $name_w ?>_borderConfirmBtn">Apply</button>
                </div>
            </div>
            <div class="row contextMenuMsgRow">
                <div class="col-xs-12 centerWithFlex"></div>
            </div>
	</div>
    
        <div id="<?= $name_w ?>_timeRangeMenuItem" data-selected="false" class="row fullCtxMenuRow timeRangeRow hasSubmenu" data-index="9" data-boundTo="<?= $name_w ?>_timeRangeSubmenu">
            <div class="col-xs-2 fullCtxMenuIcon centerWithFlex"><i class="fa fa-clock-o"></i></div>
            <div class="col-xs-10 fullCtxMenuTxt">Time range</div>
	</div>
        <div id="<?= $name_w ?>_timeRangeSubmenu" data-clicked="false" data-boundTo="timeRangeRow" class="fullCtxMenu fullCtxSubmenu dashboardCtxMenu widgetSubmenu container-fluid">
            <div class="row">
                <div class="col-xs-12 centerWithFlex">
                    <input id="<?= $name_w ?>_timeRangeSlider" type="text" data-provide="slider" />
                </div>
                <div id="<?= $name_w ?>_timeRangeDisplayed" class="col-xs-12 centerWithFlex submenuLabel" style="margin-top: 15px">
                
                </div>
            </div>
            <div class="row contextMenuBtnsRow">
                <div class="col-xs-4 centerWithFlex">
                    <button type="button" class="contextMenuQuitBtn" id="<?= $name_w ?>_timeRangeQuitBtn">Quit</button>
                </div>
                <div class="col-xs-4 centerWithFlex">
                    <button type="button" class="contextMenuCancelBtn" id="<?= $name_w ?>_timeRangeCancelBtn">Undo</button>
                </div>
                <div class="col-xs-4 centerWithFlex">
                    <button type="button" class="contextMenuConfirmBtn" id="<?= $name_w ?>_timeRangeConfirmBtn">Apply</button>
                </div>
            </div>
            <div class="row contextMenuMsgRow">
                <div class="col-xs-12 centerWithFlex"></div>
            </div>
	</div>
	
        <div id="<?= $name_w ?>_changeMetricMenuItem" data-selected="false" class="row fullCtxMenuRow changeMetricRow hasSubmenu" data-index="10" data-boundTo="<?= $name_w ?>_changeMetricSubmenu">
            <div class="col-xs-2 fullCtxMenuIcon centerWithFlex"><i class="fa fa-database"></i></div>
            <div class="col-xs-10 fullCtxMenuTxt">Change metric</div>
	</div>
        <div id="<?= $name_w ?>_changeMetricSubmenu" data-clicked="false" data-boundTo="changeMetricRow" class="fullCtxMenu fullCtxSubmenu dashboardCtxMenu widgetSubmenu widgetSubmenuLarge container-fluid">
            
	</div>

    <!-- Nuova voce di menu per la modalitÃ  di aggiunta items a mappa (per ora) -->
    <div id="<?= $name_w ?>_addMode" class="row fullCtxMenuRow addMode" data-selected="false" data-mode="additive" data-index="10">
        <div class="col-xs-2 fullCtxMenuIcon centerWithFlex"><i class="fa fa-plus-circle"></i></div>
        <div class="col-xs-10 fullCtxMenuTxt">Additive mode</div>
    </div>
    
        <!-- Di solito li lascio sempre in fondo -->
	<div class="row fullCtxMenuRow advancedOptionsRow" data-selected="false" data-index="11">
            <div class="col-xs-2 fullCtxMenuIcon centerWithFlex"><i class="fa fa-cogs"></i></div>
            <div class="col-xs-10 fullCtxMenuTxt">More options</div>
	</div>
	<div class="row fullCtxMenuRow delWidgetRow" data-selected="false" data-index="12">
            <div class="col-xs-2 fullCtxMenuIcon centerWithFlex"><i class="fa fa-close"></i></div>
            <div class="col-xs-10 fullCtxMenuTxt">Delete widget</div>
	</div>
	<div class="row fullCtxMenuRow quitRow" data-selected="false" data-index="13">
            <div class="col-xs-2 fullCtxMenuIcon centerWithFlex"><i class="fa fa-mail-reply"></i></div>
            <div class="col-xs-10 fullCtxMenuTxt">Quit</div>
	</div>
</div>

<script type='text/javascript'>
        function updateLastUsedColors(newColor)
        {
            $('.lastUsedColorsRow').each(function(j){
                if($(this).find('div.ctxMenuPaletteColor').eq(0).attr('data-color') !== newColor)
                {
                    var lastIndex = parseInt($(this).find('div.ctxMenuPaletteColor').length - 1);
                    for(var i = lastIndex; i > 0; i--)
                    {
                        $(this).find('div.ctxMenuPaletteColor').eq(i).attr('data-color', $(this).find('div.ctxMenuPaletteColor').eq(i-1).attr('data-color'));
                        $(this).find('div.ctxMenuPaletteColor').eq(i).css('background-color', $(this).find('div.ctxMenuPaletteColor').eq(i-1).attr('data-color'));
                        if($(this).find('div.ctxMenuPaletteColor').eq(i-1).attr('data-color') === 'rgba(255, 255, 255, 0)')
                        {
                            $(this).find('div.ctxMenuPaletteColor').eq(i).empty();
                            $(this).find('div.ctxMenuPaletteColor').eq(i).append('<div class="transQuadWhite"></div>');
                            $(this).find('div.ctxMenuPaletteColor').eq(i).append('<div class="transQuadGrey"></div>');
                            $(this).find('div.ctxMenuPaletteColor').eq(i).append('<div class="transQuadGrey"></div>');
                            $(this).find('div.ctxMenuPaletteColor').eq(i).append('<div class="transQuadWhite"></div>');
                        } 
                    }

                    $(this).find('div.ctxMenuPaletteColor').eq(0).attr('data-color', newColor);
                    $(this).find('div.ctxMenuPaletteColor').eq(0).css('background-color', newColor);
                    
                    if(newColor === 'rgba(255,255,255,0)')
                    {
                        $(this).find('div.ctxMenuPaletteColor').eq(0).empty();
                        $(this).find('div.ctxMenuPaletteColor').eq(0).append('<div class="transQuadWhite"></div>');
                        $(this).find('div.ctxMenuPaletteColor').eq(0).append('<div class="transQuadGrey"></div>');
                        $(this).find('div.ctxMenuPaletteColor').eq(0).append('<div class="transQuadGrey"></div>');
                        $(this).find('div.ctxMenuPaletteColor').eq(0).append('<div class="transQuadWhite"></div>');
                    } 
                }
            });
        }
    
	$(document).ready(function ()
        {
            var chart, chartMin = null;
            
           if("<?= $hostfile ?>" === 'config')
	   {
               $.ajax({
                url: "../controllers/getWidgetParams.php",
                type: "GET",
                data: {
                    widgetName: "<?= $name_w ?>"
                },
                async: true,
                dataType: 'json',
                success: function(widgetData) 
                {
                    var showTitle = widgetData.params.showTitle;
                    var fontSize = widgetData.params.fontSize;
                    var fontColor = widgetData.params.fontColor;
                    var timeToReload = widgetData.params.frequency_w;
                    var hasTimer = widgetData.params.hasTimer;
                    var widgetHeaderColor = widgetData.params.frame_color_w;
                    var widgetHeaderFontColor = widgetData.params.headerFontColor;
                    var hasChartColor = widgetData.params.hasChartColor;
                    var chartPlaneColor = widgetData.params.chartPlaneColor;
                    var chartColor = widgetData.params.chartColor;
                    var hasDataLabels = widgetData.params.hasDataLabels;
                    var dataLabelsFontSize = widgetData.params.dataLabelsFontSize; 
                    var dataLabelsFontColor = widgetData.params.dataLabelsFontColor; 
                    var hasChartLabels = widgetData.params.hasChartLabels;
                    var hasAddMode = widgetData.params.hasAddMode;
                    var chartLabelsFontSize = widgetData.params.chartLabelsFontSize; 
                    var chartLabelsFontColor = widgetData.params.chartLabelsFontColor;
                    var borderColor = widgetData.params.borderColor;
                    var hasTimeRange = widgetData.params.hasTimeRange;
                    var timeRange = widgetData.params.temporal_range_w;
                    var hasCartesianPlane = widgetData.params.hasCartesianPlane;
                    var chartAxesColor = widgetData.params.chartAxesColor;
                    var hasChangeMetric = widgetData.params.hasChangeMetric;
                    var timeRangeDisplayed, timeRangeTick, timeRangeSlider = null;

                    if(hasAddMode === 'no')
                    {
                        //Se il flag su DB non prevede questa voce di menu per questo widget, rimuoviamo dal DOM il relativo elemento di menu
                        $('#<?= $name_w ?>_addMode').remove();
                    }
                    else
                    {
                        //Gestore custom di eventi click sull'item di menu per la modalitÃ  di aggiunta elementi
                        $('#<?= $name_w ?>_addMode').off('click');
                        $('#<?= $name_w ?>_addMode').click(function(){
                            if($(this).attr('data-mode') === 'additive')
                            {
                                //Toggle dell'attributo di stato della voce di menu
                                $(this).attr('data-mode', 'exclusive');

                                //Toggle del testo della voce di menu
                                $(this).find('div.fullCtxMenuTxt').html("Exclusive mode");

                                $(this).find('div.fullCtxMenuIcon').html('<i class="fa fa-minus-circle"></i>');
                            }
                            else
                            {
                                //Toggle dell'attributo di stato della voce di menu
                                $(this).attr('data-mode', 'additive');

                                //Toggle del testo della voce di menu
                                $(this).find('div.fullCtxMenuTxt').html("Additive mode");

                                $(this).find('div.fullCtxMenuIcon').html('<i class="fa fa-plus-circle"></i>');
                            }

                            //Update parametro su DB
                            $.ajax({
                                url: "../controllers/updateWidget.php",
                                data: {
                                    action: "updateAddMode",
                                    widgetName: "<?= $name_w ?>",
                                    addMode: $(this).attr('data-mode')
                                },
                                type: "POST",
                                async: true,
                                dataType: 'json',
                                success: function(data)
                                {
                                    if(data.detail !== 'Ok')
                                    {
                                        //In caso d'errore, facciamo un altro toggle per ripristinare lo stato originario
                                        if($('#<?= $name_w ?>_addMode').attr('data-mode') === 'additive')
                                        {
                                            //Toggle dell'attributo di stato della voce di menu
                                            $('#<?= $name_w ?>_addMode').attr('data-mode', 'exclusive');

                                            //Toggle del testo della voce di menu
                                            $('#<?= $name_w ?>_addMode').find('div.fullCtxMenuTxt').html("Exclusive mode");

                                            $('#<?= $name_w ?>_addMode').find('div.fullCtxMenuIcon').html('<i class="fa fa-minus-circle"></i>');
                                        }
                                        else
                                        {
                                            //Toggle dell'attributo di stato della voce di menu
                                            $('#<?= $name_w ?>_addMode').attr('data-mode', 'additive');

                                            //Toggle del testo della voce di menu
                                            $('#<?= $name_w ?>_addMode').find('div.fullCtxMenuTxt').html("Additive mode");

                                            $('#<?= $name_w ?>_addMode').find('div.fullCtxMenuIcon').html('<i class="fa fa-plus-circle"></i>');
                                        }
                                    }
                                    else
                                    {
                                        //TODO - INSERIAMO IL TRIGGER DI UN EVENTO CUSTOM CUI RISPONDERA' IL WIDGET MAP. Nell'oggetto che rappresenta l'evento mettiamo il nuovo addMode. Il widget mappa aggiornerÃ  di conseguenza la propria variabile di addMode
                                        let addMode = $('#<?= $name_w ?>_addMode').attr('data-mode');
                                        $.event.trigger({
                                            type: "toggleAddMode",
                                            addMode: addMode
                                        });

                                        //TODO - INSERIAMO IL TRIGGER DI UN EVENTO CUSTOM CUI RISPONDERANNO GLI WIDGET PILOTA. Nell'oggetto che rappresenta l'evento mettiamo l'identificativo del widget mappa, di modo che rispondano solo i piloti che hanno tale widget mappa nella propria target list
                                    }
                                },
                                error: function(errorData)
                                {
                                    //In caso d'errore, facciamo un altro toggle per ripristinare lo stato originario
                                    if($('#<?= $name_w ?>_addMode').attr('data-mode') === 'additive')
                                    {
                                        //Toggle dell'attributo di stato della voce di menu
                                        $('#<?= $name_w ?>_addMode').attr('data-mode', 'exclusive');

                                        //Toggle del testo della voce di menu
                                        $('#<?= $name_w ?>_addMode').find('div.fullCtxMenuTxt').html("Exclusive mode");

                                        $('#<?= $name_w ?>_addMode').find('div.fullCtxMenuIcon').html('<i class="fa fa-minus-circle"></i>');
                                    }
                                    else
                                    {
                                        //Toggle dell'attributo di stato della voce di menu
                                        $('#<?= $name_w ?>_addMode').attr('data-mode', 'additive');

                                        //Toggle del testo della voce di menu
                                        $('#<?= $name_w ?>_addMode').find('div.fullCtxMenuTxt').html("Additive mode");

                                        $('#<?= $name_w ?>_addMode').find('div.fullCtxMenuIcon').html('<i class="fa fa-plus-circle"></i>');
                                    }
                                }
                            });


                            /*var contentHeight, showTitle, showHeader = null;
                            if($('#<?= $name_w ?>_header').is(':visible'))
                            {
                                $('#<?= $name_w ?>_header').hide();
                                $('#<?= $name_w ?>_widgetCtxMenu .headerVisibility .fullCtxMenuIcon i').removeClass('fa-eye-slash');
                                $('#<?= $name_w ?>_widgetCtxMenu .headerVisibility .fullCtxMenuIcon i').addClass('fa-eye');
                                $('#<?= $name_w ?>_widgetCtxMenu .headerVisibility .fullCtxMenuTxt').html('Show header');
                                contentHeight = parseInt($("#<?= $name_w ?>_div").prop("offsetHeight"));
                                showTitle = 'no';
                                showHeader = false;
                                $('#<?= $name_w ?>_widgetCtxMenuBtnCnt').css('background-color', 'rgba(51, 64, 69, 0.7)');
                                $('#<?= $name_w ?>_widgetCtxMenuBtn').css('color', 'white');
                            }
                            else
                            {
                                $('#<?= $name_w ?>_header').show();
                                $('#<?= $name_w ?>_widgetCtxMenu .headerVisibility .fullCtxMenuIcon i').removeClass('fa-eye');
                                $('#<?= $name_w ?>_widgetCtxMenu .headerVisibility .fullCtxMenuIcon i').addClass('fa-eye-slash');
                                $('#<?= $name_w ?>_widgetCtxMenu .headerVisibility .fullCtxMenuTxt').html('Hide header');
                                contentHeight = parseInt($("#<?= $name_w ?>_div").prop("offsetHeight") - $("#<?= $name_w ?>_header").prop("offsetHeight"));
                                showTitle = 'yes';
                                showHeader = true;
                                $('#<?= $name_w ?>_widgetCtxMenuBtnCnt').css('background-color', $('#<?= $name_w ?>_header').css('background-color'));
                                $('#<?= $name_w ?>_widgetCtxMenuBtn').css('color', $('#<?= $name_w ?>_header').css('color'));
                            }

                            $("#<?= $name_w ?>_content").css("height", contentHeight + 'px');

                            //Innesco di evento cambio altezza diagramma per gli widgets Highcharts
                            $.event.trigger({
                                type: "resizeHighchart_<?= $name_w ?>",
                                showHeader: showHeader
                            });

                            //Update parametro su DB
                            $.ajax({
                                url: "../controllers/updateWidget.php",
                                data: {
                                    action: "updateTitleVisibility",
                                    widgetName: "<?= $name_w ?>",
                                    showTitle: showTitle
                                },
                                type: "POST",
                                async: true,
                                dataType: 'json',
                                success: function(data)
                                {
                                    if(data.detail !== 'Ok')
                                    {
                                        if($('#<?= $name_w ?>_header').is(':visible'))
                                        {
                                            $('#<?= $name_w ?>_header').hide();
                                            $('#<?= $name_w ?>_widgetCtxMenu .headerVisibility .fullCtxMenuIcon i').removeClass('fa-eye-slash');
                                            $('#<?= $name_w ?>_widgetCtxMenu .headerVisibility .fullCtxMenuIcon i').addClass('fa-eye');
                                            $('#<?= $name_w ?>_widgetCtxMenu .headerVisibility .fullCtxMenuTxt').html('Show header');
                                            contentHeight = parseInt($("#<?= $name_w ?>_div").prop("offsetHeight"));
                                        }
                                        else
                                        {
                                            $('#<?= $name_w ?>_header').show();
                                            $('#<?= $name_w ?>_widgetCtxMenu .headerVisibility .fullCtxMenuIcon i').removeClass('fa-eye');
                                            $('#<?= $name_w ?>_widgetCtxMenu .headerVisibility .fullCtxMenuIcon i').addClass('fa-eye-slash');
                                            $('#<?= $name_w ?>_widgetCtxMenu .headerVisibility .fullCtxMenuTxt').html('Hide header');
                                            contentHeight = parseInt($("#<?= $name_w ?>_div").prop("offsetHeight") - $("#<?= $name_w ?>_header").prop("offsetHeight"));
                                        }

                                        $("#<?= $name_w ?>_content").css("height", contentHeight + 'px');
                                    }
                                },
                                error: function(errorData)
                                {
                                    if($('#<?= $name_w ?>_header').is(':visible'))
                                    {
                                        $('#<?= $name_w ?>_header').hide();
                                        $('#<?= $name_w ?>_widgetCtxMenu .headerVisibility .fullCtxMenuIcon i').removeClass('fa-eye-slash');
                                        $('#<?= $name_w ?>_widgetCtxMenu .headerVisibility .fullCtxMenuIcon i').addClass('fa-eye');
                                        $('#<?= $name_w ?>_widgetCtxMenu .headerVisibility .fullCtxMenuTxt').html('Show header');
                                        contentHeight = parseInt($("#<?= $name_w ?>_div").prop("offsetHeight"));
                                    }
                                    else
                                    {
                                        $('#<?= $name_w ?>_header').show();
                                        $('#<?= $name_w ?>_widgetCtxMenu .headerVisibility .fullCtxMenuIcon i').removeClass('fa-eye');
                                        $('#<?= $name_w ?>_widgetCtxMenu .headerVisibility .fullCtxMenuIcon i').addClass('fa-eye-slash');
                                        $('#<?= $name_w ?>_widgetCtxMenu .headerVisibility .fullCtxMenuTxt').html('Hide header');
                                        contentHeight = parseInt($("#<?= $name_w ?>_div").prop("offsetHeight") - $("#<?= $name_w ?>_header").prop("offsetHeight"));
                                    }
                                }
                            });*/
                        });

                    }

                    if(hasChangeMetric === 'no')
                    {
                        $('#<?= $name_w ?> .changeMetricRow').remove();
                        $('#<?= $name_w ?>_changeMetricSubmenu').remove();
                    }
                    else
                    {
                        //Eventuale logica del relativo sottomenu $("#source").appendTo("#destination");
                        
                    }
                    
                    if(hasChartLabels === 'no')
                    {
                        $('#<?= $name_w ?>_chartLabelsColorMenuItem').remove();
                        $('#<?= $name_w ?>_chartLabelsColorSubmenu').remove();
                    }
                    else
                    {
                        $("#<?= $name_w ?>_chartLabelsColorMenuItem").off('chartCreated');
                        $("#<?= $name_w ?>_chartLabelsColorMenuItem").on('chartCreated', function(){
                            chart = $("#<?= $name_w ?>_chartContainer").highcharts();
                            
                            //Instanziamento color picker colore grafico + shortcuts + azioni
                            $('#<?= $name_w ?>_header').attr('data-currentChartLabelsColor', chartLabelsFontColor); 
                            
                            $('#<?= $name_w ?>_chartLabelsColorPicker').colorpicker({
                                horizontal: false,
                                customClass: 'dashHeaderColorPicker',
                                inline: true,
                                format: "rgba",
                                container: true,
                                color: chartLabelsFontColor
                            }).on('changeColor', function(e){
                                var newColor = $("#<?= $name_w ?>_chartLabelsColorPicker").colorpicker('getValue');
                                $("#<?= $name_w ?> .highcharts-xaxis-labels span").css("color", newColor + " !important");
                                $("#<?= $name_w ?> .highcharts-axis-labels").css("stroke", newColor);
                                $("#<?= $name_w ?> .highcharts-axis-labels").css("stroke-width", "0.5px");
                                $("#<?= $name_w ?> .highcharts-legend-item span").css("color", newColor + " !important");
                                $('#<?= $name_w ?>_header').attr('data-newChartLabelsColor', newColor);
                            });

                            $('#<?= $name_w ?>_chartLabelsColorSubmenu div.ctxMenuPaletteColor').click(function(){
                                var newColor = $(this).attr('data-color');
                                $("#<?= $name_w ?>_chartLabelsColorPicker").colorpicker('setValue', newColor);
                                $("#<?= $name_w ?> .highcharts-xaxis-labels span").css("color", newColor + " !important");
                                $("#<?= $name_w ?> .highcharts-axis-labels").css("stroke", newColor);
                                $("#<?= $name_w ?> .highcharts-axis-labels").css("stroke-width", "0.5px");
                                $("#<?= $name_w ?> .highcharts-legend-item span").css("color", newColor + " !important");
                                $('#<?= $name_w ?>_header').attr('data-newChartLabelsColor', newColor);
                            });

                            $('#<?= $name_w ?>_chartLabelsColorCancelBtn').click(function(){
                                $("#<?= $name_w ?>_chartLabelsColorPicker").colorpicker('setValue', $('#<?= $name_w ?>_header').attr('data-currentChartLabelsColor'));
                                $("#<?= $name_w ?> .highcharts-axis-labels").css("stroke", $('#<?= $name_w ?>_header').attr('data-currentChartLabelsColor'));
                                $("#<?= $name_w ?> .highcharts-axis-labels").css("stroke-width", "0.5px");;
                                $('#<?= $name_w ?>_header').attr('data-newChartLabelsColor', $('#<?= $name_w ?>_header').attr('data-currentChartLabelsColor'));
                            });

                            $('#<?= $name_w ?>_chartLabelsColorConfirmBtn').click(function(){
                                    $(this).parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Saving&nbsp;<i class="fa fa-circle-o-notch fa-spin" style="font-size:14px"></i>');
                                    $(this).parents('div.container-fluid').find('div.contextMenuMsgRow').show();

                                    var button = $(this);

                                    $.ajax({
                                            url: "../controllers/updateWidget.php",
                                            data: {
                                                action: "updateChartLabelsColor",
                                                widgetName: "<?= $name_w ?>", 
                                                newColor: $('#<?= $name_w ?>_header').attr('data-newChartLabelsColor')
                                            },
                                            type: "POST",
                                            async: true,
                                            dataType: 'json',
                                            success: function(data) 
                                            {
                                                if(data.detail === 'Ok')
                                                {
                                                    updateLastUsedColors($('#<?= $name_w ?>_header').attr('data-newChartPlaneColor'));

                                                    button.parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Saved&nbsp;<i class="fa fa-thumbs-up" style="font-size:14px"></i>');
                                                    $('#<?= $name_w ?>_header').attr('data-currentChartColor', $('#<?= $name_w ?>_header').attr('data-newChartColor'));
                                                    setTimeout(function(){
                                                        button.parents('div.container-fluid').find('div.contextMenuMsgRow').hide();    
                                                        button.parents('div.widgetSubmenu').hide();
                                                    }, 1000);
                                                }
                                                else
                                                {
                                                    button.parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Error&nbsp;<i class="fa fa-thumbs-down" style="font-size:14px"></i>');
                                                    setTimeout(function(){
                                                            button.parents('div.container-fluid').find('div.contextMenuMsgRow').hide();
                                                    }, 1000);
                                                }
                                            },
                                            error: function(errorData)
                                            {
                                                button.parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Error&nbsp;<i class="fa fa-thumbs-down" style="font-size:14px"></i>');
                                                setTimeout(function(){
                                                        button.parents('div.container-fluid').find('div.contextMenuMsgRow').hide();
                                                }, 1000);
                                            }
                                    });
                            });
                        });
                    }
                    
                    if(hasCartesianPlane === 'no')
                    {
                        $('#<?= $name_w ?>_chartAxesColorMenuItem').remove();
                        $('#<?= $name_w ?>_chartAxesColorSubmenu').remove();
                        $('#<?= $name_w ?>_chartPlaneColorMenuItem').remove();
                        $('#<?= $name_w ?>_chartPlaneColorSubmenu').remove();
                    }
                    else
                    {
                        $("#<?= $name_w ?>_chartAxesColorMenuItem").off('chartCreated');
                        $("#<?= $name_w ?>_chartAxesColorMenuItem").on('chartCreated', function(){
                            chart = $("#<?= $name_w ?>_chartContainer").highcharts();
                            
                            //Instanziamento color picker colore grafico + shortcuts + azioni
                            $('#<?= $name_w ?>_header').attr('data-currentChartAxesColor', chartAxesColor); 

                            $('#<?= $name_w ?>_chartAxesColorPicker').colorpicker({
                                    horizontal: false,
                                    customClass: 'dashHeaderColorPicker',
                                    inline: true,
                                    format: "rgba",
                                    container: true,
                                    color: chartAxesColor
                            }).on('changeColor', function(e){
                                var newColor = $("#<?= $name_w ?>_chartAxesColorPicker").colorpicker('getValue');
                                $("#<?= $name_w ?> .highcharts-axis-line").css("stroke", newColor);
                                $('#<?= $name_w ?>_header').attr('data-newChartAxesColor', newColor);
                            });

                            $('#<?= $name_w ?>_chartAxesColorSubmenu div.ctxMenuPaletteColor').click(function(){
                                var newColor = $(this).attr('data-color');
                                $("#<?= $name_w ?>_chartAxesColorPicker").colorpicker('setValue', newColor);    
                                $("#<?= $name_w ?> .highcharts-axis-line").css("stroke", newColor);
                                $('#<?= $name_w ?>_header').attr('data-newChartAxesColor', newColor);
                            });

                            $('#<?= $name_w ?>_chartAxesColorCancelBtn').click(function(){
                                $("#<?= $name_w ?>_chartAxesColorPicker").colorpicker('setValue', $('#<?= $name_w ?>_header').attr('data-currentChartAxesColor'));
                                $("#<?= $name_w ?> .highcharts-axis-line").css("stroke", $('#<?= $name_w ?>_header').attr('data-currentChartAxesColor'));
                                $('#<?= $name_w ?>_header').attr('data-newChartAxesColor', $('#<?= $name_w ?>_header').attr('data-currentChartAxesColor'));
                            });

                            $('#<?= $name_w ?>_chartAxesColorConfirmBtn').click(function(){
                                $(this).parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Saving&nbsp;<i class="fa fa-circle-o-notch fa-spin" style="font-size:14px"></i>');
                                $(this).parents('div.container-fluid').find('div.contextMenuMsgRow').show();

                                var button = $(this);

                                    $.ajax({
                                            url: "../controllers/updateWidget.php",
                                            data: {
                                                action: "updateChartAxesColor",
                                                widgetName: "<?= $name_w ?>", 
                                                newColor: $('#<?= $name_w ?>_header').attr('data-newChartAxesColor')
                                            },
                                            type: "POST",
                                            async: true,
                                            dataType: 'json',
                                            success: function(data) 
                                            {
                                                if(data.detail === 'Ok')
                                                {
                                                    updateLastUsedColors($('#<?= $name_w ?>_header').attr('data-newChartPlaneColor'));

                                                    button.parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Saved&nbsp;<i class="fa fa-thumbs-up" style="font-size:14px"></i>');
                                                    $('#<?= $name_w ?>_header').attr('data-currentChartAxesColor', $('#<?= $name_w ?>_header').attr('data-newChartAxesColor'));
                                                    setTimeout(function(){
                                                        button.parents('div.container-fluid').find('div.contextMenuMsgRow').hide();    
                                                        button.parents('div.widgetSubmenu').hide();
                                                    }, 1000);
                                                }
                                                else
                                                {
                                                    button.parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Error&nbsp;<i class="fa fa-thumbs-down" style="font-size:14px"></i>');
                                                    setTimeout(function(){
                                                        button.parents('div.container-fluid').find('div.contextMenuMsgRow').hide();
                                                    }, 1000);
                                                }
                                            },
                                            error: function(errorData)
                                            {
                                                button.parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Error&nbsp;<i class="fa fa-thumbs-down" style="font-size:14px"></i>');
                                                setTimeout(function(){
                                                        button.parents('div.container-fluid').find('div.contextMenuMsgRow').hide();
                                                }, 1000);
                                            }
                                    });
                            });
                        });
                        
                        $("#<?= $name_w ?>_chartPlaneColorMenuItem").off('chartCreated');
                        $("#<?= $name_w ?>_chartPlaneColorMenuItem").on('chartCreated', function(){
                            chart = $("#<?= $name_w ?>_chartContainer").highcharts();
                            
                            //Instanziamento color picker colore grafico + shortcuts + azioni
                            $('#<?= $name_w ?>_header').attr('data-currentChartPlaneColor', chartPlaneColor); 

                            $('#<?= $name_w ?>_chartPlaneColorPicker').colorpicker({
                                    horizontal: false,
                                    customClass: 'dashHeaderColorPicker',
                                    inline: true,
                                    format: "rgba",
                                    container: true,
                                    color: chartPlaneColor
                            }).on('changeColor', function(e){
                                    var newColor = $("#<?= $name_w ?>_chartPlaneColorPicker").colorpicker('getValue');
                                    $("#<?= $name_w ?> .highcharts-yaxis-grid .highcharts-grid-line").css("stroke", newColor);
                                    $('#<?= $name_w ?>_header').attr('data-newChartPlaneColor', newColor);
                            });

                            $('#<?= $name_w ?>_chartPlaneColorSubmenu div.ctxMenuPaletteColor').click(function(){
                                var newColor = $(this).attr('data-color');
                                $("#<?= $name_w ?>_chartPlaneColorPicker").colorpicker('setValue', newColor);    
                                $("#<?= $name_w ?> .highcharts-yaxis-grid .highcharts-grid-line").css("stroke", newColor);
                                $('#<?= $name_w ?>_header').attr('data-newChartPlaneColor', newColor);
                            });

                            $('#<?= $name_w ?>_chartPlaneColorCancelBtn').click(function(){
                                $("#<?= $name_w ?>_chartPlaneColorPicker").colorpicker('setValue', $('#<?= $name_w ?>_header').attr('data-currentChartPlaneColor'));
                                $("#<?= $name_w ?> .highcharts-yaxis-grid .highcharts-grid-line").css("stroke", $('#<?= $name_w ?>_header').attr('data-currentChartPlaneColor'));
                                $('#<?= $name_w ?>_header').attr('data-newChartPlaneColor', $('#<?= $name_w ?>_header').attr('data-currentChartPlaneColor'));
                            });

                            $('#<?= $name_w ?>_chartPlaneColorConfirmBtn').click(function(){
                                    $(this).parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Saving&nbsp;<i class="fa fa-circle-o-notch fa-spin" style="font-size:14px"></i>');
                                    $(this).parents('div.container-fluid').find('div.contextMenuMsgRow').show();

                                    var button = $(this);

                                    $.ajax({
                                            url: "../controllers/updateWidget.php",
                                            data: {
                                                action: "updateChartPlaneColor",
                                                widgetName: "<?= $name_w ?>", 
                                                newColor: $('#<?= $name_w ?>_header').attr('data-newChartPlaneColor')
                                            },
                                            type: "POST",
                                            async: true,
                                            dataType: 'json',
                                            success: function(data) 
                                            {
                                                if(data.detail === 'Ok')
                                                {
                                                    updateLastUsedColors($('#<?= $name_w ?>_header').attr('data-newChartPlaneColor'));

                                                    button.parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Saved&nbsp;<i class="fa fa-thumbs-up" style="font-size:14px"></i>');
                                                    $('#<?= $name_w ?>_header').attr('data-currentChartColor', $('#<?= $name_w ?>_header').attr('data-newChartColor'));
                                                    setTimeout(function(){
                                                        button.parents('div.container-fluid').find('div.contextMenuMsgRow').hide();    
                                                        button.parents('div.widgetSubmenu').hide();
                                                    }, 1000);
                                                }
                                                else
                                                {
                                                    button.parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Error&nbsp;<i class="fa fa-thumbs-down" style="font-size:14px"></i>');
                                                    setTimeout(function(){
                                                            button.parents('div.container-fluid').find('div.contextMenuMsgRow').hide();
                                                    }, 1000);
                                                }
                                            },
                                            error: function(errorData)
                                            {
                                                button.parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Error&nbsp;<i class="fa fa-thumbs-down" style="font-size:14px"></i>');
                                                setTimeout(function(){
                                                        button.parents('div.container-fluid').find('div.contextMenuMsgRow').hide();
                                                }, 1000);
                                            }
                                    });
                            });
                        });
                    }
                    
                    if(hasTimeRange === 'no')
                    {
                        $('#<?= $name_w ?>_timeRangeMenuItem').remove();
                        $('#<?= $name_w ?>_timeRangeSubmenu').remove();
                    }
                    else
                    {
                        switch(timeRange)
                        {
                            case "4 Ore":
                                timeRangeDisplayed = "4 Hours";
                                timeRangeTick = 1;
                                break;

                            case "12 Ore":
                                timeRangeDisplayed = "12 Hours";
                                timeRangeTick = 2;
                                break;

                            case "Giornaliera":
                                timeRangeDisplayed = "1 Day";
                                timeRangeTick = 3;
                                break;

                            case "Settimanale":
                                timeRangeDisplayed = "7 Days";
                                timeRangeTick = 4;
                                break;

                            case "Mensile":
                                timeRangeDisplayed = "1 Month";
                                timeRangeTick = 5;
                                break;

                            case "Annuale":
                                timeRangeDisplayed = "1 Year";
                                timeRangeTick = 6;
                                break;
                        }
                        
                        timeRangeSlider = $("#<?= $name_w ?>_timeRangeSlider").bootstrapSlider({
                            min: 1,
                            max: 6,
                            step: 1,
                            value: timeRangeTick,
                            ticks: [1, 2, 3, 4, 5, 6],
                            tooltip: 'hide'
                        });
                        
                        $("#<?= $name_w ?>_timeRangeSlider").on("change", function(slideEvt) 
                        {
                            timeRangeTick = parseInt($(this).attr('data-value'));
                            switch(timeRangeTick)
                            {
                                case 1:
                                    timeRangeDisplayed = "4 Hours";
                                    timeRange = "4 Ore";
                                    break;
                                    
                                case 2:
                                    timeRangeDisplayed = "12 Hours";
                                    timeRange = "12 Ore";
                                    break;    
                                    
                                case 3:
                                    timeRangeDisplayed = "1 Day";
                                    timeRange = "Giornaliera";
                                    break;
                                    
                                case 4:
                                    timeRangeDisplayed = "7 Days";
                                    timeRange = "Settimanale";
                                    break;
                                    
                                case 5:
                                    timeRangeDisplayed = "1 Month";
                                    timeRange = "Mensile";
                                    break;
                                    
                                case 6:
                                    timeRangeDisplayed = "1 Year";
                                    timeRange = "Annuale";
                                    break;    
                            }
                            
                            $("#<?= $name_w ?>_timeRangeDisplayed").html(timeRangeDisplayed);
                            $('#<?= $name_w ?>_header').attr('data-newTimeRange', timeRange);
                        });
                        
                        $("#<?= $name_w ?>_timeRangeDisplayed").html(timeRangeDisplayed);
                        $('#<?= $name_w ?>_header').attr('data-currentTimeRange', timeRange);
                        $('#<?= $name_w ?>_header').attr('data-newTimeRange', timeRange);
                        
                        $('#<?= $name_w ?>_timeRangeCancelBtn').click(function()
                        {
                            switch($('#<?= $name_w ?>_header').attr('data-currentTimeRange'))
                            {
                                case "4 Ore":
                                    timeRangeDisplayed = "4 Hours";
                                    timeRangeTick = 1;
                                    break;

                                case "12 Ore":
                                    timeRangeDisplayed = "12 Hours";
                                    timeRangeTick = 2;
                                    break;

                                case "Giornaliera":
                                    timeRangeDisplayed = "1 Day";
                                    timeRangeTick = 3;
                                    break;

                                case "Settimanale":
                                    timeRangeDisplayed = "7 Days";
                                    timeRangeTick = 4;
                                    break;

                                case "Mensile":
                                    timeRangeDisplayed = "1 Month";
                                    timeRangeTick = 5;
                                    break;

                                case "Annuale":
                                    timeRangeDisplayed = "1 Year";
                                    timeRangeTick = 6;
                                    break;
                            }
                            
                            $("#<?= $name_w ?>_timeRangeDisplayed").html(timeRangeDisplayed);
                            $("#<?= $name_w ?>_timeRangeSlider").bootstrapSlider('setValue', timeRangeTick);
                            $('#<?= $name_w ?>_header').attr('data-newTimeRange', $('#<?= $name_w ?>_header').attr('data-currentTimeRange'));
                        });
                        
                        $('#<?= $name_w ?>_timeRangeConfirmBtn').click(function(){
                            $(this).parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Saving&nbsp;<i class="fa fa-circle-o-notch fa-spin" style="font-size:14px"></i>');
                            $(this).parents('div.container-fluid').find('div.contextMenuMsgRow').show();

                            var button = $(this);

                            $.ajax({
                                url: "../controllers/updateWidget.php",
                                data: 
                                {
                                    action: "updateTimeRange",
                                    widgetName: "<?= $name_w ?>", 
                                    newTimeRange: $('#<?= $name_w ?>_header').attr('data-newTimeRange')
                                },
                                type: "POST",
                                async: true,
                                dataType: 'json',
                                success: function(data) 
                                {
                                    if(data.detail === 'Ok')
                                    {
                                        button.parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Saved&nbsp;<i class="fa fa-thumbs-up" style="font-size:14px"></i>');
                                        $('#<?= $name_w ?>_header').attr('data-currentTimeRange', $('#<?= $name_w ?>_header').attr('data-newTimeRange'));
                                        
                                        $('#<?= $name_w ?>').trigger({
                                            type: "changeTimeRangeEvent",
                                            newTimeRange: $('#<?= $name_w ?>_header').attr('data-newTimeRange')
                                        });
                                        
                                        setTimeout(function(){
                                                button.parents('div.container-fluid').find('div.contextMenuMsgRow').hide();
                                                button.parents('div.widgetSubmenu').hide();
                                                $(".fullCtxMenuRow").css('color', 'rgb(51, 64, 69)');
                                                $(".fullCtxMenuRow").css('background-color', 'transparent');
                                                $(".fullCtxMenuRow").attr("data-selected", "false");
                                        }, 750);
                                    }
                                    else
                                    {
                                        button.parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Error&nbsp;<i class="fa fa-thumbs-down" style="font-size:14px"></i>');
                                        setTimeout(function()
                                        {
                                            button.parents('div.container-fluid').find('div.contextMenuMsgRow').hide();
                                        }, 1000);
                                    }
                                },
                                error: function(errorData)
                                {
                                    button.parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Error&nbsp;<i class="fa fa-thumbs-down" style="font-size:14px"></i>');
                                    setTimeout(function()
                                    {
                                        button.parents('div.container-fluid').find('div.contextMenuMsgRow').hide();
                                    }, 1000);
                                }
                            });
                        });
                        
                    }
                    
                    if(hasChartColor === 'no')
                    {
                        $('#<?= $name_w ?>_chartColorMenuItem').remove();
                        $('#<?= $name_w ?>_chartColorSubmenu').remove();
                    }
                    else
                    {
                        $("#<?= $name_w ?>_chartColorMenuItem").off('chartCreated');
                        $("#<?= $name_w ?>_chartColorMenuItem").on('chartCreated', function(){
                            chart = $("#<?= $name_w ?>_chartContainer").highcharts();
                            
                            //Instanziamento color picker colore grafico + shortcuts + azioni
                            $('#<?= $name_w ?>_header').attr('data-currentChartColor', chartColor); 

                            $('#<?= $name_w ?>_chartColorPicker').colorpicker({
                                    horizontal: false,
                                    customClass: 'dashHeaderColorPicker',
                                    inline: true,
                                    format: "rgba",
                                    container: true,
                                    color: chartColor
                            }).on('changeColor', function(e){
                                    var newColor = $("#<?= $name_w ?>_chartColorPicker").colorpicker('getValue');
                                    chart.series[0].update({
                                        color: newColor,
                                        fillColor: {
                                            linearGradient: {
                                                x1: 0,
                                                y1: 0,
                                                x2: 0,
                                                y2: 1
                                            },
                                            stops: [
                                                [0, Highcharts.Color(newColor).setOpacity(0.5).get('rgba')],
                                                [1, Highcharts.Color(newColor).setOpacity(0).get('rgba')]
                                            ]
                                        }
                                    });
                                    
                                    //Per il gauge chart
                                    if("<?= $name_w ?>".includes("widgetGaugeChart"))
                                    {
                                        chartMin = chart.yAxis[0].min;
                                        chart.yAxis[0].stops[0] = [chartMin, newColor];
                                    }
                                    
                                    //Per lo speedometer
                                    if("<?= $name_w ?>".includes("widgetSpeedometer"))
                                    {
                                        chart.yAxis[0].removePlotBand('noThrPlotBand');
                                        var newPlotBand = {
                                            from: chart.yAxis[0].min,
                                            to: chart.yAxis[0].max,
                                            color: newColor,
                                            id: "noThrPlotBand"
                                         };
                                         
                                        chart.yAxis[0].addPlotBand(newPlotBand); 
                                    }
                                    
                                    $('#<?= $name_w ?>_header').attr('data-newChartColor', newColor);
                            });

                            $('#<?= $name_w ?>_chartColorSubmenu div.ctxMenuPaletteColor').click(function(){
                                chart.series[0].update({
                                    color: $(this).attr('data-color'),
                                    fillColor: {
                                            linearGradient: {
                                                x1: 0,
                                                y1: 0,
                                                x2: 0,
                                                y2: 1
                                            },
                                            stops: [
                                                [0, Highcharts.Color($(this).attr('data-color')).setOpacity(0.5).get('rgba')],
                                                [1, Highcharts.Color($(this).attr('data-color')).setOpacity(0).get('rgba')]
                                            ]
                                        }
                                });    
                                
                                //Per il gauge chart
                                if("<?= $name_w ?>".includes("widgetGaugeChart"))
                                {
                                    chartMin = chart.yAxis[0].min;
                                    chart.yAxis[0].stops[0] = [chartMin, $(this).attr('data-color')];
                                }

                                //Per lo speedometer 
                                if("<?= $name_w ?>".includes("widgetSpeedometer"))
                                {
                                    chart.yAxis[0].removePlotBand('noThrPlotBand');
                                        var newPlotBand = {
                                            from: chart.yAxis[0].min,
                                            to: chart.yAxis[0].max,
                                            color: $(this).attr('data-color'),
                                            id: "noThrPlotBand"
                                         };
                                         
                                    chart.yAxis[0].addPlotBand(newPlotBand);
                                }
                                    
                               $("#<?= $name_w ?>_chartColorPicker").colorpicker('setValue', $(this).attr('data-color'));
                            });

                            $('#<?= $name_w ?>_chartColorCancelBtn').click(function(){
                                    $("#<?= $name_w ?>_chartColorPicker").colorpicker('setValue', $('#<?= $name_w ?>_header').attr('data-currentChartColor'));
                                    
                                    chart.series[0].update({
                                        color: $('#<?= $name_w ?>_header').attr('data-currentChartColor'),
                                        fillColor: {
                                            linearGradient: {
                                                x1: 0,
                                                y1: 0,
                                                x2: 0,
                                                y2: 1
                                            },
                                            stops: [
                                                [0, Highcharts.Color($('#<?= $name_w ?>_header').attr('data-currentChartColor')).setOpacity(0.5).get('rgba')],
                                                [1, Highcharts.Color($('#<?= $name_w ?>_header').attr('data-currentChartColor')).setOpacity(0).get('rgba')]
                                            ]
                                        }
                                    }); 
                                    
                                    //Per il gauge chart
                                    if("<?= $name_w ?>".includes("widgetGaugeChart"))
                                    {
                                        chartMin = chart.yAxis[0].min;
                                        chart.yAxis[0].stops[0] = [chartMin, $('#<?= $name_w ?>_header').attr('data-currentChartColor')];
                                    }
                                    
                                    //Per lo speedometer
                                    if("<?= $name_w ?>".includes("widgetSpeedometer"))
                                    {
                                        chart.yAxis[0].removePlotBand('noThrPlotBand');
                                            var newPlotBand = {
                                                from: chart.yAxis[0].min,
                                                to: chart.yAxis[0].max,
                                                color: $('#<?= $name_w ?>_header').attr('data-currentChartColor'),
                                                id: "noThrPlotBand"
                                             };

                                        chart.yAxis[0].addPlotBand(newPlotBand);
                                    }
                                    
                                    $('#<?= $name_w ?>_header').attr('data-newChartColor', $('#<?= $name_w ?>_header').attr('data-currentChartColor'));
                            });

                            $('#<?= $name_w ?>_chartColorConfirmBtn').click(function(){
                                    $(this).parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Saving&nbsp;<i class="fa fa-circle-o-notch fa-spin" style="font-size:14px"></i>');
                                    $(this).parents('div.container-fluid').find('div.contextMenuMsgRow').show();

                                    var button = $(this);

                                    $.ajax({
                                            url: "../controllers/updateWidget.php",
                                            data: {
                                                action: "updateChartColor",
                                                widgetName: "<?= $name_w ?>", 
                                                newColor: $('#<?= $name_w ?>_header').attr('data-newChartColor')
                                            },
                                            type: "POST",
                                            async: true,
                                            dataType: 'json',
                                            success: function(data) 
                                            {
                                                if(data.detail === 'Ok')
                                                {
                                                    updateLastUsedColors($('#<?= $name_w ?>_header').attr('data-newChartColor'));

                                                    button.parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Saved&nbsp;<i class="fa fa-thumbs-up" style="font-size:14px"></i>');
                                                    $('#<?= $name_w ?>_header').attr('data-currentChartColor', $('#<?= $name_w ?>_header').attr('data-newChartColor'));
                                                    setTimeout(function(){
                                                        button.parents('div.container-fluid').find('div.contextMenuMsgRow').hide();    
                                                        button.parents('div.widgetSubmenu').hide();
                                                    }, 1000);
                                                }
                                                else
                                                {
                                                    button.parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Error&nbsp;<i class="fa fa-thumbs-down" style="font-size:14px"></i>');
                                                    setTimeout(function(){
                                                            button.parents('div.container-fluid').find('div.contextMenuMsgRow').hide();
                                                    }, 1000);
                                                }
                                            },
                                            error: function(errorData)
                                            {
                                                button.parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Error&nbsp;<i class="fa fa-thumbs-down" style="font-size:14px"></i>');
                                                setTimeout(function(){
                                                        button.parents('div.container-fluid').find('div.contextMenuMsgRow').hide();
                                                }, 1000);
                                            }
                                    });
                            });
                        });
                    }
                    
                    
                    //DA FINIRE - Sospeso per passare ad instanziatore automatico widgets
                    /*if(hasDataLabels === 'no')
                    {
                        $('#<?= $name_w ?>_dataLabelsFontSizeMenuItem').remove();
                        $('#<?= $name_w ?>_dataLabelsFontSizeSubmenu').remove();
                    }
                    else
                    {
                        $('#<?= $name_w ?>_dataLabelsFontSize').val(dataLabelsFontSize);
                        
                        $("#<?= $name_w ?>_chartColorMenuItem").off('chartCreated');
                        $("#<?= $name_w ?>_chartColorMenuItem").on('chartCreated', function(){
                            chart = $("#<?= $name_w ?>_chartContainer").highcharts();
                            
                            $('#<?= $name_w ?>_dataLabelsFontSizeMinus').off('click');
                            $('#<?= $name_w ?>_dataLabelsFontSizeMinus').on('click', function(){
                                if(parseInt($('#<?= $name_w ?>_dataLabelsFontSize').val()) > 0)
                                {
                                    $('#<?= $name_w ?>_dataLabelsFontSize').val(parseInt($('#<?= $name_w ?>_dataLabelsFontSize').val()) - 1);

                                    $.ajax({
                                        url: "../controllers/updateWidget.php",
                                        data: {
                                            action: "updateDataLabelsFontSize",
                                            widgetName: "<?= $name_w ?>", 
                                            newSize: $('#<?= $name_w ?>_dataLabelsFontSize').val()
                                        },
                                        type: "POST",
                                        async: true,
                                        dataType: 'json',
                                        success: function(data) 
                                        {
                                            console.log("chart.plotOptions: " + chart.plotOptions); 
                                        },
                                        error: function(errorData)
                                        {

                                        }
                                    });
                                }
                            });

                            $('#<?= $name_w ?>_dataLabelsFontSizePlus').off('click');
                            $('#<?= $name_w ?>_dataLabelsFontSizePlus').on('click', function(){
                                $('#<?= $name_w ?>_dataLabelsFontSize').val(parseInt($('#<?= $name_w ?>_dataLabelsFontSize').val()) + 1);

                                $.ajax({
                                    url: "../controllers/updateWidget.php",
                                    data: {
                                        action: "updateDataLabelsFontSize",
                                        widgetName: "<?= $name_w ?>", 
                                        newSize: $('#<?= $name_w ?>_dataLabelsFontSize').val()
                                    },
                                    type: "POST",
                                    async: true,
                                    dataType: 'json',
                                    success: function(data) 
                                    {

                                    },
                                    error: function(errorData)
                                    {

                                    }
                                });
                            });

                            $('#<?= $name_w ?>_dataLabelsFontSize').off('input');
                            $('#<?= $name_w ?>_dataLabelsFontSize').on('input',function(e)
                            {
                                var reg = new RegExp('^\\d+$');

                                if($(this).val().trim() !== '')
                                {
                                    if(reg.test($(this).val()))
                                    {
                                        if(parseInt($(this).val()) >= 0)
                                        {
                                            $.ajax({
                                                url: "../controllers/updateWidget.php",
                                                data: {
                                                    action: "updateDataLabelsFontSize",
                                                    widgetName: "<?= $name_w ?>", 
                                                    newSize: $('#<?= $name_w ?>_dataLabelsFontSize').val()
                                                },
                                                type: "POST",
                                                async: true,
                                                dataType: 'json',
                                                success: function(data) 
                                                {

                                                },
                                                error: function(errorData)
                                                {

                                                }
                                            });
                                        }
                                    }
                                }
                            });
                            
                        });
                        
                        
                        
                    }*/
                    
                    //Click su bottone quit
                    $('#<?= $name_w ?>_widgetCtxMenu .quitRow').off('click');
                    $('#<?= $name_w ?>_widgetCtxMenu .quitRow').click(function(){
                        $('#<?= $name_w ?>_widgetCtxMenu .widgetSubmenu').hide();
                        $('#<?= $name_w ?>_widgetCtxMenu').hide();
                        $('#<?= $name_w ?>_widgetCtxMenu .widgetSubmenu').each(function(i){
                           $(this).attr('data-clicked', 'false');	
                        });

                        $('#<?= $name_w ?>_widgetCtxMenu').attr('data-shown', false);
                        
                        $(".fullCtxMenuRow").css('color', 'rgb(51, 64, 69)');
                        $(".fullCtxMenuRow").css('background-color', 'transparent');
                        $(".fullCtxMenuRow").attr("data-selected", "false");
                    });
                    
                    //Click su bottone quit di qualsiasi submenu
                    $('#<?= $name_w ?>_widgetCtxMenu .contextMenuQuitBtn').off('click');
                    $('#<?= $name_w ?>_widgetCtxMenu .contextMenuQuitBtn').click(function(){
                        $('#<?= $name_w ?>_widgetCtxMenu .widgetSubmenu').hide();
                        $('#<?= $name_w ?>_widgetCtxMenu').hide();
                        $('#<?= $name_w ?>_widgetCtxMenu .widgetSubmenu').each(function(i){
                           $(this).attr('data-clicked', 'false');	
                        });

                        $('#<?= $name_w ?>_widgetCtxMenu').attr('data-shown', false);
                        
                        $(".fullCtxMenuRow").css('color', 'rgb(51, 64, 69)');
                        $(".fullCtxMenuRow").css('background-color', 'transparent');
                        $(".fullCtxMenuRow").attr("data-selected", "false");
                    });

                    //Click su bottone menu di contesto
                    $('#<?= $name_w ?>_widgetCtxMenuBtn').off('click');
                    $('#<?= $name_w ?>_widgetCtxMenuBtn').click(function()
                    {
                        $('.applicationCtxMenu').hide();
                        $('#fullscreenBtnContainer .fullCtxSubmenu').hide();
                        $('#dashboardEditHeaderMenu').hide();
                        
                        //Ripristino eventuale titolo dashboard lasciato a mezzo
                        if($("#dashboardTitle span").html().trim() === '')
                        {
                            $("#dashboardSubtitle span").html('No subtitle');
                        }
                        else
                        {
                            $("#dashboardTitle span").html($("#dashboardTitle").attr('data-currentTitle'));
                        }

                        $("#dashboardTitle span").attr('data-underEdit', 'false');
                        $("#dashboardTitle span").attr('contenteditable', false);

                        //Ripristino eventuale sottotitolo dashboard lasciato a mezzo
                        if($("#dashboardSubtitle span").html().trim() === '')
                        {
                            $("#dashboardSubtitle span").html('No subtitle');
                        }
                        else
                        {
                            $("#dashboardSubtitle span").html($("#dashboardSubtitle").attr('data-currentSubtitle'));
                        }

                        $("#dashboardSubtitle span").attr('data-underEdit', 'false');
                        $("#dashboardSubtitle span").attr('contenteditable', false);

                        //Ripristino eventuali titoli widgets
                        $('div.titleDiv').each(function(i)
                        {
                            $(this).attr("contenteditable", false);
                            $(this).attr("data-underEdit", false);
                            $(this).html($(this).attr('data-currentTitle'));
                        });

                        $('.fullCtxMenu').each(function(i){
                            if($(this).attr('id') !== '<?= $name_w ?>_widgetCtxMenu')
                            {
                                $(this).attr('data-shown', 'false');
                                $(this).find('.widgetSubmenu').each(function(){
                                    $(this).attr('data-clicked', 'false');	
                                    $(this).hide();

                                });
                                $(this).hide();
                            }
                        });

                        if($('#<?= $name_w ?>_widgetCtxMenu').attr('data-shown') === 'true')
                        {
                            $('#<?= $name_w ?>_widgetCtxMenu').attr('data-shown', 'false');
                            $('#<?= $name_w ?>_widgetCtxMenu .widgetSubmenu').hide();
                            $('#<?= $name_w ?>_widgetCtxMenu').hide();
                            $('#<?= $name_w ?>_widgetCtxMenu .widgetSubmenu').each(function(i){
                               $(this).attr('data-clicked', 'false');	
                            });
                        }
                        else
                        {
                            $('#<?= $name_w ?>_widgetCtxMenu').attr('data-shown', 'true');
                            var widgetDistanceFromRightScreen = parseInt($(window).width() + $(document).scrollLeft() - $('#<?= $name_w ?>_widgetCtxMenuBtnCnt').offset().left);	
                            if($('#<?= $name_w ?>_widgetCtxMenu').width() > widgetDistanceFromRightScreen)
                            {
                                $('#<?= $name_w ?>_widgetCtxMenu').css('left', parseInt($('#<?= $name_w ?>_widgetCtxMenuBtnCnt').offset().left - $('#<?= $name_w ?>_widgetCtxMenu').width() + 12) + 'px');
                                $('#<?= $name_w ?>_widgetCtxMenu').attr('data-side', 'left');
                            }
                            else
                            {
                                $('#<?= $name_w ?>_widgetCtxMenu').css('left', parseInt( $('#<?= $name_w ?>_widgetCtxMenuBtnCnt').offset().left) + 'px');
                                $('#<?= $name_w ?>_widgetCtxMenu').attr('data-side', 'right');
                            }
                            $('#<?= $name_w ?>_widgetCtxMenu').css('top', parseInt($('#<?= $name_w ?>_widgetCtxMenuBtn').offset().top + 25) + 'px');
                            $('#<?= $name_w ?>_widgetCtxMenu').show();
                        }
                    });
                    
                    //Mostra/nascondi header
                    $('#<?= $name_w ?>_widgetCtxMenu .headerVisibility').off('click');
                    $('#<?= $name_w ?>_widgetCtxMenu .headerVisibility').click(function(){
                        var contentHeight, showTitle, showHeader = null;
                        if($('#<?= $name_w ?>_header').is(':visible'))
                        {
                            $('#<?= $name_w ?>_header').hide();
                            $('#<?= $name_w ?>_widgetCtxMenu .headerVisibility .fullCtxMenuIcon i').removeClass('fa-eye-slash');
                            $('#<?= $name_w ?>_widgetCtxMenu .headerVisibility .fullCtxMenuIcon i').addClass('fa-eye');
                            $('#<?= $name_w ?>_widgetCtxMenu .headerVisibility .fullCtxMenuTxt').html('Show header');
                            contentHeight = parseInt($("#<?= $name_w ?>_div").prop("offsetHeight"));
                            showTitle = 'no';
                            showHeader = false;
                            $('#<?= $name_w ?>_widgetCtxMenuBtnCnt').css('background-color', 'rgba(51, 64, 69, 0.7)');
                            $('#<?= $name_w ?>_widgetCtxMenuBtn').css('color', 'white');
                        }
                        else
                        {
                            $('#<?= $name_w ?>_header').show();
                            $('#<?= $name_w ?>_widgetCtxMenu .headerVisibility .fullCtxMenuIcon i').removeClass('fa-eye');
                            $('#<?= $name_w ?>_widgetCtxMenu .headerVisibility .fullCtxMenuIcon i').addClass('fa-eye-slash');
                            $('#<?= $name_w ?>_widgetCtxMenu .headerVisibility .fullCtxMenuTxt').html('Hide header');
                            contentHeight = parseInt($("#<?= $name_w ?>_div").prop("offsetHeight") - $("#<?= $name_w ?>_header").prop("offsetHeight"));
                            showTitle = 'yes';
                            showHeader = true;
                            $('#<?= $name_w ?>_widgetCtxMenuBtnCnt').css('background-color', $('#<?= $name_w ?>_header').css('background-color'));
                            $('#<?= $name_w ?>_widgetCtxMenuBtn').css('color', $('#<?= $name_w ?>_header').css('color'));
                        }

                        $("#<?= $name_w ?>_content").css("height", contentHeight + 'px');

                        //Innesco di evento cambio altezza diagramma per gli widgets Highcharts
                        $.event.trigger({
                           type: "resizeHighchart_<?= $name_w ?>",
                           showHeader: showHeader
                        }); 

                            //Update parametro su DB
                            $.ajax({
                                url: "../controllers/updateWidget.php",
                                data: {
                                        action: "updateTitleVisibility",
                                        widgetName: "<?= $name_w ?>", 
                                        showTitle: showTitle
                                },
                                type: "POST",
                                async: true,
                                dataType: 'json',
                                success: function(data) 
                                {
                                        if(data.detail !== 'Ok')
                                        {
                                                if($('#<?= $name_w ?>_header').is(':visible'))
                                                {
                                                        $('#<?= $name_w ?>_header').hide();
                                                        $('#<?= $name_w ?>_widgetCtxMenu .headerVisibility .fullCtxMenuIcon i').removeClass('fa-eye-slash');
                                                        $('#<?= $name_w ?>_widgetCtxMenu .headerVisibility .fullCtxMenuIcon i').addClass('fa-eye');
                                                        $('#<?= $name_w ?>_widgetCtxMenu .headerVisibility .fullCtxMenuTxt').html('Show header');
                                                        contentHeight = parseInt($("#<?= $name_w ?>_div").prop("offsetHeight"));
                                                }
                                                else
                                                {
                                                        $('#<?= $name_w ?>_header').show();
                                                        $('#<?= $name_w ?>_widgetCtxMenu .headerVisibility .fullCtxMenuIcon i').removeClass('fa-eye');
                                                        $('#<?= $name_w ?>_widgetCtxMenu .headerVisibility .fullCtxMenuIcon i').addClass('fa-eye-slash');
                                                        $('#<?= $name_w ?>_widgetCtxMenu .headerVisibility .fullCtxMenuTxt').html('Hide header');
                                                        contentHeight = parseInt($("#<?= $name_w ?>_div").prop("offsetHeight") - $("#<?= $name_w ?>_header").prop("offsetHeight"));
                                                }

                                                $("#<?= $name_w ?>_content").css("height", contentHeight + 'px');
                                        }
                                },
                                    error: function(errorData)
                                    {
                                        if($('#<?= $name_w ?>_header').is(':visible'))
                                        {
                                                $('#<?= $name_w ?>_header').hide();
                                                $('#<?= $name_w ?>_widgetCtxMenu .headerVisibility .fullCtxMenuIcon i').removeClass('fa-eye-slash');
                                                $('#<?= $name_w ?>_widgetCtxMenu .headerVisibility .fullCtxMenuIcon i').addClass('fa-eye');
                                                $('#<?= $name_w ?>_widgetCtxMenu .headerVisibility .fullCtxMenuTxt').html('Show header');
                                                contentHeight = parseInt($("#<?= $name_w ?>_div").prop("offsetHeight"));
                                        }
                                        else
                                        {
                                                $('#<?= $name_w ?>_header').show();
                                                $('#<?= $name_w ?>_widgetCtxMenu .headerVisibility .fullCtxMenuIcon i').removeClass('fa-eye');
                                                $('#<?= $name_w ?>_widgetCtxMenu .headerVisibility .fullCtxMenuIcon i').addClass('fa-eye-slash');
                                                $('#<?= $name_w ?>_widgetCtxMenu .headerVisibility .fullCtxMenuTxt').html('Hide header');
                                                contentHeight = parseInt($("#<?= $name_w ?>_div").prop("offsetHeight") - $("#<?= $name_w ?>_header").prop("offsetHeight"));
                                        }
                                    }
                            });
                    });
                    
                    //Apertura di submenu (qualsiasi)
                    $("#<?= $name_w ?>_widgetCtxMenu .hasSubmenu").click(function()
                    {
                        //Calcolo dell'indice della voce di menu
                        var menuItemIndex = -1;
                        
                        $("#<?= $name_w ?>_widgetCtxMenu .fullCtxMenuRow").each(function(){
                            menuItemIndex++;
                        });
                        
                        var submenuId = $(this).attr('data-boundTo');

                        $('#<?= $name_w ?>_widgetCtxMenu .widgetSubmenu').each(function(i){
                            if($(this).attr('id') !== submenuId)
                            {
                                $(this).hide();
                            }
                        });
                        
                        $("#<?= $name_w ?>_widgetCtxMenu .hasSubmenu").css('color', 'rgb(51, 64, 69)');
                        $("#<?= $name_w ?>_widgetCtxMenu .hasSubmenu").css('background-color', 'transparent');
                        $("#<?= $name_w ?>_widgetCtxMenu .hasSubmenu").attr("data-selected", "false");

                        var widgetDistanceFromRightScreen = null;

                        if($('#' + submenuId).is(':visible'))
                        {
                            $('#' + submenuId).hide();
                        }
                        else
                        {
                            $(this).css('color', 'white');
                            $(this).css('background-color', 'rgba(0, 162, 211, 1)');
                            $(this).attr("data-selected", "true");
                            
                            $('#' + submenuId).css('top', parseInt(menuItemIndex*$(this).height())); 

                            if($("#<?= $name_w ?>_widgetCtxMenu").attr('data-side') === 'left')
                            {
                                //Menu principale sulla sinistra, submenu sulla sinistra
                                if($('#' + submenuId).attr('data-clicked') === 'false')
                                {
                                    $('#' + submenuId).css('left', '-' + parseInt($('#' + submenuId).outerWidth() + 2) + 'px');
                                }
                                else
                                {
                                    $('#' + submenuId).css('left', '-' + parseInt($('#' + submenuId).outerWidth() + 2) + 'px');	
                                }
                            }
                            else
                            {
                                widgetDistanceFromRightScreen = parseInt($(window).width() + $(document).scrollLeft() - $('#<?= $name_w ?>_widgetCtxMenuBtnCnt').offset().left - $('#<?= $name_w ?>_widgetCtxMenu').width());	
                                if($('#' + submenuId).width() > widgetDistanceFromRightScreen)
                                {
                                    //Menu principale sulla destra, submenu sulla sinistra
                                    if($('#' + submenuId).attr('data-clicked') === 'false')
                                    {
                                        $('#' + submenuId).css('left', '-' + parseInt($('#' + submenuId).outerWidth() + 2) + 'px');
                                    }
                                    else
                                    {
                                        $('#' + submenuId).css('left', '-' + parseInt($('#' + submenuId).outerWidth() + 2) + 'px');	
                                    }
                                }
                                else
                                {
                                    //Menu principale sulla destra, spazio ulteriore per submenu sulla destra
                                    $('#' + submenuId).css('left', $('#<?= $name_w ?>_widgetCtxMenu').outerWidth() + 'px');
                                }
                            }
                                
                            //Se l'elemento di menu è quello del change metric, spostiamo l'unica istanza della tabella nel sottomenu da aprire
                            
                            if($(this).hasClass('changeMetricRow'))
                            {

                                var name_widget_m = $(this).parents('div.widgetCtxMenu').attr('data-widgetName');
                                //var widgetId = $(this).parents('li').attr('data-widgetId');
                                var widgetId = $('li[id=' + name_widget_m + ']').attr('data-widgetId');

                                $('#<?= $name_w ?>_changeMetricSubmenu').empty();
                                $("#changeMetricCnt").appendTo("#<?= $name_w ?>_changeMetricSubmenu");
                                $("#changeMetricCnt").show();
                                
                                $.event.trigger({
                                    type: "changeMetricMenuOpen",
                                    generator: "<?= $name_w ?>",
                                });
                            }
                            
                            $('#' + submenuId).show(); 	
                            
                        }

                        if($('#' + submenuId).attr('data-clicked') === 'false')
                        {
                            $('#' + submenuId).attr('data-clicked', 'true');
                        }
                    });
                    
                    //Main
                    $('body').prepend($('#<?= $name_w ?>_widgetCtxMenu')); 

                    if($('#<?= $name_w ?>_header').is(':visible'))
                    {
                            $('#<?= $name_w ?>_widgetCtxMenuBtnCnt').css('background-color', '<?= $frame_color_w1 ?>');
                            $('#<?= $name_w ?>_widgetCtxMenuBtn').css('color', '<?= $headerFontColor1 ?>');
                    }
                    else
                    {
                            $('#<?= $name_w ?>_widgetCtxMenuBtnCnt').css('background-color', 'rgba(51, 64, 69, 0.7)');
                            $('#<?= $name_w ?>_widgetCtxMenuBtn').css('color', 'white');
                    }

                    $('#<?= $name_w ?>_widgetCtxMenu .ctxMenuPaletteColor').each(function(i){
                            $(this).css('background-color', $(this).attr('data-color'));
                    });	

                    var widgetDistanceFromRightScreen = parseInt($(window).width() + $(document).scrollLeft() - $('#<?= $name_w ?>_widgetCtxMenuBtnCnt').offset().left);	
                    if($('#<?= $name_w ?>_widgetCtxMenu').width() > widgetDistanceFromRightScreen)
                    {
                            $('#<?= $name_w ?>_widgetCtxMenu').css('left', parseInt($('#<?= $name_w ?>_widgetCtxMenuBtnCnt').offset().left - $('#<?= $name_w ?>_widgetCtxMenu').width() + 12) + 'px');
                            $('#<?= $name_w ?>_widgetCtxMenu').attr('data-side', 'left');
                    }
                    else
                    {
                            $('#<?= $name_w ?>_widgetCtxMenu').css('left', parseInt( $('#<?= $name_w ?>_widgetCtxMenuBtnCnt').offset().left) + 'px');
                            $('#<?= $name_w ?>_widgetCtxMenu').attr('data-side', 'right');
                    }
                    $('#<?= $name_w ?>_widgetCtxMenu').css('top', parseInt($('#<?= $name_w ?>_widgetCtxMenuBtn').offset().top + 25) + 'px');

                    if($('#<?= $name_w ?>_header').is(':visible'))
                    {
                            $('#<?= $name_w ?>_widgetCtxMenuBtnCnt').css('background-color', $('#<?= $name_w ?>_header').css('background-color'));
                            $('#<?= $name_w ?>_widgetCtxMenuBtnCnt').css('color', $('#<?= $name_w ?>_header').css('color'));
                            $('#<?= $name_w ?>_widgetCtxMenu .headerVisibility .fullCtxMenuIcon i').addClass('fa-eye-slash');
                            $('#<?= $name_w ?>_widgetCtxMenu .headerVisibility .fullCtxMenuTxt').html('Hide header');
                    }
                    else
                    {
                            $('#<?= $name_w ?>_widgetCtxMenuBtnCnt').css('background-color', 'rgba(51, 64, 69, 0.7)');
                            $('#<?= $name_w ?>_widgetCtxMenuBtnCnt').css('color', 'white');
                            $('#<?= $name_w ?>_widgetCtxMenu .headerVisibility .fullCtxMenuIcon i').addClass('fa-eye');
                            $('#<?= $name_w ?>_widgetCtxMenu .headerVisibility .fullCtxMenuTxt').html('Show header');
                    }

                    //Effetto hover su righe menu
                    $('#<?= $name_w ?>_widgetCtxMenu .fullCtxMenuRow').hover(function(){
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
                    }); 
                    
                    //Instanziamento color picker colore header + shortcuts + azioni
                    $('#<?= $name_w ?>_header').attr('data-currentColor', $('#<?= $name_w ?>_header').css('background-color')); 

                    $('#<?= $name_w ?>_headerColorPicker').colorpicker({
                            horizontal: false,
                            customClass: 'dashHeaderColorPicker',
                            inline: true,
                            format: "rgba",
                            container: true,
                            color: $('#<?= $name_w ?>_header').css('background-color')
                    }).on('changeColor', function(e){
                            var newColor = $("#<?= $name_w ?>_headerColorPicker").colorpicker('getValue');
                            $('#<?= $name_w ?>_header').css('background-color', newColor);
                            $('#<?= $name_w ?>_widgetCtxMenuBtnCnt').css('background-color', newColor);
                            $('#<?= $name_w ?>_header').attr('data-newColor', newColor);
                    });

                    $('#<?= $name_w ?>_headerColorSubmenu div.ctxMenuPaletteColor').click(function(){
                            $('#<?= $name_w ?>_header').css('background-color', $(this).attr('data-color'));
                            $('#<?= $name_w ?>_widgetCtxMenuBtnCnt').css('background-color', $(this).attr('data-color'));
                            $('#<?= $name_w ?>_header').attr('data-newColor', $(this).attr('data-color'));
                            $("#<?= $name_w ?>_headerColorPicker").colorpicker('setValue', $(this).attr('data-color'));
                    });

                    $('#<?= $name_w ?>_headerColorCancelBtn').click(function(){
                            $("#<?= $name_w ?>_headerColorPicker").colorpicker('setValue', $('#<?= $name_w ?>_header').attr('data-currentColor'));
                            $('#<?= $name_w ?>_header').css('background-color', $('#<?= $name_w ?>_header').attr('data-currentColor'));
                            $('#<?= $name_w ?>_widgetCtxMenuBtnCnt').css('background-color', $('#<?= $name_w ?>_header').attr('data-currentColor'));
                            $('#<?= $name_w ?>_header').attr('data-newColor', $('#<?= $name_w ?>_header').attr('data-currentColor'));
                    });

                    $('#<?= $name_w ?>_headerColorConfirmBtn').click(function(){
                            $(this).parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Saving&nbsp;<i class="fa fa-circle-o-notch fa-spin" style="font-size:14px"></i>');
                            $(this).parents('div.container-fluid').find('div.contextMenuMsgRow').show();

                            var button = $(this);

                            $.ajax({
                                    url: "../controllers/updateWidget.php",
                                    data: {
                                        action: "updateHeaderColor",
                                        widgetName: "<?= $name_w ?>", 
                                        newColor: $('#<?= $name_w ?>_header').attr('data-newColor')
                                    },
                                    type: "POST",
                                    async: true,
                                    dataType: 'json',
                                    success: function(data) 
                                    {
                                            if(data.detail === 'Ok')
                                            {
                                                updateLastUsedColors($('#<?= $name_w ?>_header').attr('data-newColor'));

                                                button.parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Saved&nbsp;<i class="fa fa-thumbs-up" style="font-size:14px"></i>');
                                                $('#<?= $name_w ?>_header').attr('data-currentColor', $('#<?= $name_w ?>_header').attr('data-newColor'));
                                                setTimeout(function(){
                                                    button.parents('div.container-fluid').find('div.contextMenuMsgRow').hide();    
                                                    button.parents('div.widgetSubmenu').hide();
                                                    $(".fullCtxMenuRow").css('color', 'rgb(51, 64, 69)');
                                                    $(".fullCtxMenuRow").css('background-color', 'transparent');
                                                    $(".fullCtxMenuRow").attr("data-selected", "false");
                                                }, 750);
                                            }
                                            else
                                            {
                                                button.parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Error&nbsp;<i class="fa fa-thumbs-down" style="font-size:14px"></i>');
                                                setTimeout(function(){
                                                        button.parents('div.container-fluid').find('div.contextMenuMsgRow').hide();
                                                }, 1000);
                                            }
                                    },
                                    error: function(errorData)
                                    {
                                            button.parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Error&nbsp;<i class="fa fa-thumbs-down" style="font-size:14px"></i>');
                                            setTimeout(function(){
                                                    button.parents('div.container-fluid').find('div.contextMenuMsgRow').hide();
                                            }, 1000);
                                    }
                            });
                    });
                    
                    //Instanziamento color picker colore font titolo + shortcuts + azioni
                    $('#<?= $name_w ?>_header').attr('data-currentTitleColor', $('#<?= $name_w ?>_header').css('color')); 

                    $('#<?= $name_w ?>_titleColorPicker').colorpicker({
                            horizontal: false,
                            customClass: 'dashHeaderColorPicker',
                            inline: true,
                            format: "rgba",
                            container: true,
                            color: $('#<?= $name_w ?>_header').css('color')
                    }).on('changeColor', function(e){
                            var newColor = $("#<?= $name_w ?>_titleColorPicker").colorpicker('getValue');
                            $('#<?= $name_w ?>_titleDiv').css('color', newColor);
                            $('#<?= $name_w ?> .info_source').css('color', newColor);
                            $('#<?= $name_w ?>_countdownDiv').css('color', newColor);
                            $('#<?= $name_w ?>_countdownDiv').css('border-color', newColor);
                            $('#<?= $name_w ?>_widgetCtxMenuBtn').css('color', newColor);
                            $('#<?= $name_w ?>_header').attr('data-newTitleColor', newColor);
                    });

                    $('#<?= $name_w ?>_titleColorSubmenu div.ctxMenuPaletteColor').click(function(){
                            $('#<?= $name_w ?>_titleDiv').css('color', $(this).attr('data-color'));
                            $('#<?= $name_w ?> .info_source').css('color', $(this).attr('data-color'));
                            $('#<?= $name_w ?>_countdownDiv').css('color', $(this).attr('data-color'));
                            $('#<?= $name_w ?>_countdownDiv').css('border-color', $(this).attr('data-color'));
                            $('#<?= $name_w ?>_widgetCtxMenuBtn').css('color', $(this).attr('data-color'));
                            $('#<?= $name_w ?>_header').attr('data-newTitleColor', $(this).attr('data-color'));
                            $("#<?= $name_w ?>_titleColorPicker").colorpicker('setValue', $(this).attr('data-color'));
                    });

                    $('#<?= $name_w ?>_titleColorCancelBtn').click(function(){
                            $("#<?= $name_w ?>_titleColorPicker").colorpicker('setValue', $('#<?= $name_w ?>_header').attr('data-currentTitleColor'));
                            $('#<?= $name_w ?>_titleDiv').css('color', $('#<?= $name_w ?>_header').attr('data-currentTitleColor'));
                            $('#<?= $name_w ?> .info_source').css('color', $('#<?= $name_w ?>_header').attr('data-currentTitleColor'));
                            $('#<?= $name_w ?>_countdownDiv').css('color', $('#<?= $name_w ?>_header').attr('data-currentTitleColor'));
                            $('#<?= $name_w ?>_countdownDiv').css('border-color', $('#<?= $name_w ?>_header').attr('data-currentTitleColor'));
                            $('#<?= $name_w ?>_widgetCtxMenuBtn').css('color', $('#<?= $name_w ?>_header').attr('data-currentTitleColor'));
                            $('#<?= $name_w ?>_header').attr('data-newTitleColor', $('#<?= $name_w ?>_header').attr('data-currentTitleColor'));
                    });

                    $('#<?= $name_w ?>_titleColorConfirmBtn').click(function(){
                            $(this).parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Saving&nbsp;<i class="fa fa-circle-o-notch fa-spin" style="font-size:14px"></i>');
                            $(this).parents('div.container-fluid').find('div.contextMenuMsgRow').show();

                            var button = $(this);

                            $.ajax({
                                    url: "../controllers/updateWidget.php",
                                    data: {
                                            action: "updateTitleColor",
                                            widgetName: "<?= $name_w ?>", 
                                            newColor: $('#<?= $name_w ?>_header').attr('data-newTitleColor')
                                    },
                                    type: "POST",
                                    async: true,
                                    dataType: 'json',
                                    success: function(data) 
                                    {
                                            if(data.detail === 'Ok')
                                            {
                                                updateLastUsedColors($('#<?= $name_w ?>_header').attr('data-newTitleColor'));
                                                    button.parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Saved&nbsp;<i class="fa fa-thumbs-up" style="font-size:14px"></i>');
                                                    $('#<?= $name_w ?>_header').attr('data-currentTitleColor', $('#<?= $name_w ?>_header').attr('data-newTitleColor'));
                                                    setTimeout(function(){
                                                        button.parents('div.container-fluid').find('div.contextMenuMsgRow').hide();    
                                                        button.parents('div.widgetSubmenu').hide();
                                                        $(".fullCtxMenuRow").css('color', 'rgb(51, 64, 69)');
                                                        $(".fullCtxMenuRow").css('background-color', 'transparent');
                                                        $(".fullCtxMenuRow").attr("data-selected", "false");
                                                    }, 750);
                                            }
                                            else
                                            {
                                                    button.parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Error&nbsp;<i class="fa fa-thumbs-down" style="font-size:14px"></i>');
                                                    setTimeout(function(){
                                                            button.parents('div.container-fluid').find('div.contextMenuMsgRow').hide();
                                                    }, 1000);
                                            }
                                    },
                                    error: function(errorData)
                                    {
                                            button.parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Error&nbsp;<i class="fa fa-thumbs-down" style="font-size:14px"></i>');
                                            setTimeout(function(){
                                                    button.parents('div.container-fluid').find('div.contextMenuMsgRow').hide();
                                            }, 1000);
                                    }
                            });
                    });
                    
                    //Instanziamento color picker colore background + shortcuts + azioni
                    $('#<?= $name_w ?>_header').attr('data-currentBackgroundColor', $('#<?= $name_w ?>_content').css('background-color'));

                    $('#<?= $name_w ?>_backgroundColorPicker').colorpicker({
                            horizontal: false,
                            customClass: 'dashHeaderColorPicker',
                            inline: true,
                            format: "rgba",
                            container: true,
                            color: $('#<?= $name_w ?>_content').css('background-color')
                    }).on('changeColor', function(e){
                            var newColor = $("#<?= $name_w ?>_backgroundColorPicker").colorpicker('getValue');
                            $('#<?= $name_w ?>_content').css('background-color', newColor);
                            $('#<?= $name_w ?>_header').attr('data-newBackgroundColor', newColor);
                    });

                    $('#<?= $name_w ?>_backgroundColorSubmenu div.ctxMenuPaletteColor').click(function(){
                            $('#<?= $name_w ?>_content').css('background-color', $(this).attr('data-color'));
                            $('#<?= $name_w ?>_header').attr('data-newBackgroundColor', $(this).attr('data-color'));
                            $("#<?= $name_w ?>_backgroundColorPicker").colorpicker('setValue', $(this).attr('data-color'));
                    });

                    $('#<?= $name_w ?>_backgroundColorCancelBtn').click(function(){
                            $("#<?= $name_w ?>_backgroundColorPicker").colorpicker('setValue', $('#<?= $name_w ?>_header').attr('data-currentBackgroundColor'));
                            $('#<?= $name_w ?>_content').css('background-color', $('#<?= $name_w ?>_header').attr('data-currentBackgroundColor'));
                            $('#<?= $name_w ?>_header').attr('data-newBackgroundColor', $('#<?= $name_w ?>_header').attr('data-currentBackgroundColor'));
                    });

                    $('#<?= $name_w ?>_backgroundColorConfirmBtn').click(function(){
                            $(this).parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Saving&nbsp;<i class="fa fa-circle-o-notch fa-spin" style="font-size:14px"></i>');
                            $(this).parents('div.container-fluid').find('div.contextMenuMsgRow').show();

                            var button = $(this);

                            $.ajax({
                                    url: "../controllers/updateWidget.php",
                                    data: {
                                            action: "updateBackgroundColor",
                                            widgetName: "<?= $name_w ?>", 
                                            newColor: $('#<?= $name_w ?>_header').attr('data-newBackgroundColor')
                                    },
                                    type: "POST",
                                    async: true,
                                    dataType: 'json',
                                    success: function(data) 
                                    {
                                            if(data.detail === 'Ok')
                                            {
                                                updateLastUsedColors($('#<?= $name_w ?>_header').attr('data-newBackgroundColor'));
                                                    button.parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Saved&nbsp;<i class="fa fa-thumbs-up" style="font-size:14px"></i>');
                                                    $('#<?= $name_w ?>_header').attr('data-currentBackgroundColor', $('#<?= $name_w ?>_header').attr('data-newBackgroundColor'));
                                                    setTimeout(function(){
                                                        button.parents('div.container-fluid').find('div.contextMenuMsgRow').hide();    
                                                        button.parents('div.widgetSubmenu').hide();
                                                        $(".fullCtxMenuRow").css('color', 'rgb(51, 64, 69)');
                                                        $(".fullCtxMenuRow").css('background-color', 'transparent');
                                                        $(".fullCtxMenuRow").attr("data-selected", "false");
                                                    }, 750);
                                            }
                                            else
                                            {
                                                    button.parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Error&nbsp;<i class="fa fa-thumbs-down" style="font-size:14px"></i>');
                                                    setTimeout(function(){
                                                            button.parents('div.container-fluid').find('div.contextMenuMsgRow').hide();
                                                    }, 1000);
                                            }
                                    },
                                    error: function(errorData)
                                    {
                                            button.parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Error&nbsp;<i class="fa fa-thumbs-down" style="font-size:14px"></i>');
                                            setTimeout(function(){
                                                    button.parents('div.container-fluid').find('div.contextMenuMsgRow').hide();
                                            }, 1000);
                                    }
                            });
                    });
                    
                    //Instanziamento color picker colore bordo + shortcuts + azioni
                    $('#<?= $name_w ?>_header').attr('data-currentBorderColor', $('#<?= $name_w ?>').css('border-color')); 

                    $('#<?= $name_w ?>_borderColorPicker').colorpicker({
                            horizontal: false,
                            customClass: 'dashHeaderColorPicker',
                            inline: true,
                            format: "rgba",
                            container: true,
                            color: borderColor
                    }).on('changeColor', function(e){
                            var newColor = $("#<?= $name_w ?>_borderColorPicker").colorpicker('getValue');
                            $('#<?= $name_w ?>').css('border', '1px solid ' + newColor);
                            $('#<?= $name_w ?>_header').attr('data-newBorderColor', newColor);
                    });

                    $('#<?= $name_w ?>_borderSubmenu div.ctxMenuPaletteColor').click(function(){
                        $('#<?= $name_w ?>').css('border', '1px solid ' + $(this).attr('data-color'));
                        $('#<?= $name_w ?>_header').attr('data-newBorderColor', $(this).attr('data-color'));
                        $("#<?= $name_w ?>_borderColorPicker").colorpicker('setValue', $(this).attr('data-color'));
                    });

                    $('#<?= $name_w ?>_borderCancelBtn').click(function(){
                        $("#<?= $name_w ?>_borderColorPicker").colorpicker('setValue', $('#<?= $name_w ?>_header').attr('data-currentBorderColor'));
                        $('#<?= $name_w ?>').css('border', '1px solid ' + $('#<?= $name_w ?>_header').attr('data-currentBorderColor'));
                        $('#<?= $name_w ?>_header').attr('data-newBorderColor', $('#<?= $name_w ?>_header').attr('data-currentBorderColor'));
                    });
                    
                    $('#<?= $name_w ?>_borderConfirmBtn').click(function(){
                        $(this).parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Saving&nbsp;<i class="fa fa-circle-o-notch fa-spin" style="font-size:14px"></i>');
                        $(this).parents('div.container-fluid').find('div.contextMenuMsgRow').show();

                        var button = $(this);

                        $.ajax({
                                url: "../controllers/updateWidget.php",
                                data: {
                                        action: "updateBorderColor",
                                        widgetName: "<?= $name_w ?>", 
                                        newColor: $('#<?= $name_w ?>_header').attr('data-newBorderColor')
                                },
                                type: "POST",
                                async: true,
                                dataType: 'json',
                                success: function(data) 
                                {
                                    if(data.detail === 'Ok')
                                    {
                                        updateLastUsedColors($('#<?= $name_w ?>_header').attr('data-newBorderColor'));
                                            button.parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Saved&nbsp;<i class="fa fa-thumbs-up" style="font-size:14px"></i>');
                                            $('#<?= $name_w ?>_header').attr('data-currentBorderColor', $('#<?= $name_w ?>_header').attr('data-newBorderColor'));
                                            setTimeout(function(){
                                                button.parents('div.container-fluid').find('div.contextMenuMsgRow').hide();   
                                                button.parents('div.widgetSubmenu').hide();
                                                $(".fullCtxMenuRow").css('color', 'rgb(51, 64, 69)');
                                                $(".fullCtxMenuRow").css('background-color', 'transparent');
                                                $(".fullCtxMenuRow").attr("data-selected", "false");
                                            }, 750);
                                    }
                                    else
                                    {
                                            button.parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Error&nbsp;<i class="fa fa-thumbs-down" style="font-size:14px"></i>');
                                            setTimeout(function(){
                                                    button.parents('div.container-fluid').find('div.contextMenuMsgRow').hide();
                                            }, 1000);
                                    }
                                },
                                error: function(errorData)
                                {
                                    button.parents('div.container-fluid').find('div.contextMenuMsgRow div.col-xs-12').html('Error&nbsp;<i class="fa fa-thumbs-down" style="font-size:14px"></i>');
                                    setTimeout(function(){
                                            button.parents('div.container-fluid').find('div.contextMenuMsgRow').hide();
                                    }, 1000);
                                }
                        });
                    });
                },
                error: function(errorData)
                {
            
                }
            });
            }
            else
            {
                $('#<?= $name_w ?>_widgetCtxMenu').remove();
            }
	});
</script>