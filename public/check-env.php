<?php
// Check environment configuration
header('Content-Type: text/plain');

echo "=== Environment Check ===\n\n";

echo "APP_URL: " . ($_ENV['APP_URL'] ?? 'NOT SET') . "\n";
echo "ASSET_URL: " . ($_ENV['ASSET_URL'] ?? 'NOT SET') . "\n";
echo "TRUSTED_PROXIES: " . ($_ENV['TRUSTED_PROXIES'] ?? 'NOT SET') . "\n";
echo "APP_DEBUG: " . ($_ENV['APP_DEBUG'] ?? 'NOT SET') . "\n";
echo "LOG_CHANNEL: " . ($_ENV['LOG_CHANNEL'] ?? 'NOT SET') . "\n";
echo "SESSION_DRIVER: " . ($_ENV['SESSION_DRIVER'] ?? 'NOT SET') . "\n";
echo "CACHE_DRIVER: " . ($_ENV['CACHE_DRIVER'] ?? 'NOT SET') . "\n";
echo "DB_CONNECTION: " . ($_ENV['DB_CONNECTION'] ?? 'NOT SET') . "\n";

echo "\n=== Request Info ===\n\n";
echo "Request Scheme: " . ($_SERVER['REQUEST_SCHEME'] ?? 'unknown') . "\n";
echo "HTTPS: " . ($_SERVER['HTTPS'] ?? 'not set') . "\n";
echo "HTTP_X_FORWARDED_PROTO: " . ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'not set') . "\n";
echo "HTTP_X_FORWARDED_FOR: " . ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? 'not set') . "\n";
echo "SERVER_NAME: " . ($_SERVER['SERVER_NAME'] ?? 'unknown') . "\n";
echo "SERVER_PORT: " . ($_SERVER['SERVER_PORT'] ?? 'unknown') . "\n";

echo "\n=== Laravel URL Generation ===\n\n";
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

echo "url('/'): " . url('/') . "\n";
echo "asset('test.js'): " . asset('test.js') . "\n";
echo "secure_url('/'): " . secure_url('/') . "\n";

echo "\n=== Storage Permissions ===\n\n";
$storagePath = __DIR__.'/../storage/framework/sessions';
echo "Sessions dir exists: " . (is_dir($storagePath) ? 'YES' : 'NO') . "\n";
echo "Sessions dir writable: " . (is_writable($storagePath) ? 'YES' : 'NO') . "\n";

$cachePath = __DIR__.'/../storage/framework/cache';
echo "Cache dir exists: " . (is_dir($cachePath) ? 'YES' : 'NO') . "\n";
echo "Cache dir writable: " . (is_writable($cachePath) ? 'YES' : 'NO') . "\n";

echo "\n=== Database Connection ===\n\n";
try {
    $pdo = new PDO(
        'pgsql:host=' . ($_ENV['DB_HOST'] ?? 'localhost') . ';port=' . ($_ENV['DB_PORT'] ?? '5432') . ';dbname=' . ($_ENV['DB_DATABASE'] ?? 'postgres'),
        $_ENV['DB_USERNAME'] ?? 'postgres',
        $_ENV['DB_PASSWORD'] ?? ''
    );
    echo "Database connection: SUCCESS\n";
} catch (Exception $e) {
    echo "Database connection: FAILED - " . $e->getMessage() . "\n";
}
