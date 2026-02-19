<?php
/**
 * Test Database Connection
 * Run: php test-db-connection.php
 */

echo "🔍 Testing Database Connections...\n\n";

// Test configurations
$configs = [
    'Direct Connection (Port 5432)' => [
        'host' => 'db.lggzjlevfmqlqhqoinwh.supabase.co',
        'port' => '5432',
        'dbname' => 'postgres',
        'user' => 'postgres',
        'password' => '116161Shiloh2026',
        'sslmode' => 'require',
    ],
    'Pooler Connection (Port 6543)' => [
        'host' => 'aws-1-ap-southeast-1.pooler.supabase.com',
        'port' => '6543',
        'dbname' => 'postgres',
        'user' => 'postgres.lggzjlevfmqlqhqoinwh',
        'password' => '116161Shiloh2026',
        'sslmode' => 'require',
    ],
];

foreach ($configs as $name => $config) {
    echo "Testing: {$name}\n";
    echo str_repeat('-', 50) . "\n";
    
    $dsn = sprintf(
        "pgsql:host=%s;port=%s;dbname=%s;sslmode=%s",
        $config['host'],
        $config['port'],
        $config['dbname'],
        $config['sslmode']
    );
    
    echo "DSN: {$dsn}\n";
    echo "User: {$config['user']}\n";
    
    try {
        $startTime = microtime(true);
        $pdo = new PDO(
            $dsn,
            $config['user'],
            $config['password'],
            [
                PDO::ATTR_TIMEOUT => 10,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]
        );
        $endTime = microtime(true);
        $duration = round(($endTime - $startTime) * 1000, 2);
        
        echo "✅ SUCCESS! Connected in {$duration}ms\n";
        
        // Test query
        $stmt = $pdo->query('SELECT version()');
        $version = $stmt->fetchColumn();
        echo "PostgreSQL Version: " . substr($version, 0, 50) . "...\n";
        
        // Test table access
        $stmt = $pdo->query("SELECT COUNT(*) FROM users");
        $count = $stmt->fetchColumn();
        echo "Users table: {$count} records\n";
        
        echo "\n✨ Use this configuration in Render!\n";
        echo "DB_HOST={$config['host']}\n";
        echo "DB_PORT={$config['port']}\n";
        echo "DB_USERNAME={$config['user']}\n";
        
    } catch (PDOException $e) {
        echo "❌ FAILED: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

echo "💡 Recommendation:\n";
echo "Use whichever connection succeeded above.\n";
echo "Update your Render environment variables accordingly.\n";
