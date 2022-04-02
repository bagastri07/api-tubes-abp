<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Owner extends Model
{
    use HasFactory;
    use HasApiTokens;
    protected $fillable = ['name', 'email', 'password', 'birthday', 'phone_number'];

    public function cashiers() {
        return $this->hasMany(Cashier::class);
    }
}
