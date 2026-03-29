<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== PROGRAMS/SERVICES ===\n";
$programs = App\Models\Program::all();
foreach ($programs as $program) {
    echo "✓ {$program->name}\n";
}

echo "\n=== PACKAGES ===\n";
$packages = App\Models\Package::all();
foreach ($packages as $package) {
    echo "\n{$package->name}\n";
    echo "  Total Fee: PHP " . number_format($package->total_fee, 2) . "\n";
    echo "  Description: {$package->description}\n";
}

echo "\n✅ All data seeded successfully!\n";
