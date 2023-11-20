<?php
$defaultTheme = isset($layoutDefault) ? $layoutDefault : 'dark'; // Set the default theme

// Check if the theme is already set in the cookie
if (isset($_COOKIE['selected_theme'])) {
	$selectedTheme = $_COOKIE['selected_theme'];
} else {
	$selectedTheme = $defaultTheme;
	//setcookie('selected_theme', $selectedTheme, time() + (30 * 24 * 60 * 60), "/", $cookieDomain, true); // Set the cookie for 30 days
    //setcookie('selected_theme', $selectedTheme, time() + (30 * 24 * 60 * 60), "/", $cookieDomain, true, false, 'None');
    if (PHP_VERSION_ID < 70300) {
        setcookie('selected_theme', $selectedTheme, time() + (30 * 24 * 60 * 60), "/" . "; samesite='None'", $cookieDomain, true);

        //setcookie('selected_theme', $selectedTheme, time() + (30 * 24 * 60 * 60), "/", $cookieDomain, true);
        //header('Set-Cookie: selected_theme=' . $selectedTheme . '; expires=' . gmdate('D, d M Y H:i:s T', time() + (30 * 24 * 60 * 60)) . '; path=/; domain=' . $cookieDomain . '; secure; httponly; samesite=None', false);
    } else {
        setcookie('selected_theme', $selectedTheme, [
            'expires' => time() + (30 * 24 * 60 * 60), // Set the cookie for 30 days
            'secure' => true,
            'samesite' => 'None',
        ]);
    }

}

$current_page = basename($_SERVER['PHP_SELF']);

switch ($current_page) {
	case 'synopticTemplatesForm.php':
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboard.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboardList.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboardView.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-addWidgetWizard2.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-addDashboardTab.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboard_configdash.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-iotApplications.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-synopticTemplatesForm.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/fontawesome-free-6.2.0-web/css/all.min.css">';
		break;
	
	case 'synopticsForm.php':
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboard.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboardList.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboardView.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-addWidgetWizard2.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-addDashboardTab.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboard_configdash.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-iotApplications.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-synopticsForm.css.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/fontawesome-free-6.2.0-web/css/all.min.css">';
		break;
	
	case 'metrics.php':
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboard.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboardList.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboardView.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-addWidgetWizard2.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-addDashboardTab.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboard_configdash.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/fontawesome-free-6.2.0-web/css/all.min.css">';
		break;
		
	case 'inspector.php':
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboard.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboardList.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboardView.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-addWidgetWizard2.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-addDashboardTab.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboard_configdash.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-iotApplications.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-addWidgetWizardDataInspector.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/fontawesome-free-6.2.0-web/css/all.min.css">';
		break;
		
		
	case 'externalServicesForm.php':
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboard.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboardList.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboardView.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-addWidgetWizard2.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-addDashboardTab.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboard_configdash.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-iotApplications.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-externalServicesForm.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/fontawesome-free-6.2.0-web/css/all.min.css">';
		break;
		
	case 'dashboard_register.php':
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboard.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboardList.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboardView.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-addWidgetWizard2.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-addDashboardTab.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboard_configdash.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/fontawesome-free-6.2.0-web/css/all.min.css">';
		break;
		
	case 'dashboard_examples.php':
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboard.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboardList.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboardView.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-addWidgetWizard2.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-addDashboardTab.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboard_configdash.css">';
        echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-snapTour.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/fontawesome-free-6.2.0-web/css/all.min.css">';
		break;
		
	case 'bimmanager.php':
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboard.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboardList.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboardView.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-addWidgetWizard2.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-addDashboardTab.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboard_configdash.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/fontawesome-free-6.2.0-web/css/all.min.css">';
		break;
	
	default:
		// Include the appropriate CSS file based on the selected theme
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboard.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboardList.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboardView.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-addWidgetWizard2.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-addDashboardTab.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboard_configdash.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-iotApplications.css">';
        echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-snapTour.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/fontawesome-free-6.2.0-web/css/all.min.css">';
		break;
}

?>

<script>
  document.addEventListener('DOMContentLoaded', function() {
	var themeSwitcher = document.getElementById('theme-switcher');
	themeSwitcher.value = '<?php echo $selectedTheme; ?>'; // Set the initial selected theme

	themeSwitcher.addEventListener('change', function() {
	  var selectedTheme = themeSwitcher.value;
      //document.cookie = 'selected_theme=' + selectedTheme + '; expires=' + new Date(Date.now() + (30 * 24 * 60 * 60 * 1000)).toUTCString(); // Update the cookie with the selected theme
      document.cookie = 'selected_theme=' + selectedTheme + '; expires=' + new Date(Date.now() + (30 * 24 * 60 * 60 * 1000)).toUTCString() + '; Path="/" ; Domain=<?= $cookieDomain; ?>' // Update the cookie with the selected theme
	  location.reload(); // Refresh the page to apply the new theme
	});
  });
</script>