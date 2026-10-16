<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'BarberSystem') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles

</head>

<body class="font-sans antialiased text-gray-900 bg-gray-50/50">

    <div class="h-screen flex w-full overflow-hidden" x-data="{ sidebarOpen: window.innerWidth >= 768 }">

        <div class="fixed inset-0 bg-gray-950/50 z-40 md:hidden" x-show="sidebarOpen" x-transition:opacity
            @click="sidebarOpen = false" style="display: none;">
        </div>

        <aside
            class="bg-gray-950 text-white flex flex-col z-50 transition-all duration-300 ease-in-out fixed inset-y-0 left-0 md:static flex-shrink-0 border-r border-gray-900"
            :class="{
                'translate-x-0 w-64': sidebarOpen,
                '-translate-x-full w-64 md:translate-x-0 md:w-0 md:overflow-hidden md:border-r-0': !sidebarOpen
            }">

            <div class="w-64 flex flex-col h-full flex-shrink-0 min-w-[16rem]">

                <!-- Logo y botón de cierre en móvil -->
                <div class="h-16 flex items-center justify-between px-6 border-b border-gray-900 flex-shrink-0">
                    @php
                        $url = match (auth()->user()?->role) {
                            'admin' => route('admin.dashboard'),
                            'barber' => route('barber.dashboard'),
                            default => route('client.dashboard'),
                        };
                    @endphp

                    <a href="{{ $url }}" class="flex items-center space-x-3 min-w-0">
                        <x-application-logo class="w-8 h-8 text-amber-500 shrink-0" />
                        <span class="font-black text-xl tracking-wider uppercase truncate">
                            {{ $company->name ?? config('app.name', 'BarberSystem') }}
                        </span>
                    </a>

                    <button @click="sidebarOpen = false"
                        class="md:hidden text-gray-400 hover:text-white p-1 focus:outline-none">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto w-full">

                    @if (auth()->user()?->role === 'admin')
                        @include('layouts.partials.sidebar-admin')
                    @elseif(auth()->user()?->role === 'barber')
                        @include('layouts.partials.sidebar-barber')
                    @elseif(auth()->user()?->role === 'client')
                        @include('layouts.partials.sidebar-client')
                    @else
                        <p class="text-xs text-red-400 p-2">Rol no reconocido</p>
                    @endif
                </nav>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0 w-full h-full overflow-hidden">

            <header
                class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 md:px-8 z-30 shadow-sm flex-shrink-0">
                <div class="flex items-center space-x-4">
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="p-2 rounded-xl text-gray-600 hover:text-gray-950 hover:bg-gray-100 transition focus:outline-none">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>
                    <div class="hidden sm:block">
                        <h2 class="font-extrabold text-base md:text-xl text-gray-900 tracking-tight">{{ $title }}
                        </h2>
                    </div>
                </div>

                <div class="flex items-center space-x-3 md:space-x-4">

                    <div class="flex items-center space-x-1 sm:space-x-2 h-full py-2">
                        <!-- Venta -->
                        <a href="{{ route('admin.sales.create') }}"
                            class="relative flex items-center space-x-2 px-3.5 py-2 rounded-lg text-xs font-semibold transition-all duration-200 {{ request()->routeIs('admin.sales.*') ? 'text-emerald-600 bg-emerald-50/80 font-bold shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}"
                            title="Nueva Venta">
                            <i class="fa-solid fa-cart-shopping text-sm {{ request()->routeIs('admin.sales.*') ? 'text-emerald-600' : 'text-gray-400' }}"></i>
                            <span class="hidden lg:inline">Nueva venta</span>
                            @if(request()->routeIs('admin.sales.*'))
                                <span class="absolute bottom-0 left-2 right-2 h-0.5 bg-emerald-600 rounded-full"></span>
                            @endif
                        </a>
                    
                        <!-- Productos -->
                        <a href="{{ route('admin.products.index') }}"
                            class="relative flex items-center space-x-2 px-3.5 py-2 rounded-lg text-xs font-semibold transition-all duration-200 {{ request()->routeIs('admin.products.*') ? 'text-blue-600 bg-blue-50/80 font-bold shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}"
                            title="Productos">
                            <i class="fa-solid fa-box text-sm {{ request()->routeIs('admin.products.*') ? 'text-blue-600' : 'text-gray-400' }}"></i>
                            <span class="hidden lg:inline">Productos</span>
                            @if(request()->routeIs('admin.products.*'))
                                <span class="absolute bottom-0 left-2 right-2 h-0.5 bg-blue-600 rounded-full"></span>
                            @endif
                        </a>
                    
                        <!-- Caja -->
                        <a href="{{ route('admin.boxes.index') }}"
                            class="relative flex items-center space-x-2 px-3.5 py-2 rounded-lg text-xs font-semibold transition-all duration-200 {{ request()->routeIs('admin.boxes.*') ? 'text-amber-600 bg-amber-50/80 font-bold shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}"
                            title="Control de Caja">
                            <i class="fa-solid fa-cash-register text-sm {{ request()->routeIs('admin.boxes.*') ? 'text-amber-600' : 'text-gray-400' }}"></i>
                            <span class="hidden lg:inline">Caja</span>
                            @if(request()->routeIs('admin.boxes.*'))
                                <span class="absolute bottom-0 left-2 right-2 h-0.5 bg-amber-600 rounded-full"></span>
                            @endif
                        </a>
                    </div>

                    <div class="h-6 w-px bg-gray-200"></div>

                    @if (auth()->user()?->role === 'admin' || auth()->user()?->role === 'barber')
                        <livewire:admin.notification-booking />
                    @endif

                    <div class="h-6 w-px bg-gray-200"></div>

                    <div x-data="{ openUser: false }" @click.away="openUser = false" class="relative">
                        <button @click="openUser = !openUser" class="flex items-center space-x-3 focus:outline-none">
                            <div class="text-right hidden md:block">
                                <p class="text-xs font-bold text-gray-800">{{ Auth::user()->name ?? 'Usuario' }}</p>
                                <p class="text-[10px] font-medium text-gray-400 uppercase">
                                    {{ Auth::user()->role ?? 'Rol' }}</p>
                            </div>
                            <div
                                class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-500 to-violet-600 flex items-center justify-center text-white font-black text-sm">
                                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                            </div>
                        </button>
                        <div x-show="openUser" x-transition
                            class="absolute right-0 mt-2.5 w-48 bg-white border border-gray-100 rounded-xl shadow-xl py-1 z-50 text-sm"
                            style="display: none;">
                            <a href="{{ route('profile') }}"
                                class="flex items-center space-x-2 px-4 py-2.5 text-gray-600 hover:bg-indigo-50">
                                <i class="fas fa-user text-gray-400 w-4"></i> <span>Mi Perfil</span>
                            </a>
                            <div class="border-t border-gray-100"></div>
                            <livewire:actions.logout-button />
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-4 md:p-8">
                <div class="max-w-7xl w-full mx-auto space-y-6">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        window.URL_PRINTER = "{{ env('URL_PRINTER') }}";

        document.addEventListener('livewire:init', () => {
            Livewire.on('swal', (event) => {
                const data = event[0];
                Swal.fire({
                    title: data.title,
                    text: data.text,
                    icon: data.icon
                });
            });

            // Confirmación de eliminación
            Livewire.on('confirm-delete', (valor) => {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "Esta acción eliminará al usuario permanentemente.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#374151',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('delete-confirmed', {
                            id: valor.id
                        });
                    }
                });
            });
        });

        function updateBranch(branchId) {
            fetch("{{ route('admin.update-branch') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        branch_id: branchId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Aviso',
                            text: 'Sucursal actualizada exitosamente',
                            icon: 'success',
                            toast: true,
                            timer: 3000,
                            position: 'top-end',
                            showConfirmButton: false,
                        });
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function printViaBluetooth(url) {
            window.location.href = `vidaprint://imprimir?modo=ticket&url=` + encodeURIComponent(url);
        }

        function printViaPC(url, printerName) {
            fetch(window.URL_PRINTER, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        venta_url: url,
                        printer: printerName,
                    })
                })
                .then(r => r.json())
                .then(res => {
                    if (!res.success) {
                        alert("Error de comunicación con la impresora: " + res.message);
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                        return;
                    }

                    window.location.reload();
                })
                .catch(err => {
                    alert("Error de comunicación con la impresora: " + err.message);

                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                });
        }

        function abrirVentanaEmergente(url) {
            const width = 800;
            const height = 600;
            const left = (window.screen.width / 2) - (width / 2);
            const top = (window.screen.height / 2) - (height / 2);
            const pdfWindow = window.open(
                url,
                '_blank',
                `width=${width},height=${height},top=${top},scrollbars=yes,resizable=yes`
            );
            pdfWindow.onload = function() {
                pdfWindow.print();
            };
        }
    </script>

    @stack('js')

    @livewireScripts

</body>

</html>
