<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PickupPoint extends Model
{
    protected $fillable = ['name','campus_code','address','lat','lng','opening_hours','is_active'];
    protected $casts = ['opening_hours' => 'array', 'is_active' => 'boolean'];

    public function listings() { return $this->belongsToMany(Listing::class); }
}
