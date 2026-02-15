<?php
// Show the actual error from index.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<pre>";
echo "Attempting to load Laravel index.php...\n\n";

try {
    ob_start();
    include __DIR__ . '/index.php';
    $output = ob_get_clean();
    echo "Success! Output:\n";
    echo $output;
} catch (Throwable $e) {
    ob_end_clean();
    echo "ERROR CAUGHT:\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\nStack Trace:\n";
    echo $e->getTraceAsString() . "\n";
}
echo "</pre>";
