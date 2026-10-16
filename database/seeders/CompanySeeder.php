<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Company::create([
            'name' => 'The Barber Shop',
            'email' => 'contacto@barbershop.com',
            'phone' => '123456789',
            'address' => 'Av. Principal 123, Centro',
            'currency_code' => 'USD',
            'timezone' => 'America/Lima',
            'logo_path' => 'logos/default-logo.png',
            'hours' => 'Lunes a Sábado: 09:00 AM - 08:00 PM',
            'social_links' => [
                'facebook' => 'https://facebook.com/thebarbershop',
                'instagram' => 'https://instagram.com/thebarbershop',
                'whatsapp' => 'https://wa.me/123456789'
            ],
            'extra_config' => []
        ]);
    }
}
