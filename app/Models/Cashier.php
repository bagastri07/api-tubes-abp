<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Cashier extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasApiTokens;
    protected $fillable = ['name', 'email', 'password', 'birthday', 'phone_number'];
}
