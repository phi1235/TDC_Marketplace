<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailOtp extends Model
{
    protected $fillable = [
        'email','code_hash','expires_at','attempts','used_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at'    => 'datetime',
    ];

    public function scopeActive($q) {
        return $q->whereNull('used_at')->where('expires_at','>', now());
    }
}
