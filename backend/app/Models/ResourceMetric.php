<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResourceMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'engine',
        'metric_type',
        'value',
        'unit',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'value' => 'float',
    ];

    public function scopeByEngine($query, $engine)
    {
        return $query->where('engine', $engine);
    }

    public function scopeByMetricType($query, $metricType)
    {
        return $query->where('metric_type', $metricType);
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
