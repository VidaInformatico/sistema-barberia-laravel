<div class="px-4 pt-3 pb-1 text-[11px] font-bold uppercase tracking-wider text-gray-500">Principal</div>

@php
    $isDashboardActive = request()->routeIs('barber.dashboard');
@endphp

<a href="{{ route('barber.dashboard') }}"
    class="group flex items-center space-x-3 px-4 py-2.5 mx-2 transition-all duration-200 {{ $isDashboardActive ? 'bg-amber-500/10 text-amber-500 border-l-4 border-amber-500' : 'text-gray-400 hover:bg-white/5 hover:text-white border-l-4 border-transparent' }}">
    <i class="fa-solid fa-chart-pie text-sm"></i>
    <span class="font-medium text-sm">Mi Panel</span>
</a>

<div class="px-4 pt-5 pb-1 text-[11px] font-bold uppercase tracking-wider text-gray-500">Gestión</div>

@php
    $isBookingsActive = request()->routeIs('barber.bookings.*');
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
                ['route' => 'barber.bookings.create', 'label' => 'Nueva Cita'],
                ['route' => 'barber.bookings.index', 'label' => 'Historial Citas'],
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

@php
    $isCashActive = request()->routeIs('barber.cash.*');
@endphp

<div x-data="{ open: @js($isCashActive) }">
    <button @click="open = !open"
        class="w-full group flex items-center justify-between px-4 py-2.5 mx-2 rounded-lg transition-all duration-200 {{ $isCashActive ? 'text-white' : 'text-gray-400 hover:text-white' }}">

        <div class="flex items-center space-x-3">
            <i class="fa-solid fa-vault text-sm"></i>
            <span class="font-medium text-sm">Caja</span>
        </div>

        <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-300"
            :class="open ? 'rotate-180' : ''"></i>
    </button>

    <div x-show="open" x-collapse class="relative ml-8 mt-1 space-y-1">
        <div class="absolute left-0 top-0 bottom-4 w-px bg-gray-800"></div>

        @php
            $cashLinks = [
                ['route' => 'barber.cash.register', 'label' => 'Apertura y Cierre'],
                ['route' => 'barber.cash.expenses', 'label' => 'Gastos'],
            ];
        @endphp

        @foreach ($cashLinks as $link)
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

@php
    $isSalesActive = request()->routeIs('barber.sales.*');
@endphp

<div x-data="{ open: @js($isSalesActive) }">
    <button @click="open = !open"
        class="w-full group flex items-center justify-between px-4 py-2.5 mx-2 rounded-lg transition-all duration-200 {{ $isSalesActive ? 'text-white' : 'text-gray-400 hover:text-white' }}">

        <div class="flex items-center space-x-3">
            <i class="fa-solid fa-cash-register text-sm"></i>
            <span class="font-medium text-sm">Ventas</span>
        </div>

        <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-300"
            :class="open ? 'rotate-180' : ''"></i>
    </button>

    <div x-show="open" x-collapse class="relative ml-8 mt-1 space-y-1">
        <div class="absolute left-0 top-0 bottom-4 w-px bg-gray-800"></div>

        @php
            $salesLinks = [
                ['route' => 'barber.sales.create', 'label' => 'Nueva Venta'],
                ['route' => 'barber.sales.index', 'label' => 'Listar Ventas'],
                ['route' => 'barber.sales.receivables', 'label' => 'Cuentas por Cobrar'],
                ['route' => 'barber.sales.payments', 'label' => 'Historial Pagos'],
            ];
        @endphp

        @foreach ($salesLinks as $link)
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

<div class="px-4 pt-5 pb-1 text-[11px] font-bold uppercase tracking-wider text-gray-500">Otros</div>

@php
    $isReviewsActive = request()->routeIs('barber.reviews.index');
@endphp

<a href="{{ route('barber.reviews.index') }}"
    class="group flex items-center space-x-3 px-4 py-2.5 mx-2 transition-all duration-200 {{ $isReviewsActive ? 'bg-amber-500/10 text-amber-500 border-l-4 border-amber-500' : 'text-gray-400 hover:bg-white/5 hover:text-white border-l-4 border-transparent' }}">
    <i class="fa-solid fa-star text-sm"></i>
    <span class="font-medium text-sm">Reseñas</span>
</a>