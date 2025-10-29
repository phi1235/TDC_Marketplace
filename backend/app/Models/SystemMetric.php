<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemMetric extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'endpoint','method','status','duration_ms','user_id','created_at'
    ];
}


