<x-barber-layout>

    <x-slot name="title">Mi Panel de Reservas</x-slot>
    
    <x-slot name="subtitle">
        <span class="hidden sm:block">Gestiona tus citas y servicios con tu barbero profesional.</span>
    </x-slot>

    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 mt-4">
        <!-- Ventas del Mes -->
        <div
            class="bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-xl p-3.5 text-white flex items-center shadow-md">
            <i class="fa-solid fa-sack-dollar text-2xl mr-3 opacity-70 shrink-0"></i>
            <div class="min-w-0">
                <p class="text-[10px] uppercase font-bold opacity-80 truncate">Ventas del Mes</p>
                <p class="text-lg font-black leading-tight truncate">{{ $company->currency_code }}
                    {{ number_format($totalSalesMonth, 2) }}</p>
            </div>
        </div>

        <!-- Ventas de Hoy -->
        <div class="bg-gradient-to-br from-teal-500 to-teal-700 rounded-xl p-3.5 text-white flex items-center shadow-md">
            <i class="fa-solid fa-chart-line text-2xl mr-3 opacity-70 shrink-0"></i>
            <div class="min-w-0">
                <p class="text-[10px] uppercase font-bold opacity-80 truncate">Ventas Hoy</p>
                <p class="text-lg font-black leading-tight truncate">{{ $company->currency_code }}
                    {{ number_format($salesToday, 2) }}</p>
            </div>
        </div>

        <!-- Saldo en Caja -->
        <div
            class="bg-gradient-to-br from-cyan-600 to-blue-800 rounded-xl p-3.5 text-white flex items-center shadow-md">
            <i class="fa-solid fa-cash-register text-2xl mr-3 opacity-70 shrink-0"></i>
            <div class="min-w-0">
                <p class="text-[10px] uppercase font-bold opacity-80 truncate">Saldo en Caja</p>
                <p class="text-lg font-black leading-tight truncate">{{ $company->currency_code }}
                    {{ number_format($cashBalance, 2) }}</p>
            </div>
        </div>

        <!-- Órdenes del Mes -->
        <div
            class="bg-gradient-to-br from-purple-600 to-indigo-800 rounded-xl p-3.5 text-white flex items-center shadow-md">
            <i class="fa-solid fa-cart-shopping text-2xl mr-3 opacity-70 shrink-0"></i>
            <div class="min-w-0">
                <p class="text-[10px] uppercase font-bold opacity-80 truncate">Órdenes del Mes</p>
                <p class="text-lg font-black leading-tight truncate">{{ $company->currency_code }}
                    {{ number_format($totalOrdersMonth, 2) }}</p>
            </div>
        </div>

        <!-- Reservas del Mes -->
        <div
            class="bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl p-3.5 text-white flex items-center shadow-md">
            <i class="fa-solid fa-calendar-check text-2xl mr-3 opacity-70 shrink-0"></i>
            <div class="min-w-0">
                <p class="text-[10px] uppercase font-bold opacity-80 truncate">Reservas del Mes</p>
                <p class="text-lg font-black leading-tight truncate">{{ $totalBookingsMonth }}</p>
            </div>
        </div>

        <!-- Total Productos -->
        <div
            class="bg-gradient-to-br from-sky-500 to-indigo-600 rounded-xl p-3.5 text-white flex items-center shadow-md">
            <i class="fa-solid fa-boxes-stacked text-2xl mr-3 opacity-70 shrink-0"></i>
            <div class="min-w-0">
                <p class="text-[10px] uppercase font-bold opacity-80 truncate">Total Productos</p>
                <p class="text-lg font-black leading-tight truncate">{{ $totalProducts }}</p>
            </div>
        </div>

        <!-- Servicios Activos -->
        <div
            class="bg-gradient-to-br from-violet-500 to-purple-700 rounded-xl p-3.5 text-white flex items-center shadow-md">
            <i class="fa-solid fa-scissors text-2xl mr-3 opacity-70 shrink-0"></i>
            <div class="min-w-0">
                <p class="text-[10px] uppercase font-bold opacity-80 truncate">Servicios Activos</p>
                <p class="text-lg font-black leading-tight truncate">{{ $activeServices }}</p>
            </div>
        </div>

        <!-- Stock Bajo -->
        <div class="bg-gradient-to-br from-red-500 to-red-700 rounded-xl p-3.5 text-white flex items-center shadow-md">
            <i class="fa-solid fa-triangle-exclamation text-2xl mr-3 opacity-70 shrink-0"></i>
            <div class="min-w-0">
                <p class="text-[10px] uppercase font-bold opacity-80 truncate">Stock Bajo</p>
                <p class="text-lg font-black leading-tight truncate">{{ $lowStockProducts }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-6">

        <div
            class="bg-white p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow duration-300 rounded-2xl">
            <div class="flex items-center gap-4 mb-6">
                <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
                    <i class="fas fa-chart-line text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 text-lg">Ventas vs Reservas vs Órdenes</h3>
                    <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Análisis mensual</p>
                </div>
            </div>
            <div class="relative w-full h-64">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>

        <!-- Tarjeta 2: Actividad Semanal -->
        <div
            class="bg-white p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow duration-300 rounded-2xl">
            <div class="flex items-center gap-4 mb-6">
                <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                    <i class="fas fa-calendar-check text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 text-lg">Actividad Semanal</h3>
                    <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Flujo operativo</p>
                </div>
            </div>
            <div class="relative w-full h-64">
                <canvas id="weeklyChart"></canvas>
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">

        <div
            class="bg-white p-6 border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300 rounded-2xl">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-amber-50 text-amber-600 rounded-lg"><i class="fas fa-cut"></i></div>
                <h3 class="font-bold text-gray-900">Top Servicios</h3>
            </div>
            <div class="space-y-4">
                @foreach ($topServices as $service)
                    <div>
                        <div class="flex justify-between text-sm mb-1.5">
                            <span class="text-gray-600 font-medium truncate">{{ $service->name }}</span>
                            <span class="font-bold text-gray-900">{{ $service->bookings_count ?? 0 }}</span>
                        </div>
                        <div class="h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-amber-400 to-amber-600 rounded-full"
                                style="width: {{ min(100, ($service->bookings_count ?? 0) * 10) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Top Barberos -->
        <div
            class="bg-white p-6 border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300 rounded-2xl">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg"><i class="fas fa-user-tie"></i></div>
                <h3 class="font-bold text-gray-900">Top Barberos</h3>
            </div>
            <div class="space-y-3">
                @foreach ($topBarbers as $barber)
                    <div
                        class="flex justify-between items-center bg-gray-50/50 hover:bg-gray-50 transition-colors p-3 rounded-2xl border border-gray-100">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-xs font-bold">
                                {{ substr($barber->user->full_name ?? 'B', 0, 1) }}
                            </div>
                            <span
                                class="font-semibold text-gray-800 text-sm">{{ $barber->user->full_name ?? 'Barbero' }}</span>
                        </div>
                        <span
                            class="font-black text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg text-xs">{{ $barber->bookings_count }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Top Productos -->
        <div
            class="bg-white p-6 border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300 rounded-2xl">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg"><i class="fas fa-shopping-bag"></i></div>
                <h3 class="font-bold text-gray-900">Top Productos</h3>
            </div>
            <div class="space-y-4">
                @foreach ($topProducts as $product)
                    <div class="flex justify-between items-center group">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600">
                                <i class="fas fa-tag text-xs"></i>
                            </div>
                            <span
                                class="text-sm text-gray-600 font-medium group-hover:text-emerald-700 transition-colors">{{ $product->name }}</span>
                        </div>
                        <span class="font-bold text-emerald-600 text-sm">{{ $product->sale_items_count }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const chartInstances = {};

            function initCharts() {
                Object.values(chartInstances).forEach(c => c.destroy());

                Chart.defaults.font.family = "'Inter', 'Plus Jakarta Sans', sans-serif";
                Chart.defaults.color = '#64748b';

                const mCanvas = document.getElementById('monthlyChart');
                if (mCanvas) {
                    const mCtx = mCanvas.getContext('2d');
                    const height = mCanvas.clientHeight || 260;

                    const gradSales = mCtx.createLinearGradient(0, 0, 0, height);
                    gradSales.addColorStop(0, 'rgba(16, 185, 129, 0.5)');
                    gradSales.addColorStop(1, 'rgba(16, 185, 129, 0.05)');

                    const gradOrders = mCtx.createLinearGradient(0, 0, 0, height);
                    gradOrders.addColorStop(0, 'rgba(139, 92, 246, 0.5)');
                    gradOrders.addColorStop(1, 'rgba(139, 92, 246, 0.05)');

                    const gradBookings = mCtx.createLinearGradient(0, 0, 0, height);
                    gradBookings.addColorStop(0, 'rgba(14, 165, 233, 0.5)');
                    gradBookings.addColorStop(1, 'rgba(14, 165, 233, 0.05)');

                    chartInstances['monthly'] = new Chart(mCtx, {
                        type: 'line',
                        data: {
                            labels: @json($salesLabels),
                            datasets: [{
                                    label: 'Ventas Presenciales',
                                    data: @json($salesByMonth->pluck('total')),
                                    borderColor: 'rgb(16, 185, 129)',
                                    borderWidth: 3,
                                    backgroundColor: gradSales,
                                    fill: true,
                                    tension: 0.4,
                                    pointBackgroundColor: 'rgb(16, 185, 129)',
                                    pointHoverRadius: 6,
                                    pointRadius: 4
                                },
                                {
                                    label: 'Órdenes Online',
                                    data: @json($ordersByMonth->pluck('total')),
                                    borderColor: 'rgb(139, 92, 246)',
                                    borderWidth: 3,
                                    backgroundColor: gradOrders,
                                    fill: true,
                                    tension: 0.4,
                                    pointBackgroundColor: 'rgb(139, 92, 246)',
                                    pointHoverRadius: 6,
                                    pointRadius: 4
                                },
                                {
                                    label: 'Reservas',
                                    data: @json($bookingsByMonth->pluck('total')),
                                    borderColor: 'rgb(14, 165, 233)',
                                    borderWidth: 3,
                                    backgroundColor: gradBookings,
                                    fill: true,
                                    tension: 0.4,
                                    pointBackgroundColor: 'rgb(14, 165, 233)',
                                    pointHoverRadius: 6,
                                    pointRadius: 4
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                mode: 'index',
                                intersect: false,
                            },
                            plugins: {
                                legend: {
                                    position: 'top',
                                    align: 'end',
                                    labels: {
                                        usePointStyle: true,
                                        boxWidth: 8,
                                        boxHeight: 8,
                                        padding: 20,
                                        font: {
                                            size: 12,
                                            weight: '600'
                                        }
                                    }
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                    titleColor: '#f8fafc',
                                    bodyColor: '#cbd5e1',
                                    borderColor: 'rgba(255, 255, 255, 0.1)',
                                    borderWidth: 1,
                                    padding: 12,
                                    cornerRadius: 12,
                                    boxPadding: 6,
                                    usePointStyle: true
                                }
                            },
                            scales: {
                                x: {
                                    grid: {
                                        display: false,
                                        drawBorder: false
                                    },
                                    ticks: {
                                        font: {
                                            size: 11
                                        }
                                    }
                                },
                                y: {
                                    grid: {
                                        color: 'rgba(226, 232, 240, 0.8)',
                                        drawBorder: false
                                    },
                                    ticks: {
                                        font: {
                                            size: 11
                                        },
                                        callback: value => '{{ $company->currency_code }} ' + value
                                    }
                                }
                            }
                        }
                    });
                }

                const wCanvas = document.getElementById('weeklyChart');
                if (wCanvas) {
                    const wCtx = wCanvas.getContext('2d');

                    chartInstances['weekly'] = new Chart(wCtx, {
                        type: 'bar',
                        data: {
                            labels: @json($salesByWeek->pluck('day')),
                            datasets: [{
                                    label: 'Ventas',
                                    data: @json($salesByWeek->pluck('total')),
                                    backgroundColor: 'rgb(16, 185, 129)',
                                    borderRadius: 6,
                                    borderSkipped: false,
                                    borderWidth: 0,
                                    barPercentage: 0.6,
                                    categoryPercentage: 0.5
                                },
                                {
                                    label: 'Órdenes',
                                    data: @json($ordersByWeek->pluck('total')),
                                    backgroundColor: 'rgb(139, 92, 246)',
                                    borderRadius: 6,
                                    borderSkipped: false,
                                    borderWidth: 0,
                                    barPercentage: 0.6,
                                    categoryPercentage: 0.5
                                },
                                {
                                    label: 'Reservas',
                                    data: @json($bookingsByWeek->pluck('total')),
                                    backgroundColor: 'rgb(14, 165, 233)',
                                    borderRadius: 6,
                                    borderSkipped: false,
                                    borderWidth: 0,
                                    barPercentage: 0.6,
                                    categoryPercentage: 0.5
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                    align: 'end',
                                    labels: {
                                        usePointStyle: true,
                                        boxWidth: 8,
                                        boxHeight: 8,
                                        padding: 20,
                                        font: {
                                            size: 12,
                                            weight: '600'
                                        }
                                    }
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                    titleColor: '#f8fafc',
                                    bodyColor: '#cbd5e1',
                                    borderColor: 'rgba(255, 255, 255, 0.1)',
                                    borderWidth: 1,
                                    padding: 12,
                                    cornerRadius: 12,
                                    boxPadding: 6,
                                    usePointStyle: true
                                }
                            },
                            scales: {
                                x: {
                                    grid: {
                                        display: false,
                                        drawBorder: false
                                    },
                                    ticks: {
                                        font: {
                                            size: 11
                                        }
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(226, 232, 240, 0.8)',
                                        drawBorder: false
                                    },
                                    ticks: {
                                        font: {
                                            size: 11
                                        },
                                        callback: value => '{{ $company->currency_code }} ' + value
                                    }
                                }
                            }
                        }
                    });
                }
            }

            document.addEventListener('DOMContentLoaded', initCharts);
        </script>
    @endpush
    
</x-barber-layout>
