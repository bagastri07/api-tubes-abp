<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Cashier;
use App\Models\Owner;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $owners = Owner::factory(14)
            ->has(Cashier::factory()->count(3))
            ->has(Product::factory()->count(10))
            ->has(Address::factory())->count(14)
            ->create();
        
        foreach ($owners as $owner) {
            $cashiers = $owner->cashiers;
            $products = $owner->products;
            $sizeOfProducts = sizeof($products);

            foreach($cashiers as $cashier) {
                for ($i=0; $i < 2; $i++) { 
                    $randomIndex = random_int(0, $sizeOfProducts - 1);
                    $product = $products[$randomIndex];
                    $quantity = random_int(1, 10);
                    $purcaseAmount = $quantity * $product->price;
                    Transaction::factory(1)->create([
                        'product_id' => $product->id,
                        'owner_id' => $owner->id,
                        'cashier_id' => $cashier->id,
                        'quantity' => $quantity,
                        'purchase amount' => $purcaseAmount
                    ]);
                }
            }
        }
    }
}
