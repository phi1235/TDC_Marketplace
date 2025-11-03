<?php
// app/Models/SupportRequest.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportRequest extends Model
{
    protected $fillable = ['user_id','name','email','topic','message','status'];
}

