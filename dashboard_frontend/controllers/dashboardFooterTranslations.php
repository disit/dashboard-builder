<?php
/* Policy-link display keys shared by read-only views and locked registration. */
function dashboardFooterTranslationKey($text)
{
    // Footer labels must use the same CR/LF + trim cleanup as the view dictionary.
    return trim(str_replace(["\r", "\n"], '', $text));
}

function dashboardFooterTranslationKeys()
{
    $defaults = [
        'privacyPolicyLinkName' => 'Privacy Policy',
        'cookiesPolicyLinkName' => 'Cookies Policy',
        'termsAndConditionsLinkName' => 'Terms and Conditions',
        'contactUsLinkName' => 'Contact us',
    ];
    $keys = [];
    foreach ($defaults as $name => $fallback) {
        $text = isset($GLOBALS[$name]) ? $GLOBALS[$name] : $fallback;
        if (!is_string($text) || strlen($text) > 65535 || !preg_match('//u', $text)) continue;
        $key = dashboardFooterTranslationKey($text);
        if ($key !== '') $keys[$key] = $key;
    }
    return array_values($keys);
}

function dashboardFooterPolicyLabel($text, $translations, $selectorVisible)
{
    $text = is_scalar($text) ? (string) $text : '';
    $key = dashboardFooterTranslationKey($text);
    if ($selectorVisible === 'yes' && is_array($translations) && isset($translations[$key]) &&
        is_string($translations[$key]) && trim($translations[$key]) !== '' && preg_match('//u', $translations[$key])) {
        $text = $translations[$key];
    }
    // Plain display text only: dictionary content must never become executable HTML.
    return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
