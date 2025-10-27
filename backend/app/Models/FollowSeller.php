<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FollowSeller extends Model
{
    protected $table = 'follow_sellers';
    protected $fillable = ['user_id', 'seller_id'];

    // User đang follow
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    // Seller được follow
    public function seller()
    {
        return $this->belongsTo(\App\Models\SellerProfile::class, 'seller_id', 'user_id');
    }
}
