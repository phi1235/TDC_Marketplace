<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Wishlist;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'major_id',
        'phone',
        'avatar',
        'email_verified_at',
        'phone_verified_at',
        'is_active',
        'last_login_at',
        'login_count',
        'remember_token',
        'rating',
        'total_ratings',
        'total_sales',
        'total_revenue',
        'terms_accepted',
        'terms_accepted_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }

    public function sellerProfile(): HasOne
    {
        return $this->hasOne(SellerProfile::class);
    }

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class, 'seller_id');
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class, 'buyer_id');
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function receivedReviews(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    public function disputes(): HasMany
    {
        return $this->hasMany(Dispute::class, 'opener_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(UserActivity::class);
    }

    public function ordersAsBuyer(): HasMany
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    public function ordersAsSeller(): HasMany
    {
        return $this->hasMany(Order::class, 'seller_id');
    }

    public function userConsents(): HasMany
    {
        return $this->hasMany(UserConsent::class);
    }

    public function auditLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    //rolers for rbac
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole($role)
    {
        return $this->roles()->where('name', $role)->exists();
    }

    //wish list

}
