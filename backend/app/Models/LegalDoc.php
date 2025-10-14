<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LegalDoc extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'slug',
        'content',
        'version',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function userConsents(): HasMany
    {
        return $this->hasMany(UserConsent::class);
    }
}
