<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarberScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $barbers = DB::table('barbers')->get();

        foreach ($barbers as $barber) {
            
            $schedules = [];
            for ($day = 1; $day <= 6; $day++) {
                $schedules[] = [
                    'barber_id' => $barber->id,
                    'day_of_week' => $day,
                    'start_time' => '09:00:00',
                    'end_time' => '18:00:00',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            DB::table('barber_schedules')->insert($schedules);

            DB::table('barber_blocked_times')->insert([
                [
                    'barber_id' => $barber->id,
                    'date' => now()->addDays(5)->format('Y-m-d'),
                    'start_time' => null,
                    'end_time' => null,
                    'reason' => 'Trámite personal / Descanso',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'barber_id' => $barber->id,
                    'date' => now()->addDays(2)->format('Y-m-d'),
                    'start_time' => '13:00:00',
                    'end_time' => '15:00:00',
                    'reason' => 'Cita médica',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        }
    }
}
