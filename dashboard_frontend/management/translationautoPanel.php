<?php
/* Included after RootAdmin access checks, in both manager layouts. */
if(($_SESSION['loggedRole'] ?? '')!=='RootAdmin')return;
require_once __DIR__.'/../controllers/translationAuto/policy.php';
if(!isset($_SESSION['translationAutoCsrf']))$_SESSION['translationAutoCsrf']=bin2hex(random_bytes(32));
try{$translationAutoSettings=\S4C\TranslationAuto\settings();$includeEnglish=$translationAutoSettings['include_english'];}
catch(Throwable $e){$includeEnglish=true;}
$translationAutoLanguages=\S4C\TranslationAuto\languageMap($localizations,$includeEnglish);
?>
<style>
.translation-manager-toolbar {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    gap: 20px;
    margin: 2%;
}
.translation-manager-toolbar > #select_element_type {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    min-width: 0;
    padding: 16px 0;
    margin: 0 !important;
    float: none !important;
}
.translation-manager-toolbar > #select_element_type > .btn,
.translation-manager-toolbar > #select_element_type > .dropdown {
    margin: 0 !important;
    float: none !important;
}
.translation-manager-toolbar #dropdownMenuButton { float: none; }
#translation-auto {
    flex: 1 1 450px;
    min-width: 0;
    max-width: 680px;
    box-sizing: border-box;
    overflow-wrap: anywhere;
    color: var(--text-color-, inherit);
    background-color: var(--modal-body-bg-, transparent);
}
#translation-auto label, #translation-auto p, #translation-auto small,
#translation-auto ul, #translation-auto li, #translation-auto a,
#translation-auto .translation-auto-controls, #translation-auto .translation-auto-options {
    color: inherit;
}
#translation-auto select, #translation-auto option {
    color: var(--text-color-, inherit);
    background-color: var(--modal-body-bg-, white);
}
#translation-auto .translation-auto-controls {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 12px;
}
#translation-auto .translation-auto-language-control {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    max-width: 100%;
}
#translation-auto .translation-auto-options {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 12px;
    max-width: 100%;
}
#translation-auto label { margin: 0; }
#translation-auto-reset[hidden] { display: none; }
#translation-auto select { min-width: 0; max-width: 100%; }
#translation-auto small { font-size: inherit; }
#translation-auto a { text-decoration: underline; }
#translation-auto-log {
    margin: 11px 0;
    border: 1px solid currentColor;
    border-radius: 8px;
    overflow: hidden;
}
#translation-auto-log > summary {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 6px 12px;
    padding: 10px 12px;
    cursor: pointer;
    list-style: none;
}
#translation-auto-log > summary::-webkit-details-marker { display: none; }
#translation-auto-log > summary::before {
    content: '';
    width: 7px;
    height: 7px;
    border-right: 2px solid currentColor;
    border-bottom: 2px solid currentColor;
    transform: rotate(-45deg);
    flex-shrink: 0;
}
#translation-auto-log[open] > summary::before { transform: rotate(45deg); }
#translation-auto-log > summary:focus-visible { outline: 2px solid currentColor; outline-offset: -3px; }
#translation-auto-log-state { margin-left: auto; font-size: inherit; }
#translation-auto-log .translation-auto-log-action { text-decoration: underline; }
#translation-auto-log .translation-auto-log-hide,
#translation-auto-log[open] .translation-auto-log-show { display: none; }
#translation-auto-log[open] .translation-auto-log-hide { display: inline; }
#translation-auto-log .translation-auto-log-body {
    padding: 12px;
    border-top: 1px solid currentColor;
    max-height: 20rem;
    overflow: auto;
    overflow-wrap: anywhere;
    scrollbar-gutter: stable;
}
#translation-auto-status { margin: 0 0 10px; }
#translation-auto-results { margin-bottom: 0; padding-left: 20px; }
#translation-auto-missing { margin: 0 0 14px; }
#translation-auto-missing > summary { display: flex; align-items: center; flex-wrap: wrap; gap: 6px; cursor: pointer; padding: 2px 0; list-style: none; }
#translation-auto-missing > summary::-webkit-details-marker { display: none; }
#translation-auto-missing > summary::before { content: ''; width: 6px; height: 6px; border-right: 2px solid currentColor; border-bottom: 2px solid currentColor; transform: rotate(-45deg); margin-right: 4px; flex-shrink: 0; }
#translation-auto-missing[open] > summary::before { transform: rotate(45deg); }
#translation-auto-missing-total { font-weight: bold; }
#translation-auto-missing table { width: 100%; margin: 8px 0; color: inherit; }
#translation-auto-missing th, #translation-auto-missing td { padding: 5px 8px; text-align: left; border-bottom: 1px solid currentColor; }
#translation-auto-missing th:last-child, #translation-auto-missing td:last-child { text-align: right; }
#translation-auto-missing-updated { display: block; margin: 6px 0; }
</style>
<div id="translation-auto" data-endpoint="../management/translationauto.php"
     data-csrf="<?= htmlspecialchars($_SESSION['translationAutoCsrf'],ENT_QUOTES,'UTF-8') ?>"
     style="padding:15px;border:1px solid #000">
    <div class="translation-auto-controls">
    <button type="button" id="translation-auto-start" class="btn btn-warning" disabled>Translate missing texts</button>
    <div class="translation-auto-options">
    <span class="translation-auto-language-control">
    <label for="translation-auto-language">Language</label>
    <select id="translation-auto-language">
        <option value="">All configured languages</option>
        <?php foreach($translationAutoLanguages as $key=>$code): ?>
        <option value="<?= htmlspecialchars($key,ENT_QUOTES,'UTF-8') ?>"><?= htmlspecialchars($code,ENT_QUOTES,'UTF-8') ?></option>
        <?php endforeach; ?>
    </select>
    </span>
    <button type="button" id="translation-auto-reset" class="btn btn-warning" hidden disabled
            title="Discard pending work and choose languages again. Saved translations are kept.">Reset pending run</button>
    </div>
    </div>
    <details id="translation-auto-log">
        <summary title="Expand or collapse the translation log">
            <strong>Translation log</strong>
            <span id="translation-auto-log-state" role="status" aria-live="polite">Checking…</span>
            <span class="translation-auto-log-action translation-auto-log-show">Show details</span>
            <span class="translation-auto-log-action translation-auto-log-hide">Hide details</span>
        </summary>
        <div class="translation-auto-log-body" tabindex="0" aria-label="Translation log details">
            <details id="translation-auto-missing">
                <summary title="Expand or collapse missing translations by target language">Missing translations: <span id="translation-auto-missing-total">Checking…</span></summary>
                <table aria-label="Missing translations by target language">
                    <thead><tr><th scope="col">Target language</th><th scope="col">Empty entries</th></tr></thead>
                    <tbody id="translation-auto-missing-rows"></tbody>
                </table>
                <small id="translation-auto-missing-updated"></small>
            </details>
            <p id="translation-auto-status">Checking translation service…</p>
            <ul id="translation-auto-results"></ul>
        </div>
    </details>
    <small>Only empty translations are filled. Existing translations are preserved.
        <a href="">Refresh dictionary after completion</a></small>
</div>
<script src="../js/translationAutoManager.js?v=<?= (int)filemtime(__DIR__.'/../js/translationAutoManager.js') ?>" defer></script>
