<div>
    <nav id="navbar"
        class="fixed top-0 w-full z-40 transition-all duration-300 border-b border-gray-200/50 h-20 bg-white/80 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-full flex items-center justify-between gap-2 sm:gap-4">

            <!-- Logo -->
            <a href="{{ route('welcome') }}"
                class="text-xl sm:text-2xl font-black text-gray-900 tracking-tighter flex items-center gap-2 shrink-0">
                <i class="fa-solid fa-scissors text-amber-500 text-lg sm:text-xl"></i>
                <span class="truncate max-w-[120px] sm:max-w-none">{{ $company->name ?? env('APP_NAME') }}</span>
            </a>

            <!-- Enlaces de Escritorio -->
            <div class="hidden lg:flex items-center space-x-8 font-semibold text-sm text-gray-700 tracking-wide">
                <a href="{{ route('welcome') }}#section-service"
                    class="relative py-2 hover:text-gray-900 transition-colors after:absolute after:bottom-0 after:left-0 after:w-0 after:h-[2px] after:bg-amber-500 hover:after:w-full after:transition-all after:duration-300 flex items-center gap-2">
                    <i class="fa-solid fa-chair text-xs opacity-70"></i> Servicios
                </a>
                <a href="{{ route('shop') }}"
                    class="relative py-2 hover:text-gray-900 transition-colors after:absolute after:bottom-0 after:left-0 after:w-0 after:h-[2px] after:bg-amber-500 hover:after:w-full after:transition-all after:duration-300 flex items-center gap-2">
                    <i class="fa-solid fa-bag-shopping text-xs opacity-70"></i> Tienda
                </a>
                <a href="{{ route('welcome') }}#section-contact"
                    class="relative py-2 hover:text-gray-900 transition-colors after:absolute after:bottom-0 after:left-0 after:w-0 after:h-[2px] after:bg-amber-500 hover:after:w-full after:transition-all after:duration-300 flex items-center gap-2">
                    <i class="fa-solid fa-envelope text-xs opacity-70"></i> Contacto
                </a>
            </div>

            <div class="hidden md:flex items-center flex-1 max-w-xs mx-2 relative" x-data="{ openResults: false }">
                <div class="relative w-full">
                    <input type="text" wire:model.live.debounce.300ms="searchQuery" @focus="openResults = true"
                        placeholder="Buscar productos..."
                        class="w-full bg-gray-100 text-gray-800 text-xs font-medium pl-9 pr-4 py-2.5 border border-transparent rounded-sm focus:outline-none focus:bg-white focus:border-amber-500/50 transition-all">

                    <i
                        class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                </div>

                @if (!empty($searchQuery))
                    <div x-show="openResults" @click.away="openResults = false" x-transition
                        class="absolute top-full left-0 right-0 mt-2 bg-white border border-gray-200 shadow-2xl rounded-sm overflow-hidden z-50">

                        <div
                            class="px-4 py-2 text-[10px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 flex justify-between items-center bg-gray-50">
                            <span>Resultados</span>
                            <span wire:loading class="text-amber-600 font-normal">Buscando...</span>
                        </div>

                        <div class="max-h-60 overflow-y-auto divide-y divide-gray-100">
                            @forelse($searchResults as $product)
                                <a href="{{ route('shop.show', $product->slug) }}"
                                    class="flex items-center gap-3 px-4 py-3 hover:bg-amber-50/60 transition-colors group">

                                    <div
                                        class="w-10 h-10 bg-gray-100 rounded flex items-center justify-center shrink-0 overflow-hidden border border-gray-200">
                                        @if ($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}"
                                                alt="{{ $product->name }}" class="w-full h-full object-cover">
                                        @else
                                            <i class="fa-solid fa-box text-amber-500 text-xs"></i>
                                        @endif
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <h4
                                            class="text-xs font-bold text-gray-900 truncate group-hover:text-amber-600 transition-colors">
                                            {{ $product->name }}
                                        </h4>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="text-[10px] text-amber-600 font-mono font-bold">
                                                {{ number_format($product->price, 2) }}
                                            </span>
                                            <span class="text-gray-300">•</span>
                                            <span class="text-[10px] text-gray-500 flex items-center gap-1 truncate">
                                                <i class="fa-solid fa-location-dot text-[9px] text-amber-500"></i>
                                                {{ $product->branch->name ?? 'Sucursal general' }}
                                            </span>
                                        </div>
                                    </div>

                                    <i
                                        class="fa-solid fa-chevron-right text-[10px] text-gray-400 group-hover:text-amber-600 transition-colors"></i>
                                </a>
                            @empty
                                <div class="px-4 py-6 text-center text-gray-500 text-xs">
                                    No hay resultados para "<span
                                        class="text-gray-800 font-medium">{{ $searchQuery }}</span>"
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endif
            </div>

            <!-- Acciones de la Derecha -->
            <div class="flex items-center gap-2 sm:gap-4 shrink-0">

                <!-- Sucursales -->
                <div class="relative group cursor-pointer hidden sm:block">
                    <div
                        class="flex items-center gap-2 bg-gray-100 text-gray-800 px-3 sm:px-4 py-2 rounded-sm text-xs font-bold uppercase tracking-widest hover:bg-amber-500 hover:text-white transition-all duration-300 border border-gray-200">
                        <i class="fa-solid fa-location-dot text-amber-500 group-hover:text-white text-sm"></i>
                        <span class="max-w-[100px] truncate">{{ $currentBranch->name ?? 'Sucursal' }}</span>
                        <i class="fa-solid fa-chevron-down text-[8px] opacity-50"></i>
                    </div>

                    <div
                        class="absolute right-0 pt-2 w-56 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                        <div class="bg-white border border-gray-100 shadow-xl rounded-sm py-1">
                            <div
                                class="px-4 py-2 text-[10px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100">
                                Nuestras Sucursales
                            </div>
                            @foreach ($branches as $branch)
                                <button type="button" wire:click="setBranch({{ $branch->id }})"
                                    class="w-full flex items-center justify-between px-4 py-3 text-xs font-bold text-gray-700 hover:bg-amber-50 hover:text-amber-600 uppercase tracking-widest transition-colors text-left">
                                    <span>{{ $branch->name }}</span>
                                    @if (isset($currentBranch) && $currentBranch->id == $branch->id)
                                        <i class="fa-solid fa-check text-amber-500 text-xs"></i>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Carrito -->
                <button @click="$dispatch('open-cart')"
                    class="relative p-2 text-gray-700 hover:text-amber-500 transition-colors duration-300 focus:outline-none"
                    aria-label="Ver Carrito">
                    <i class="fa-solid fa-basket-shopping text-xl"></i>
                    <span
                        class="absolute -top-1 -right-1 bg-amber-500 text-gray-950 font-mono text-[10px] font-black w-5 h-5 rounded-full flex items-center justify-center shadow-md">
                        {{ array_sum(array_column($cart, 'quantity')) }}
                    </span>
                </button>

                <!-- Autenticación (Oculto en móvil pequeño, pasa al menú hamburguesa) -->
                <div class="hidden md:flex items-center gap-4">
                    @auth
                        <div class="relative group cursor-pointer">
                            <div
                                class="flex items-center gap-2 bg-gray-800 text-white px-4 py-2.5 rounded-sm text-xs font-bold uppercase tracking-widest hover:bg-gray-900 transition-all duration-300">
                                <i class="fa-solid fa-user-circle text-amber-500 text-sm"></i>
                                <span class="max-w-[100px] truncate">{{ auth()->user()->name }}</span>
                                <i class="fa-solid fa-chevron-down text-[8px] opacity-50"></i>
                            </div>

                            <div
                                class="absolute right-0 pt-2 w-52 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                                <div class="bg-white border border-gray-100 shadow-xl rounded-sm py-1">
                                    @php
                                        if (auth()->user()?->role === 'admin') {
                                            $dashboardRoute = '/admin/dashboard';
                                        } elseif (auth()->user()?->role === 'barber') {
                                            $dashboardRoute = '/barber/dashboard';
                                        } else {
                                            $dashboardRoute = '/client/dashboard';
                                        }
                                    @endphp
                                    <a href="{{ $dashboardRoute }}"
                                        class="flex items-center gap-3 px-4 py-3 text-xs font-bold text-gray-700 hover:bg-amber-50 hover:text-amber-600 uppercase tracking-widest transition-colors">
                                        <i class="fa-solid fa-gauge-high text-sm"></i> Dashboard
                                    </a>
                                    <div class="border-t border-gray-100"></div>
                                    <livewire:actions.logout-button />
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="relative group cursor-pointer">
                            <div
                                class="flex items-center gap-2 bg-gray-900 text-white px-4 py-2.5 rounded-sm text-xs font-bold uppercase tracking-widest hover:bg-amber-500 transition-all duration-300">
                                <i class="fa-solid fa-lock text-xs"></i>
                                <span>Acceder</span>
                            </div>

                            <div
                                class="absolute right-0 pt-2 w-48 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                                <div class="bg-white border border-gray-100 shadow-xl rounded-sm py-1">
                                    <a href="{{ route('login') }}"
                                        class="flex items-center gap-3 px-4 py-3 text-xs font-bold text-gray-700 hover:bg-amber-50 hover:text-amber-600 uppercase tracking-widest transition-colors">
                                        <i class="fa-solid fa-right-to-bracket text-sm"></i> Ingresar
                                    </a>
                                    <div class="border-t border-gray-100"></div>
                                    <a href="{{ route('register') }}"
                                        class="flex items-center gap-3 px-4 py-3 text-xs font-bold text-gray-700 hover:bg-amber-50 hover:text-amber-600 uppercase tracking-widest transition-colors">
                                        <i class="fa-solid fa-user-plus text-sm"></i> Registro
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endauth
                </div>

                <!-- Botón Menú Móvil -->
                <button id="menu-btn"
                    class="lg:hidden text-gray-900 p-2 focus:outline-none transition-transform active:scale-90"
                    aria-label="Abrir menú">
                    <i class="fa-solid fa-bars-staggered text-xl sm:text-2xl"></i>
                </button>
            </div>
        </div>
    </nav>

    <div x-data="{ open: false }" @open-cart.window="open = true" class="relative">

        <div x-show="open" class="fixed inset-0 z-[100] pointer-events-auto" style="display: none;">

            <div @click="open = false" x-show="open" x-transition:enter="transition-opacity duration-500"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity duration-500" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" class="absolute inset-0 bg-stone-950/60 backdrop-blur-sm">
            </div>

            <div x-show="open" x-transition:enter="transition-transform duration-500 ease-in-out"
                x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                x-transition:leave="transition-transform duration-500 ease-in-out"
                x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
                class="absolute top-0 right-0 h-full w-full max-w-md bg-stone-900 text-white shadow-2xl flex flex-col justify-between border-l border-stone-800">

                <div class="p-6 border-b border-stone-800 flex items-center justify-between">
                    <h3 class="text-lg font-black uppercase tracking-tight flex items-center gap-2">
                        <i class="fa-solid fa-basket-shopping text-amber-500"></i> Tu Bolsa
                        <span
                            class="text-xs font-mono text-stone-500 font-normal">({{ array_sum(array_column($cart, 'quantity')) }}
                            artículos)</span>
                    </h3>
                    <button @click="open = false"
                        class="text-stone-400 hover:text-amber-500 p-2 transition-colors focus:outline-none"
                        aria-label="Cerrar bolsa">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto p-6 space-y-6">
                    @forelse($cart as $id => $item)
                        <div class="flex gap-4 border-b border-stone-800/50 pb-4">
                            <div
                                class="w-16 h-16 bg-stone-950 rounded-sm overflow-hidden p-1 flex items-center justify-center shrink-0 border border-stone-800">
                                <img src="{{ Storage::url($item['image']) }}" alt="{{ $item['name'] }}"
                                    class="max-h-full object-contain">
                            </div>

                            <div class="flex-1 flex flex-col justify-between">
                                <div>
                                    <h4 class="text-sm font-bold tracking-tight text-stone-200 line-clamp-1">
                                        {{ $item['name'] }}</h4>
                                    <span
                                        class="text-[10px] font-mono text-stone-500 uppercase tracking-widest">{{ $item['category'] ?? '' }}</span>
                                </div>

                                <div class="flex items-center justify-between mt-2">
                                    <div
                                        class="flex items-center border border-stone-800 bg-stone-950 rounded-sm overflow-hidden">
                                        <button wire:click="updateQuantity('{{ $id }}', 'dec')"
                                            class="px-3 py-1 text-stone-400 hover:text-white hover:bg-stone-800 transition-colors text-xs font-bold border-r border-stone-800">-</button>

                                        <span
                                            class="px-3 text-xs font-mono font-bold text-white">{{ $item['quantity'] }}</span>

                                        <button wire:click="updateQuantity('{{ $id }}', 'inc')"
                                            class="px-3 py-1 text-stone-400 hover:text-white hover:bg-stone-800 transition-colors text-xs font-bold border-l border-stone-800">+</button>
                                    </div>

                                    <button wire:click="removeItem('{{ $id }}')"
                                        class="text-stone-600 hover:text-red-500 transition-all p-2" title="Eliminar">
                                        <i class="fa-solid fa-circle-xmark text-lg"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="flex items-center">
                                <span class="text-sm font-bold font-mono text-amber-500">
                                    {{ $company->currency_code }}{{ number_format($item['price'] * $item['quantity'], 2) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-stone-500 text-sm text-center py-10">Tu carrito está vacío.</p>
                    @endforelse
                </div>

                <div class="p-6 border-t border-stone-800 bg-stone-950/50">
                    <div class="space-y-2 mb-6 text-sm font-medium">
                        <div
                            class="border-t border-stone-800/80 my-2 pt-2 flex justify-between text-base font-black uppercase tracking-tight">
                            <span>Total Estimado</span>
                            <span
                                class="font-mono text-amber-500">{{ $company->currency_code }}{{ number_format(collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']), 2) }}</span>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <a href="{{ route('cart.index') }}"
                            class="w-full bg-amber-500 hover:bg-amber-400 text-stone-950 font-bold uppercase text-xs tracking-widest py-4 rounded-sm transition-all block text-center shadow-lg active:scale-[0.98]">
                            Ver Carrito
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div id="mobile-menu"
        class="fixed inset-0 z-50 bg-gray-950 translate-x-full transition-transform duration-500 ease-in-out flex flex-col p-8 invisible"
        wire:ignore.self>

        <!-- Cabecera del Menú -->
        <div class="flex items-center justify-between w-full border-b border-white/10 pb-6 mb-6">
            <span class="text-xl font-black text-white tracking-tighter flex items-center gap-2">
                <i class="fa-solid fa-scissors text-amber-500"></i> {{ $company->name ?? env('APP_NAME') }}<span
                    class="text-amber-500">.</span>
            </span>
            <button id="close-menu" class="text-white p-2 hover:text-amber-500 transition-colors focus:outline-none"
                aria-label="Cerrar menú">
                <i class="fa-solid fa-xmark text-2xl"></i>
            </button>
        </div>

        <div class="w-full mb-4" x-data="{ openResults: false }">
            <div class="relative w-full">
                <input type="text" wire:model.live.debounce.300ms="searchQuery" @focus="openResults = true"
                    @click.away="openResults = false" placeholder="¿Qué producto buscas?"
                    class="w-full bg-stone-900 text-white text-xs font-medium pl-10 pr-4 py-3 border border-stone-800 rounded-sm focus:outline-none focus:border-amber-500 transition-all placeholder-stone-500">

                <i
                    class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-stone-500 text-xs"></i>
            </div>

            @if (!empty($searchQuery))
                <div x-show="openResults" x-transition
                    class="mt-2 bg-stone-900 border border-stone-800 shadow-2xl rounded-sm overflow-hidden z-50">

                    <div
                        class="px-4 py-2 text-[10px] font-bold text-stone-400 uppercase tracking-wider border-b border-stone-800 flex justify-between items-center">
                        <span>Resultados de búsqueda</span>
                        <span wire:loading class="text-amber-500 font-normal">Buscando...</span>
                    </div>

                    <div class="max-h-60 overflow-y-auto divide-y divide-stone-800/50">
                        @forelse($searchResults as $product)
                            <a href="{{ route('shop.show', $product->slug) }}"
                                class="flex items-center gap-3 px-4 py-3 hover:bg-stone-800/80 transition-colors group">

                                <div
                                    class="w-10 h-10 bg-stone-800 rounded flex items-center justify-center shrink-0 overflow-hidden border border-stone-700">
                                    @if ($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}"
                                            alt="{{ $product->name }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="fa-solid fa-box text-amber-500 text-xs"></i>
                                    @endif
                                </div>

                                <div class="flex-1 min-w-0">
                                    <h4
                                        class="text-xs font-bold text-white truncate group-hover:text-amber-400 transition-colors">
                                        {{ $product->name }}
                                    </h4>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-[10px] text-amber-500 font-mono font-bold">
                                            {{ number_format($product->price, 2) }}
                                        </span>
                                        <span class="text-stone-600">•</span>
                                        <span class="text-[10px] text-stone-400 flex items-center gap-1 truncate">
                                            <i class="fa-solid fa-location-dot text-[9px] text-amber-500"></i>
                                            {{ $product->branch->name ?? 'Sucursal general' }}
                                        </span>
                                    </div>
                                </div>

                                <i
                                    class="fa-solid fa-chevron-right text-[10px] text-stone-600 group-hover:text-white transition-colors"></i>
                            </a>
                        @empty
                            <div class="px-4 py-6 text-center text-stone-500 text-xs">
                                No se encontraron productos para "<span class="text-white">{{ $searchQuery }}</span>"
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif
        </div>

        <!-- Selector de Sucursales (Móvil) -->
        <div class="w-full mb-6">
            <div x-data="{ openBranch: false }" class="relative">
                <button @click="openBranch = !openBranch" type="button"
                    class="w-full flex items-center justify-between bg-stone-900 border border-stone-800 text-white px-4 py-3 rounded-sm text-xs font-bold uppercase tracking-widest hover:border-amber-500 transition-all">
                    <span class="flex items-center gap-2 truncate">
                        <i class="fa-solid fa-location-dot text-amber-500"></i>
                        <span class="truncate">{{ $currentBranch->name ?? 'Seleccionar Sucursal' }}</span>
                    </span>
                    <i class="fa-solid fa-chevron-down text-[10px] opacity-75 transition-transform duration-300"
                        :class="{ 'rotate-180': openBranch }"></i>
                </button>

                <!-- Lista desplegable de sucursales -->
                <div x-show="openBranch" @click.away="openBranch = false" x-transition
                    class="absolute left-0 right-0 mt-2 bg-stone-900 border border-stone-800 shadow-2xl rounded-sm py-1 z-50">
                    <div
                        class="px-4 py-2 text-[10px] font-bold text-stone-400 uppercase tracking-wider border-b border-stone-800">
                        Nuestras Sucursales
                    </div>
                    @foreach ($branches as $branch)
                        <button type="button" wire:click="setBranch({{ $branch->id }})"
                            class="w-full flex items-center justify-between px-4 py-3 text-xs font-bold text-stone-300 hover:bg-amber-500 hover:text-gray-950 uppercase tracking-widest transition-colors text-left">
                            <span>{{ $branch->name }}</span>
                            @if (isset($currentBranch) && $currentBranch->id == $branch->id)
                                <i class="fa-solid fa-check text-amber-500 text-xs"></i>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Enlaces de Navegación -->
        <div class="flex flex-col space-y-2 overflow-y-auto flex-1 pr-2">
            <a href="{{ route('welcome') }}#section-service"
                class="text-gray-400 hover:text-white text-xl font-bold uppercase tracking-wider py-3 border-b border-white/5 flex items-center justify-between transition-colors">
                <span class="flex items-center gap-4"><i class="fa-solid fa-chair text-amber-500 text-base w-6"></i>
                    Servicios</span>
            </a>
            <a href="{{ route('shop') }}"
                class="text-gray-400 hover:text-white text-xl font-bold uppercase tracking-wider py-3 border-b border-white/5 flex items-center justify-between transition-colors">
                <span class="flex items-center gap-4"><i
                        class="fa-solid fa-bag-shopping text-amber-500 text-base w-6"></i> Tienda</span>
            </a>
            <a href="{{ route('welcome') }}#section-contact"
                class="text-gray-400 hover:text-white text-xl font-bold uppercase tracking-wider py-3 border-b border-white/5 flex items-center justify-between transition-colors">
                <span class="flex items-center gap-4"><i
                        class="fa-solid fa-envelope text-amber-500 text-base w-6"></i>
                    Contacto</span>
            </a>
            <a href="{{ route('login') }}"
                class="text-gray-400 hover:text-white text-xl font-bold uppercase tracking-wider py-3 flex items-center justify-between transition-colors">
                <span class="flex items-center gap-4"><i
                        class="fa-solid fa-user-lock text-amber-500 text-base w-6"></i>
                    Mi Cuenta</span>
            </a>
        </div>

        <!-- Pie del menú móvil -->
        <div class="border-t border-white/10 pt-6 mt-auto flex flex-col gap-3 shrink-0">
            <div class="flex items-center gap-3 text-gray-400 text-xs">
                <i class="fa-solid fa-phone text-amber-500"></i>
                <span>+51 999 999 999</span>
            </div>
            <div class="flex items-center gap-3 text-gray-400 text-xs">
                <i class="fa-solid fa-clock text-amber-500"></i>
                <span>Lun - Sáb: 9:00 - 20:00</span>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const menuBtn = document.getElementById('menu-btn');
            const closeMenuBtn = document.getElementById('close-menu');
            const mobileMenu = document.getElementById('mobile-menu');

            // const cartOpenBtn = document.getElementById('cart-open-btn');
            // const cartCloseBtn = document.getElementById('cart-close-btn');
            // const cartSidebar = document.getElementById('cart-sidebar');
            // const cartOverlay = document.getElementById('cart-overlay');
            // const cartContainer = document.getElementById('cart-container');

            const menuLinks = mobileMenu?.querySelectorAll('a');

            const openMobileMenu = () => {
                mobileMenu.classList.remove('invisible');
                setTimeout(() => mobileMenu.classList.remove('translate-x-full'), 10);
                document.body.classList.add('overflow-hidden');
            };

            const closeMobileMenu = () => {
                mobileMenu.classList.add('translate-x-full');
                setTimeout(() => mobileMenu.classList.add('invisible'), 500);
                document.body.classList.remove('overflow-hidden');
            };

            menuBtn?.addEventListener('click', openMobileMenu);
            closeMenuBtn?.addEventListener('click', closeMobileMenu);

            // 2. Escuchar el clic en cada enlace para cerrar el menú
            menuLinks?.forEach(link => {
                link.addEventListener('click', () => {
                    closeMobileMenu();
                });
            });

            // const openCart = () => {
            //     if (mobileMenu && !mobileMenu.classList.contains('translate-x-full')) {
            //         closeMobileMenu();
            //     }
            //     cartSidebar.classList.remove('invisible');
            //     setTimeout(() => {
            //         cartOverlay.classList.remove('opacity-0');
            //         cartContainer.classList.remove('translate-x-full');
            //     }, 10);
            //     document.body.classList.add('overflow-hidden');
            // };

            // const closeCart = () => {
            //     cartOverlay.classList.add('opacity-0');
            //     cartContainer.classList.add('translate-x-full');
            //     setTimeout(() => cartSidebar.classList.add('invisible'), 500);
            //     document.body.classList.remove('overflow-hidden');
            // };

            // cartOpenBtn?.addEventListener('click', openCart);
            // cartCloseBtn?.addEventListener('click', closeCart);
            // cartOverlay?.addEventListener('click', closeCart);
        });
    </script>
</div>
