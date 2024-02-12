<?php
$defaultTheme = isset($layoutDefault) ? $layoutDefault : 'dark'; // Set the default theme

// Check if the theme is already set in the cookie
if (isset($_COOKIE['selected_theme'])) {
	$selectedTheme = $_COOKIE['selected_theme'];
} else {
	$selectedTheme = $defaultTheme;
    if (PHP_VERSION_ID < 70300) {
        setcookie('selected_theme', $selectedTheme, time() + (30 * 24 * 60 * 60), "/" . "; samesite='None'", $cookieDomain, true);
    } else {
        setcookie('selected_theme', $selectedTheme, [
            'expires' => time() + (30 * 24 * 60 * 60), // Set the cookie for 30 days
            'path' => '/',
            'domain' => $cookieDomain,
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
        if(isset($_SESSION['loggedRole']) && $_SESSION['loggedRole'] == 'RootAdmin') {
            echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-addWidgetWizardOS.css">';
        } else {
            echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-addWidgetWizard2.css">';
        }
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
        if(isset($_SESSION['loggedRole']) && $_SESSION['loggedRole'] == 'RootAdmin') {
            echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-addWidgetWizardOS.css">';
        } else {
            echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-addWidgetWizard2.css">';
        }
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-addDashboardTab.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboard_configdash.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-iotApplications.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-synopticsForm.css.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/fontawesome-free-6.2.0-web/css/all.min.css">';
		break;

    case 'dashboard_register.php':
    case 'bimmanager.php':
    case 'metrics.php':
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboard.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboardList.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboardView.css">';
        if(isset($_SESSION['loggedRole']) && $_SESSION['loggedRole'] == 'RootAdmin') {
		    echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-addWidgetWizardOS.css">';
        } else {
            echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-addWidgetWizard2.css">';
        }
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-addDashboardTab.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboard_configdash.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/fontawesome-free-6.2.0-web/css/all.min.css">';
		break;

    case 'inspectorOS.php':
	case 'inspector.php':
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboard.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboardList.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboardView.css">';
        if(isset($_SESSION['loggedRole']) && $_SESSION['loggedRole'] == 'RootAdmin') {
            echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-addWidgetWizardOS.css">';
        } else {
            echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-addWidgetWizard2.css">';
        }
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
        if(isset($_SESSION['loggedRole']) && $_SESSION['loggedRole'] == 'RootAdmin') {
            echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-addWidgetWizardOS.css">';
        } else {
            echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-addWidgetWizard2.css">';
        }
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-addDashboardTab.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboard_configdash.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-iotApplications.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-externalServicesForm.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/fontawesome-free-6.2.0-web/css/all.min.css">';
		break;

    case 'dashboard_examples.php':
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboard.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboardList.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboardView.css">';
        if(isset($_SESSION['loggedRole']) && $_SESSION['loggedRole'] == 'RootAdmin') {
            echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-addWidgetWizardOS.css">';
        } else {
            echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-addWidgetWizard2.css">';
        }
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-addDashboardTab.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboard_configdash.css">';
        echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-snapTour.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/fontawesome-free-6.2.0-web/css/all.min.css">';
		break;

    default:
		// Include the appropriate CSS file based on the selected theme

		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboard.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboardList.css">';
		echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-dashboardView.css">';
        if(isset($_SESSION['loggedRole']) && $_SESSION['loggedRole'] == 'RootAdmin') {
            if ($current_page == 'dashboards.php') {
                echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-addWidgetWizardOS-W.css">';
            } else {
                echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-addWidgetWizardOS.css">';
            }
        } else {
            echo '<link rel="stylesheet" type="text/css" href="' . $themeBaseUri . '/css/s4c-css/themes/' . $selectedTheme . '/s4c-addWidgetWizard2.css">';
        }
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