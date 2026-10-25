<?php
// Read the announcement ID, retrieve JSON, and render the template. Retain compatibility with existing ?id= links.
require_once __DIR__ . '/includes/show_detail.php';
$rawId = isset($_GET['announcement_id']) ? $_GET['announcement_id'] : (isset($_GET['id']) ? $_GET['id'] : null);
$result = loadAnnouncementDetailJson($rawId);
http_response_code($result['status']);
$detailJson = $result['json'];
require __DIR__ . '/template/announcement_detail_template.php';
