<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarberSeeder extends Seeder
{
    public function run(): void
    {
        $barberUsers = DB::table('users')->where('role', 'barber')->get();

        $barbers = [];

        foreach ($barberUsers as $index => $user) {
            $barbers[] = [
                'user_id' => $user->id,
                'bio' => 'Especialista en cortes urbanos y degradados perfectos número ' . ($index + 1) . '.',
                'specialty' => $index % 2 === 0 ? 'Fade / Barbería Clásica' : 'Perfilado de Barba y Tijera',
                'experience_years' => rand(3, 10),
                'is_available' => true,
                'instagram' => '@' . strtolower($user->name) . '_barber',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($barbers)) {
            DB::table('barbers')->insert($barbers);
        }
    }
}