<?php
// Called only by explicit endpoint boundaries; no global exception handler.
function dashboardDatabaseFailure($exception, $link, $bufferLevel)
{
    $code = (int) $exception->getCode();
    $messages = [
        1040 => ['too_many_connections', 'Database temporaneamente sovraccarico. Riprovare tra poco.'],
        1044 => ['database_access_denied', "Accesso al database non consentito per l'utente configurato."],
        1045 => ['authentication_failed', 'Autenticazione al database non riuscita.'],
        1049 => ['database_missing', 'Database configurato non presente sul server.'],
        1054 => ['column_missing', 'Colonna richiesta non presente nel database.'],
        1062 => ['duplicate_value', 'Esiste un record con lo stesso valore univoco.'],
        1064 => ['query_syntax_error', 'Errore nella sintassi della query al database.'],
        1142 => ['operation_denied', "Operazione sul database non consentita per l'utente configurato."],
        1146 => ['table_missing', 'Tabella richiesta non presente nel database.'],
        1205 => ['lock_wait_timeout', 'Tempo di attesa del database scaduto. Riprovare.'],
        1213 => ['deadlock', 'Conflitto tra transazioni del database. Riprovare.'],
        1451 => ['referenced_record', 'Impossibile modificare il record: esistono dati collegati.'],
        1452 => ['reference_missing', 'Impossibile salvare il record: manca un dato collegato.'],
        2002 => ['connection_failed', 'Impossibile connettersi al server del database.'],
        2003 => ['connection_failed', 'Impossibile connettersi al server del database.'],
        2006 => ['connection_lost', 'Connessione al database interrotta.'],
        2013 => ['connection_lost', 'Connessione al database interrotta.'],
    ];
    $description = $messages[$code] ?? ['unclassified_error', 'Operazione sul database non riuscita.'];
    $context = '';
    // Extract only identifier-only diagnostic forms, never arbitrary SQL or values.
    $identifierPatterns = [
        1049 => ['database', "/^Unknown database '([A-Za-z0-9_.$-]{1,256})'$/D"],
        1054 => ['column', "/^Unknown column '([A-Za-z0-9_.$-]{1,256})' in '[A-Za-z ]+'$/D"],
        1146 => ['table', "/^Table '([A-Za-z0-9_.$-]{1,256})' doesn't exist$/D"],
    ];
    if (isset($identifierPatterns[$code]) &&
        preg_match($identifierPatterns[$code][1], $exception->getMessage(), $matches)) {
        $context .= ' ' . $identifierPatterns[$code][0] . '=' . $matches[1];
    }
    // getSqlState() is available only from PHP 8.1.2; keep PHP 7.3 support.
    if (method_exists($exception, 'getSqlState')) {
        $sqlState = strtoupper($exception->getSqlState());
        if (preg_match('/\A[A-Z0-9]{5}\z/', $sqlState)) {
            $context .= ' SQLSTATE=' . $sqlState;
        }
    }
    if ($link instanceof mysqli) {
        try { $link->rollback(); } catch (Throwable $rollbackError) { /* The connection may have failed or closed. */ }
    }
    while (ob_get_level()>$bufferLevel) ob_end_clean();
    // Avoid logging SQL text, bind values, passwords or access tokens.
    error_log('Dashboard database failure ['.$code.'] at '.basename($exception->getFile()).':'.$exception->getLine().
        ' category='.$description[0].$context.' message='.$description[1]);
    http_response_code(500);
    if (!headers_sent()) header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(['detail'=>'Ko','error'=>'database_error','mysql_code'=>$code,'message'=>$description[1]]);
}
