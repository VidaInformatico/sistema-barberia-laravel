<nav id="navbar"
    class="fixed top-0 w-full z-40 transition-all duration-300 border-b border-gray-200/50 h-20 bg-white/80 backdrop-blur-md">
    <div class="max-w-7xl mx-auto px-6 h-full flex items-center justify-between gap-4">

        <a href="/" class="text-2xl font-black text-gray-900 tracking-tighter flex items-center gap-2 shrink-0">
            <i class="fa-solid fa-scissors text-amber-500 text-xl"></i>
            <span>BARBER<span class="text-amber-500">.</span></span>
        </a>

        <div class="hidden lg:flex items-center space-x-8 font-semibold text-sm text-gray-700 tracking-wide">
            <a href="{{ route('welcome') }}"
                class="relative py-2 hover:text-gray-900 transition-colors after:absolute after:bottom-0 after:left-0 after:w-0 after:h-[2px] after:bg-amber-500 hover:after:w-full after:transition-all after:duration-300 flex items-center gap-2">
                <i class="fa-solid fa-house text-xs opacity-70"></i> Inicio
            </a>
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

        <div class="hidden md:flex items-center flex-1 max-w-xs mx-4">
            <div class="relative w-full">
                <input type="text" placeholder="Buscar productos..."
                    class="w-full bg-gray-100 text-gray-800 text-xs font-medium pl-9 pr-4 py-2.5 border border-transparent rounded-sm focus:outline-none focus:bg-white focus:border-amber-500/50 transition-all">
                <i
                    class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            </div>
        </div>

        <div class="flex items-center gap-4 md:gap-6 shrink-0">

            <button id="cart-open-btn"
                class="relative p-2.5 text-gray-700 hover:text-amber-500 transition-colors duration-300 focus:outline-none"
                aria-label="Ver Carrito">
                <i class="fa-solid fa-basket-shopping text-xl"></i>
                <span
                    class="absolute -top-1 -right-1 bg-amber-500 text-gray-950 font-mono text-[10px] font-black w-5 h-5 rounded-full flex items-center justify-center shadow-md">
                    3
                </span>
            </button>

            <div class="hidden xl:flex flex-col items-end text-xs tracking-wider">
                <span class="font-bold text-gray-900 flex items-center gap-1.5">
                    <i class="fa-solid fa-phone text-amber-500 text-[10px]"></i> +51 999 999 999
                </span>
                <span class="text-gray-500 font-medium mt-0.5">Lun - Sáb: 9:00 - 20:00</span>
            </div>

            <div class="flex items-center gap-4">
                @auth
                    <!-- Perfil Usuario -->
                    <div class="relative group cursor-pointer">
                        <div
                            class="flex items-center gap-2 bg-gray-800 text-white px-5 py-3 rounded-sm text-xs font-bold uppercase tracking-widest hover:bg-gray-900 transition-all duration-300">
                            <i class="fa-solid fa-user-circle text-amber-500 text-sm"></i>
                            <span>{{ auth()->user()->name }}</span>
                            <i class="fa-solid fa-chevron-down text-[8px] opacity-50"></i>
                        </div>

                        <div
                            class="absolute right-0 pt-2 w-52 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                            <div class="bg-white border border-gray-100 shadow-xl rounded-sm py-1">
                                <a href="/admin/dashboard"
                                    class="flex items-center gap-3 px-4 py-3 text-xs font-bold text-gray-700 hover:bg-amber-50 hover:text-amber-600 uppercase tracking-widest transition-colors">
                                    <i class="fa-solid fa-gauge-high text-sm"></i> Dashboard
                                </a>
                                <div class="border-t border-gray-100"></div>
                                <form method="POST" action="#">
                                    @csrf
                                    <button type="submit"
                                        class="flex w-full items-center gap-3 px-4 py-3 text-xs font-bold text-red-600 hover:bg-red-50 uppercase tracking-widest transition-colors">
                                        <i class="fa-solid fa-right-from-bracket text-sm"></i> Salir
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Acceso -->
                    <div class="relative group cursor-pointer">
                        <div
                            class="flex items-center gap-2 bg-gray-900 text-white px-5 py-3 rounded-sm text-xs font-bold uppercase tracking-widest hover:bg-amber-500 transition-all duration-300">
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

            <button id="menu-btn"
                class="lg:hidden text-gray-900 p-2 focus:outline-none transition-transform active:scale-90"
                aria-label="Abrir menú">
                <i class="fa-solid fa-bars-staggered text-2xl"></i>
            </button>
        </div>
    </div>
</nav>


<div id="cart-sidebar" class="fixed inset-0 z-50 pointer-events-none invisible" role="dialog" aria-modal="true">
    <div id="cart-overlay"
        class="absolute inset-0 bg-stone-950/60 backdrop-blur-sm opacity-0 transition-opacity duration-500 pointer-events-auto">
    </div>

    <div id="cart-container"
        class="absolute top-0 right-0 h-full w-full max-w-md bg-stone-900 text-white shadow-2xl translate-x-full transition-transform duration-500 ease-in-out flex flex-col justify-between pointer-events-auto border-l border-stone-800">

        <div class="p-6 border-b border-stone-800 flex items-center justify-between">
            <h3 class="text-lg font-black uppercase tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-basket-shopping text-amber-500"></i> Tu Bolsa <span
                    class="text-xs font-mono text-stone-500 font-normal">(3 artículos)</span>
            </h3>
            <button id="cart-close-btn"
                class="text-stone-400 hover:text-amber-500 p-2 transition-colors focus:outline-none"
                aria-label="Cerrar bolsa">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-6 space-y-6">
            <div class="flex gap-4 border-b border-stone-800/50 pb-4">
                <div
                    class="w-16 h-16 bg-stone-950 rounded-sm overflow-hidden p-2 flex items-center justify-center shrink-0 border border-stone-800">
                    <img src="https://images.unsplash.com/photo-1608248597481-496100c8c836?auto=format&fit=crop&q=80&w=100"
                        alt="Pomada" class="max-h-full object-contain">
                </div>
                <div class="flex-1 flex flex-col justify-between">
                    <div>
                        <h4 class="text-sm font-bold tracking-tight text-stone-200 line-clamp-1">Pomada Mate Efecto
                            Arcilla</h4>
                        <span class="text-[10px] font-mono text-stone-500 uppercase tracking-widest">Reuzel
                            Holland</span>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <div class="flex items-center border border-stone-800 bg-stone-950 rounded-sm">
                            <button class="px-2 py-0.5 text-stone-400 hover:text-white text-xs">-</button>
                            <span class="px-2 text-xs font-mono font-bold">1</span>
                            <button class="px-2 py-0.5 text-stone-400 hover:text-white text-xs">+</button>
                        </div>
                        <span class="text-sm font-bold font-mono text-amber-500">$24.00</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 border-t border-stone-800 bg-stone-950/50">
            <div class="space-y-2 mb-6 text-sm font-medium">
                <div class="flex justify-between text-stone-400">
                    <span>Subtotal</span>
                    <span class="font-mono">$61.00</span>
                </div>
                <div
                    class="border-t border-stone-800/80 my-2 pt-2 flex justify-between text-base font-black uppercase tracking-tight">
                    <span>Total Estimado</span>
                    <span class="font-mono text-amber-500">$61.00</span>
                </div>
            </div>
            <div class="space-y-3">
                <a href="/checkout"
                    class="w-full bg-amber-500 hover:bg-amber-400 text-stone-950 font-bold uppercase text-xs tracking-widest py-4 rounded-sm transition-all block text-center shadow-lg active:scale-[0.98]">
                    Procesar Pedido Confidencial
                </a>
            </div>
        </div>
    </div>
</div>


<div id="mobile-menu"
    class="fixed inset-0 z-50 bg-gray-950 translate-x-full transition-transform duration-500 ease-in-out flex flex-col p-8 invisible">

    <div class="flex items-center justify-between w-full border-b border-white/10 pb-6 mb-6">
        <span class="text-xl font-black text-white tracking-tighter flex items-center gap-2">
            <i class="fa-solid fa-scissors text-amber-500"></i> BARBER<span class="text-amber-500">.</span>
        </span>
        <button id="close-menu" class="text-white p-2 hover:text-amber-500 transition-colors focus:outline-none"
            aria-label="Cerrar menú">
            <i class="fa-solid fa-xmark text-2xl"></i>
        </button>
    </div>

    <div class="w-full mb-6">
        <div class="relative w-full">
            <input type="text" placeholder="¿Qué producto buscas?"
                class="w-full bg-stone-900 text-white text-xs font-medium pl-10 pr-4 py-3 border border-stone-800 rounded-sm focus:outline-none focus:border-amber-500 transition-all placeholder-stone-500">
            <i
                class="fa-solid fa-magnifying-glass absolute left-3ebd top-1/2 -translate-y-1/2 text-stone-500 text-xs left-3.5"></i>
        </div>
    </div>

    <div class="flex flex-col space-y-2 overflow-y-auto flex-1 pr-2">
        <a href="/"
            class="text-gray-400 hover:text-white text-xl font-bold uppercase tracking-wider py-3 border-b border-white/5 flex items-center justify-between transition-colors">
            <span class="flex items-center gap-4"><i class="fa-solid fa-house text-amber-500 text-base w-6"></i>
                Inicio</span>
        </a>
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
            <span class="flex items-center gap-4"><i class="fa-solid fa-envelope text-amber-500 text-base w-6"></i>
                Contacto</span>
        </a>
        <a href="route('login')"
            class="text-gray-400 hover:text-white text-xl font-bold uppercase tracking-wider py-3 flex items-center justify-between transition-colors">
            <span class="flex items-center gap-4"><i class="fa-solid fa-user-lock text-amber-500 text-base w-6"></i>
                Mi Cuenta</span>
        </a>
    </div>

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

        const cartOpenBtn = document.getElementById('cart-open-btn');
        const cartCloseBtn = document.getElementById('cart-close-btn');
        const cartSidebar = document.getElementById('cart-sidebar');
        const cartOverlay = document.getElementById('cart-overlay');
        const cartContainer = document.getElementById('cart-container');

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

        const openCart = () => {
            if (mobileMenu && !mobileMenu.classList.contains('translate-x-full')) {
                closeMobileMenu();
            }
            cartSidebar.classList.remove('invisible');
            setTimeout(() => {
                cartOverlay.classList.remove('opacity-0');
                cartContainer.classList.remove('translate-x-full');
            }, 10);
            document.body.classList.add('overflow-hidden');
        };

        const closeCart = () => {
            cartOverlay.classList.add('opacity-0');
            cartContainer.classList.add('translate-x-full');
            setTimeout(() => cartSidebar.classList.add('invisible'), 500);
            document.body.classList.remove('overflow-hidden');
        };

        cartOpenBtn?.addEventListener('click', openCart);
        cartCloseBtn?.addEventListener('click', closeCart);
        cartOverlay?.addEventListener('click', closeCart);
    });
</script>
