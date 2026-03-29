<?php
// Test Supabase connection directly
$hosts = [
    'db.lggzjlevfmqlqhqoinwh.supabase.co',
    'aws-1-ap-southeast-1.pooler.supabase.com'
];

foreach ($hosts as $host) {
    echo "Testing connection to: $host\n";
    
    try {
        $pdo = new PDO(
            "pgsql:host=$host;port=5432;dbname=postgres;sslmode=require",
            'postgres.lggzjlevfmqlqhqoinwh',
            '116161Shiloh2026'
        );
        echo "✅ SUCCESS: Connected to $host\n";
        
        // Test a simple query
        $stmt = $pdo->query("SELECT version()");
        $version = $stmt->fetchColumn();
        echo "Database version: $version\n";
        break;
        
    } catch (PDOException $e) {
        echo "❌ FAILED: $host - " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

// Also test pooler on port 6543
echo "Testing pooler connection...\n";
try {
    $pdo = new PDO(
        "pgsql:host=aws-1-ap-southeast-1.pooler.supabase.com;port=6543;dbname=postgres;sslmode=require",
        'postgres.lggzjlevfmqlqhqoinwh',
        '116161Shiloh2026'
    );
    echo "✅ SUCCESS: Connected to pooler\n";
} catch (PDOException $e) {
    echo "❌ FAILED: Pooler - " . $e->getMessage() . "\n";
}
?>