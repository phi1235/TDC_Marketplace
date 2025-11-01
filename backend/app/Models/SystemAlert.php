<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemAlert extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'rule','level','message','context','active','created_at','resolved_at'
    ];
    protected $casts = [
        'context' => 'array',
        'active' => 'boolean',
        'created_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];
}


