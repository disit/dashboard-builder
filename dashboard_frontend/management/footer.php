<?php
if (!function_exists('s4cMenuFooterIframeLink')) {
    function s4cMenuFooterIframeLink($link, $pageTitle)
    {
        if ($link === '#' || stripos($link, 'iframeApp.php') !== false) {
            return $link;
        }

        return 'iframeApp.php?linkUrl=' . urlencode($link) . '&pageTitle=' . urlencode($pageTitle) . '&fromSubmenu=false';
    }
}

$menuPrivacyPolicyLink = (isset($privacyPolicyLink) && trim($privacyPolicyLink) !== '') ? trim($privacyPolicyLink) : '#';
$menuPrivacyPolicyLinkName = (isset($privacyPolicyLinkName) && trim($privacyPolicyLinkName) !== '') ? trim($privacyPolicyLinkName) : 'Privacy Policy';
$menuCookiesPolicyLink = (isset($cookiesPolicyLink) && trim($cookiesPolicyLink) !== '') ? trim($cookiesPolicyLink) : '#';
$menuCookiesPolicyLinkName = (isset($cookiesPolicyLinkName) && trim($cookiesPolicyLinkName) !== '') ? trim($cookiesPolicyLinkName) : 'Cookies Policy';

$menuPrivacyPolicyLink = s4cMenuFooterIframeLink($menuPrivacyPolicyLink, $menuPrivacyPolicyLinkName);
$menuCookiesPolicyLink = s4cMenuFooterIframeLink($menuCookiesPolicyLink, $menuCookiesPolicyLinkName);

$menuPrivacyPolicyLinkAttr = function_exists('s4cHtmlAttr') ? s4cHtmlAttr($menuPrivacyPolicyLink) : htmlspecialchars($menuPrivacyPolicyLink, ENT_QUOTES, 'UTF-8');
$menuPrivacyPolicyLinkNameText = function_exists('s4cHtmlText') ? s4cHtmlText($menuPrivacyPolicyLinkName) : htmlspecialchars($menuPrivacyPolicyLinkName, ENT_QUOTES, 'UTF-8');
$menuCookiesPolicyLinkAttr = function_exists('s4cHtmlAttr') ? s4cHtmlAttr($menuCookiesPolicyLink) : htmlspecialchars($menuCookiesPolicyLink, ENT_QUOTES, 'UTF-8');
$menuCookiesPolicyLinkNameText = function_exists('s4cHtmlText') ? s4cHtmlText($menuCookiesPolicyLinkName) : htmlspecialchars($menuCookiesPolicyLinkName, ENT_QUOTES, 'UTF-8');
?>
<div id="footer">
	<div class="footer-links">
		<a href="<?php echo $menuPrivacyPolicyLinkAttr; ?>"><?php echo $menuPrivacyPolicyLinkNameText; ?></a>
		<a href="<?php echo $menuCookiesPolicyLinkAttr; ?>"><?php echo $menuCookiesPolicyLinkNameText; ?></a>
		<div class="theme-toggler">
			
			<select id="theme-switcher">
			  <option value="dark">Dark</option>
			  <option value="light">Light</option>
			  <option value="snap4ai">Snap4ai</option>
			</select>
		</div>
	</div>
	<?php include "uniFilogoFooter.php" ?>
</div>


