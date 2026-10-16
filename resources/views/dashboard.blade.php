<x-barber-layout>
    <x-slot name="title">Mi Panel de Reservas</x-slot>

    <x-slot name="subtitle">
        <span class="hidden sm:block">Gestiona tus citas y servicios con tu barbero profesional.</span>
    </x-slot>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
        <div
            class="relative overflow-hidden bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl p-6 shadow-xl shadow-amber-500/10 text-gray-950 flex items-center group">
            <div
                class="absolute -right-6 -bottom-6 text-gray-950/10 text-9xl font-black select-none pointer-events-none group-hover:scale-110 transition-transform duration-300">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div
                class="p-3.5 rounded-xl bg-white/15 backdrop-blur-md text-gray-950 mr-5 shadow-sm border border-white/10">
                <i class="fa-solid fa-clock text-2xl"></i>
            </div>
            <div class="relative z-10">
                <p class="text-xs font-black uppercase tracking-widest text-gray-950/70">Próxima Cita</p>
                <p class="font-black mt-1">
                    @if ($nextBooking)
                        {{ \Carbon\Carbon::parse($nextBooking->booking_date->format('Y-m-d') . ' ' . $nextBooking->start_time)->diffForHumans() }}
                    @else
                        Sin citas
                    @endif
                </p>
            </div>
        </div>

        <div
            class="relative overflow-hidden bg-gradient-to-br from-gray-900 to-gray-950 rounded-2xl p-6 shadow-xl shadow-gray-950/20 text-white flex items-center group border border-gray-800">
            <div
                class="absolute -right-6 -bottom-6 text-white/[0.03] text-9xl font-black select-none pointer-events-none group-hover:scale-110 transition-transform duration-300">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <div class="p-3.5 rounded-xl bg-amber-500 text-gray-950 mr-5 shadow-md shadow-amber-500/10">
                <i class="fa-solid fa-calendar-check text-2xl"></i>
            </div>
            <div class="relative z-10">
                <p class="text-xs font-bold uppercase tracking-widest text-gray-400">Citas Totales</p>
                <p class="text-2xl font-black text-amber-500 mt-1">{{ $totalCompleted }} <span
                        class="text-sm font-semibold text-gray-400">Asistidas</span></p>
            </div>
        </div>

        <div
            class="relative overflow-hidden bg-gradient-to-br from-emerald-600 to-teal-700 rounded-2xl p-6 shadow-xl shadow-emerald-600/10 text-white flex items-center group">
            <div
                class="absolute -right-4 -bottom-6 text-white/10 text-9xl font-black select-none pointer-events-none group-hover:scale-110 transition-transform duration-300">
                <i class="fa-solid fa-users"></i>
            </div>

            <div class="p-3.5 rounded-xl bg-white/15 backdrop-blur-md text-white mr-5 shadow-sm border border-white/10">
                <i class="fa-solid fa-scissors text-2xl"></i>
            </div>

            <div class="relative z-10">
                <p class="text-xs font-black uppercase tracking-widest text-white/70">Total de Barberos</p>
                <p class="text-2xl font-black text-white mt-1 tracking-wide">{{ $totalBarbers }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white shadow-sm border border-gray-200/80 overflow-hidden flex flex-col justify-between rounded-xl">
            <div>
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/70 flex items-center justify-between">
                    <div class="flex items-center space-x-2.5">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Reservas Activas</h3>
                    </div>
                    <a href="{{ route('client.bookings.index') }}" class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold text-gray-700 bg-white border border-gray-200 rounded-md shadow-sm hover:bg-gray-50 hover:text-amber-600 transition duration-150">
                        Ver todos
                        <i class="fa-solid fa-arrow-right ml-1.5 text-[10px]"></i>
                    </a>
                </div>
    
                <div class="divide-y divide-gray-100">
                    @forelse($activeBookings as $booking)
                        <div class="p-5 flex items-center justify-between gap-4 hover:bg-gray-50/40 transition duration-150">
                            <div class="flex items-center space-x-4 min-w-0">
                                <div class="flex-shrink-0 w-14 h-14 bg-gray-950 text-amber-500 rounded-xl flex flex-col items-center justify-center font-black text-sm uppercase shadow-md border border-gray-900 tracking-wider">
                                    <span class="text-xl leading-none">{{ $booking->booking_date->format('d') }}</span>
                                    <span class="text-[9px] font-bold mt-0.5">{{ $booking->booking_date->format('M') }}</span>
                                </div>
                                <div class="min-w-0 flex-1 pr-2">
                                    <h4 class="font-extrabold text-gray-900 text-base truncate">
                                        {{ $booking->services->first()->service_name ?? 'Servicio' }}
                                    </h4>
    
                                    <div class="mt-1 flex items-center gap-2 flex-wrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-gray-100 text-gray-800 border border-gray-200/80">
                                            <i class="fa-solid fa-shop mr-1 text-amber-500 text-[9px]"></i>
                                            {{ $booking->branch->name ?? 'Sucursal' }}
                                        </span>
                                    </div>
    
                                    <div class="mt-1.5 flex items-center gap-4 text-xs text-gray-500 font-medium">
                                        <span class="flex items-center truncate">
                                            <i class="fa-solid fa-user-tie mr-1.5 text-amber-500 text-[11px]"></i>
                                            {{ $booking->barber->name }}
                                        </span>
                                        <span class="flex items-center flex-shrink-0">
                                            <i class="fa-solid fa-clock mr-1.5 text-gray-400 text-[11px]"></i>
                                            {{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-{{ $booking->status_color }}-50 text-{{ $booking->status_color }}-800 border border-{{ $booking->status_color }}-200/60 shadow-sm">
                                    <i class="fa-solid fa-circle-check mr-1.5 text-{{ $booking->status_color }}-500"></i>
                                    {{ $booking->status_label }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="p-6 text-center text-gray-500">No tienes reservas activas.</p>
                    @endforelse
                </div>
            </div>
        </div>
    
        <div class="bg-white shadow-sm border border-gray-200/80 overflow-hidden flex flex-col justify-between rounded-xl">
            <div>
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/70 flex items-center justify-between">
                    <div class="flex items-center space-x-2.5">
                        <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Órdenes Recientes</h3>
                    </div>
                    <a href="{{ route('client.orders.index') }}" class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold text-gray-700 bg-white border border-gray-200 rounded-md shadow-sm hover:bg-gray-50 hover:text-blue-600 transition duration-150">
                        Ver todos
                        <i class="fa-solid fa-arrow-right ml-1.5 text-[10px]"></i>
                    </a>
                </div>
    
                <div class="divide-y divide-gray-100">
                    @forelse($recentOrders as $order)
                        <div class="p-5 flex items-center justify-between gap-4 hover:bg-gray-50/40 transition duration-150">
                            <div class="flex items-center space-x-4 min-w-0">
                                <div class="flex-shrink-0 w-14 h-14 bg-gray-950 text-blue-500 rounded-xl flex flex-col items-center justify-center font-black text-sm uppercase shadow-md border border-gray-900 tracking-wider">
                                    <span class="text-[10px] leading-tight">ORD</span>
                                    <span class="text-[10px] font-bold mt-0.5">#{{ $order->id }}</span>
                                </div>
                                <div class="min-w-0 flex-1 pr-2">
                                    <h4 class="font-extrabold text-gray-900 text-base truncate">
                                        {{ $order->order_number }}
                                    </h4>
    
                                    <div class="mt-1 flex items-center gap-2 flex-wrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-gray-100 text-gray-800 border border-gray-200/80">
                                            <i class="fa-solid fa-shop mr-1 text-blue-500 text-[9px]"></i>
                                            {{ $order->branch->name ?? 'Sucursal' }}
                                        </span>
                                    </div>
    
                                    <div class="mt-1.5 flex items-center gap-4 text-xs text-gray-500 font-medium">
                                        <span class="flex items-center truncate">
                                            <i class="fa-solid fa-tag mr-1.5 text-blue-500 text-[11px]"></i>
                                            {{ $order->state->name ?? 'Estado' }}
                                        </span>
                                        <span class="flex items-center font-bold text-gray-900 flex-shrink-0">
                                            <i class="fa-solid fa-dollar-sign mr-0.5 text-gray-400 text-[11px]"></i>
                                            {{ number_format($order->total, 2) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-800 border border-gray-200 shadow-sm">
                                    <i class="fa-solid fa-box mr-1.5 text-blue-500"></i>
                                    {{ $order->created_at->format('d/m/Y') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="p-6 text-center text-gray-500">No tienes órdenes recientes.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white shadow-sm border border-gray-200/80 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/70">
            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Historial de Citas</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="bg-gray-50/30 border-b border-gray-200 text-xs font-bold text-gray-400 uppercase tracking-wider">
                        <th class="py-4 px-6">Fecha</th>
                        <th class="py-4 px-6">Servicio</th>
                        <th class="py-4 px-6">Barbero</th>
                        <th class="py-4 px-6">Precio</th>
                        <th class="py-4 px-6 text-right">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm font-medium text-gray-600">
                    @forelse ($history as $booking)
                        <tr class="hover:bg-gray-50/30 transition duration-150">
                            <td class="py-4 px-6 font-semibold text-gray-900">
                                {{ $booking->booking_date->format('d \d\e M, Y') }}
                            </td>
                            <td class="py-4 px-6 font-bold text-gray-900">
                                {{ $booking->services->first()->service_name ?? 'N/A' }}
                            </td>
                            <td class="py-4 px-6">{{ $booking->barber->name }}</td>
                            <td class="py-4 px-6 font-extrabold text-gray-950">
                                {{ $company->currency_code }}{{ number_format($booking->total_price, 2) }}
                            </td>
                            <td class="py-4 px-6 text-right">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-{{ $booking->status_color }}-50 text-{{ $booking->status_color }}-800 border border-{{ $booking->status_color }}-100">
                                    {{ $booking->status_label }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-500 italic">
                                No hay historial de citas disponible.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-barber-layout>
