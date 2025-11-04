<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TermsConsent extends Model
{
    protected $fillable = ['user_id','version','consented_at','ip_address','user_agent'];
    protected $casts = ['consented_at' => 'datetime'];
}
