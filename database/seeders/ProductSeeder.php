<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    private int $skuCounter = 1;

    public function run(): void
    {
        // ===== Categorías (fijas del negocio) =====
        $paletas     = Category::firstOrCreate(['slug' => 'paletas'],     ['name' => 'Paletas', 'active' => true]);
        $overgrips   = Category::firstOrCreate(['slug' => 'overgrips'],   ['name' => 'Overgrips', 'active' => true]);
        $protectores = Category::firstOrCreate(['slug' => 'protectores'], ['name' => 'Protectores', 'active' => true]);

        // ===== Marcas =====
        $bullpadel = Brand::firstOrCreate(['slug' => 'bullpadel'], ['name' => 'Bullpadel', 'active' => true]);
        $head      = Brand::firstOrCreate(['slug' => 'head'],      ['name' => 'Head', 'active' => true]);
        $nox       = Brand::firstOrCreate(['slug' => 'nox'],       ['name' => 'Nox', 'active' => true]);
        $adidas    = Brand::firstOrCreate(['slug' => 'adidas'],    ['name' => 'Adidas', 'active' => true]);
        $siux      = Brand::firstOrCreate(['slug' => 'siux'],      ['name' => 'Siux', 'active' => true]);

        // ===== PALETAS (con shape y level) =====
        $this->crearProducto($paletas, $bullpadel, [
            'name'        => 'Bullpadel Vertex 04',
            'description' => 'Paleta de potencia para juego agresivo.',
            'shape'       => 'diamante',
            'level'       => 'avanzado',
        ], [
            ['color' => 'Negro/Amarillo', 'weight' => 0.365, 'price' => 320000, 'stock' => 8],
            ['color' => 'Azul',           'weight' => 0.370, 'price' => 320000, 'stock' => 4],
        ]);

        $this->crearProducto($paletas, $head, [
            'name'        => 'Head Delta Pro',
            'description' => 'Paleta de control y salida de bola, formato lágrima.',
            'shape'       => 'lagrima',
            'level'       => 'intermedio',
        ], [
            ['color' => 'Negro/Naranja', 'weight' => 0.360, 'price' => 285000, 'stock' => 6],
        ]);

        $this->crearProducto($paletas, $nox, [
            'name'        => 'Nox AT10 Genius',
            'description' => 'Paleta redonda, equilibrada y cómoda.',
            'shape'       => 'redonda',
            'level'       => 'intermedio',
        ], [
            ['color' => 'Azul/Naranja', 'weight' => 0.358, 'price' => 295000, 'stock' => 7],
        ]);

        $this->crearProducto($paletas, $siux, [
            'name'        => 'Siux Electra ST3',
            'description' => 'Paleta redonda ideal para iniciación.',
            'shape'       => 'redonda',
            'level'       => 'iniciacion',
        ], [
            ['color' => 'Negro',  'weight' => 0.355, 'price' => 180000, 'stock' => 12],
            ['color' => 'Blanco', 'weight' => 0.355, 'price' => 180000, 'stock' => 9],
        ]);

        $this->crearProducto($paletas, $adidas, [
            'name'        => 'Adidas Adipower Multiweight',
            'description' => 'Paleta diamante de alto rendimiento.',
            'shape'       => 'diamante',
            'level'       => 'avanzado',
        ], [
            ['color' => 'Negro/Rojo', 'weight' => 0.368, 'price' => 340000, 'stock' => 5],
        ]);

        // ===== OVERGRIPS (sin shape ni level) =====
        $this->crearProducto($overgrips, $bullpadel, [
            'name'        => 'Bullpadel Overgrip GB1601',
            'description' => 'Overgrip perforado, buena absorción.',
        ], [
            ['color' => 'Negro',   'price' => 4500, 'stock' => 50],
            ['color' => 'Blanco',  'price' => 4500, 'stock' => 45],
            ['color' => 'Surtido', 'price' => 4500, 'stock' => 30],
        ]);

        $this->crearProducto($overgrips, $head, [
            'name'        => 'Head Overgrip Xtreme Soft',
            'description' => 'Overgrip suave, pack x3.',
        ], [
            ['color' => 'Negro',  'price' => 6000, 'stock' => 40],
            ['color' => 'Blanco', 'price' => 6000, 'stock' => 38],
        ]);

        // ===== PROTECTORES (sin shape ni level) =====
        $this->crearProducto($protectores, $bullpadel, [
            'name'        => 'Protector Bullpadel Universal',
            'description' => 'Protector de marco autoadhesivo.',
        ], [
            ['color' => 'Transparente', 'price' => 5500, 'stock' => 60],
            ['color' => 'Negro',        'price' => 5500, 'stock' => 35],
        ]);

        $this->crearProducto($protectores, $nox, [
            'name'        => 'Protector Nox Premium',
            'description' => 'Protector resistente para mayor durabilidad.',
        ], [
            ['color' => 'Transparente', 'price' => 6500, 'stock' => 25],
        ]);

        $this->command->info('Productos y variantes creados correctamente.');
    }

    /**
     * Crea un producto con sus variantes y stock.
     * shape y level solo se aplican si vienen en $data (paletas).
     */
    private function crearProducto(Category $category, Brand $brand, array $data, array $variants): void
    {
        $product = Product::firstOrCreate(
            ['slug' => Str::slug($data['name'])],
            [
                'name'        => $data['name'],
                'description' => $data['description'] ?? null,
                'category_id' => $category->id,
                'brand_id'    => $brand->id,
                'shape'       => $data['shape'] ?? null,
                'level'       => $data['level'] ?? null,
                'active'      => true,
            ]
        );

        foreach ($variants as $v) {
            $variant = ProductVariant::firstOrCreate(
                [
                    'product_id' => $product->id,
                    'color'      => $v['color'],
                ],
                [
                    'sku'    => $this->generarSku($brand),
                    'price'  => $v['price'],
                    'weight' => $v['weight'] ?? null,
                    'size'   => $v['size'] ?? null,
                ]
            );

            $variant->stock()->firstOrCreate(
                ['product_variant_id' => $variant->id],
                [
                    'quantity'     => $v['stock'],
                    'min_quantity' => 5,
                ]
            );
        }
    }

    private function generarSku(Brand $brand): string
    {
        $prefijo = Str::upper(Str::substr($brand->slug, 0, 3));
        return $prefijo . '-' . str_pad($this->skuCounter++, 4, '0', STR_PAD_LEFT);
    }
}