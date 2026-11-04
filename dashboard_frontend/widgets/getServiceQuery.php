<?php
    /* Dashboard Builder.
   Copyright (C) 2016 DISIT Lab https://www.disit.org - University of Florence

   This program is free software: you can redistribute it and/or modify
   it under the terms of the GNU Affero General Public License as
   published by the Free Software Foundation, either version 3 of the
   License, or (at your option) any later version.
   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU Affero General Public License for more details.
   You should have received a copy of the GNU Affero General Public License
   along with this program.  If not, see <http://www.gnu.org/licenses/>. */
    include '../config.php';
require_once __DIR__.'/widgetDatabase.php';
$widgetDatabaseGuard = dashboardWidgetDatabaseBegin();
try {

    
    $metricName = $_GET['nomeMetrica'];

    $link = new mysqli($host, $username, $password, $dbname);

    if ($link->connect_error) 
    {
        die("Connection failed: " . $link->connect_error);
    }

    $rows = array();
    foreach($metricName as $metricNameIteration)
    { 
        $metricNameIteration = mysqli_real_escape_string($link, $metricNameIteration);
        $sql = "SELECT query FROM Descriptions WHERE IdMetric = '" . $metricNameIteration . "'";
        $result = $link->query($sql);

        while ($r = mysqli_fetch_assoc($result)) 
        {
            $parameters = array('param' => $r);
        }
    }    
    $par_json = json_encode($parameters);
    $link->close();
    echo($par_json);


} catch (\mysqli_sql_exception $widgetDatabaseError) {
    dashboardWidgetDatabaseFailure($widgetDatabaseError,isset($link)?$link:(isset($esbLink)?$esbLink:null),$widgetDatabaseGuard,'json');
} catch (\DashboardWidgetIconDirectoryException $widgetDirectoryError) {
    dashboardWidgetDatabaseHttpFailure($widgetDatabaseGuard,503,'icon_directory_unavailable','Widget icon directory is unavailable.');
} catch (\PDOException $widgetPdoError) {
    error_log('Widget PostgreSQL operation failed at '.basename($widgetPdoError->getFile()).':'.$widgetPdoError->getLine());
    dashboardWidgetDatabaseHttpFailure($widgetDatabaseGuard,500,'database_error','PostgreSQL database operation failed.');
} finally {
    dashboardWidgetDatabaseEnd($widgetDatabaseGuard);
}
