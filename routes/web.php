<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UnifiedLoginController;

// Unified login routes
Route::get('/login', [UnifiedLoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [UnifiedLoginController::class, 'login'])->name('unified.login');
Route::post('/logout', [UnifiedLoginController::class, 'logout'])->name('logout')->middleware('auth');

// Temporary report file download (signed URL)
Route::get('/report-download/{filename}', function (string $filename) {
    $path = storage_path("app/temp-reports/{$filename}");
    if (!file_exists($path)) {
        abort(404, 'Report file not found or has expired.');
    }
    return response()->download($path)->deleteFileAfterSend(true);
})->name('report.download')->middleware(['auth', 'signed']);

// Root redirect
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->isParent()) {
            return redirect('/parent');
        }
        return redirect('/admin');
    }
    return redirect('/login');
});

// Database health check endpoint (for monitoring)
Route::get('/health/database', function () {
    try {
        $start = microtime(true);
        \DB::connection()->getPdo();
        $connectionTime = round((microtime(true) - $start) * 1000, 2);

        $start = microtime(true);
        $studentCount = \DB::table('students')->count();
        $queryTime = round((microtime(true) - $start) * 1000, 2);

        return response()->json([
            'status' => 'healthy',
            'database' => [
                'connected' => true,
                'connection_time_ms' => $connectionTime,
                'query_time_ms' => $queryTime,
                'host' => config('database.connections.pgsql.host'),
                'port' => config('database.connections.pgsql.port'),
                'database' => config('database.connections.pgsql.database'),
            ],
            'metrics' => [
                'students_count' => $studentCount,
            ],
            'timestamp' => now()->toIso8601String(),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'unhealthy',
            'database' => [
                'connected' => false,
                'error' => $e->getMessage(),
            ],
            'timestamp' => now()->toIso8601String(),
        ], 503);
    }
})->name('health.database');
