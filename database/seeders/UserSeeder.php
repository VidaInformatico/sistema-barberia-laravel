<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Administrador',
                'last_name' => 'General',
                'slug' => Str::slug('Administrador General'),
                'email' => 'admin@gmail.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'is_active' => true,
                'phone' => '987654321',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Roberto',
                'last_name' => 'Gómez',
                'slug' => Str::slug('Roberto Gómez'),
                'email' => 'admin2@gmail.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'is_active' => true,
                'phone' => '987654322',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Carlos',
                'last_name' => 'Barber',
                'slug' => Str::slug('Carlos Barber'),
                'email' => 'carlos@barbershop.com',
                'password' => Hash::make('carlos123'),
                'role' => 'barber',
                'is_active' => true,
                'phone' => '987654323',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Miguel',
                'last_name' => 'Ángel',
                'slug' => Str::slug('Miguel Ángel'),
                'email' => 'miguel@barbershop.com',
                'password' => Hash::make('miguel123'),
                'role' => 'barber',
                'is_active' => true,
                'phone' => '987654324',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Luis',
                'last_name' => 'Torres',
                'slug' => Str::slug('Luis Torres'),
                'email' => 'luis@barbershop.com',
                'password' => Hash::make('luis123'),
                'role' => 'barber',
                'is_active' => true,
                'phone' => '987654325',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'José',
                'last_name' => 'Martínez',
                'slug' => Str::slug('José Martínez'),
                'email' => 'jose@barbershop.com',
                'password' => Hash::make('jose123'),
                'role' => 'barber',
                'is_active' => true,
                'phone' => '987654326',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'David',
                'last_name' => 'Ruiz',
                'slug' => Str::slug('David Ruiz'),
                'email' => 'david@barbershop.com',
                'password' => Hash::make('david123'),
                'role' => 'barber',
                'is_active' => true,
                'phone' => '987654327',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Juan',
                'last_name' => 'Pérez',
                'slug' => Str::slug('Juan Pérez'),
                'email' => 'juan@gmail.com',
                'password' => Hash::make('juan123'),
                'role' => 'client',
                'is_active' => true,
                'phone' => '987654328',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pedro',
                'last_name' => 'Sánchez',
                'slug' => Str::slug('Pedro Sánchez'),
                'email' => 'pedro@gmail.com',
                'password' => Hash::make('pedro123'),
                'role' => 'client',
                'is_active' => true,
                'phone' => '987654329',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Andrés',
                'last_name' => 'Castro',
                'slug' => Str::slug('Andrés Castro'),
                'email' => 'andres@gmail.com',
                'password' => Hash::make('andres123'),
                'role' => 'client',
                'is_active' => true,
                'phone' => '987654330',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}