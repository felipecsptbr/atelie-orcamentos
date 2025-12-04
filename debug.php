<?php
echo "<h1>Debug Railway Environment</h1>";

echo "<h2>Railway Detection:</h2>";
echo "RAILWAY_ENVIRONMENT: " . (getenv('RAILWAY_ENVIRONMENT') ?: 'NOT SET') . "<br>";
echo "Is Railway: " . (getenv('RAILWAY_ENVIRONMENT') !== false ? 'YES' : 'NO') . "<br>";

echo "<h2>MySQL Variables:</h2>";
echo "MYSQLHOST: " . (getenv('MYSQLHOST') ?: 'NOT SET') . "<br>";
echo "MYSQLPORT: " . (getenv('MYSQLPORT') ?: 'NOT SET') . "<br>";
echo "MYSQLDATABASE: " . (getenv('MYSQLDATABASE') ?: 'NOT SET') . "<br>";
echo "MYSQLUSER: " . (getenv('MYSQLUSER') ?: 'NOT SET') . "<br>";
echo "MYSQLPASSWORD: " . (getenv('MYSQLPASSWORD') ? '***SET***' : 'NOT SET') . "<br>";

echo "<h2>All Environment Variables:</h2>";
echo "<pre>";
$env = getenv();
ksort($env);
foreach($env as $key => $value) {
    if (strpos($key, 'MYSQL') !== false || strpos($key, 'RAILWAY') !== false) {
        if (strpos(strtolower($key), 'password') !== false) {
            echo "$key = ***HIDDEN***\n";
        } else {
            echo "$key = $value\n";
        }
    }
}
echo "</pre>";

echo "<h2>Config Constants (after require):</h2>";
require_once __DIR__ . '/config/config.php';
echo "DB_HOST: " . DB_HOST . "<br>";
echo "DB_PORT: " . (defined('DB_PORT') ? DB_PORT : 'NOT DEFINED') . "<br>";
echo "DB_NAME: " . DB_NAME . "<br>";
echo "DB_USER: " . DB_USER . "<br>";
echo "DB_PASS: " . (DB_PASS ? '***SET***' : 'EMPTY') . "<br>";

echo "<h2>Connection Test:</h2>";
try {
    $dsn = "mysql:host=" . DB_HOST;
    if (defined('DB_PORT')) {
        $dsn .= ";port=" . DB_PORT;
    }
    $dsn .= ";dbname=" . DB_NAME;
    
    echo "DSN: $dsn<br>";
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    echo "<span style='color:green'>✓ Conexão bem-sucedida!</span>";
} catch(PDOException $e) {
    echo "<span style='color:red'>✗ Erro: " . $e->getMessage() . "</span>";
}
