<?php
include '../config.php';
require '../sso/autoload.php';

if ( isset( $_SERVER ) && isset( $_SERVER['REQUEST_METHOD'] ) ) {
    echo 'This script must be run from the command line';
    exit;
} 

use Jumbojett\OpenIDConnectClient;
include_once '../opensearch/OpenSearchS4C.php';
$open_search = new OpenSearchS4C();
$open_search->initDashboardWizard();

$open_search->ingestionSqlDataToOpenSearch($host,$username, $password);