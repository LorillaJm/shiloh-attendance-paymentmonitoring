<?php
// Simple health check
echo "OK - PHP is working\n";
echo "PHP Version: " . phpversion() . "\n";
echo "Extensions: " . implode(', ', get_loaded_extensions()) . "\n";

// Test database connection
try {
    $host = getenv('DB_HOST');
    $port = getenv('DB_PORT');
    $dbname = getenv('DB_DATABASE');
    $user = getenv('DB_USERNAME');
    $pass = getenv('DB_PASSWORD');
    
    echo "\nDatabase Config:\n";
    echo "Host: $host\n";
    echo "Port: $port\n";
    echo "Database: $dbname\n";
    echo "Username: $user\n";
    
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";
    $pdo = new PDO($dsn, $user, $pass);
    echo "\nDatabase: Connected ✓\n";
} catch (Exception $e) {
    echo "\nDatabase: Failed ✗\n";
    echo "Error: " . $e->getMessage() . "\n";
}
