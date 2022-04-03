<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Product extends Model
{
    use HasFactory;
    use HasApiTokens;

    protected $fillable = ['name', 'price', 'type', 'owner_id', 'image_url'];
}
