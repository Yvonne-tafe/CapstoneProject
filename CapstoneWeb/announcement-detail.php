<?php
// 接收公告 ID，取得 JSON，再交由模板呈現。保留舊的 ?id= 連結相容性。
require_once __DIR__ . '/includes/show_detail.php';
$rawId = isset($_GET['announcement_id']) ? $_GET['announcement_id'] : (isset($_GET['id']) ? $_GET['id'] : null);
$result = loadAnnouncementDetailJson($rawId);
http_response_code($result['status']);
$detailJson = $result['json'];
require __DIR__ . '/template/announcement_detail_template.php';
