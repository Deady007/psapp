<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            ['name' => 'LIMS', 'code' => 'LIMS'],
            ['name' => 'DMS', 'code' => 'DMS'],
            ['name' => 'LMS', 'code' => 'LMS'],
            ['name' => 'Projectry', 'code' => 'PROJECTRY'],
            ['name' => 'CRO', 'code' => 'CRO'],
            ['name' => 'CRM', 'code' => 'CRM'],
        ];

        foreach ($products as $product) {
            $record = Product::withTrashed()->firstOrCreate(
                ['name' => $product['name']],
                ['code' => $product['code']],
            );

            if ($record->trashed()) {
                $record->restore();
            }

            if ($record->code !== $product['code']) {
                $record->update(['code' => $product['code']]);
            }
        }
    }
}
