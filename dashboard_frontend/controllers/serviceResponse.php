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

// Only fixed diagnostic codes and metadata may reach the client or the log.
function dashboardControllerServiceError(array $diagnostics = [])
{
    $safe = [];
    $allowed = [
        'service'=>['ownership', 'public_delegations', 'user_delegations'],
        'reason'=>['transport_error', 'upstream_http_error', 'invalid_json', 'invalid_list', 'invalid_list_entry', 'upstream_error', 'missing_field', 'invalid_field_type', 'invalid_dashboard_id', 'invalid_data_service_response'],
        'field'=>['elementId', 'elementType', 'usernameDelegator', 'groupnameDelegated', 'id'],
        'value_type'=>['missing', 'NULL', 'boolean', 'integer', 'double', 'string', 'array', 'object', 'resource', 'resource (closed)']
    ];
    foreach ($allowed as $key=>$values) {
        if (isset($diagnostics[$key]) && in_array($diagnostics[$key], $values, true)) $safe[$key] = $diagnostics[$key];
    }
    foreach (['http_status', 'index'] as $key) {
        if (isset($diagnostics[$key]) && is_int($diagnostics[$key]) &&
            ($key === 'http_status' ? ($diagnostics[$key] >= 100 && $diagnostics[$key] <= 599) : $diagnostics[$key] >= 0)) $safe[$key] = $diagnostics[$key];
    }
    $response = ['error'=>'invalid_data_service_response', 'result'=>'KO'];
    if ($safe) {
        $response['details'] = $safe;
        error_log('Dashboard data service failed: '.json_encode($safe));
    }
    http_response_code(502);
    if (!headers_sent()) header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($response);
}

// Inspect a failed response without returning its contents, URL, or authentication data.
function dashboardControllerServiceFailureDetails($body, array $headers, array $fields)
{
    $details = [];
    foreach ($headers as $header) {
        if (preg_match('/^HTTP\/\S+\s+(\d{3})\b/i', $header, $match)) $details['http_status'] = (int)$match[1];
    }
    if (isset($details['http_status']) && ($details['http_status'] < 200 || $details['http_status'] >= 300)) {
        $details['reason'] = 'upstream_http_error';
    } elseif (!is_string($body)) {
        $details['reason'] = 'transport_error';
    } else {
        $list = json_decode($body);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $details['reason'] = 'invalid_json';
        } elseif (!dashboardIsList($list)) {
            $details['reason'] = 'invalid_list';
        } else {
            foreach ($list as $index=>$row) {
                $details['index'] = $index;
                if (!is_object($row)) { $details['reason'] = 'invalid_list_entry'; break; }
                if (property_exists($row, 'error')) { $details['reason'] = 'upstream_error'; break; }
                foreach ($fields as $field) {
                    if (!property_exists($row, $field)) {
                        $details += ['reason'=>'missing_field', 'field'=>$field, 'value_type'=>'missing'];
                        break 2;
                    }
                    if (!is_string($row->$field) && !is_int($row->$field) && !is_float($row->$field)) {
                        $details += ['reason'=>'invalid_field_type', 'field'=>$field, 'value_type'=>gettype($row->$field)];
                        break 2;
                    }
                }
            }
        }
    }
    if (!isset($details['reason'])) $details['reason'] = 'invalid_data_service_response';
    return $details;
}

function dashboardControllerRequireList($body, array $headers = [], array $fields = [], $associative = false, array $diagnostics = [])
{
    try { return dashboardControllerDecodeList($body, $headers, $fields, $associative); }
    catch (UnexpectedValueException $e) {
        dashboardControllerServiceError($diagnostics ? array_merge($diagnostics, dashboardControllerServiceFailureDetails($body, $headers, $fields)) : []);
        exit;
    }
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
