<?php

namespace Database\Seeders;

use App\Models\OrderState;
use Illuminate\Database\Seeder;

class OrderStateSeeder extends Seeder
{
    public function run(): void
    {
        $states = [
            [
                'name'        => 'Pendiente de Pago',
                'slug'        => 'pendiente-pago',
                'color'       => '#f59e0b',
                'is_initial'  => true,
                'is_canceled' => false,
                'is_final'    => false,
            ],
            [
                'name'        => 'Pago Confirmado',
                'slug'        => 'pago-confirmado',
                'color'       => '#3b82f6',
                'is_initial'  => false,
                'is_canceled' => false,
                'is_final'    => false,
            ],
            [
                'name'        => 'En Preparación',
                'slug'        => 'en-preparacion',
                'color'       => '#8b5cf6',
                'is_initial'  => false,
                'is_canceled' => false,
                'is_final'    => false,
            ],
            [
                'name'        => 'Enviado',
                'slug'        => 'enviado',
                'color'       => '#06b6d4',
                'is_initial'  => false,
                'is_canceled' => false,
                'is_final'    => false,
            ],
            [
                'name'        => 'Entregado',
                'slug'        => 'entregado',
                'color'       => '#10b981',
                'is_initial'  => false,
                'is_canceled' => false,
                'is_final'    => true,
            ],
            [
                'name'        => 'Cancelado',
                'slug'        => 'cancelado',
                'color'       => '#ef4444',
                'is_initial'  => false,
                'is_canceled' => true,
                'is_final'    => false,
            ],
            [
                'name'        => 'Reembolsado',
                'slug'        => 'reembolsado',
                'color'       => '#6b7280',
                'is_initial'  => false,
                'is_canceled' => true,
                'is_final'    => false,
            ],
        ];

        foreach ($states as $state) {
            OrderState::updateOrCreate(
                ['slug' => $state['slug']],
                $state
            );
        }
    }
}