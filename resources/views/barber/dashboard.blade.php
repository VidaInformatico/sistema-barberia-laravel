<x-barber-layout>
    <x-slot name="title">Mi Panel de Reservas</x-slot>

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
            class="relative overflow-hidden bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl p-6 shadow-xl shadow-blue-600/10 text-white flex items-center group">
            <div
                class="absolute -right-4 -bottom-6 text-white/10 text-9xl font-black select-none pointer-events-none group-hover:scale-110 transition-transform duration-300">
                <i class="fa-solid fa-star"></i>
            </div>
            <div class="p-3.5 rounded-xl bg-white/15 backdrop-blur-md text-white mr-5 shadow-sm border border-white/10">
                <i class="fa-solid fa-star text-2xl"></i>
            </div>
            <div class="relative z-10">
                <p class="text-xs font-black uppercase tracking-widest text-white/70">Calificación</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <p class="text-2xl font-black text-white tracking-wide">{{ number_format($averageRating, 1) }}</p>
                    <span class="text-sm font-bold text-white/60">/ 5.0</span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white shadow-sm rounded-2xl border border-gray-200/80 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/70 flex items-center justify-between">
            <div class="flex items-center space-x-2.5">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Reservas Activas</h3>
            </div>
            <a href="{{ route('barber.bookings.index') }}"
                class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold text-gray-700 bg-white border border-gray-200 rounded-md shadow-sm hover:bg-gray-50 hover:text-amber-600 transition duration-150">
                Ver todos
                <i class="fa-solid fa-arrow-right ml-1.5 text-[10px]"></i>
            </a>
        </div>

        <div class="divide-y divide-gray-100">
            @forelse($activeBookings as $booking)
                <div
                    class="p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 hover:bg-gray-50/40 transition duration-150">
                    <div class="flex items-center space-x-5">
                        <div
                            class="flex-shrink-0 w-14 h-14 bg-gray-950 text-amber-500 rounded-xl flex flex-col items-center justify-center font-black text-sm uppercase shadow-md border border-gray-900 tracking-wider">
                            <span class="text-xl leading-none">{{ $booking->booking_date->format('d') }}</span>
                            <span class="text-[9px] font-bold mt-0.5">{{ $booking->booking_date->format('M') }}</span>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-gray-900 text-lg">
                                {{ $booking->services->first()->service_name ?? 'Servicio' }}
                            </h4>
                            <div
                                class="mt-1 flex flex-wrap items-center gap-y-1 gap-x-5 text-sm text-gray-500 font-medium">
                                <span class="flex items-center">
                                    <i class="fa-solid fa-user-tie mr-2 text-amber-500"></i>
                                    {{ $booking->barber->name }}
                                </span>
                                <span class="flex items-center">
                                    <i class="fa-solid fa-clock mr-2 text-gray-400"></i>
                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} -
                                    {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3">
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-{{ $booking->status_color }}-50 text-{{ $booking->status_color }}-800 border border-{{ $booking->status_color }}-200/60 shadow-sm">
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

    <div class="bg-white shadow-sm rounded-2xl border border-gray-200/80 overflow-hidden">
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
