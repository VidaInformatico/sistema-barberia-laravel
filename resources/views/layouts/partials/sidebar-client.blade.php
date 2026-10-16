@php $isDashboardActive = request()->routeIs('client.dashboard'); @endphp
<a href="{{ route('client.dashboard') }}"
    class="group flex items-center space-x-3 px-4 py-2.5 mx-2 transition-all duration-200 {{ $isDashboardActive ? 'bg-amber-500/10 text-amber-500 border-l-4 border-amber-500' : 'text-gray-400 hover:bg-white/5 hover:text-white border-l-4 border-transparent' }}">
    <i class="fa-solid fa-chart-pie text-sm"></i>
    <span class="font-medium text-sm">Mi Panel</span>
</a>

<div>
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

@php
    $isBookingsActive = request()->routeIs('client.bookings.*');
@endphp

<div x-data="{ open: @js($isBookingsActive) }">
    <button @click="open = !open"
        class="w-full group flex items-center justify-between px-4 py-2.5 mx-2 rounded-lg transition-all duration-200 {{ $isBookingsActive ? 'text-white' : 'text-gray-400 hover:text-white' }}">

        <div class="flex items-center space-x-3">
            <i class="fa-solid fa-calendar-check text-sm"></i>
            <span class="font-medium text-sm">Mis Citas</span>
        </div>

        <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-300"
            :class="open ? 'rotate-180' : ''"></i>
    </button>

    <div x-show="open" x-collapse class="relative ml-8 mt-1 space-y-1">
        <div class="absolute left-0 top-0 bottom-4 w-px bg-gray-800"></div>

        @php
            $links = [
                ['route' => 'client.bookings.create', 'label' => 'Nueva Cita'],
                ['route' => 'client.bookings.index', 'label' => 'Historial Citas'],
            ];
        @endphp

        @foreach ($links as $link)
            @php $isSubActive = request()->routeIs($link['route']); @endphp
            <a href="{{ route($link['route']) }}"
                class="relative flex items-center py-2 pl-6 text-[13px] transition-all duration-200 group/link {{ $isSubActive ? 'text-amber-500 font-semibold' : 'text-gray-500 hover:text-gray-300' }}">

                <span
                    class="absolute -left-[5px] w-2.5 h-2.5 rounded-full border-2 border-gray-950 {{ $isSubActive ? 'bg-amber-500' : 'bg-gray-700 group-hover/link:bg-gray-500' }}"></span>

                {{ $link['label'] }}
            </a>
        @endforeach
    </div>
</div>

{{-- Órdenes --}}
@php $isOrdersActive = request()->routeIs('client.orders.index'); @endphp
<a href="{{ route('client.orders.index') }}"
    class="group flex items-center space-x-3 px-4 py-2.5 mx-2 transition-all duration-200 {{ $isOrdersActive ? 'bg-amber-500/10 text-amber-500 border-l-4 border-amber-500' : 'text-gray-400 hover:bg-white/5 hover:text-white border-l-4 border-transparent' }}">
    <i class="fa-solid fa-bag-shopping text-sm"></i>
    <span class="font-medium text-sm">Órdenes</span>
</a>

@php $isBarbersActive = request()->routeIs('client.barbers.index'); @endphp
<a href="{{ route('client.barbers.index') }}"
    class="group flex items-center space-x-3 px-4 py-2.5 mx-2 transition-all duration-200 {{ $isBarbersActive ? 'bg-amber-500/10 text-amber-500 border-l-4 border-amber-500' : 'text-gray-400 hover:bg-white/5 hover:text-white border-l-4 border-transparent' }}">
    <i class="fa-solid fa-scissors text-sm"></i>
    <span class="font-medium text-sm">Barberos</span>
</a>

@php $isReviewsActive = request()->routeIs('client.reviews.index'); @endphp
<a href="{{ route('client.reviews.index') }}"
    class="group flex items-center space-x-3 px-4 py-2.5 mx-2 transition-all duration-200 {{ $isReviewsActive ? 'bg-amber-500/10 text-amber-500 border-l-4 border-amber-500' : 'text-gray-400 hover:bg-white/5 hover:text-white border-l-4 border-transparent' }}">
    <i class="fa-solid fa-star text-sm"></i>
    <span class="font-medium text-sm">Reseñas</span>
</a>

<a href="{{ route('shop') }}"
    class="group flex items-center space-x-3 px-4 py-2.5 mx-2 transition-all duration-200 text-gray-400 hover:bg-white/5 hover:text-white border-l-4 border-transparent">
    <i class="fa-solid fa-store text-sm"></i>
    <span class="font-medium text-sm">Tienda</span>
</a>
