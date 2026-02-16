<?php
// Test basic Laravel bootstrap
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<pre>";
echo "Testing Laravel bootstrap...\n\n";

try {
    define('LARAVEL_START', microtime(true));
    
    echo "1. Loading autoloader...\n";
    require __DIR__.'/../vendor/autoload.php';
    echo "   ✓ Autoloader loaded\n\n";
    
    echo "2. Loading app...\n";
    $app = require_once __DIR__.'/../bootstrap/app.php';
    echo "   ✓ App loaded\n\n";
    
    echo "3. Creating kernel...\n";
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    echo "   ✓ Kernel created\n\n";
    
    echo "4. Capturing request...\n";
    $request = Illuminate\Http\Request::capture();
    echo "   ✓ Request captured\n\n";
    
    echo "5. Handling request...\n";
    $response = $kernel->handle($request);
    echo "   ✓ Request handled\n\n";
    
    echo "6. Response status: " . $response->getStatusCode() . "\n";
    echo "   Response content length: " . strlen($response->getContent()) . " bytes\n\n";
    
    if ($response->getStatusCode() == 500) {
        echo "ERROR: Response is 500\n";
        echo "Content preview:\n";
        echo substr($response->getContent(), 0, 500) . "\n";
    } else {
        echo "✅ SUCCESS! App is working\n";
    }
    
} catch (Throwable $e) {
    echo "\n❌ ERROR CAUGHT:\n";
    echo "Type: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
    echo "Stack Trace:\n";
    echo $e->getTraceAsString() . "\n";
}

echo "</pre>";
