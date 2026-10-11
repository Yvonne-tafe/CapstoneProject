<?php
/**
 * CityLink 本地資料庫初始化工具（相容 PHP 5.4）。
 * 流程：讀取 sql/citylink.sql → 分割 SQL → 載入 PDO 連線 → 依序執行 → 顯示結果。
 * 瀏覽器開啟時先顯示表單，按下按鈕才執行；從命令列執行則直接開始。
 * 此檔案只供本地開發使用，不應部署到公開網站。
 */
// PHP_SAPI 判斷執行方式；瀏覽器請求只接受本機 IPv4 / IPv6 位址。
if (PHP_SAPI !== 'cli' && !in_array(isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '', array('127.0.0.1', '::1'), true)) {
    http_response_code(403);
    exit('This setup page is available on localhost only.');
}

/**
 * 將整份 SQL 文字轉成依原順序排列的指令陣列。
 * 不能直接用 explode(';', $sql)，因為字串或註解內也可能包含分號。
 * 此函式逐字追蹤引號與註解狀態，只在外部遇到分號時切割。
 * 支援一般 SQL，不支援 DELIMITER 或預存程序等需要自訂分隔符號的語法。
 *
 * @param string $sql SQL 檔案的完整內容。
 * @return array 每個元素是一筆 SQL 指令，不含結尾分號。
 * @throws RuntimeException 引號／區塊註解未結束，或遇到不支援的語法。
 */
function citylinkSqlStatements($sql)
{
    // 移除部分編輯器加在 UTF-8 檔案開頭的 BOM，避免干擾第一筆指令。
    $sql = preg_replace('/^\xEF\xBB\xBF/', '', $sql);
    // statements 保存完整指令；buffer 累積目前尚未結束的一筆指令。
    $statements = array();
    $buffer = '';
    // quote 記錄目前的引號；comment 記錄 line / block；null 表示不在其中。
    $quote = null;
    $comment = null;
    $length = strlen($sql);
    for ($i = 0; $i < $length; $i++) {
        $c = $sql[$i];
        $next = $i + 1 < $length ? $sql[$i + 1] : '';
        // 單行註解略過到換行；保留換行，避免前後 SQL 單字黏在一起。
        if ($comment === 'line') {
            if ($c === "\n") { $comment = null; $buffer .= "\n"; }
            continue;
        }
        // 區塊註解略過到結束符號，並跳過該符號的第二個字元。
        if ($comment === 'block') {
            if ($c === '*' && $next === '/') { $comment = null; $i++; $buffer .= ' '; }
            continue;
        }
        // 引號內的分號屬於內容；反斜線跳脫及連續兩個相同引號也要保留。
        if ($quote !== null) {
            $buffer .= $c;
            if ($c === '\\' && $next !== '') { $buffer .= $next; $i++; }
            elseif ($c === $quote) {
                if ($next === $quote) { $buffer .= $next; $i++; }
                else { $quote = null; }
            }
            continue;
        }
        // 單／雙引號表示字串，反引號用於 MySQL 的資料表或欄位名稱。
        if ($c === "'" || $c === '"' || $c === '`') { $quote = $c; $buffer .= $c; continue; }
        // MySQL 的 -- 註解需要後接空白或控制字元；# 也可以開始單行註解。
        if ($c === '#' || ($c === '-' && $next === '-' && ($i + 2 >= $length || ord($sql[$i + 2]) <= 32))) {
            $comment = 'line'; $buffer .= ' '; continue;
        }
        // 一般區塊註解可略過；可執行的特殊註解必須拒絕，避免漏掉 SQL。
        if ($c === '/' && $next === '*') {
            if (substr($sql, $i, 3) === '/*!' || substr($sql, $i, 4) === '/*M!') {
                throw new RuntimeException('Executable SQL comments are not supported. Use plain SQL statements.');
            }
            $comment = 'block'; $i++; continue;
        }
        // 到這裡代表不在引號或註解內：分號結束一筆指令，空指令不加入陣列。
        if ($c === ';') {
            if (trim($buffer) !== '') { $statements[] = trim($buffer); }
            $buffer = '';
        } else { $buffer .= $c; }
    }
    // 先檢查整份檔案是否完整，再接受沒有結尾分號的最後一筆指令。
    if ($quote !== null || $comment === 'block') { throw new RuntimeException('Unclosed SQL quote or block comment.'); }
    if (trim($buffer) !== '') { $statements[] = trim($buffer); }
    foreach ($statements as $statement) {
        if (preg_match('/^DELIMITER\b/im', $statement)) { throw new RuntimeException('DELIMITER / stored routines are not supported by this runner.'); }
    }
    return $statements;
}

// 顯示 SQL 摘要或錯誤訊息前，將 HTML 特殊字元轉義，避免被當成網頁標籤。
function citylinkEscape($value) { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); }

// 瀏覽器透過 session 保存表單驗證碼；必須在輸出 HTML 前啟動 session。
if (PHP_SAPI !== 'cli') { session_start(); }
// __DIR__ 是本 PHP 檔案所在目錄，固定路徑不受目前工作目錄影響。
// 本檔案已在 dev 目錄內，因此直接從 dev 下的 sql 資料夾讀取。
$sqlFile = __DIR__ . '/sql/citylink.sql';
// 分別保存成功紀錄、錯誤訊息，以及是否全部完成。
$results = array();
$error = null;
$complete = false;
// 隨機 token 隨表單提交並與 session 比對，降低外部網站偽造提交的風險。
if (PHP_SAPI !== 'cli' && empty($_SESSION['database_setup_token'])) {
    $_SESSION['database_setup_token'] = bin2hex(openssl_random_pseudo_bytes(32));
}
// GET 開啟頁面不執行 SQL；POST 提交表單或命令列呼叫才會執行。
$run = PHP_SAPI === 'cli' || (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST');
if ($run) {
    try {
        // 在任何資料庫操作之前，先確認瀏覽器提交的 token 正確。
        if (PHP_SAPI !== 'cli' && (!isset($_POST['token']) || !is_string($_POST['token']) || $_SESSION['database_setup_token'] !== $_POST['token'])) {
            throw new RuntimeException('Invalid form token. Reload the page and try again.');
        }
        // 先讀取並分割完整檔案，讀檔或解析失敗時不開始執行 SQL。
        if (!is_readable($sqlFile)) { throw new RuntimeException('Cannot read sql/citylink.sql.'); }
        $sql = file_get_contents($sqlFile);
        if ($sql === false) { throw new RuntimeException('Failed to read the SQL file.'); }
        $statements = citylinkSqlStatements($sql);
        if (!$statements) { throw new RuntimeException('The SQL file contains no statements.'); }
        // 載入共用連線設定，取得 $pdo。輸出緩衝會攔截並移除連線檔的 echo 訊息。
        // 即使載入時發生例外，也要清除緩衝，再交由外層 catch 處理。
        ob_start();
        // config 位於 dev 的上一層；__DIR__ 後需加 / 再接 .. 返回上一層。
        try { require __DIR__ . '/../config/database.php'; }
        catch (Exception $exception) { ob_end_clean(); throw $exception; }
        ob_end_clean();
        if (!isset($pdo) || !($pdo instanceof PDO)) { throw new RuntimeException('Database connection failed. Check config/database.php and USBWebServer.'); }
        // 讓 PDO 在 SQL 失敗時拋出例外，方便指出失敗的指令並停止。
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // 啟用 MySQL 結果緩衝，避免未讀完的查詢結果阻擋下一筆 SQL。
        $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
        // 分割器按 MySQL 一般反斜線跳脫方式解析，故先排除不相容的 SQL 模式。
        $modeQuery = $pdo->query('SELECT @@SESSION.sql_mode');
        $mode = $modeQuery->fetchColumn();
        $modeQuery->closeCursor();
        unset($modeQuery);
        if (strpos($mode, 'NO_BACKSLASH_ESCAPES') !== false) { throw new RuntimeException('This runner requires NO_BACKSLASH_ESCAPES to be disabled.'); }
        // 依檔案順序逐筆執行。CREATE TABLE 等 DDL 可能隱含提交，不能保證整批回復。
        foreach ($statements as $index => $statement) {
            // 陣列索引從 0 開始，畫面上的指令編號從 1 開始。
            $number = $index + 1;
            try {
                // 有資料列的查詢使用 query；建表、USE、SET、INSERT 等使用 exec。
                // 舊版 PDO MySQL 對沒有結果集的 DDL 使用 query 時可能出現 2014 錯誤。
                if (preg_match('/^\s*(SELECT|SHOW|DESCRIBE|DESC|EXPLAIN)\b/i', $statement)) {
                    $query = $pdo->query($statement);
                    // 讀完並釋放結果，再執行下一筆；此工具不顯示查詢資料列。
                    while ($query->fetch(PDO::FETCH_NUM) !== false) {}
                    $query->closeCursor();
                    unset($query);
                } else {
                    $pdo->exec($statement);
                }
                // 成功後記錄指令編號與前 120 bytes 摘要，將換行壓成空白以便閱讀。
                $results[] = 'Statement ' . $number . ' completed: ' . preg_replace('/\s+/', ' ', substr($statement, 0, 120));
            } catch (PDOException $exception) {
                // 拋到外層 catch 會離開迴圈；之前已成功的操作可能仍保留。
                throw new RuntimeException('Stopped at statement ' . $number . ': ' . $exception->getMessage());
            }
        }
        // 只有全部指令都成功執行，才標記完成。
        $complete = true;
    } catch (Exception $exception) { $error = $exception->getMessage(); }
}
// 命令列只輸出純文字，成功回傳退出碼 0，失敗回傳 1，不輸出下方 HTML。
if (PHP_SAPI === 'cli') {
    foreach ($results as $result) { echo $result . PHP_EOL; }
    echo ($complete ? 'SQL file completed successfully.' : $error) . PHP_EOL;
    exit($complete ? 0 : 1);
}
// 以下是瀏覽器畫面：顯示操作說明、POST 表單及執行結果。
?>
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>CityLink database setup</title>
<style>body{font-family:system-ui,sans-serif;max-width:900px;margin:48px auto;padding:0 24px;line-height:1.6}button{padding:12px 20px;cursor:pointer}li{margin:8px 0;overflow-wrap:anywhere}.error{color:#a32020}.success{color:#176735}</style></head>
<body>
<h1>CityLink database setup</h1>
<p>Read <code>sql/citylink.sql</code> and execute its statements in order using <code>../config/database.php</code>.</p>
<p>The supplied file creates the citylink database, missing tables and initial lookup records. It does not delete existing data or update existing table structures.</p>
<p>Execution stops on the first error. MySQL table creation is not rolled back: earlier successful statements may remain applied.</p>
<?php // 隱藏欄位帶回 session token；按鈕提交到目前這個 PHP 頁面。 ?>
<form method="post"><input type="hidden" name="token" value="<?= citylinkEscape($_SESSION['database_setup_token']) ?>"><button type="submit">Run SQL file</button></form>
<?php // 初次開啟時不顯示結果；執行後顯示完成／錯誤訊息與已成功的指令清單。 ?>
<?php if ($complete): ?><p class="success">SQL file completed successfully.</p><?php endif; ?>
<?php if ($error !== null): ?><p class="error"><?= citylinkEscape($error) ?></p><?php endif; ?>
<?php if ($results): ?><ol><?php foreach ($results as $result): ?><li><?= citylinkEscape($result) ?></li><?php endforeach; ?></ol><?php endif; ?>
</body></html>
