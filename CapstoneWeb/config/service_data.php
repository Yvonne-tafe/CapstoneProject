<?php
// Shared service page data and rendering helpers, compatible with PHP 5.4.
function serviceEscape($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
function serviceDatabase() {
    // The existing connection file echoes output; capture it to prevent public output or interference with HTTP status codes.
    ob_start();
    try { require __DIR__ . '/database.php'; }
    catch (Exception $e) { ob_end_clean(); throw $e; }
    ob_end_clean();
    if (!isset($pdo) || !($pdo instanceof PDO)) { throw new RuntimeException('Database unavailable.'); }
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
    $pdo->exec('USE `citylink`');
    $pdo->exec("SET time_zone = '+08:00'");
    return $pdo;
}
function servicePositiveInt($value) {
    if (!is_string($value) || !preg_match('/^[1-9][0-9]*$/D', $value)) { return false; }
    return filter_var($value, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => 2147483647)));
}
function serviceFee($service) {
    if ($service['service_fee'] === null) { return 'Contact us for pricing'; }
    if ((float) $service['service_fee'] == 0) { return 'Free'; }
    return '$' . number_format((float) $service['service_fee'], 2) . (!empty($service['service_fee_basis']) ? ' ' . $service['service_fee_basis'] : '');
}
function serviceImageUrl($value) {
    $url = trim((string) $value);
    if ($url === '' || preg_match('/[\x00-\x20\\\\]/', $url) || strpos($url, '//') === 0) { return ''; }
    if (strpos($url, ':') !== false && !preg_match('~^https?://~i', $url)) { return ''; }
    return $url;
}
// Render each content block by content_type so new content_key values can also be displayed.
function renderServiceBlock($block) {
    $type = $block['content_type'];
    $heading = isset($block['content_heading']) ? $block['content_heading'] : '';
    $key = $block['content_key'];
    if ($type === 'image') {
        $url = serviceImageUrl($block['image_url']);
        if ($url === '') { return; }
        echo '<figure class="announcement-feature-image" data-content-key="' . serviceEscape($key) . '">';
        echo '<img src="' . serviceEscape($url) . '" alt="' . serviceEscape($block['image_alt_text']) . '">';
        if (!empty($block['image_caption'])) { echo '<figcaption>' . serviceEscape($block['image_caption']) . '</figcaption>'; }
        echo '</figure>'; return;
    }
    if ($type !== 'paragraph' && $type !== 'list') { return; }
    $items = array();
    if ($type === 'list') {
        $items = json_decode($block['content_items'], true);
        if (!is_array($items)) { return; }
        foreach ($items as $item) { if (!is_string($item)) { return; } }
    }
    $tag = $key === 'accessibility' ? 'aside' : 'section';
    echo '<' . $tag . ($key === 'accessibility' ? ' class="announcement-notice"' : '') . ' data-content-key="' . serviceEscape($key) . '">';
    if ($heading !== '') { echo '<h2>' . serviceEscape($heading) . '</h2>'; }
    if ($type === 'paragraph') { echo '<p>' . nl2br(serviceEscape($block['content_body'])) . '</p>'; }
    else {
        echo '<ul>'; foreach ($items as $item) { echo '<li>' . serviceEscape($item) . '</li>'; } echo '</ul>';
    }
    echo '</' . $tag . '>';
}
