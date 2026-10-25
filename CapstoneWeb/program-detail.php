<?php
// Program entry point: delegate queries and JSON preparation to the shared data layer and rendering to the shared template.
require_once __DIR__ . '/includes/show_detail.php';
$detailResult = loadServiceDetailJson(isset($_GET['service_id']) ? $_GET['service_id'] : null, 'Program');
http_response_code($detailResult['status']);
$detailJson = $detailResult['json'];
require __DIR__ . '/template/service_detail_template.php';
