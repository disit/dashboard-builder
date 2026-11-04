<?php
require_once __DIR__.'/../phpCompatibility.php';
function userStatsOrganizationNames($json)
{
    $records=dashboardDecodeServiceJson($json,true,[[]]);
    $names=[];
    foreach ($records as $record) {
        if (!is_array($record) || !isset($record['organizationName']) || !is_string($record['organizationName']) || trim($record['organizationName'])==='') {
            throw new UnexpectedValueException('invalid_organization_list');
        }
        $names[]=$record['organizationName'];
    }
    return $names;
}
function userStatsUsageTable($data)
{
    if (!is_array($data)) throw new UnexpectedValueException('invalid_usage_report');
    $all=array_key_exists('all_organizations',$data);
    $datasets=$all ? $data : [$data];
    foreach ($datasets as $dataset) {
        if (!is_array($dataset)) throw new UnexpectedValueException('invalid_usage_report');
        foreach ($dataset as $row) if (!is_array($row)) throw new UnexpectedValueException('invalid_usage_report');
    }
    return $all ? $data['all_organizations'] : $data;
}
