<?php
$menuItems = [
    ['label' => 'PRINCIPAL', 'items' => [['name' => 'Dashboard', 'icon' => 'fa-solid fa-chart-pie', 'route' => 'admin.dashboard', 'submenu' => []]]],
    [
        'label' => 'GESTIÓN',
        'items' => [
            ['name' => 'Personal', 'icon' => 'fa-solid fa-scissors', 'route' => null, 'submenu' => [['name' => 'Gestionar Barberos', 'route' => 'admin.barbers.index'], ['name' => 'Horarios Laborales', 'route' => 'admin.barbers.schedules'], ['name' => 'Descansos', 'route' => 'admin.barbers.blocked-times'], ['name' => 'Servicios', 'route' => 'admin.barbers.services']]],
            ['name' => 'Reservas y Citas', 'icon' => 'fa-solid fa-calendar-check', 'route' => null, 'submenu' => [['name' => 'Nueva Cita', 'route' => 'admin.bookings.create'], ['name' => 'Historial Reservas', 'route' => 'admin.bookings.index'], ['name' => 'Historial de Estados', 'route' => 'admin.bookings.history'], ['name' => 'Políticas de Reserva', 'route' => 'admin.bookings.policies']]],
            ['name' => 'Servicios', 'icon' => 'fa-solid fa-id-card-clip', 'route' => null, 'submenu' => [['name' => 'Lista de Servicios', 'route' => 'admin.services.index'], ['name' => 'Categorías', 'route' => 'admin.services.categories']]],
            ['name' => 'Reseñas', 'icon' => 'fa-solid fa-star', 'route' => 'admin.reviews.index', 'submenu' => []],
        ],
    ],
    [
        'label' => 'NEGOCIO',
        'items' => [
            ['name' => 'Inventario', 'icon' => 'fa-solid fa-boxes-stacked', 'route' => null, 
            'submenu' => [
                ['name' => 'Productos', 'route' => 'admin.products.index'], 
                ['name' => 'Categorías', 'route' => 'admin.products.categories'],
                ['name' => 'Marcas', 'route' => 'admin.products.brands'],
                 ['name' => 'Movimientos de Stock', 'route' => 'admin.products.stock-movements']]],
            // ORDENES
            [
                'name' => 'Ordenes',
                'icon' => 'fa-solid fa-receipt',
                'route' => null,
                'submenu' => [['name' => 'Historial de Ordenes', 'route' => 'admin.orders.index'], ['name' => 'Estados de Ordenes', 'route' => 'admin.orders.states']],
            ],
            // VENTAS
            [
                'name' => 'Ventas',
                'icon' => 'fa-solid fa-cart-shopping',
                'route' => null,
                'submenu' => [['name' => 'Nueva Venta / POS', 'route' => 'admin.sales.create'], ['name' => 'Historial de ventas', 'route' => 'admin.sales.index'], ['name' => 'Cuentas por cobrar', 'route' => 'admin.sales.credit'], ['name' => 'Historial de pagos', 'route' => 'admin.sales.payments']],
            ],
            //CAJAS
            [
                'name' => 'Cajas',
                'icon' => 'fa-solid fa-cash-register',
                'route' => null,
                'submenu' => [['name' => 'Método de pago', 'route' => 'admin.payment-method.index'], ['name' => 'Gastos | Egresos', 'route' => 'admin.expenses.index'], ['name' => 'Estado de Caja', 'route' => 'admin.boxes.index']],
            ],
            [
                'name' => 'Compras',
                'icon' => 'fa-solid fa-cash-register',
                'route' => null,
                'submenu' => [
                    ['name' => 'Nueva Compra', 'route' => 'admin.purchases.create'],
                    ['name' => 'Historial de compras', 'route' => 'admin.purchases.index'],
                    ['name' => 'Cuentas por pagar', 'route' => 'admin.purchases.credit'],
                    ['name' => 'Historial de pagos', 'route' => 'admin.purchases.payments'],
                    [
                        'name' => 'Proveedores',
                        'route' => 'admin.suppliers.index',
                    ],
                ],
            ],
        ],
    ],
    ['label' => 'SISTEMA', 'items' => [['name' => 'Administración', 'icon' => 'fa-solid fa-shield-halved', 'route' => null, 'submenu' => [['name' => 'Galería Comercial', 'route' => 'admin.gallery.index'], ['name' => 'Banner Comercial', 'route' => 'admin.slides.index'], ['name' => 'Usuarios y Clientes', 'route' => 'admin.users.index'], ['name' => 'Empresa y Ajustes', 'route' => 'admin.company.index'], ['name' => 'Sucursales', 'route' => 'admin.branches.index']]]]],
];
?>

<div class="space-y-6">
    <div class="mb-4">
        <div class="px-4 mb-2 text-[10px] font-bold text-gray-600 uppercase tracking-widest">
            Sucursales
        </div>
        <div class="space-y-1">
            <div x-data="{ openSucursal: false }">
                <button @click="openSucursal = !openSucursal"
                    class="w-full group flex items-center justify-between px-4 py-2.5 mx-2 rounded-lg transition-all duration-200 text-gray-400 hover:text-white">
                    <div class="flex items-center space-x-3 truncate">
                        <i class="fa-solid fa-store text-sm"></i>
                        <span class="font-medium text-sm truncate">
                            {{ optional($sucursales->firstWhere('id', auth()->user()->branch_id))->name ?? 'Seleccionar Sucursal' }}
                        </span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-300 shrink-0"
                        :class="openSucursal ? 'rotate-180' : ''"></i>
                </button>

                <div x-show="openSucursal" x-collapse class="relative ml-8 mt-1 space-y-1">
                    <div class="absolute left-0 top-0 bottom-4 w-px bg-gray-800"></div>

                    @if (isset($sucursales))
                        @foreach ($sucursales as $sucursal)
                            <a href="#" @click.prevent="updateBranch({{ $sucursal->id }})"
                                class="relative flex items-center py-2 pl-6 text-[13px] transition-all duration-200 group/link {{ auth()->user()->branch_id === $sucursal->id ? 'text-amber-500 font-semibold' : 'text-gray-500 hover:text-gray-300' }}">

                                <span
                                    class="absolute -left-[5px] w-2.5 h-2.5 rounded-full border-2 border-gray-950 {{ auth()->user()->branch_id === $sucursal->id ? 'bg-amber-500' : 'bg-gray-700 group-hover/link:bg-gray-500' }}"></span>

                                {{ $sucursal->name }}
                            </a>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    @foreach ($menuItems as $section)
        <div>
            <div class="px-4 mb-2 text-[10px] font-bold text-gray-600 uppercase tracking-widest">{{ $section['label'] }}
            </div>
            <div class="space-y-1">

                @foreach ($section['items'] as $item)
                    @if (empty($item['submenu']))
                        @php $isActive = $item['route'] && request()->routeIs($item['route']); @endphp
                        <a href="{{ $item['route'] ? route($item['route']) : '#' }}"
                            class="group flex items-center space-x-3 px-4 py-2.5 mx-2 transition-all duration-200 {{ $isActive ? 'bg-amber-500/10 text-amber-500 border-l-4 border-amber-500' : 'text-gray-400 hover:bg-white/5 hover:text-white border-l-4 border-transparent' }}">
                            <i class="{{ $item['icon'] }} text-sm"></i>
                            <span class="font-medium text-sm">{{ $item['name'] }}</span>
                        </a>
                    @else
                        @php
                            $subRoutes = collect($item['submenu'])->pluck('route')->toArray();
                            $hasActiveChild = request()->routeIs($subRoutes);
                        @endphp
                        <div x-data="{ open: {{ $hasActiveChild ? 'true' : 'false' }} }">
                            <button @click="open = !open"
                                class="w-full group flex items-center justify-between px-4 py-2.5 mx-2 rounded-lg transition-all duration-200 {{ $hasActiveChild ? 'text-white' : 'text-gray-400 hover:text-white' }}">
                                <div class="flex items-center space-x-3">
                                    <i class="{{ $item['icon'] }} text-sm"></i>
                                    <span class="font-medium text-sm">{{ $item['name'] }}</span>
                                </div>
                                <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-300"
                                    :class="open ? 'rotate-180' : ''"></i>
                            </button>

                            <div x-show="open" x-collapse class="relative ml-8 mt-1 space-y-1">
                                <div class="absolute left-0 top-0 bottom-4 w-px bg-gray-800"></div>

                                @foreach ($item['submenu'] as $subItem)
                                    @php $isSubActive = request()->routeIs($subItem['route']); @endphp
                                    <a href="{{ route($subItem['route']) }}"
                                        class="relative flex items-center py-2 pl-6 text-[13px] transition-all duration-200 group/link {{ $isSubActive ? 'text-amber-500 font-semibold' : 'text-gray-500 hover:text-gray-300' }}">

                                        <span
                                            class="absolute -left-[5px] w-2.5 h-2.5 rounded-full border-2 border-gray-950 {{ $isSubActive ? 'bg-amber-500' : 'bg-gray-700 group-hover/link:bg-gray-500' }}"></span>

                                        {{ $subItem['name'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endforeach
</div>
