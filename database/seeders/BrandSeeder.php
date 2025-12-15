<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Define brands for the POS system
        $brands = [
            ['name' => 'Apple'],
            ['name' => 'Samsung'],
            ['name' => 'Nike'],
            ['name' => 'Coca-Cola'],
            ['name' => 'Nestle'],
            ['name' => 'Sony'],
            ['name' => 'Unilever'],
        ];

        // Insert brands into the database
        Brand::insert($brands);
    }
}
