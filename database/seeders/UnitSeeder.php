<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['name' => 'Piece', 'short_name' => 'pc'],
            ['name' => 'Kilogram', 'short_name' => 'kg'],
            ['name' => 'Gram', 'short_name' => 'g'],
            ['name' => 'Liter', 'short_name' => 'l'],
            ['name' => 'Milliliter', 'short_name' => 'ml'],
            ['name' => 'Box', 'short_name' => 'box'],
            ['name' => 'Pack', 'short_name' => 'pack'],
        ];
        // Insert units into the database
        Unit::insert($units);
    }
}
