<?php
$dashboardLogoLinkValue = s4cConfigValue('dashboardLogoLink', 'https://www.snap4city.org');
$dashboardLogoImageValue = s4cConfigValue('dashboardLogoImage', '../img/applicationLogos/snap4city-logo.png');
$dashboardLogoAltValue = s4cConfigValue('dashboardLogoAlt', 'Snap4City.org');
?>
<div id="snapLogo">
    <a href="<?php echo s4cHtmlAttr($dashboardLogoLinkValue); ?>" target="_blank"><img id="snapLogoImg" src="<?php echo s4cHtmlAttr($dashboardLogoImageValue); ?>" alt="<?php echo s4cHtmlAttr($dashboardLogoAltValue); ?>"></a>
</div>
