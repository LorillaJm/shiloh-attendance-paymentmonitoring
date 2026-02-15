<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Private channel for admin updates
// Only admins can listen to real-time updates
Broadcast::channel('admin-updates', function (User $user) {
    return $user->isAdmin();
});

// Private channel for user-specific updates
Broadcast::channel('user.{userId}', function (User $user, int $userId) {
    return (int) $user->id === (int) $userId;
});

// Private channel for enrollment-specific updates
Broadcast::channel('enrollment.{enrollmentId}', function (User $user, int $enrollmentId) {
    // Check if user has permission to view this enrollment
    return $user->isAdmin();
});
