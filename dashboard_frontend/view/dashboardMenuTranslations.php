<?php
// Included only by the six translated EC/Gea views. No queries or writes here.
if (($orgMenuVisible ?? 'no') === 'yes' && ($langSelectorVisible ?? 'no') === 'yes' && isset($dashId)) {
?>
<script>
$(function () {
    if (typeof initializeDashboardMenuTranslations === 'function') {
        initializeDashboardMenuTranslations(<?php echo json_encode((string) $dashId, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>, true);
    }
});
</script>
<?php
}
