<?php

namespace Database\Seeders;

use App\Models\HeroSlide;
use Illuminate\Database\Seeder;

class HeroSlideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $slides = [
            [
                'title'      => 'Estilo & Tradición en Cada Corte',
                'subtitle'   => 'BIENVENIDO A TU BARBERÍA DE CONFIANZA',
                'text'       => 'Ofrecemos cortes de cabello clásicos, fados modernos y perfilado de barba con el cuidado que mereces.',
                'image_url'  => 'images/hero/hero-slide-1.jpg',
                'btn1_text'  => 'Reservar Cita',
                'btn1_url'   => '/reservar',
                'btn1_icon'  => 'fas fa-calendar-alt',
                'btn2_text'  => 'Ver Servicios',
                'btn2_url'   => '/servicios',
                'btn2_icon'  => 'fas fa-cut',
                'order'      => 1,
                'is_active'  => true,
            ],
            [
                'title'      => 'Experiencia de Afeitado Tradicional',
                'subtitle'   => 'CUIDADO PREMIUM PARA TU BARBA',
                'text'       => 'Relájate con nuestro servicio de toalla caliente, masaje facial y afeitado clásico con navaja.',
                'image_url'  => 'images/hero/hero-slide-2.jpg',
                'btn1_text'  => 'Nuestros Servicios',
                'btn1_url'   => '/servicios',
                'btn1_icon'  => 'fas fa-concierge-bell',
                'btn2_text'  => 'Conocer Barberos',
                'btn2_url'   => '/equipo',
                'btn2_icon'  => 'fas fa-user-check',
                'order'      => 2,
                'is_active'  => true,
            ],
            [
                'title'      => 'Productos de Calidad Profesional',
                'subtitle'   => 'LLEVA EL MEJOR CUIDADO A CASA',
                'text'       => 'Descubre nuestra línea de pomadas, aceites de barba y ceras de las mejores marcas.',
                'image_url'  => 'images/hero/hero-slide-3.jpg',
                'btn1_text'  => 'Ir a la Tienda',
                'btn1_url'   => '/tienda',
                'btn1_icon'  => 'fas fa-shopping-bag',
                'btn2_text'  => 'Contáctanos',
                'btn2_url'   => '/contacto',
                'btn2_icon'  => 'fas fa-envelope',
                'order'      => 3,
                'is_active'  => true,
            ],
        ];

        foreach ($slides as $slide) {
            HeroSlide::create($slide);
        }
    }
}