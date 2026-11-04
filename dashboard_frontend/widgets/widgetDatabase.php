<?php
// Explicit widget request boundaries only; no global error/exception handler.
require_once __DIR__.'/../databaseException.php';
class DashboardWidgetIconDirectoryException extends RuntimeException {}
function dashboardWidgetDatabaseBegin()
{
    $guard=['level'=>ob_get_level(),'reportMode'=>null];
    ob_start();
    if(class_exists('mysqli_driver')) {
        $guard['reportMode']=(new mysqli_driver())->report_mode;
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    }
    return $guard;
}
function dashboardWidgetDatabaseClear($guard)
{
    while(ob_get_level()>$guard['level']+1) ob_end_clean();
    if(ob_get_level()===$guard['level']+1) ob_clean();
}
function dashboardWidgetDatabaseHttpFailure($guard,$status,$error,$message)
{
    dashboardWidgetDatabaseClear($guard);
    http_response_code($status);
    if(!headers_sent()) header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(['detail'=>'Ko','error'=>$error,'message'=>$message]);
}
function dashboardWidgetDatabaseFailure($exception,$link,$guard,$format='json')
{
    dashboardWidgetDatabaseClear($guard);
    dashboardDatabaseFailure($exception,$link,$guard['level']+1);
    if($format==='html') {
        dashboardWidgetDatabaseClear($guard);
        if(!headers_sent()) header('Content-Type: text/html; charset=UTF-8');
        echo '<div class="widgetDatabaseError" role="alert">Database operation failed ('.(int)$exception->getCode().').</div>';
    }
}
function dashboardWidgetDatabaseEnd($guard)
{
    if($guard['reportMode']!==null) mysqli_report($guard['reportMode']);
    while(ob_get_level()>$guard['level']) ob_end_flush();
}
