<?php
include '../config.php';
require '../sso/autoload.php';


use Jumbojett\OpenIDConnectClient;
include_once '../opensearch/OpenSearchS4C.php';
$open_search = new OpenSearchS4C();
$open_search->initDashboardWizard();

$open_search->ingestionSqlDataToOpenSearch("localhost","root", "root");