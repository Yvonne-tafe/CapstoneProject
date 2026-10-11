<?php
// 控制器：接收網址參數，取得 JSON，設定狀態碼，再交給版型呈現。
require_once __DIR__ . '/includes/show_detail.php';
$detailResult = loadServiceDetailJson(isset($_GET['service_id']) ? $_GET['service_id'] : null);
http_response_code($detailResult['status']);
$detailJson = $detailResult['json'];
require __DIR__ . '/template/service_detail_template.php';
