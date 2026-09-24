<?php
/** 本地工具：依 template 的 data-* 標記產生服務詳情快照，相容 PHP 5.4。 */
if (!in_array(isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '', array('127.0.0.1', '::1'), true)) {
    http_response_code(403); exit('Localhost only.');
}
session_start();
require_once __DIR__ . '/../layout.php';
function gdEscape($text) { return htmlspecialchars($text, ENT_QUOTES, 'UTF-8'); }
function gdText($node, $text) {
    while ($node->firstChild) { $node->removeChild($node->firstChild); }
    $node->appendChild($node->ownerDocument->createTextNode((string) $text));
}
// 沿祖先找來源或內容 key，讓 figure / aside 內的子元素也能對應同一筆資料。
function gdAttribute($node, $name) {
    while ($node instanceof DOMElement) {
        if ($node->hasAttribute($name)) { return $node->getAttribute($name); }
        $node = $node->parentNode;
    }
    return '';
}
function gdRender($service, $blocks, $template) {
    $html = preg_replace('/<\?php[\s\S]*?\?>/', '', $template);
    $dom = new DOMDocument('1.0', 'UTF-8');
    $old = libxml_use_internal_errors(true);
    $dom->loadHTML('<?xml encoding="UTF-8"><html><body>' . $html . '</body></html>');
    libxml_clear_errors(); libxml_use_internal_errors($old);
    $xp = new DOMXPath($dom);
    // 清除版型製作說明，保留 HTML 結構與 CSS class。
    $comments = array(); foreach ($xp->query('//comment()') as $n) { $comments[] = $n; }
    foreach ($comments as $n) { $n->parentNode->removeChild($n); }
    $article = $xp->query('//article')->item(0);
    if (!$article) { throw new RuntimeException('Template must contain an article.'); }
    $article->setAttribute('data-service-id', $service['service_id']);
    $matched = array();
    foreach ($xp->query('//*[@data-content-key]') as $node) {
        $key = $node->getAttribute('data-content-key');
        if (isset($blocks[$key])) { $node->removeAttribute('hidden'); $matched[$key] = true; }
        else { $node->setAttribute('hidden', 'hidden'); }
    }
    foreach ($xp->query('//*[@data-field]') as $node) {
        $field = $node->getAttribute('data-field');
        $key = gdAttribute($node, 'data-content-key');
        $source = gdAttribute($node, 'data-source');
        if ($key !== '') {
            if (!isset($blocks[$key])) { continue; }
            $data = $blocks[$key];
        } elseif ($source === 'ServiceSession') {
            // 此工具產生服務內容頁，尚未提供場次選擇；不顯示假的日期或預約按鈕。
            $node->setAttribute('hidden', 'hidden'); continue;
        } else { $data = $service; }
        if ($field === 'service_fee,service_fee_basis') {
            $value = $service['service_fee'] === null ? 'Contact us for pricing' : ((float) $service['service_fee'] == 0 ? 'Free' : '$' . number_format($service['service_fee'], 2) . ' ' . $service['service_fee_basis']);
        } else { $value = isset($data[$field]) ? $data[$field] : ''; }
        if ($field === 'content_items') {
            $items = json_decode($value, true);
            if (!is_array($items)) { throw new RuntimeException('Invalid content_items for service ' . $service['service_id']); }
            gdText($node, '');
            foreach ($items as $item) {
                if (!is_string($item)) { throw new RuntimeException('List items must be strings.'); }
                $li = $dom->createElement('li'); gdText($li, $item); $node->appendChild($li);
            }
        } elseif ($field === 'image_url') {
            // 僅接受網站相對路徑或 http(s) 圖片，拒絕其他協定。
            if ($value === '' || preg_match('/[\x00-\x20\\\\]/', $value) || (strpos($value, ':') !== false && !preg_match('~^https?://~i', $value)) || strpos($value, '//') === 0) {
                $node->parentNode->setAttribute('hidden', 'hidden'); continue;
            }
            $node->setAttribute('src', $value);
            $node->setAttribute('alt', isset($data['image_alt_text']) ? $data['image_alt_text'] : '');
        } else {
            gdText($node, $value);
            if ($node->hasAttribute('data-link-prefix') && $value !== '') {
                $node->setAttribute('href', $node->getAttribute('data-link-prefix') . rawurlencode($value));
            }
            if ($value === '') {
                $node->setAttribute('hidden', 'hidden');
                if ($node->tagName === 'a' && $node->parentNode->nodeName === 'p') { $node->parentNode->setAttribute('hidden', 'hidden'); }
            }
        }
    }
    // 日期、時間未載入場次時，改顯示服務提供的文字時程。
    foreach ($xp->query('//*[contains(concat(" ",normalize-space(@class)," ")," announcement-meta ")]/div[position() < 3]') as $node) { $node->setAttribute('hidden', 'hidden'); }
    $meta = $xp->query('//dl')->item(0);
    if ($meta && !empty($service['service_session_details'])) {
        $div = $dom->createElement('div'); $dt = $dom->createElement('dt', 'Schedule');
        $dd = $dom->createElement('dd'); gdText($dd, $service['service_session_details']);
        $div->appendChild($dt); $div->appendChild($dd); $meta->appendChild($div);
    }
    // 不認識的 content_key 明確報錯，避免產生看似成功卻漏掉內容的頁面。
    foreach ($blocks as $key => $block) {
        if (isset($matched[$key])) { continue; }
        throw new RuntimeException('Template has no matching content_key: ' . $key . '. Add a matching template section before generating.');
    }
    ob_start();
    showHeader($service['service_title'], 'events');
    $head = ob_get_clean();
    // 新頁面位於 detail-page，base 讓共用 CSS、JS、圖片與導覽仍從網站根目錄載入。
    $head = str_replace('<head>', '<head>' . "\n" . '<base href="../">', $head);
    ob_start(); showBreadcrumb(array('Events' => './events.php', $service['service_title'] => null)); $crumb = ob_get_clean();
    ob_start(); showFooter(); $foot = ob_get_clean();
    return $head . $crumb . $dom->saveHTML($article) . $foot;
}
// 比對確認畫面出現時的檔案狀態，避免覆蓋等待確認期間才被修改的檔案。
function gdFileState($path) {
    if (is_link($path) || (file_exists($path) && !is_file($path))) { throw new RuntimeException('Output path is not a regular file.'); }
    if (!file_exists($path)) { return null; }
    $hash = hash_file('sha256', $path);
    if ($hash === false) { throw new RuntimeException('Cannot read existing output file.'); }
    return $hash;
}
function gdWriteJobs($jobs, $dir, $action, &$results, &$skipped) {
    // 先檢查整批，再開始寫入。
    foreach ($jobs as $job) {
        if (gdFileState($dir . '/' . $job['name']) !== $job['state']) {
            throw new RuntimeException('Output files changed. Generate again to review the latest conflicts.');
        }
    }
    foreach ($jobs as $job) {
        if ($action === 'skip' && $job['state'] !== null) { $skipped[] = $job['name']; continue; }
        $path = $dir . '/' . $job['name'];
        if ($job['state'] !== null) {
            if ($action !== 'overwrite') { throw new RuntimeException('Overwrite confirmation required.'); }
            // 先完整寫入暫存檔，再替換舊檔；寫入失敗時保留原檔案。
            $tmp = tempnam($dir, '.details-');
            if ($tmp === false) { throw new RuntimeException('Cannot create temporary output.'); }
            if (file_put_contents($tmp, $job['code']) !== strlen($job['code'])) {
                unlink($tmp); throw new RuntimeException('Incomplete file write. Original file retained.');
            }
            if (gdFileState($path) !== $job['state'] || !rename($tmp, $path)) {
                unlink($tmp); throw new RuntimeException('Cannot replace ' . $job['name']);
            }
        } else {
            $handle = fopen($path, 'x');
            if (!$handle) { throw new RuntimeException('Cannot create ' . $job['name']); }
            $written = fwrite($handle, $job['code']); fclose($handle);
            if ($written !== strlen($job['code'])) { unlink($path); throw new RuntimeException('Incomplete file write.'); }
        }
        $results[] = $job['name'];
    }
}
$error = null; $results = array(); $services = array(); $skipped = array(); $confirmation = null; $notice = null;
try {
    if (empty($_SESSION['generate_details_token'])) { $_SESSION['generate_details_token'] = bin2hex(openssl_random_pseudo_bytes(32)); }
    ob_start();
    try { require __DIR__ . '/../config/database.php'; }
    catch (Exception $e) { ob_end_clean(); throw $e; }
    ob_end_clean();
    if (!isset($pdo) || !($pdo instanceof PDO)) { throw new RuntimeException('Database connection failed.'); }
    $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true); $pdo->exec('USE citylink');
    $q = $pdo->query('SELECT s.* FROM Service s JOIN ServiceType t ON t.service_type_id=s.service_type_id WHERE s.active_status_id=1 AND t.active_status_id=1 ORDER BY s.service_id');
    $services = $q->fetchAll(PDO::FETCH_ASSOC); $q->closeCursor();
    if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['token']) || !is_string($_POST['token']) || $_POST['token'] !== $_SESSION['generate_details_token']) { throw new RuntimeException('Invalid form token. Reload this page.'); }
        if (isset($_POST['conflict_action'])) {
            $action = $_POST['conflict_action'];
            if (!is_string($action) || !in_array($action, array('skip', 'cancel', 'overwrite'), true)) { throw new RuntimeException('Invalid conflict action.'); }
            if (empty($_SESSION['details_pending']) || !isset($_POST['batch']) || !is_string($_POST['batch']) || $_POST['batch'] !== $_SESSION['details_pending']['id']) {
                throw new RuntimeException('Confirmation expired. Generate again.');
            }
            $pending = $_SESSION['details_pending'];
            // 確認只使用一次，重新整理 POST 不會再覆蓋一次。
            unset($_SESSION['details_pending']);
            if ($action === 'cancel') { $notice = 'Cancelled. No pages were created or changed.'; }
            else {
                gdWriteJobs($pending['jobs'], __DIR__ . '/../detail-page', $action, $results, $skipped);
                $notice = 'Completed: ' . count($results) . ' saved, ' . count($skipped) . ' skipped.';
            }
        } else {
        unset($_SESSION['details_pending']);
        $choice = isset($_POST['service']) && is_string($_POST['service']) ? $_POST['service'] : '';
        $targets = array(); foreach ($services as $s) { if ($choice === 'all' || $choice === (string) $s['service_id']) { $targets[] = $s; } }
        if (!$targets) { throw new RuntimeException('Select a service.'); }
        $template = file_get_contents(__DIR__ . '/../template/event-detail-template.php');
        if ($template === false) { throw new RuntimeException('Cannot read template.'); }
        $dir = __DIR__ . '/../detail-page';
        if (!is_dir($dir) && !mkdir($dir, 0775, true)) { throw new RuntimeException('Cannot create detail-page folder.'); }
        $jobs = array(); $names = array();
        foreach ($targets as $service) {
            // Windows 禁用字元改為底線；保留標題的空格及 Unicode，避免路徑穿越。
            $title = preg_replace('/[<>:"\/\\\\|?*\x00-\x1F]/u', '_', $service['service_title']);
            $title = trim($title, " .\t\r\n");
            if ($title === '' || strlen($title) > 180) { throw new RuntimeException('Service title is empty or too long for a filename.'); }
            $filename = 'details' . $title . '.php';
            $key = strtolower($filename);
            if (isset($names[$key])) { throw new RuntimeException('Duplicate output filename: ' . $filename); }
            $names[$key] = true;
            $state = gdFileState($dir . '/' . $filename);
            if ($state !== null && $choice !== 'all') { throw new RuntimeException('File already exists; no overwrite: ' . $filename); }
            $q = $pdo->prepare('SELECT * FROM ServiceWebContent WHERE service_id=:id ORDER BY display_order,service_web_content_id');
            $q->execute(array(':id' => $service['service_id'])); $blocks = array();
            foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $block) { $blocks[$block['content_key']] = $block; } $q->closeCursor();
            if (!$blocks) { throw new RuntimeException('No ServiceWebContent for ' . $service['service_title']); }
            $html = gdRender($service, $blocks, $template);
            // HTML 以 PHP 字串輸出，資料內即使包含 PHP 標記也不會被當成程式執行。
            $code = "<?php\n// Generated service detail snapshot. Regenerate after changing database content.\necho " . var_export($html, true) . ";\n";
            $jobs[] = array('name' => $filename, 'code' => $code, 'state' => $state);
        }
        $conflicts = array();
        foreach ($jobs as $job) { if ($job['state'] !== null) { $conflicts[] = $job['name']; } }
        if ($choice === 'all' && $conflicts) {
            // 所有內容準備好後才顯示確認；此時沒有寫入任何正式頁面。
            $confirmation = array('id' => bin2hex(openssl_random_pseudo_bytes(32)), 'jobs' => $jobs, 'conflicts' => $conflicts);
            $_SESSION['details_pending'] = $confirmation;
        } else {
            gdWriteJobs($jobs, $dir, 'skip', $results, $skipped);
        }
        }
    }
} catch (Exception $e) { $error = $e->getMessage(); }
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Generate detail pages</title>
<style>body{font:16px/1.6 system-ui,sans-serif;max-width:850px;margin:40px auto;padding:0 24px}select,button{font:inherit;padding:10px;max-width:100%}.error{color:#a21c1c}label{display:block}li{overflow-wrap:anywhere}</style></head><body>
<h1>Generate service detail pages</h1><p>Fill the event detail template with Service and ServiceWebContent data. Save PHP snapshots in <code>detail-page/</code>. For All active services, existing filenames require confirmation before any pages are saved.</p>
<p>Pages keep the shared CSS and JavaScript. Database changes require generating a new snapshot. Session booking is not enabled by this tool.</p>
<?php if ($error): ?><p class="error" role="alert"><?= gdEscape($error) ?></p><?php endif; ?>
<?php if ($notice): ?><p role="status"><?= gdEscape($notice) ?></p><?php endif; ?>
<?php if ($confirmation): ?>
<section aria-labelledby="conflict-heading"><h2 id="conflict-heading">Existing files found</h2>
<p>No pages have been saved yet. Choose one action for all existing files below.</p>
<ul><?php foreach ($confirmation['conflicts'] as $file): ?><li><?= gdEscape($file) ?></li><?php endforeach; ?></ul>
<form method="post">
<input type="hidden" name="token" value="<?= gdEscape($_SESSION['generate_details_token']) ?>">
<input type="hidden" name="batch" value="<?= gdEscape($confirmation['id']) ?>">
<p><strong>Skip:</strong> keep existing files and create only missing pages.<br><strong>Cancel:</strong> cancel the entire batch.<br><strong>Overwrite:</strong> replace listed files and create missing pages.</p>
<button type="submit" name="conflict_action" value="skip">Skip existing</button>
<button type="submit" name="conflict_action" value="cancel">Cancel</button>
<button type="submit" name="conflict_action" value="overwrite">Overwrite existing</button>
</form></section>
<?php else: ?>
<form method="post"><input type="hidden" name="token" value="<?= gdEscape(isset($_SESSION['generate_details_token']) ? $_SESSION['generate_details_token'] : '') ?>">
<label for="service">Service</label><select id="service" name="service" required><option value="">Choose a service</option><option value="all">All active services</option>
<?php foreach ($services as $s): ?><option value="<?= gdEscape($s['service_id']) ?>"><?= gdEscape($s['service_title']) ?></option><?php endforeach; ?></select>
<button type="submit">Generate PHP pages</button></form>
<?php endif; ?>
<?php if ($results): ?><h2>Saved pages</h2><ul><?php foreach ($results as $file): ?><li><a href="../detail-page/<?= rawurlencode($file) ?>"><?= gdEscape($file) ?></a></li><?php endforeach; ?></ul><?php endif; ?>
<?php if ($skipped): ?><h2>Skipped pages</h2><ul><?php foreach ($skipped as $file): ?><li><?= gdEscape($file) ?></li><?php endforeach; ?></ul><?php endif; ?>
</body></html>
