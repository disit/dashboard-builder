<?php
require_once __DIR__.'/../phpCompatibility.php';

// A failed source must never look like an authoritative empty or partial list.
function dashboardControllerAssertServiceHttp(array $headers)
{
    $status = null;
    foreach ($headers as $header) {
        if (preg_match('/^HTTP\/\S+\s+(\d{3})\b/i', $header, $match)) $status = (int)$match[1];
    }
    if ($status !== null && ($status < 200 || $status >= 300)) {
        throw new UnexpectedValueException('invalid_data_service_response');
    }
    return $status;
}

function dashboardControllerDecodeList($body, array $headers = [], array $fields = [], $associative = false)
{
    $status = dashboardControllerAssertServiceHttp($headers);
    // The data service also represents a successful empty list with HTTP 204.
    if ($status === 204 && is_string($body) && trim($body) === '') return [];
    $list = dashboardDecodeServiceJson($body, $associative, [[]]);
    foreach ($list as $row) {
        if (($associative ? !is_array($row) : !is_object($row))) throw new UnexpectedValueException('invalid_data_service_response');
        $values = (array)$row;
        if (array_key_exists('error', $values)) throw new UnexpectedValueException('invalid_data_service_response');
        foreach ($fields as $field) {
            if (!array_key_exists($field, $values) ||
                (!is_string($values[$field]) && !is_int($values[$field]) && !is_float($values[$field]))) {
                throw new UnexpectedValueException('invalid_data_service_response');
            }
        }
    }
    return $list;
}

function dashboardControllerServiceError()
{
    http_response_code(502);
    if (!headers_sent()) header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(['error'=>'invalid_data_service_response', 'result'=>'KO']);
}

function dashboardControllerRequireList($body, array $headers = [], array $fields = [], $associative = false)
{
    try { return dashboardControllerDecodeList($body, $headers, $fields, $associative); }
    catch (UnexpectedValueException $e) { dashboardControllerServiceError(); exit; }
}

function dashboardControllerRequireDelegations($body, array $headers = [])
{
    $list = dashboardControllerRequireList($body, $headers, ['elementId', 'id']);
    foreach ($list as $row) {
        foreach (['usernameDelegated', 'groupnameDelegated'] as $field) {
            if (!property_exists($row, $field)) $row->$field = null;
            if ($row->$field !== null && !is_string($row->$field)) { dashboardControllerServiceError(); exit; }
        }
        if ($row->usernameDelegated === null && $row->groupnameDelegated === null) {
            dashboardControllerServiceError(); exit;
        }
    }
    return $list;
}
