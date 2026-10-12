<?php
$dashboardFooterPartnerLogosJson = trim((string) s4cConfigValue('dashboardFooterPartnerLogos', ''));
$validatedDashboardFooterPartnerLogos = array();

if ($dashboardFooterPartnerLogosJson !== '') {
    $configuredPartnerLogosJsonValue = json_decode($dashboardFooterPartnerLogosJson);
    $configuredPartnerLogosIsList = is_array($configuredPartnerLogosJsonValue) && json_last_error() === JSON_ERROR_NONE;

    if ($configuredPartnerLogosIsList) {
        $configuredPartnerLogos = json_decode($dashboardFooterPartnerLogosJson, true);
        foreach ($configuredPartnerLogos as $configuredPartnerLogo) {
            if (!is_array($configuredPartnerLogo) ||
                !isset($configuredPartnerLogo['link'], $configuredPartnerLogo['image'], $configuredPartnerLogo['alt']) ||
                !is_string($configuredPartnerLogo['link']) ||
                !is_string($configuredPartnerLogo['image']) ||
                !is_string($configuredPartnerLogo['alt'])) {
                continue;
            }

            $partnerLogoLink = trim($configuredPartnerLogo['link']);
            $partnerLogoImage = trim($configuredPartnerLogo['image']);
            $partnerLogoAlt = trim($configuredPartnerLogo['alt']);

            if ($partnerLogoLink === '' || $partnerLogoImage === '' || $partnerLogoAlt === '' ||
                preg_match('/[\x00-\x1F\x7F]/', $partnerLogoLink) ||
                preg_match('/[\x00-\x1F\x7F]/', $partnerLogoImage)) {
                continue;
            }

            $partnerLogoLinkScheme = parse_url($partnerLogoLink, PHP_URL_SCHEME);
            $partnerLogoImageScheme = parse_url($partnerLogoImage, PHP_URL_SCHEME);
            if ($partnerLogoLinkScheme === false || $partnerLogoImageScheme === false ||
                ($partnerLogoLinkScheme !== null && $partnerLogoLinkScheme !== '' && !in_array(strtolower($partnerLogoLinkScheme), array('http', 'https'), true)) ||
                ($partnerLogoImageScheme !== null && $partnerLogoImageScheme !== '' && !in_array(strtolower($partnerLogoImageScheme), array('http', 'https'), true))) {
                continue;
            }

            $validatedDashboardFooterPartnerLogos[] = array(
                'link' => $partnerLogoLink,
                'image' => $partnerLogoImage,
                'alt' => $partnerLogoAlt
            );
        }
    }
}

if (count($validatedDashboardFooterPartnerLogos) > 0) {
$dashboardFooterPartnerLogosKnownVariant = isset($dashboardFooterPartnerLogosVariant) &&
    in_array($dashboardFooterPartnerLogosVariant, array('standard', 'gea'), true);
?>
<style>
    .footerPartnerLogos {
        display: flex;
    }
<?php if ($dashboardFooterPartnerLogosKnownVariant) { ?>
    .footerLogo {
        display: block;
        padding: 0;
    }
<?php } ?>
<?php if (isset($dashboardFooterPartnerLogosVariant) && $dashboardFooterPartnerLogosVariant === 'standard') { ?>
    #footerLogoId .footerLogo img,
    .footerPartnerLogos .footerLogo img {
        max-height: none;
        height: 4.5vh !important;
    }
    .footerNavRow {
        margin: 0 2.5%;
    }
    #footerLogoId {
        max-height: max-content;
    }
<?php } elseif (isset($dashboardFooterPartnerLogosVariant) && $dashboardFooterPartnerLogosVariant === 'gea') { ?>
    .footerLogo img {
        max-height: none;
        height: 4.5vh !important;
    }
<?php } else { ?>
    .footerPartnerLogos .footerPartnerLogo {
        display: block;
        padding: 0;
    }
    .footerPartnerLogos .footerPartnerLogo img {
        max-height: none;
        height: 4.5vh !important;
    }
<?php } ?>
</style>
<div class="footerPartnerLogos">
<?php foreach ($validatedDashboardFooterPartnerLogos as $dashboardFooterPartnerLogo) { ?>
    <a href="<?php echo s4cHtmlAttr($dashboardFooterPartnerLogo['link']); ?>" target="_blank" rel="noopener noreferrer" class="footerLogo footerPartnerLogo"><img src="<?php echo s4cHtmlAttr($dashboardFooterPartnerLogo['image']); ?>" alt="<?php echo s4cHtmlAttr($dashboardFooterPartnerLogo['alt']); ?>"></a>
<?php } ?>
</div>
<?php
}
