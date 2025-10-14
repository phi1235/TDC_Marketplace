<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'student_id',
        'student_id_image',
        'verified_student',
        'verified_at',
        'rating',
        'total_ratings',
        'total_sales',
        'total_revenue',
        'bio',
        'academic_year',
        'major',
        'is_featured',
        'featured_until',
    ];

    protected $casts = [
        'verified_student' => 'boolean',
        'verified_at' => 'datetime',
        'rating' => 'decimal:2',
        'total_revenue' => 'decimal:2',
        'is_featured' => 'boolean',
        'featured_until' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
