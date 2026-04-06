<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'target_audience', // 'all', 'parents', 'admins', 'specific_user'
        'target_user_id',
        'created_by',
        'is_published',
        'send_guardian_email',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'send_guardian_email' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }
}
