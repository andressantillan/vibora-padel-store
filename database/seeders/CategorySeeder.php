<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name'        => 'Paletas',
                'slug'        => 'paletas',
                'description' => 'Paletas de pádel de todas las marcas y modelos.',
                'active'      => true,
            ],
            [
                'name'        => 'Overgrips',
                'slug'        => 'overgrips',
                'description' => 'Overgrips para mejorar el agarre de la paleta.',
                'active'      => true,
            ],
            [
                'name'        => 'Protectores',
                'slug'        => 'protectores',
                'description' => 'Protectores para alargar la vida útil de la paleta.',
                'active'      => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}

