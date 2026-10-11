<?php
$host = 'localhost';
$port = '3307'; // Keeping your configured port
$user = 'root';
$pass = 'usbw'; // USBWebServer's standard default password
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];



try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     echo "Successfully connected to USBWebServer MySQL on port 3307!";
} catch (\PDOException $e) {
     echo "Connection failed: " . $e->getMessage();
}

?>