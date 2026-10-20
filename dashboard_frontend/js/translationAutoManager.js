/* RootAdmin controls. Status/proposals are always rendered as plain text. */
(function () {
    'use strict';
    function init() {
        var panel = document.getElementById('translation-auto');
        if (!panel) return;
        var button = document.getElementById('translation-auto-start');
        var language = document.getElementById('translation-auto-language');
        var reset = document.getElementById('translation-auto-reset');
        var status = document.getElementById('translation-auto-status');
        var results = document.getElementById('translation-auto-results');
        var log = document.getElementById('translation-auto-log');
        var logState = document.getElementById('translation-auto-log-state');
        var missingTotal = document.getElementById('translation-auto-missing-total');
        var missingRows = document.getElementById('translation-auto-missing-rows');
        var missingUpdated = document.getElementById('translation-auto-missing-updated');
        var missingGeneration = 0, lastRunState;
        var timer, posting = false, enabled = false, resumable = false, runId = null, requestGeneration = 0;
        button.disabled = true;
        function api(body) {
            var controller = new AbortController();
            var timeout = setTimeout(function () { controller.abort(); }, body.action === 'start' ? 20000 : 10000);
            return fetch(panel.getAttribute('data-endpoint') || 'translationauto.php', {
                method: 'POST', credentials: 'same-origin', signal: controller.signal,
                headers: {'Content-Type': 'application/json', 'X-S4C-Translation-Registration': '1',
                    'X-S4C-Translation-Csrf': panel.getAttribute('data-csrf')},
                body: JSON.stringify(body)
            }).then(function (response) {
                if (!response.ok) throw new Error('unavailable');
                return response.json();
            }).finally(function () { clearTimeout(timeout); });
        }
        function refreshMissing() {
            if (!missingTotal || !missingRows || !missingUpdated) return;
            var generation = ++missingGeneration;
            api({action: 'missing'}).then(function (data) {
                if (generation !== missingGeneration) return;
                var summary = data.missing;
                if (!summary || !Array.isArray(summary.languages)) throw new Error('unavailable');
                missingTotal.textContent = String(summary.total);
                while (missingRows.firstChild) missingRows.removeChild(missingRows.firstChild);
                summary.languages.forEach(function (entry) {
                    var row = document.createElement('tr'), name = document.createElement('td'), count = document.createElement('td');
                    name.textContent = (entry.language || 'Unspecified') + (entry.automatic ? '' : ' (not in automatic targets)');
                    count.textContent = String(entry.count);
                    row.appendChild(name); row.appendChild(count); missingRows.appendChild(row);
                });
                if (!summary.languages.length) {
                    var row = document.createElement('tr'), cell = document.createElement('td');
                    cell.colSpan = 2; cell.textContent = 'No missing translations.'; row.appendChild(cell); missingRows.appendChild(row);
                }
                missingUpdated.textContent = 'Dictionary snapshot: ' + summary.updated_at + ' UTC. Refreshed when a run stops or this page reloads.';
            }).catch(function () {
                if (generation !== missingGeneration) return;
                missingTotal.textContent = 'Unavailable';
                while (missingRows.firstChild) missingRows.removeChild(missingRows.firstChild);
                missingUpdated.textContent = 'Could not read missing translations. Reload the page to retry.';
            });
        }
        function render(data) {
            var runState = (data.run ? data.run.id + ':' + data.run.status : 'none') + ':' + (data.busy === true);
            if (lastRunState !== undefined && lastRunState !== runState && data.busy !== true) refreshMissing();
            lastRunState = runState;
            enabled = data.enabled === true;
            resumable = data.resumable === true;
            runId = data.run ? data.run.id : null;
            button.disabled = posting || !enabled || data.busy === true;
            button.textContent = resumable ? 'Resume translation' : 'Translate missing texts';
            language.disabled = button.disabled || resumable;
            if (reset) {
                reset.hidden = !enabled || !resumable;
                reset.disabled = posting || button.disabled || !resumable || !runId;
            }
            if ((resumable || data.busy) && data.run) { language.value = (data.run.languages || []).length === 1 ? data.run.languages[0] : ''; }
            while (results.firstChild) results.removeChild(results.firstChild);
            if (!enabled) {
                if (logState) logState.textContent = 'Disabled';
                status.textContent = 'Manual translation is disabled. Enable it in the server configuration.';
                return;
            }
            var run = data.run, text = '';
            if (run) {
                var counts = run.counts || {};
                var authentication = run.auth_mode === 'dedicated' ? 'Technical account' :
                    (run.auth_mode === 'session' ? 'Signed-in user' : '');
                if (logState) logState.textContent = (data.busy ? 'In progress' : (resumable ? 'Resume required' : run.status)) +
                    (authentication ? ' · ' + authentication : '') +
                    ' · ' + (counts.applied || 0) + ' applied · ' + (counts.review || 0) + ' review';
                if (data.busy) {
                    text = data.already_running ? 'Translation already running. ' : 'Translation in progress. ';
                    text += 'Started by ' + (run.started_by || run.requested_by || run.origin) + ' at ' + (run.launch_at || run.started_at || run.created_at) + ' UTC. ';
                    if (!data.worker_active) text += 'Waiting for the authenticated worker. ';
                } else if (resumable) {
                    text = run.status === 'waiting_auth' ?
                        (String(run.error_code || '').indexOf('dedicated_') === 0 ?
                            'Technical authentication is unavailable or was rejected. Check dedicated credentials and API/IP permissions, then resume. ' :
                            'The session token expired or was rejected by the LLM. Sign in again or check API permissions, then resume. ') :
                        'Translation was interrupted. Resume translation; the original selection is preserved. ';
                } else text = 'Last run: ' + run.status + '. ';
                if (authentication) text += 'Authentication: ' + authentication + '. ';
                text += 'Applied: ' + (counts.applied || 0) +
                    ', preserved: ' + (counts.preserved || 0) + ', review: ' + (counts.review || 0) +
                    ', deferred/errors: ' + (counts.failed || 0) +
                    (counts.cancelled ? ', cancelled: ' + counts.cancelled : '') +
                    ', pending: ' + ((counts.pending || 0) + (counts.generated || 0)) + '.';
                if (run.error_code) text += ' Error: ' + run.error_code + '.';
                (run.samples || []).forEach(function (item) {
                    var li = document.createElement('li');
                    li.textContent = item.target_language + ': ' + item.menu_text + ' → ' +
                        (item.proposed_text || '') + ' [' + item.status + ']' +
                        (item.error_code ? ' — ' + item.error_code : '');
                    results.appendChild(li);
                });
            } else {
                text = 'Ready. Only missing translations are processed.';
                if (logState) logState.textContent = 'Ready';
            }
            status.textContent = text;
        }
        function schedule(busy) {
            clearTimeout(timer);
            timer = setTimeout(poll, document.hidden ? 30000 : (busy ? 2500 : 15000));
        }
        function poll() {
            if (posting) { schedule(true); return; }
            var generation = requestGeneration;
            api({action: 'status'}).then(function (data) {
                if (generation !== requestGeneration) return;
                render(data); schedule(data.busy);
            }).catch(function () {
                if (generation !== requestGeneration) return;
                button.disabled = true; language.disabled = true;
                if (reset) reset.disabled = true;
                if (logState) logState.textContent = 'Status unavailable';
                status.textContent = 'Translation status unavailable. Check your session and the service configuration.';
                schedule(false);
            });
        }
        button.addEventListener('click', function () {
            if (button.disabled) return;
            var bytes = new Uint8Array(16); window.crypto.getRandomValues(bytes);
            var key = Array.prototype.map.call(bytes, function (b) {return ('0' + b.toString(16)).slice(-2);}).join('');
            requestGeneration++;
            posting = true; button.disabled = true; language.disabled = true; clearTimeout(timer);
            if (reset) reset.disabled = true;
            if (log) log.open = true;
            if (logState) logState.textContent = 'Starting…';
            status.textContent = 'Starting translation…';
            api({action: 'start', mode: 'apply',
                languages: language.value ? [language.value] : [], request_key: key})
                .then(function (data) { posting = false; render(data); schedule(data.busy); })
                .catch(function () {
                    posting = false;
                    status.textContent = 'Could not confirm the start. Checking the server status…';
                    poll();
                });
        });
        if (reset) reset.addEventListener('click', function () {
            if (posting || reset.disabled || !resumable || !runId) return;
            requestGeneration++;
            posting = true; reset.disabled = true; button.disabled = true; language.disabled = true; clearTimeout(timer);
            if (log) log.open = true;
            if (logState) logState.textContent = 'Resetting…';
            status.textContent = 'Resetting pending translation…';
            api({action: 'reset', run_id: runId})
                .then(function (data) { posting = false; render(data); schedule(data.busy); })
                .catch(function () {
                    posting = false;
                    status.textContent = 'Could not confirm the reset. Checking the server status…';
                    poll();
                });
        });
        document.addEventListener('visibilitychange', function () {if (!document.hidden && !posting) poll();});
        refreshMissing();
        poll();
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();
})();
