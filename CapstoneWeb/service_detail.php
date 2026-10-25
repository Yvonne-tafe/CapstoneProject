<?php
// Controller: read URL parameters, retrieve JSON, set the status code, and render the template.
require_once __DIR__ . '/includes/show_detail.php';
$detailResult = loadServiceDetailJson(isset($_GET['service_id']) ? $_GET['service_id'] : null);
http_response_code($detailResult['status']);
$detailJson = $detailResult['json'];
require __DIR__ . '/template/service_detail_template.php';
