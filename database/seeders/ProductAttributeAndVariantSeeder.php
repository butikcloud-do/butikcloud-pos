<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\Variant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductAttributeAndVariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define attributes and their respective values
        $attributes = [
            'Size'       => ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
            'Color'      => ['Red', 'Blue', 'Green', 'Black', 'White', 'Yellow'],
            'Material'   => ['Cotton', 'Polyester', 'Leather', 'Wool', 'Silk'],
            'Weight'     => ['250g', '500g', '1kg', '2kg', '5kg'],
            'Dimensions' => ['10x10x10 cm', '20x20x20 cm', '30x30x30 cm', '50x50x50 cm'],
            'Flavor'     => ['Vanilla', 'Chocolate', 'Strawberry', 'Mint', 'Lemon'],
            'Warranty'   => ['6 months', '1 year', '2 years', 'No Warranty'],
            'Capacity'   => ['8GB', '16GB', '32GB', '64GB', '128GB'],
        ];

        foreach ($attributes as $attributeName => $values) {
            // Create the attribute
            $attribute = Attribute::create(['name' => $attributeName]);

            foreach ($values as $value) {
                Variant::create([
                    'attribute_id' => $attribute->id,
                    'name'        => $value,
                ]);
            }
        }
    }
}
