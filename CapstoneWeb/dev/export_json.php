<?php
/**
 * 本地資料表 JSON 匯出工具，相容 PHP 5.4。
 * GET 顯示下拉選單；POST 驗證選擇後，將資料下載為 JSON，不修改資料庫。
 */
if (PHP_SAPI !== 'cli' && !in_array(isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '', array('127.0.0.1', '::1'), true)) {
    http_response_code(403);
    exit('This export page is available on localhost only.');
}
session_start();
function citylinkExportEscape($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
$tables = array();
$selected = '';
$error = null;
try {
    if (empty($_SESSION['database_export_token'])) {
        $bytes = openssl_random_pseudo_bytes(32, $strong);
        if ($bytes === false || !$strong) {
            throw new RuntimeException('Unable to generate a form token.');
        }
        $_SESSION['database_export_token'] = bin2hex($bytes);
    }

    // 攔截連線設定檔的成功訊息，避免它混入下載的 JSON 或提前送出 HTTP 標頭。
    ob_start();
    try { require __DIR__ . '/../config/database.php'; }
    catch (Exception $exception) { ob_end_clean(); throw $exception; }
    ob_end_clean();
    if (!isset($pdo) || !($pdo instanceof PDO)) {
        throw new RuntimeException('Cannot connect to MySQL. Check USBWebServer and config/database.php.');
    }
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
    $pdo->exec('USE `citylink`');
    $pdo->exec("SET time_zone = '+08:00'");

    // 僅列出 citylink 中實際存在的資料表，並用這份清單驗證 POST 的表名。
    $query = $pdo->query("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_TYPE = 'BASE TABLE' ORDER BY TABLE_NAME");
    $tables = $query->fetchAll(PDO::FETCH_COLUMN);
    $query->closeCursor();

    if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['token']) || !is_string($_POST['token']) || $_POST['token'] !== $_SESSION['database_export_token']) {
            throw new RuntimeException('Invalid form token. Reload the page and try again.');
        }
        $selected = isset($_POST['table']) && is_string($_POST['table']) ? $_POST['table'] : '';
        if (!in_array($selected, $tables, true)) {
            throw new RuntimeException('Please select an existing table.');
        }

        // SQL 表名不能用參數綁定：先比對允許清單，再跳脫反引號。
        $quotedTable = '`' . str_replace('`', '``', $selected) . '`';
        // 依主鍵排序，讓多次匯出的順序穩定，方便比較 JSON 差異。
        $query = $pdo->query('SHOW KEYS FROM ' . $quotedTable . " WHERE Key_name = 'PRIMARY'");
        $keys = $query->fetchAll(PDO::FETCH_ASSOC);
        $query->closeCursor();
        $order = array();
        foreach ($keys as $key) {
            $order[(int) $key['Seq_in_index']] = '`' . str_replace('`', '``', $key['Column_name']) . '`';
        }
        ksort($order);
        $sql = 'SELECT * FROM ' . $quotedTable;
        if ($order) { $sql .= ' ORDER BY ' . implode(', ', $order); }
        $query = $pdo->query($sql);
        $rows = $query->fetchAll(PDO::FETCH_ASSOC);
        $query->closeCursor();

        // 保留全部欄位（含 ID）、NULL 與字串；空表輸出 []。
        // 不使用 JSON_NUMERIC_CHECK，以免電話前導零或字串數字被改寫。
        // TEXT 內的 JSON（例如 content_items）保持字串，以相容 seed_json.php。
        $json = json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            throw new RuntimeException('JSON encoding failed. Error code: ' . json_last_error());
        }
        $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($selected)) . '.json';
        session_write_close();
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-store');
        header('X-Content-Type-Options: nosniff');
        echo $json . "\n";
        exit;
    }
} catch (Exception $exception) {
    $error = $exception->getMessage();
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CityLink JSON Export</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 760px; margin: 48px auto; padding: 0 24px; line-height: 1.6; color: #263238; }
        label { display: block; font-weight: 600; margin-bottom: 8px; }
        select { padding: 10px; min-width: 240px; max-width: 100%; font: inherit; }
        button { padding: 11px 20px; margin: 12px 0; font: inherit; color: white; background: #0b57d0; border: 0; border-radius: 4px; cursor: pointer; }
        button:disabled { opacity: .5; cursor: default; }
        .error { color: #a32121; background: #fff0f0; padding: 12px; overflow-wrap: anywhere; }
    </style>
</head>
<body>
    <h1>CityLink JSON Export</h1>
    <p>Select a table from <strong>citylink</strong> and download its records as a JSON file.</p>
    <p>The export includes all columns, including IDs. Empty tables produce <code>[]</code>. Existing files and database records are not changed.</p>
    <?php if ($error !== null): ?><p class="error" role="alert"><?= citylinkExportEscape($error) ?></p><?php endif; ?>
    <form method="post">
        <input type="hidden" name="token" value="<?= citylinkExportEscape(isset($_SESSION['database_export_token']) ? $_SESSION['database_export_token'] : '') ?>">
        <label for="table">Database table</label>
        <select id="table" name="table" required>
            <option value="">Choose a table</option>
            <?php foreach ($tables as $table): ?>
                <option value="<?= citylinkExportEscape($table) ?>"<?= $selected === $table ? ' selected' : '' ?>><?= citylinkExportEscape($table) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit"<?= empty($tables) ? ' disabled' : '' ?>>Download JSON</button>
    </form>
</body>
</html>
