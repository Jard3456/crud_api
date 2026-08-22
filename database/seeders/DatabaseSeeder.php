<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Product::factory()->createMany([
            [
                'name' => 'Teclado mecánico',
                'description' => 'Teclado mecánico retroiluminado para oficina y gaming.',
                'price' => 499.90,
                'stock' => 25,
                'active' => true,
            ],
            [
                'name' => 'Mouse inalámbrico',
                'description' => 'Mouse ergonómico con conexión inalámbrica.',
                'price' => 189.50,
                'stock' => 40,
                'active' => true,
            ],
        ]);

        Product::factory(13)->create();
    }
}
