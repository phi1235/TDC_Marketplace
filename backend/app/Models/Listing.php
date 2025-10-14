<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

class Listing extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'seller_id',
        'category_id',
        'title',
        'slug',
        'description',
        'condition_grade',
        'price',
        'original_price',
        'currency',
        'status',
        'featured_until',
        'view_count',
        'favorite_count',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'featured_until' => 'datetime',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ListingImage::class);
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(ListingView::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'condition_grade' => $this->condition_grade,
            'status' => $this->status,
            'category_name' => $this->category->name ?? '',
        ];
    }
}
