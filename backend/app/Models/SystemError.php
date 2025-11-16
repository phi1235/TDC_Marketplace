<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemError extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'level','status','message','trace','route','method','user_id','ip_address','user_agent','request_id','created_at'
    ];
}


