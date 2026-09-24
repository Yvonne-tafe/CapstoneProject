<?php
/**
 * CityLink JSON Database Seeding Tool (compatible with PHP 5.4+).
 * Workflow: Read all JSON files from the json/ directory -> Map filename to table name -> Skip if table already contains data -> Insert JSON records if empty -> Halt immediately on error.
 */

// Allow access from localhost only
if (PHP_SAPI !== 'cli' && !in_array(isset($_SERVER['REMOTE_ADDR']) ?$_SERVER['REMOTE_ADDR'] : '', array('127.0.0.1', '::1'), true)) {
    http_response_code(403);
    exit('This setup page is available on localhost only.');
}

function citylinkEscape($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

if (PHP_SAPI !== 'cli') {
    session_start();
}

// Path to the json directory (located in the same folder as this script)
$jsonDir = __DIR__ . '/json';
$results = array();$error = null;
$complete = false;

// Generate CSRF token for the form
if (PHP_SAPI !== 'cli' && empty($_SESSION['database_seed_token'])) {$_SESSION['database_seed_token'] = bin2hex(openssl_random_pseudo_bytes(32));
}

$run = PHP_SAPI === 'cli' || (isset($_SERVER['REQUEST_METHOD']) &&$_SERVER['REQUEST_METHOD'] === 'POST');

if ($run) {
    try {
        // Validate CSRF token
        if (PHP_SAPI !== 'cli' && (!isset($_POST['token']) || !is_string($_POST['token']) || $_SESSION['database_seed_token'] !==$_POST['token'])) {
            throw new RuntimeException('Invalid form token. Please reload the page and try again.');
        }

        // Check if json directory exists
        if (!is_dir($jsonDir)) {
            throw new RuntimeException("Directory not found: {$jsonDir}");
        }

        // Scan for all .json files
        $files = glob($jsonDir . '/*.json');
        if (empty($files)) {
            throw new RuntimeException('No .json files found in the json/ directory.');
        }

        // Load database connection
        ob_start();
        try {
            require __DIR__ . '/../config/database.php';
        } catch (Exception $exception) {
            ob_end_clean();
            throw $exception;
        }
        ob_end_clean();

        if (!isset($pdo) || !($pdo instanceof PDO)) {
            throw new RuntimeException('Database connection failed. Please check config/database.php.');
        }

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
        // 連線設定目前只連到伺服器；本次匯入必須另外選定資料庫。
        $pdo->exec('USE `citylink`');

        // Process each JSON file
        foreach ($files as$filePath) {
            $tableName = pathinfo($filePath, PATHINFO_FILENAME);
            if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/D', $tableName)) {
                throw new RuntimeException('Invalid table filename: ' . basename($filePath));
            }

            // Check if the table exists
            // MySQL 5.6 不支援此處 SHOW TABLES LIKE 的原生參數綁定，改查 metadata。
            $checkTable =$pdo->prepare("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND LOWER(TABLE_NAME) = LOWER(:table) AND TABLE_TYPE = 'BASE TABLE'");
            $checkTable->execute(array(':table' =>$tableName));
            $existingTable = $checkTable->fetchColumn();
            $checkTable->closeCursor();
            if ($existingTable === false) {
                throw new RuntimeException("Table `{$tableName}` does not exist in the database (file: " . basename($filePath) . ").");
            }
            $tableName = $existingTable;

            // Check if the table already contains data
            $checkData =$pdo->query("SELECT 1 FROM `{$tableName}` LIMIT 1");
            $hasData = ($checkData->fetch() !== false);$checkData->closeCursor();

            if ($hasData) {
                // Skip table if data already exists
                $results[] = array(
                    'table' => $tableName,
                    'status' => 'SKIPPED',
                    'message' => "Table already contains data. Skipped."
                );
                continue;
            }

            // Read and decode JSON file
            $content = file_get_contents($filePath);
            if ($content === false) {
                throw new RuntimeException("Cannot read file: " . basename($filePath));
            }

            $rows = json_decode($content, true);
            if ($rows === null && json_last_error() !== JSON_ERROR_NONE) {
                // PHP 5.4 尚未提供 json_last_error_msg()，改顯示錯誤代碼。
                throw new RuntimeException("Failed to decode JSON (" . basename($filePath) . "), error code: " . json_last_error());
            }

            if (!is_array($rows) || (!empty($rows) && array_keys($rows) !== range(0, count($rows) - 1))) {
                throw new RuntimeException('JSON must contain an array of records: ' . basename($filePath));
            }

            if (empty($rows)) {$results[] = array(
                    'table' => $tableName,
                    'status' => 'EMPTY',
                    'message' => "JSON file contains no records. Skipped."
                );
                continue;
            }

            // Prepare dynamic insert statement based on JSON keys
            $firstRow = reset($rows);
            if (!is_array($firstRow) || empty($firstRow)) {
                throw new RuntimeException('Invalid JSON record: ' . basename($filePath));
            }
            $columns = array_keys($firstRow);
            foreach ($columns as $column) {
                if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/D', $column)) {
                    throw new RuntimeException('Invalid column name: ' . $column);
                }
            }
            foreach ($rows as $row) {
                if (!is_array($row) || count($row) !== count($columns) || array_diff($columns, array_keys($row))) {
                    throw new RuntimeException('All JSON records must have matching fields: ' . basename($filePath));
                }
                foreach ($row as $value) {
                    if (is_array($value) || is_object($value)) {
                        throw new RuntimeException('Nested JSON values are not supported: ' . basename($filePath));
                    }
                }
            }
            $colList = '`' . implode('`, `', $columns) . '`';
            $placeholders = ':' . implode(', :', $columns);

            $insertSql = "INSERT INTO `{$tableName}` ({$colList}) VALUES ({$placeholders})";
            $stmt = $pdo->prepare($insertSql);

            // Execute insert inside a transaction; rollback and abort on any error
            $pdo->beginTransaction();
            try {
                $count = 0;
                foreach ($rows as $row) {$stmt->execute($row);$count++;
                }
                $pdo->commit();

                $results[] = array(
                    'table' => $tableName,
                    'status' => 'INSERTED',
                    'message' => "Successfully inserted {$count} records."
                );
            } catch (Exception $e) {
                if ($pdo->inTransaction()) {$pdo->rollBack();
                }
                throw new RuntimeException("Error inserting into `{$tableName}`. Process halted: " . $e->getMessage());
            }
        }

        $complete = true;
    } catch (Exception $exception) {
        $error =$exception->getMessage();
    }
}

// Command-line execution mode (CLI)
if (PHP_SAPI === 'cli') {
    foreach ($results as$item) {
        echo "[{$item['status']}] {$item['table']}: {$item['message']}" . PHP_EOL;
    }
    echo ($complete ? 'All JSON files processed successfully.' : ('Halted with error: ' . $error)) . PHP_EOL;
    exit($complete ? 0 : 1);
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CityLink JSON Data Seeder</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; max-width: 900px; margin: 48px auto; padding: 0 24px; line-height: 1.6; color: #333; }
        button { padding: 12px 24px; font-size: 16px; font-weight: bold; background-color: #0b57d0; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #0842a0; }
        .badge { display: inline-block; padding: 2px 8px; font-size: 12px; font-weight: bold; border-radius: 4px; margin-right: 8px; }
        .badge-inserted { background: #e6f4ea; color: #137333; }
        .badge-skipped { background: #feefe3; color: #b06000; }
        .badge-empty { background: #f1f3f4; color: #5f6368; }
        .error { color: #d93025; font-weight: bold; padding: 12px; background: #fce8e6; border-radius: 4px; margin-top: 16px; }
        .success { color: #188038; font-weight: bold; padding: 12px; background: #e6f4ea; border-radius: 4px; margin-top: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #dadce0; padding: 10px 14px; text-align: left; }
        th { background: #f8f9fa; }
    </style>
</head>
<body>
    <h1>CityLink JSON Data Seeder</h1>
    <p>Reads all JSON files in the <code>json/</code> directory, matching filenames to database tables:</p>
    <ul>
        <li>If the table already contains data: <strong>Skip</strong>.</li>
        <li>If the table is empty: <strong>Insert JSON records</strong>.</li>
        <li>If an error occurs: <strong>Halt execution immediately and report error</strong>.</li>
    </ul>

    <form method="post">
        <input type="hidden" name="token" value="<?= citylinkEscape($_SESSION['database_seed_token']) ?>">
        <button type="submit">Run Seed</button>
    </form>

    <?php if ($complete): ?>
        <p class="success">✔ All JSON files checked and processed successfully!</p>
    <?php endif; ?>

    <?php if ($error !== null): ?>
        <div class="error">❌ Execution halted: <?= citylinkEscape($error) ?></div>
    <?php endif; ?>

    <?php if (!empty($results)): ?>
        <h2>Execution Log:</h2>
        <table>
            <thead>
                <tr>
                    <th style="width: 25%;">Table Name</th>
                    <th style="width: 25%;">Status</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as$res): ?>
                    <tr>
                        <td><strong><?= citylinkEscape($res['table']) ?></strong></td>
                        <td>
                            <?php if ($res['status'] === 'INSERTED'): ?>
                                <span class="badge badge-inserted">INSERTED</span>
                            <?php elseif ($res['status'] === 'SKIPPED'): ?>
                                <span class="badge badge-skipped">SKIPPED (EXISTS)</span>
                            <?php else: ?>
                                <span class="badge badge-empty">SKIPPED (EMPTY)</span>
                            <?php endif; ?>
                        </td>
                        <td><?= citylinkEscape($res['message']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
