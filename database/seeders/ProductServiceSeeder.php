<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductServiceSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('brands')->insert([
            ['id' => 1, 'name' => 'American Crew', 'description' => 'Productos de aseo personal masculino', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Suavecito', 'description' => 'Pomadas y geles de alta fijación', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Reuzel', 'description' => 'Cuidado capilar y para barba tradicional', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('categories')->insert([
            ['id' => 1, 'name' => 'Cortes de Cabello', 'slug' => 'cortes-de-cabello', 'icon' => 'scissors', 'description' => 'Servicios de corte', 'type' => 'service', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Cuidado de la Barba', 'slug' => 'cuidado-de-la-barba', 'icon' => 'razor', 'description' => 'Afeitado y perfilado', 'type' => 'service', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Productos Capilares', 'slug' => 'productos-capilares', 'icon' => 'bottle', 'description' => 'Ceras y champús', 'type' => 'product', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $items = [
            ['cat' => 1, 'brand' => null, 'name' => 'Corte Degradado (Fade)', 'price' => 15.00, 'dur' => 30, 'type' => 'service', 'sku' => null],
            ['cat' => 1, 'brand' => null, 'name' => 'Corte Clásico', 'price' => 12.00, 'dur' => 25, 'type' => 'service', 'sku' => null],
            ['cat' => 1, 'brand' => null, 'name' => 'Corte Infantil', 'price' => 10.00, 'dur' => 20, 'type' => 'service', 'sku' => null],
            ['cat' => 1, 'brand' => null, 'name' => 'Diseño de Cejas', 'price' => 8.00, 'dur' => 15, 'type' => 'service', 'sku' => null],
            ['cat' => 2, 'brand' => null, 'name' => 'Perfilado de Barba Premium', 'price' => 10.00, 'dur' => 20, 'type' => 'service', 'sku' => null],
            ['cat' => 2, 'brand' => null, 'name' => 'Afeitado Tradicional', 'price' => 12.00, 'dur' => 25, 'type' => 'service', 'sku' => null],
            ['cat' => 3, 'brand' => 1, 'name' => 'Cera Mate 100g', 'price' => 20.00, 'dur' => null, 'type' => 'product', 'sku' => 'CERA-01'],
            ['cat' => 3, 'brand' => 2, 'name' => 'Pomada Brillo Intenso', 'price' => 22.00, 'dur' => null, 'type' => 'product', 'sku' => 'POM-02'],
            ['cat' => 3, 'brand' => 3, 'name' => 'Aceite para Barba', 'price' => 25.00, 'dur' => null, 'type' => 'product', 'sku' => 'OIL-03'],
            ['cat' => 3, 'brand' => 1, 'name' => 'Shampoo Anticaída', 'price' => 30.00, 'dur' => null, 'type' => 'product', 'sku' => 'SHP-04'],
        ];

        foreach ($items as $index => $item) {
            DB::table('products')->insert([
                'category_id' => $item['cat'],
                'brand_id' => $item['brand'],
                'name' => $item['name'],
                'slug' => Str::slug($item['name']),
                'description' => 'Descripción de ' . $item['name'],
                'sku' => $item['sku'],
                'price' => $item['price'],
                'stock' => $item['type'] === 'product' ? 50 : 0,
                'tax' => 5,
                'type' => $item['type'],
                'is_active' => true,
                'duration_minutes' => $item['dur'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $barbers = DB::table('barbers')->get();
        $services = DB::table('products')->where('type', 'service')->get();

        foreach ($barbers as $barber) {
            foreach ($services as $service) {
                DB::table('barber_services')->insert([
                    'barber_id' => $barber->id,
                    'product_id' => $service->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}