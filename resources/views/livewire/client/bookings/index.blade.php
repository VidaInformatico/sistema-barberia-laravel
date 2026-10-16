<div class="page-container">
    <header class="page-header">
        <div class="flex items-center gap-3">
            <div class="header-icon">
                <i class="fas fa-history"></i>
            </div>
            <div>
                <h2 class="text-2xl font-black tracking-tight text-slate-800 leading-none">Panel de Reservas</h2>
                <p class="text-slate-400 text-[11px] font-bold uppercase tracking-wider mt-1">
                    Monitorea y gestiona las citas de la barbería en tiempo real
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3 flex-wrap">
            <div
                class="flex items-center gap-2 bg-emerald-50 text-emerald-600 px-3 py-1.5 rounded-full text-[10px] font-black uppercase border border-emerald-200 tracking-wider">
                <span class="relative flex h-2 w-2">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span>Sincronizado</span>
            </div>

            <a href="{{ route('client.bookings.create') }}" class="btn-primary inline-flex items-center gap-2">
                <i class="fas fa-plus text-xs"></i>
                Nueva Reserva
            </a>
        </div>
    </header>

    <div class="toolbar-container grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end w-full">

        <div class="w-full">
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">
                Buscar Código
            </label>
            <div class="relative flex items-center bg-slate-50 rounded-lg border border-slate-100 w-full h-[42px]">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                    <i class="fas fa-search text-sm"></i>
                </div>
                <input type="text" wire:model.live.debounce.300ms="search"
                    class="bg-transparent border-none pl-11 pr-4 text-sm font-bold text-slate-700 focus:ring-0 w-full h-full placeholder-slate-400"
                    placeholder="Ej. BK-2026...">
            </div>
        </div>

        <div class="w-full">
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">
                Barbero
            </label>
            <div class="flex items-center bg-slate-50 px-3 rounded-lg border border-slate-100 w-full h-[42px]">
                <select wire:model.live="selectedBarber"
                    class="bg-transparent border-none p-0 pr-8 text-sm font-bold text-slate-700 focus:ring-0 cursor-pointer w-full h-full">
                    <option value="">Todos los barberos</option>
                    @foreach ($barbers as $barber)
                        <option value="{{ $barber->id }}">{{ $barber->user->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="w-full">
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">
                Estado
            </label>
            <div class="flex items-center bg-slate-50 px-3 rounded-lg border border-slate-100 w-full h-[42px]">
                <select wire:model.live="selectedStatus"
                    class="bg-transparent border-none p-0 pr-8 text-sm font-bold text-slate-700 focus:ring-0 cursor-pointer w-full h-full">
                    <option value="">Todos los estados</option>
                    @foreach ($statuses as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="w-full">
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">
                Fecha de Reserva
            </label>
            <div class="flex items-center bg-slate-50 px-3 rounded-lg border border-slate-100 w-full h-[42px]">
                <input type="date" wire:model.live="selectedDate"
                    class="bg-transparent border-none p-0 text-sm font-bold text-slate-700 focus:ring-0 cursor-pointer w-full h-full">
            </div>
        </div>

    </div>

    <div class="block md:hidden space-y-3">
        @forelse($bookings as $booking)
            @php
                $colorMap = [
                    'yellow' => 'bg-amber-100 text-amber-600',
                    'blue' => 'bg-blue-100 text-blue-600',
                    'indigo' => 'bg-indigo-100 text-indigo-600',
                    'green' => 'bg-emerald-100 text-emerald-600',
                    'red' => 'bg-rose-100 text-rose-600',
                    'gray' => 'bg-slate-100 text-slate-500',
                ];
                $theme = $colorMap[$booking->status_color] ?? $colorMap['gray'];
            @endphp

            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-3"
                wire:key="booking-card-{{ $booking->id }}">
                <!-- Encabezado: Código de Reserva, Estado y Precio -->
                <div class="flex items-start justify-between border-b border-slate-100 pb-2.5">
                    <div class="flex flex-col">
                        <span class="text-xs font-black text-indigo-600 tracking-tight">
                            {{ $booking->booking_number }}
                        </span>
                        <span class="text-xs font-bold text-slate-700 mt-0.5">
                            Barbero: <span
                                class="text-slate-800 font-black">{{ $booking->barber?->user?->name ?? 'Sin asignar' }}</span>
                        </span>
                    </div>

                    <div class="flex flex-col items-end gap-1">
                        <span class="px-2.5 py-0.5 text-[10px] font-black rounded-full uppercase {{ $theme }}">
                            {{ $booking->status_label }}
                        </span>
                        <span class="text-xs font-black text-slate-800">
                            {{ $company->currency_code }}{{ number_format($booking->total_price, 2) }}
                        </span>
                    </div>
                </div>

                <!-- Detalles de Horario y Fecha -->
                <div
                    class="bg-slate-50 p-2.5 rounded-lg border border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                    <span class="flex items-center gap-1.5 font-bold text-slate-700">
                        <i class="far fa-calendar-alt text-slate-400"></i>
                        {{ $booking->booking_date->format('d/m/Y') }}
                    </span>
                    <span class="flex items-center gap-1.5 font-medium">
                        <i class="far fa-clock text-slate-400"></i>
                        {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} -
                        {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }} hs
                    </span>
                </div>

                <!-- Acciones Rápidas Móvil -->
                <div class="flex items-center justify-between gap-2 pt-1 border-t border-slate-50">
                    <button wire:click="showBooking({{ $booking->id }})"
                        class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors shrink-0"
                        title="Ver Historial y Detalles">
                        <i class="fas fa-eye text-xs"></i>
                    </button>

                    <div class="flex items-center gap-1.5 flex-wrap justify-end">
                        @if ($booking->isPending())
                            <button wire:click="openStatusModal({{ $booking->id }}, 'confirmed')"
                                class="px-2.5 py-1 text-[10px] font-black uppercase bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition-colors">
                                Confirmar
                            </button>
                        @endif

                        @if ($booking->isConfirmed())
                            <button wire:click="openStatusModal({{ $booking->id }}, 'in_progress')"
                                class="px-2.5 py-1 text-[10px] font-black uppercase bg-indigo-100 text-indigo-600 rounded-lg hover:bg-indigo-200 transition-colors">
                                Iniciar
                            </button>
                        @endif

                        @if ($booking->isInProgress())
                            <button wire:click="openStatusModal({{ $booking->id }}, 'completed')"
                                class="px-2.5 py-1 text-[10px] font-black uppercase bg-emerald-100 text-emerald-600 rounded-lg hover:bg-emerald-200 transition-colors">
                                Finalizar
                            </button>
                        @endif

                        @if (!$booking->isCompleted() && !$booking->isCancelled() && !$booking->isNoShow())
                            <button wire:click="openStatusModal({{ $booking->id }}, 'no_show')"
                                class="px-2 py-1 text-[10px] font-black uppercase bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200 transition-colors">
                                No Asistió
                            </button>
                            <button wire:click="openStatusModal({{ $booking->id }}, 'cancelled')"
                                class="px-2 py-1 text-[10px] font-black uppercase bg-rose-100 text-rose-600 rounded-lg hover:bg-rose-200 transition-colors">
                                Cancelar
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white p-8 rounded-xl border border-slate-200 text-center">
                <i class="fas fa-calendar-times text-slate-200 text-3xl mb-3 block"></i>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">
                    No se encontraron reservas con los filtros seleccionados.
                </p>
            </div>
        @endforelse
    </div>

    <div class="datatable-container hidden md:block overflow-x-auto">
        <table class="datatable w-full border-collapse">
            <thead class="datatable-thead">
                <tr>
                    <th class="datatable-th py-3 px-4">Código / Horario</th>
                    <th class="datatable-th py-3 px-4">Barbero</th>
                    <th class="datatable-th py-3 px-4">Estado</th>
                    <th class="datatable-th py-3 px-4">Total</th>
                    <th class="datatable-th py-3 px-4 text-right">Acciones Rápidas</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($bookings as $booking)
                    <tr class="datatable-tr hover:bg-slate-50/50 transition-colors"
                        wire:key="booking-row-{{ $booking->id }}">
                        <td class="datatable-td py-4 px-4 align-top">
                            <div class="flex flex-col gap-1">
                                <span class="text-xs font-black text-indigo-600 tracking-tight">
                                    {{ $booking->booking_number }}
                                </span>
                                <div class="flex items-center text-xs text-slate-500 font-medium">
                                    <i class="far fa-clock text-[10px] mr-1 text-slate-400"></i>
                                    <span>{{ $booking->booking_date->format('d/m/Y') }}</span>
                                    <span class="mx-1">•</span>
                                    <span>{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</span>
                                </div>
                            </div>
                        </td>

                        <td class="datatable-td py-4 px-4 align-top">
                            <span class="text-xs font-bold text-slate-700">
                                {{ $booking->barber?->user?->name ?? 'No asignado' }}
                            </span>
                        </td>

                        <td class="datatable-td py-4 px-4 align-top whitespace-nowrap">
                            @php
                                $colorMap = [
                                    'yellow' => 'bg-amber-100 text-amber-700',
                                    'blue' => 'bg-blue-100 text-blue-700',
                                    'indigo' => 'bg-indigo-100 text-indigo-700',
                                    'green' => 'bg-emerald-100 text-emerald-700',
                                    'red' => 'bg-rose-100 text-rose-700',
                                    'gray' => 'bg-slate-100 text-slate-600',
                                ];
                                $theme = $colorMap[$booking->status_color] ?? $colorMap['gray'];
                            @endphp
                            <span
                                class="inline-flex items-center px-2.5 py-1 text-[10px] font-black rounded-full uppercase tracking-wider {{ $theme }}">
                                {{ $booking->status_label }}
                            </span>
                        </td>

                        <td class="datatable-td py-4 px-4 align-top whitespace-nowrap">
                            <span class="text-sm font-black text-slate-800">
                                {{ $company->currency_code }}{{ number_format($booking->total_price, 2) }}
                            </span>
                        </td>

                        <td class="datatable-td py-4 px-4 text-right align-top">
                            <div class="inline-flex items-center justify-end gap-1.5 flex-wrap">
                                <button wire:click="showBooking({{ $booking->id }})"
                                    class="p-1.5 text-xs font-bold bg-slate-100 text-slate-600 rounded-md hover:bg-slate-200 transition-colors shadow-sm"
                                    title="Ver Historial y Detalles">
                                    <i class="fas fa-eye text-[11px]"></i>
                                </button>

                                @if (!$booking->isCompleted() && !$booking->isCancelled() && !$booking->isNoShow())
                                    <div class="h-4 w-[1px] bg-slate-200 mx-0.5"></div>
                                @endif

                                @if ($allow_client_cancellation && !$booking->isCompleted() && !$booking->isCancelled() && !$booking->isNoShow())
                                    <button wire:click="openStatusModal({{ $booking->id }}, 'cancelled')"
                                        class="px-2 py-1 text-[10px] font-black uppercase tracking-wider bg-rose-50 text-rose-600 border border-rose-200 rounded-md hover:bg-rose-100 transition-colors"
                                        title="Cancelar">
                                        Cancelar
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-16 text-center">
                            <i class="fas fa-calendar-times text-slate-300 text-3xl mb-3 block"></i>
                            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">
                                No se encontraron reservas con los filtros seleccionados.
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if (method_exists($bookings, 'links'))
        <div class="mt-4 px-2">
            {{ $bookings->links() }}
        </div>
    @endif

    <div x-data="{ open: false }"
        x-on:open-modal.window="if ($event.detail.name === 'status-confirmation-modal') open = true"
        x-on:close-modal.window="if ($event.detail.name === 'status-confirmation-modal') open = false" x-show="open"
        class="modal-overlay" style="display: none;">
        <div class="modal-backdrop" x-on:click="open = false"></div>

        <div class="modal-container max-w-lg">
            <div class="modal-header">
                <div class="flex items-center gap-3">
                    <div class="modal-icon-box">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <h3 class="modal-title">Cambiar Estado de la Reserva</h3>
                </div>
                <button x-on:click="open = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <form wire:submit.prevent="processStatusChange">
                <div class="modal-body">
                    <p class="text-xs text-slate-500 font-medium mb-4 leading-relaxed">
                        Vas a actualizar la reserva de forma irreversible. Agrega notas adicionales para el historial si
                        es necesario.
                    </p>

                    <div class="space-y-4">
                        @if ($newStatus === 'cancelled')
                            <div>
                                <label class="modal-label !text-rose-600">Motivo de Cancelación (Requerido)</label>
                                <div class="modal-input-group !border-rose-200">
                                    <i class="fas fa-exclamation-circle modal-input-icon !text-rose-400"></i>
                                    <input type="text" wire:model="cancellationReason" class="modal-input"
                                        placeholder="Ej: Cliente canceló por llamada">
                                </div>
                                @error('cancellationReason')
                                    <span
                                        class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif

                        <div>
                            <label class="modal-label">Comentarios Internos / Notas de Auditoría</label>
                            <textarea wire:model="adminComment" class="modal-input min-h-[100px] !p-3"
                                placeholder="Escribe un comentario justificando la acción..."></textarea>
                            @error('adminComment')
                                <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" x-on:click="open = false" class="btn-modal-secondary">Cancelar</button>
                    <button type="submit" class="btn-modal-primary">
                        <span class="relative z-10">Confirmar Cambio</span>
                        <i
                            class="fas fa-check-circle text-indigo-400 group-hover:text-white transition-colors text-xs relative z-10"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if ($isDetailOpen && $selectedBookingData)
        <div class="modal-overlay">
            <div class="modal-backdrop" wire:click="closeDetailModal"></div>

            <div class="modal-container max-w-2xl">
                <div class="modal-header">
                    <div class="flex items-center gap-3">
                        <div class="modal-icon-box">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div>
                            <h3 class="modal-title">Detalles de Reserva: {{ $selectedBookingData->booking_number }}
                            </h3>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Historial y
                                auditoría de la cita</p>
                        </div>
                    </div>
                    <button wire:click="closeDetailModal" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                <div class="modal-body !p-6">
                    <div
                        class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-100 mb-6">
                        <div>
                            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-2">Información
                                del Cliente</h4>
                            <div class="flex flex-col gap-1">
                                <span class="text-sm font-bold text-slate-700"><i
                                        class="fas fa-user text-slate-400 mr-2 text-xs"></i>{{ $selectedBookingData->client?->name ?? 'Cliente General' }}</span>
                                <span class="text-xs font-medium text-slate-600"><i
                                        class="fas fa-phone text-slate-400 mr-2 text-xs"></i>{{ $selectedBookingData->client?->phone ?? '-' }}</span>
                                <span class="text-xs font-medium text-slate-600"><i
                                        class="fas fa-envelope text-slate-400 mr-2 text-xs"></i>{{ $selectedBookingData->client?->email ?? '-' }}</span>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-2">Asignación
                                de Servicio</h4>
                            <div class="flex flex-col gap-1">
                                <span class="text-xs font-bold text-slate-700"><i
                                        class="fas fa-user-tie text-slate-400 mr-2 text-xs"></i>Barbero: <span
                                        class="text-slate-600 font-medium">{{ $selectedBookingData->barber->user?->name }}</span></span>
                                <span class="text-xs font-bold text-slate-700"><i
                                        class="fas fa-calendar text-slate-400 mr-2 text-xs"></i>Fecha: <span
                                        class="text-slate-600 font-medium">{{ $selectedBookingData->booking_date->format('d/m/Y') }}</span></span>
                                <span class="text-xs font-bold text-slate-700"><i
                                        class="fas fa-clock text-slate-400 mr-2 text-xs"></i>Bloque: <span
                                        class="text-slate-600 font-medium">{{ \Carbon\Carbon::parse($selectedBookingData->start_time)->format('H:i') }}
                                        - {{ \Carbon\Carbon::parse($selectedBookingData->end_time)->format('H:i') }}
                                        hs</span></span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-4"><i
                                class="fas fa-stream mr-1"></i> Línea de Tiempo del Estado</h4>

                        <div class="relative border-l-2 border-slate-100 ml-3 pl-6 space-y-5">

                            <div class="relative">
                                <span
                                    class="absolute -left-[31px] top-0.5 bg-emerald-50 border-2 border-emerald-500 rounded-full w-4 h-4 flex items-center justify-center"></span>
                                <div class="flex flex-col">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-black text-slate-700">Estado Actual:
                                            {{ $selectedBookingData->status_label }}</span>
                                        <span
                                            class="text-[10px] bg-slate-100 text-slate-500 font-bold px-1.5 py-0.5 rounded">Último
                                            cambio</span>
                                    </div>
                                    @if ($selectedBookingData->adminComment)
                                        <p
                                            class="text-xs text-slate-600 bg-slate-50 p-2.5 rounded-lg border border-slate-100 mt-1.5 italic">
                                            "{{ $selectedBookingData->adminComment }}"
                                        </p>
                                    @endif
                                    @if ($selectedBookingData->newStatus === 'cancelled' && $selectedBookingData->cancellationReason)
                                        <p
                                            class="text-xs text-rose-600 bg-rose-50 p-2.5 rounded-lg border border-rose-100 mt-1.5 font-medium">
                                            <i class="fas fa-ban mr-1"></i> Motivo:
                                            {{ $selectedBookingData->cancellationReason }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <div class="relative">
                                <span class="absolute -left-[29px] top-0.5 bg-slate-200 rounded-full w-3 h-3"></span>
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-slate-500">Reserva Solicitada / Creada</span>
                                    <span class="text-[10px] text-slate-400">Total transaccionado:
                                        {{ $company->currency_code }}{{ number_format($selectedBookingData->total_price, 2) }}</span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-slate-50">
                    <button type="button" wire:click="closeDetailModal"
                        class="btn-modal-secondary w-full sm:w-auto">
                        Cerrar Detalle
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
