<?php
namespace Dashboard\Ldap;

function searchPages($connection, $base, $filter, $pageSize=100)
{
    $cookie=''; $seen=[];
    do {
        $controls=[['oid'=>LDAP_CONTROL_PAGEDRESULTS,'iscritical'=>true,'value'=>['size'=>$pageSize,'cookie'=>$cookie]]];
        $result=ldap_search($connection,$base,$filter,[],0,0,0,LDAP_DEREF_NEVER,$controls);
        if ($result===false) throw new \RuntimeException('ldap_paged_search_failed');
        try {
            $code=0; $matched=''; $message=''; $referrals=[]; $responseControls=[];
            if (!ldap_parse_result($connection,$result,$code,$matched,$message,$referrals,$responseControls) || $code!==0) {
                throw new \RuntimeException('ldap_paged_result_failed');
            }
            $next=$responseControls[LDAP_CONTROL_PAGEDRESULTS]['value']['cookie'] ?? null;
            if (!is_string($next)) throw new \RuntimeException('ldap_paging_control_missing');
            if ($next!=='' && isset($seen[base64_encode($next)])) throw new \RuntimeException('ldap_paging_cookie_repeated');
            $entries=ldap_get_entries($connection,$result);
            if (!is_array($entries)) throw new \RuntimeException('ldap_paged_entries_failed');
        } finally {
            ldap_free_result($result);
        }
        if ($next!=='') $seen[base64_encode($next)]=true;
        $cookie=$next;
        yield $entries;
    } while ($cookie!=='');
}
