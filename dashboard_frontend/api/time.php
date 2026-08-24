<?php
echo json_encode([
    'utc' => gmdate('Y-m-d\TH:i:s.') . substr(microtime(), 2, 3) . 'Z',
]);