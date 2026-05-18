<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Brand;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['name' => 'Babolat',    'slug' => 'babolat'],
            ['name' => 'Bullpadel',  'slug' => 'bullpadel'],
            ['name' => 'Head',       'slug' => 'head'],
            ['name' => 'Nox',        'slug' => 'nox'],
            ['name' => 'Adidas',     'slug' => 'adidas'],
            ['name' => 'Wilson',     'slug' => 'wilson'],
            ['name' => 'Drop Shot',  'slug' => 'drop-shot'],
            ['name' => 'Siux',       'slug' => 'siux'],
            ['name' => 'Royal',      'slug' => 'royal'],
            ['name' => 'Star Vie',   'slug' => 'star-vie'],
        ];

        foreach ($brands as $brand) {
            Brand::updateOrCreate(
                ['slug' => $brand['slug']],
                array_merge($brand, ['active' => true])
            );
        }
    }
}
