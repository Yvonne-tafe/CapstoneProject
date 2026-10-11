<?php
// 活動入口：資料查詢與 JSON 整理交給共用資料層，版面交給共用 template。
require_once __DIR__ . '/includes/show_detail.php';
$detailResult = loadServiceDetailJson(isset($_GET['service_id']) ? $_GET['service_id'] : null, 'Event');
http_response_code($detailResult['status']);
$detailJson = $detailResult['json'];
require __DIR__ . '/template/service_detail_template.php';
