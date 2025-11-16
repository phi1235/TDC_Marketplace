<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    // Nếu cần, liên kết với user nhận thông báo
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
