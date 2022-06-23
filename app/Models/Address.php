<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;
    protected $fillable = ['owner_id', 'post_code', 'street', 'district', 'province', 'latitude', 'longitude'];
}
