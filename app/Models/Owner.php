<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Owner extends Model
{
    use HasFactory;
    use HasApiTokens;
    protected $fillable = ['name', 'email', 'password', 'birthday', 'phone_number', 'shop'];

    public function cashiers() {
        return $this->hasMany(Cashier::class);
    }

    public function products() {
        return $this->hasMany(Product::class);
    }

    public function transactions() {
        return $this->hasMany(Transaction::class);
    }
}
