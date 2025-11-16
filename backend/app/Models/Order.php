<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'buyer_id',
        'seller_id',
        'listing_id',
        'offer_id',
        'product_title',
        'product_price',
        'quantity',
        'total_amount',
        'has_rated',
        'currency',
        'status',
        'pickup_point_id',
        'delivery_method',
        'delivery_address',
        'delivery_notes',
        'paid_at',
        'confirmed_at',
        'shipped_at',
        'delivered_at',
        'completed_at',
        'cancelled_at',
        'notes',
        'admin_notes',
    ];

    protected $casts = [
        'product_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function pickupPoint(): BelongsTo
    {
        return $this->belongsTo(CampusPickup::class, 'pickup_point_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function confirmations(): HasMany
    {
        return $this->hasMany(OrderConfirmation::class);
    }
    public function escrowAccount(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(EscrowAccount::class, 'order_id', 'id');
    }
}
