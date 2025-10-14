<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CampusPickup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'building',
        'floor',
        'room',
        'landmarks',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class, 'pickup_point_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'pickup_point_id');
    }
}
