<?php
// Test different connection methods
$configs = [
    [
        'name' => 'Direct Connection (Port 5432)',
        'host' => 'db.lggzjlevfmqlqhqoinwh.supabase.co',
        'port' => 5432
    ],
    [
        'name' => 'Pooler Connection (Port 6543)', 
        'host' => 'aws-1-ap-southeast-1.pooler.supabase.com',
        'port' => 6543
    ],
    [
        'name' => 'Alternative Pooler Format',
        'host' => 'aws-1-ap-southeast-1.pooler.supabase.com',
        'port' => 5432
    ]
];

foreach ($configs as $config) {
    echo "Testing: {$config['name']}\n";
    echo "Host: {$config['host']}:{$config['port']}\n";
    
    try {
        $dsn = "pgsql:host={$config['host']};port={$config['port']};dbname=postgres;sslmode=require";
        $pdo = new PDO($dsn, 'postgres.lggzjlevfmqlqhqoinwh', '116161Shiloh2026');
        
        echo "✅ SUCCESS: Connected!\n";
        
        // Test a simple query
        $stmt = $pdo->query("SELECT current_database(), version()");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Database: {$result['current_database']}\n";
        echo "Version: " . substr($result['version'], 0, 50) . "...\n";
        
        // Test if we can see tables
        $stmt = $pdo->query("SELECT count(*) as table_count FROM information_schema.tables WHERE table_schema = 'public'");
        $count = $stmt->fetchColumn();
        echo "Public tables: $count\n";
        
        echo "\n✅ This connection works! Use this configuration.\n";
        break;
        
    } catch (PDOException $e) {
        echo "❌ FAILED: " . $e->getMessage() . "\n";
    }
    
    echo "\n" . str_repeat("-", 50) . "\n\n";
}
?>