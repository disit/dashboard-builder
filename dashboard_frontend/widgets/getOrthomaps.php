<?php
/* Dashboard Builder.
Copyright (C) 2018 DISIT Lab https://www.disit.org - University of Florence

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
include '../config.php';//Escape

$data = [];
$link = null;
try {
    $link = new mysqli($host, $username, $password, $dbname);
    if ($link->connect_error || !$link->set_charset("utf8")) {
        throw new UnexpectedValueException('orthomap_database_unavailable');
    }
    $metricName = mysqli_real_escape_string($link, $_GET['widgetName']);
    $q1 = "SELECT * FROM Dashboard.Config_widget_dashboard widgetDash INNER JOIN Dashboard.Config_dashboard Dash ON Dash.id = widgetDash.id_dashboard WHERE widgetDash.name_w = '$metricName'";
    $r1 = mysqli_query($link, $q1);
    if (!$r1) throw new UnexpectedValueException('orthomap_database_unavailable');
    if (mysqli_num_rows($r1) === 0) throw new RuntimeException('widget_not_found', 404);
    while ($row1 = mysqli_fetch_assoc($r1)) {
        $org = mysqli_real_escape_string($link, $row1['organizations']);
    }
    $sql = "SELECT orthomapJson FROM Dashboard.Organizations WHERE organizationName = '$org';";
    $result = $link->query($sql);
    if (!$result) throw new UnexpectedValueException('orthomap_database_unavailable');
    while ($row = mysqli_fetch_assoc($result)) {
        // SQL NULL / no configuration means no configured orthomaps.
        $json = $row['orthomapJson'] ?? null;
        if ($json === null || $json === '') continue;
        if (!is_string($json)) throw new UnexpectedValueException('invalid_orthomap_payload');
        $orthomaps = json_decode($json);
        if (json_last_error() !== JSON_ERROR_NONE || !is_object($orthomaps)) {
            throw new UnexpectedValueException('invalid_orthomap_payload');
        }
        foreach (['error', 'Error', 'errors'] as $errorKey) {
            if (property_exists($orthomaps, $errorKey)) throw new UnexpectedValueException('invalid_orthomap_payload');
        }
        $dropdown = property_exists($orthomaps, 'dropdownMenu') ? $orthomaps->dropdownMenu : [];
        if (!is_array($dropdown)) throw new UnexpectedValueException('invalid_orthomap_payload');
        $data = [];
        foreach ($dropdown as $entry) {
            if (!is_object($entry) || !isset($entry->id) || (!is_string($entry->id) && !is_int($entry->id))
                || !isset($entry->label) || !is_string($entry->label)) {
                throw new UnexpectedValueException('invalid_orthomap_payload');
            }
            if ($entry->id) $data[] = [$entry->id => $entry->label];
        }
    }
} catch (Throwable $e) {
    $notFound = $e instanceof RuntimeException && $e->getCode() === 404;
    http_response_code($notFound ? 404 : 502);
    $data = ['result' => 'KO', 'error' => $notFound ? 'widget_not_found' : 'orthomaps_unavailable'];
} finally {
    if ($link !== null && !$link->connect_error) $link->close();
}
header('Content-Type: application/json');
echo json_encode($data);
