<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'owner_id', 'product_id', 'cashier_id', 'quantity', 'purchase amount', 'buyer_name'];

    public function cashier()
    {
        return $this->belongsTo(Cashier::class);
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
