<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispute extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'order_id',
        'opener_id',
        'against_user_id',
        'reason',
        'description',
        'status',
        'resolution',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function opener()
    {
        return $this->belongsTo(User::class, 'opener_id');
    }

    public function againstUser()
    {
        return $this->belongsTo(User::class, 'against_user_id');
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }
}
