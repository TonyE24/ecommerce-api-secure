<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    $products = [
        ['name' => 'Laptop Pro 15"', 'description' => '16GB RAM, 512GB SSD', 'price' => 1299.99, 'stock' => 15],
        ['name' => 'Teclado Mecánico RGB', 'description' => 'Switches táctiles silenciosos', 'price' => 89.50, 'stock' => 40],
        ['name' => 'Monitor 27" 4K IPS', 'description' => '144Hz con soporte USB-C', 'price' => 349.00, 'stock' => 20],
        ['name' => 'Mouse Inalámbrico Ergonómico', 'description' => 'Batería recargable 70h', 'price' => 49.99, 'stock' => 50],
    ];

    foreach ($products as $product) {
        \App\Models\Product::create($product);
    }
}
}
