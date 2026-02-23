<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

Route::get('/setup/create-admin', function () {
    // Only allow in production or when APP_ENV is not local
    if (app()->environment('local') && !request()->has('force')) {
        return response()->json([
            'error' => 'Not available in local environment',
            'note' => 'Add ?force=1 to the URL if you really want to run this locally',
            'current_env' => app()->environment(),
        ], 403);
    }

    try {
        // Check if admin already exists
        $existingAdmin = User::where('email', 'admin@shiloh.local')->first();
        
        if ($existingAdmin) {
            return response()->json([
                'success' => false,
                'message' => 'Admin user already exists',
                'email' => 'admin@shiloh.local',
                'note' => 'Use password reset if you forgot the password',
            ]);
        }

        // Create admin user
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@shiloh.local',
            'password' => Hash::make('admin123'),
            'role' => 'ADMIN',
            'email_verified_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Admin user created successfully',
            'credentials' => [
                'email' => 'admin@shiloh.local',
                'password' => 'admin123',
            ],
            'warning' => 'CHANGE THIS PASSWORD IMMEDIATELY after first login!',
            'login_url' => url('/admin/login'),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => app()->environment('production') ? 'Hidden' : $e->getTraceAsString(),
        ], 500);
    }
});

Route::get('/setup/reset-admin-password', function () {
    // Only allow in production or when APP_ENV is not local
    if (app()->environment('local') && !request()->has('force')) {
        return response()->json([
            'error' => 'Not available in local environment',
            'note' => 'Add ?force=1 to the URL if you really want to run this locally',
            'current_env' => app()->environment(),
        ], 403);
    }

    try {
        $admin = User::where('email', 'admin@shiloh.local')->first();
        
        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Admin user not found',
                'note' => 'Visit /setup/create-admin first',
            ], 404);
        }

        // Reset password
        $admin->password = Hash::make('admin123');
        $admin->save();

        return response()->json([
            'success' => true,
            'message' => 'Admin password reset successfully',
            'credentials' => [
                'email' => 'admin@shiloh.local',
                'password' => 'admin123',
            ],
            'warning' => 'CHANGE THIS PASSWORD IMMEDIATELY after login!',
            'login_url' => url('/admin/login'),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
        ], 500);
    }
});

Route::get('/setup/list-users', function () {
    // Only allow in production or when APP_ENV is not local
    if (app()->environment('local') && !request()->has('force')) {
        return response()->json([
            'error' => 'Not available in local environment',
            'note' => 'Add ?force=1 to the URL if you really want to run this locally',
            'current_env' => app()->environment(),
        ], 403);
    }

    try {
        $users = User::select('id', 'name', 'email', 'role', 'created_at')->get();

        return response()->json([
            'success' => true,
            'count' => $users->count(),
            'users' => $users,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
        ], 500);
    }
});
