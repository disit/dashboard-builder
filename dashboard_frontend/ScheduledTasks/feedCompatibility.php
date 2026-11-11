<?php
// Boundary contracts for scheduled feeds; compatible with PHP 7.3 and PHP 8.x.
function feedHttpJson($url, $context = null)
{
    $http_response_header = [];
    try {
        $body = $context === null ? @file_get_contents($url) : @file_get_contents($url, false, $context);
    } catch (\Throwable $error) {
        $body = false;
    }
    return ['body' => $body, 'headers' => $http_response_header];
}

function feedHttpSucceeded($headers)
{
    if (!is_array($headers)) return false;
    $status = 0;
    foreach ($headers as $header) {
        if (is_string($header) && preg_match('/^HTTP\/\S+\s+(\d{3})(?:\s|$)/', $header, $matches)) $status = (int)$matches[1];
    }
    return $status >= 200 && $status < 300;
}

function feedAbort($source)
{
    // Do not log remote URLs, access tokens or response bodies.
    error_log('Scheduled feed aborted: invalid or unavailable ' . $source . ' response');
    exit(1);
}

function feedOwnershipList($url, $context)
{
    $response = feedHttpJson($url, $context);
    if (!feedHttpSucceeded($response['headers']) || !is_string($response['body'])) feedAbort('ownership');
    $owned = json_decode($response['body']);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($owned)) feedAbort('ownership');
    foreach ($owned as $entry) {
        if (!is_object($entry) || !property_exists($entry, 'elementId') ||
            !(is_int($entry->elementId) || (is_string($entry->elementId) && trim($entry->elementId) !== ''))) {
            feedAbort('ownership');
        }
        if (!property_exists($entry, 'username') || !is_string($entry->username) || trim($entry->username) === '') {
            feedAbort('ownership');
        }
    }
    return $owned;
}

function feedSparqlResult($url, $context = null)
{
    $response = feedHttpJson($url, $context);
    if (!feedHttpSucceeded($response['headers']) || !is_string($response['body'])) feedAbort('SPARQL');
    // Decode as objects first so a malformed bindings:{} cannot impersonate [].
    $result = json_decode($response['body']);
    if (json_last_error() !== JSON_ERROR_NONE || !is_object($result) ||
        !isset($result->results) || !is_object($result->results) ||
        !isset($result->results->bindings) || !is_array($result->results->bindings)) feedAbort('SPARQL');
    foreach ($result->results->bindings as $binding) {
        if (!is_object($binding) || !isset($binding->s) || !is_object($binding->s) ||
            !isset($binding->s->value) || !is_string($binding->s->value) || trim($binding->s->value) === '') feedAbort('SPARQL');
    }
    return json_decode($response['body'], true);
}
