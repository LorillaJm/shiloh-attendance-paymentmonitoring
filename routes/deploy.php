<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Deployment Routes
|--------------------------------------------------------------------------
|
| These routes allow you to run deployment tasks via web browser
| when you don't have shell access (Render free tier)
|
| SECURITY: These routes are protected and should only be used during deployment
|
*/

Route::get('/deploy/migrate', function () {
    if (app()->environment('local')) {
        return response()->json(['error' => 'Not available in local environment'], 403);
    }

    try {
        Artisan::call('migrate', ['--force' => true]);
        $output = Artisan::output();
        
        return response()->json([
            'success' => true,
            'message' => 'Migration completed',
            'output' => $output
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});

Route::get('/deploy/cache-clear', function () {
    if (app()->environment('local')) {
        return response()->json(['error' => 'Not available in local environment'], 403);
    }

    try {
        Artisan::call('optimize:clear');
        Artisan::call('config:cache');
        Artisan::call('route:cache');
        Artisan::call('view:cache');
        
        return response()->json([
            'success' => true,
            'message' => 'Caches cleared and rebuilt'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});

Route::get('/deploy/warm-cache', function () {
    if (app()->environment('local')) {
        return response()->json(['error' => 'Not available in local environment'], 403);
    }

    try {
        Artisan::call('dashboard:warm-cache');
        
        return response()->json([
            'success' => true,
            'message' => 'Dashboard cache warmed'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});

Route::get('/deploy/all', function () {
    if (app()->environment('local')) {
        return response()->json(['error' => 'Not available in local environment'], 403);
    }

    try {
        $results = [];
        
        // Run migration
        Artisan::call('migrate', ['--force' => true]);
        $results['migrate'] = Artisan::output();
        
        // Clear caches
        Artisan::call('optimize:clear');
        Artisan::call('config:cache');
        Artisan::call('route:cache');
        Artisan::call('view:cache');
        $results['cache'] = 'Cleared and rebuilt';
        
        // Warm dashboard cache
        try {
            Artisan::call('dashboard:warm-cache');
            $results['dashboard'] = 'Cache warmed';
        } catch (\Exception $e) {
            $results['dashboard'] = 'Skipped: ' . $e->getMessage();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'All deployment tasks completed',
            'results' => $results
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});
