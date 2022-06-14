<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Owner extends Model
{
    use HasFactory;
    use HasApiTokens;
    protected $fillable = ['name', 'email', 'password', 'birthday', 'phone_number', 'shop', 'shop_img_url'];

    public function cashiers() {
        return $this->hasMany(Cashier::class);
    }

    public function products() {
        return $this->hasMany(Product::class);
    }

    public function transactions() {
        return $this->hasMany(Transaction::class);
    }

    public function address()
    {
        return $this->hasOne(Address::class, 'foreign_key', 'owner_id');
    }

    public static function getRandomIcon()
    {
        $iconList = config('constants.wesata_shop_icon_list');
        $size = sizeof($iconList);
        $randomIndex = random_int(0, $size - 1);

        return $iconList[$randomIndex];
    }
}
