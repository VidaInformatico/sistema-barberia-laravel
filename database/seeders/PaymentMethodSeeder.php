<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('payment_methods')->insert([
            [
                'name' => 'Efectivo',
                'is_efectivo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tarjeta de Crédito / Débito',
                'is_efectivo' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Transferencia Bancaria',
                'is_efectivo' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Billetera Digital',
                'is_efectivo' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
