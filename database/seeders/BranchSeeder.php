<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        Branch::insert([
            [
                'name' => 'Sucursal Principal Centro',
                'phone' => '987654321',
                'email' => 'centro@empresa.com',
                'address' => 'Av. España 123, Trujillo',
                'active' => true,
                'direct_printing' => true,
                'printer_name' => 'Impresora_Principal',
                'print_size' => '80',
            ],
            [
                'name' => 'Sucursal Mall Aventura',
                'phone' => '912345678',
                'email' => 'mall@empresa.com',
                'address' => 'Av. América Oeste 750, Trujillo',
                'active' => true,
                'direct_printing' => false,
                'printer_name' => null,
                'print_size' => '80',
            ],
            [
                'name' => 'Sucursal Real Plaza',
                'phone' => '923456789',
                'email' => 'realplaza@empresa.com',
                'address' => 'Av. César Vallejo 1345, Trujillo',
                'active' => true,
                'direct_printing' => true,
                'printer_name' => 'Impresora_RealPlaza',
                'print_size' => '58',
            ],
            [
                'name' => 'Sucursal Víctor Larco',
                'phone' => '934567891',
                'email' => 'larco@empresa.com',
                'address' => 'Av. Larco 555, Trujillo',
                'active' => false,
                'direct_printing' => false,
                'printer_name' => null,
                'print_size' => '80',
            ],
        ]);
    }
}