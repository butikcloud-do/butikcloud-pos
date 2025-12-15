<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Electronics'],
            ['name' => 'Groceries'],
            ['name' => 'Clothing'],
            ['name' => 'Stationery'],
            ['name' => 'Beverages'],
            ['name' => 'Beauty & Personal Care'],
            ['name' => 'Home Appliances'],
        ];

        Category::insert($categories);
    }
}
