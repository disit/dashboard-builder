<?php
// Small PHP 7.3-compatible contracts shared by legacy PHP endpoints.
function dashboardIsList($value)
{
    if (!is_array($value)) return false;
    $index=0;
    foreach ($value as $key=>$unused) if ($key !== $index++) return false;
    return true;
}

function dashboardDecodeStringList($json)
{
    if (!is_string($json)) return null;
    $list=json_decode($json);
    if (json_last_error() !== JSON_ERROR_NONE || !dashboardIsList($list)) return null;
    foreach ($list as $value) if (!is_string($value) || trim($value)==='') return null;
    return $list;
}

function dashboardServiceList($response, $field)
{
    if (!is_array($response) || !array_key_exists($field,$response) || !dashboardIsList($response[$field])) {
        throw new UnexpectedValueException('invalid_data_service_response');
    }
    return $response[$field];
}

function dashboardDecodeServiceJson($json, $associative=false, array $listPaths=[])
{
    if (!is_string($json)) throw new UnexpectedValueException('invalid_data_service_response');
    // Validate JSON lists before associative decoding can turn {} into [].
    $value=json_decode($json);
    if (json_last_error()!==JSON_ERROR_NONE || (!is_array($value) && !is_object($value))) {
        throw new UnexpectedValueException('invalid_data_service_response');
    }
    foreach ($listPaths as $path) {
        $field=$value;
        foreach ($path as $key) {
            if (!is_object($field) || !property_exists($field,$key)) throw new UnexpectedValueException('invalid_data_service_response');
            $field=$field->$key;
        }
        if (!dashboardIsList($field)) throw new UnexpectedValueException('invalid_data_service_response');
    }
    return $associative ? json_decode($json,true) : $value;
}

function dashboardInvalidServiceResponse($bufferLevel)
{
    while (ob_get_level()>$bufferLevel) ob_end_clean();
    http_response_code(502);
    if (!headers_sent()) header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(['code'=>'502','message'=>'Invalid response from data service']);
}

// Match the optional MySQLi port contract without relying on PHP 7 coercions.
function dashboardNormalizeMysqlPort($port)
{
    if (is_string($port)) {
        $port=trim($port);
        if ($port==='' || strcasecmp($port,'null')===0) return null;
    }
    if ($port===null) return null;
    if ((!is_int($port) && !(is_string($port) && ctype_digit($port))) || (int)$port<0 || (int)$port>65535) {
        throw new InvalidArgumentException('Invalid database port');
    }
    return (int)$port;
}
