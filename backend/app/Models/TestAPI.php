<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestAPI extends Model
{
    use HasFactory;
    protected $table = 'test_api';
    protected $fillable = ['name', 'email', 'description', 'age'];
}
