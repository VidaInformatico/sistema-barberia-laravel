<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            HeroSlideSeeder::class,
            CompanySeeder::class,
            BranchSeeder::class,
            UserSeeder::class,
            PaymentMethodSeeder::class,
            BarberSeeder::class,
            BarberScheduleSeeder::class,
            ProductServiceSeeder::class,
            OrderStateSeeder::class,
        ]);
    }
}
