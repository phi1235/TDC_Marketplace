<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Major extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'is_active',
        'display_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    /**
     * Get users belong to this major
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get listings tagged with this major
     */
    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class);
    }

    /**
     * Scope for active majors only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope ordered by display_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('name');
    }
}
