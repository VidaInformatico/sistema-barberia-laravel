<div class="page-container">
    <header class="page-header">
        <div class="flex items-center gap-3">
            <div class="header-icon">
                <i class="fas fa-history"></i>
            </div>
            <div>
                <h2 class="text-2xl font-black tracking-tight text-slate-800 leading-none">Panel de Reservas</h2>
            </div>
        </div>

        <div class="flex items-center gap-3 flex-wrap">
            <a href="{{ route('admin.bookings.export', ['type' => 'pdf', 'selectedBarber' => $selectedBarber, 'selectedDate' => $selectedDate, 'selectedStatus' => $selectedStatus, 'search' => $search]) }}"
                target="_blank"
                class="inline-flex items-center gap-1.5 bg-rose-50 text-rose-600 px-3 py-1.5 rounded-lg text-xs font-bold uppercase border border-rose-200 tracking-wider hover:bg-rose-100 transition">
                <i class="fas fa-file-pdf"><span> PDF</span></i>
            </a>

            <a href="{{ route('admin.bookings.export', ['type' => 'excel', 'selectedBarber' => $selectedBarber, 'selectedDate' => $selectedDate, 'selectedStatus' => $selectedStatus, 'search' => $search]) }}"
                target="_blank"
                class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-600 px-3 py-1.5 rounded-lg text-xs font-bold uppercase border border-emerald-200 tracking-wider hover:bg-emerald-100 transition">
                <i class="fas fa-file-excel"><span> Excel</span></i>
            </a>

            <a href="{{ route('admin.bookings.create') }}" class="btn-primary inline-flex items-center gap-2">
                <i class="fas fa-plus text-xs"></i>
                Nueva Reserva
            </a>
        </div>
    </header>

    <div class="toolbar-container grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end w-full">

        <div class="w-full">
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">
                Buscar Cliente / Código
            </label>
            <div class="relative flex items-center bg-slate-50 rounded-lg border border-slate-100 w-full h-[42px]">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                    <i class="fas fa-search text-sm"></i>
                </div>
                <input type="text" wire:model.live.debounce.300ms="search"
                    class="bg-transparent border-none pl-11 pr-4 text-sm font-bold text-slate-700 focus:ring-0 w-full h-full placeholder-slate-400"
                    placeholder="Ej. BK-2026... o Juan">
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
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-3"
                wire:key="booking-card-{{ $booking->id }}">
                <div class="flex items-start justify-between border-b border-slate-100 pb-2">
                    <div class="flex flex-col">
                        <span
                            class="text-sm font-black text-indigo-600 tracking-tight">{{ $booking->booking_number }}</span>
                        <span class="text-sm font-bold text-slate-700 mt-0.5">
                            @if ($booking->client)
                                {{ $booking->client->name }} {{ $booking->client->last_name ?? '' }}
                            @else
                                Cliente General
                            @endif
                        </span>
                        <span class="text-xs text-slate-400 mt-0.5">
                            <i class="fas fa-phone text-[10px] mr-1"></i>{{ $booking->client?->phone }}
                        </span>
                    </div>
                    <div class="flex flex-col items-end gap-1">
                        @php
                            $colorMap = [
                                'yellow' => 'bg-amber-600 text-white',
                                'blue' => 'bg-blue-600 text-white',
                                'indigo' => 'bg-indigo-600 text-white',
                                'green' => 'bg-emerald-600 text-white',
                                'red' => 'bg-rose-600 text-white',
                                'gray' => 'bg-slate-600 text-white',
                            ];
                            $theme = $colorMap[$booking->status_color] ?? $colorMap['gray'];
                        @endphp
                        <span class="px-2 py-1 text-[10px] font-black rounded-full uppercase {{ $theme }}">
                            {{ $booking->status_label }}
                        </span>
                        <span
                            class="text-sm font-black text-slate-700 mt-1">{{ number_format($booking->total_price, 2) }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div>
                        <span class="text-slate-400 font-medium block">Barbero:</span>
                        <span
                            class="font-bold text-slate-600 block truncate">{{ $booking->barber?->user?->name }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-medium block">Horario:</span>
                        <span
                            class="font-bold text-slate-700 block">{{ $booking->booking_date->format('d/m/Y') }}</span>
                        <span class="text-slate-400 text-[11px] block">
                            <i
                                class="far fa-clock text-[10px] mr-1"></i>{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}
                            - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }} hs
                        </span>
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-100 grid grid-cols-2 gap-2">
                    <button wire:click="showBooking({{ $booking->id }})"
                        class="w-full px-2 py-1.5 text-xs font-bold bg-slate-700 text-white rounded-lg hover:bg-slate-800 transition-colors flex items-center justify-center gap-1"
                        title="Ver Historial y Detalles">
                        <i class="fas fa-eye text-xs"><span>Ver</span></i>
                    </button>

                    <button
                        onclick="abrirVentanaEmergente('{{ route('printer.ticket.booking', Crypt::encrypt($booking->id)) }}')"
                        class="w-full px-2 py-1.5 text-xs font-bold bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center justify-center gap-1"
                        title="Generar Ticket">
                        <i class="fas fa-ticket-alt text-xs"><span>Ticket</span></i>
                    </button>

                    @if ($booking->isPending())
                        <button wire:click="openStatusModal({{ $booking->id }}, 'confirmed')"
                            class="col-span-2 w-full px-2.5 py-1.5 text-[10px] font-black uppercase bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-center">Confirmar</button>
                    @endif

                    @if ($booking->isConfirmed())
                        <button wire:click="openStatusModal({{ $booking->id }}, 'in_progress')"
                            class="col-span-2 w-full px-2.5 py-1.5 text-[10px] font-black uppercase bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-center">Iniciar</button>
                    @endif

                    @if ($booking->isInProgress())
                        <button wire:click="openStatusModal({{ $booking->id }}, 'completed')"
                            class="col-span-2 w-full px-2.5 py-1.5 text-[10px] font-black uppercase bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors text-center">Finalizar</button>
                    @endif

                    @if (!$booking->isCompleted() && !$booking->isCancelled() && !$booking->isNoShow())
                        <button wire:click="openStatusModal({{ $booking->id }}, 'no_show')"
                            class="w-full px-2.5 py-1.5 text-[10px] font-black uppercase bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors text-center">No
                            Asistió</button>
                        <button wire:click="openStatusModal({{ $booking->id }}, 'cancelled')"
                            class="w-full px-2 py-1.5 text-[10px] font-black uppercase bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-center">Cancelar</button>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white p-8 rounded-xl border border-slate-200 text-center">
                <i class="fas fa-calendar-times text-slate-200 text-3xl mb-3 block"></i>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">No se encontraron reservas con los
                    filtros seleccionados.</p>
            </div>
        @endforelse
    </div>

    <div class="datatable-container hidden md:block overflow-x-auto">
        <table class="datatable w-full">
            <thead class="datatable-thead">
                <tr>
                    <th class="datatable-th">Acciones Rápidas</th>
                    <th class="datatable-th">Código / Cliente</th>
                    <th class="datatable-th">Barbero</th>
                    <th class="datatable-th">Horario</th>
                    <th class="datatable-th">Estado</th>
                    <th class="datatable-th text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($bookings as $booking)
                    <tr class="datatable-tr" wire:key="booking-row-{{ $booking->id }}">
                        <td class="datatable-td">
                            <div class="grid grid-cols-2 gap-1.5 w-48">
                                <button wire:click="showBooking({{ $booking->id }})"
                                    class="w-full px-2 py-1.5 text-[11px] font-bold bg-slate-700 text-white rounded hover:bg-slate-800 transition-colors flex items-center justify-center gap-1"
                                    title="Ver Historial y Detalles">
                                    <i class="fas fa-eye text-[10px]"></i> Ver
                                </button>

                                <button
                                    onclick="abrirVentanaEmergente('{{ route('printer.ticket.booking', Crypt::encrypt($booking->id)) }}')"
                                    class="w-full px-2 py-1.5 text-[11px] font-bold bg-purple-600 text-white rounded hover:bg-purple-700 transition-colors flex items-center justify-center gap-1"
                                    title="Generar Ticket">
                                    <i class="fas fa-ticket-alt text-[10px]"></i> Ticket
                                </button>

                                @if ($booking->isPending())
                                    <button wire:click="openStatusModal({{ $booking->id }}, 'confirmed')"
                                        class="col-span-2 w-full px-2.5 py-1 text-[10px] font-black uppercase bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors text-center">Confirmar</button>
                                @endif

                                @if ($booking->isConfirmed())
                                    <button wire:click="openStatusModal({{ $booking->id }}, 'in_progress')"
                                        class="col-span-2 w-full px-2.5 py-1 text-[10px] font-black uppercase bg-indigo-600 text-white rounded hover:bg-indigo-700 transition-colors text-center">Iniciar</button>
                                @endif

                                @if ($booking->isInProgress())
                                    <button wire:click="openStatusModal({{ $booking->id }}, 'completed')"
                                        class="col-span-2 w-full px-2.5 py-1 text-[10px] font-black uppercase bg-emerald-600 text-white rounded hover:bg-emerald-700 transition-colors text-center">Finalizar</button>
                                @endif

                                @if (!$booking->isCompleted() && !$booking->isCancelled() && !$booking->isNoShow())
                                    <button wire:click="openStatusModal({{ $booking->id }}, 'no_show')"
                                        class="w-full px-2 py-1 text-[10px] font-black uppercase bg-amber-600 text-white rounded hover:bg-amber-700 transition-colors text-center">No
                                        Asistió</button>
                                    <button wire:click="openStatusModal({{ $booking->id }}, 'cancelled')"
                                        class="w-full px-2 py-1 text-[10px] font-black uppercase bg-red-600 text-white rounded hover:bg-red-700 transition-colors text-center">Cancelar</button>
                                @endif
                            </div>
                        </td>
                        <td class="datatable-td">
                            <div class="flex flex-col">
                                <span
                                    class="text-sm font-black text-indigo-600 tracking-tight">{{ $booking->booking_number }}</span>

                                <span class="text-sm font-bold text-slate-700 mt-0.5">
                                    @if ($booking->client)
                                        {{ $booking->client->name }} {{ $booking->client->last_name ?? '' }}
                                    @else
                                        Cliente General
                                    @endif
                                </span>

                                <span class="text-xs text-slate-400">
                                    <i class="fas fa-phone text-[10px] mr-1"></i>{{ $booking->client?->phone }}
                                </span>
                            </div>
                        </td>
                        <td class="datatable-td">
                            <span class="text-xs font-bold text-slate-600">{{ $booking->barber?->user?->name }}</span>
                        </td>
                        <td class="datatable-td">
                            <div class="flex flex-col">
                                <span
                                    class="text-xs font-bold text-slate-700">{{ $booking->booking_date->format('d/m/Y') }}</span>
                                <span class="text-xs text-slate-400 mt-0.5">
                                    <i
                                        class="far fa-clock text-[10px] mr-1"></i>{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}
                                    - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }} hs
                                </span>
                            </div>
                        </td>
                        <td class="datatable-td">
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
                            <span class="px-2 py-1 text-[10px] font-black rounded-full uppercase {{ $theme }}">
                                {{ $booking->status_label }}
                            </span>
                        </td>
                        <td class="datatable-td text-right">
                            <span
                                class="text-sm font-black text-slate-700">{{ number_format($booking->total_price, 2) }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-16 text-center">
                            <i class="fas fa-calendar-times text-slate-200 text-3xl mb-3 block"></i>
                            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">No se encontraron
                                reservas con los filtros seleccionados.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 px-2">
        {{ $bookings->links() }}
    </div>

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
                                        class="text-slate-600 font-medium">{{ $selectedBookingData->barber?->name }}</span></span>
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
                                        {{ number_format($selectedBookingData->total_price, 2) }}</span>
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
