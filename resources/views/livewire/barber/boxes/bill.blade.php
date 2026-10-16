<div class="page-container">
    <header class="page-header">
        <div class="flex items-center gap-3">
            <div class="header-icon">
                <i class="fas fa-receipt"></i>
            </div>
            <div>
                <h2 class="text-2xl font-black tracking-tight text-slate-800 leading-none">Gastos</h2>
                <p class="text-slate-400 text-[11px] font-bold uppercase tracking-wider mt-1">
                    Controla las salidas de dinero de las cajas
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button wire:click="create" class="btn-primary">
                <i class="fas fa-plus"></i> Nuevo Gasto
            </button>
        </div>
    </header>

    <div class="toolbar-container flex flex-col md:flex-row gap-4">
        <div class="search-input-group relative flex-1">
            <input type="text" wire:model.live.debounce.500ms="search" class="search-input"
                placeholder="Buscar gasto por concepto...">
            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                <i class="fas fa-search text-sm"></i>
            </div>
        </div>

        <div class="flex items-center bg-white border border-slate-200 rounded-2xl px-4 py-2 shadow-sm gap-3">
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-black uppercase text-slate-400">Desde:</span>
                <input wire:model.live="start_date" type="date"
                    class="text-xs font-bold text-slate-700 border-0 focus:ring-0 cursor-pointer">
            </div>
            <div class="h-4 w-px bg-slate-200"></div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-black uppercase text-slate-400">Hasta:</span>
                <input wire:model.live="end_date" type="date"
                    class="text-xs font-bold text-slate-700 border-0 focus:ring-0 cursor-pointer">
            </div>
        </div>
    </div>

    <div class="block md:hidden space-y-3">
        @forelse ($bills as $expense)
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-3"
                wire:key="expense-card-{{ $expense->id }}">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <div class="flex flex-col pr-2">
                        <span class="text-sm font-bold text-slate-700 leading-tight">{{ $expense->concept }}</span>
                        <span class="text-[10px] text-slate-400 font-bold uppercase mt-0.5">
                            <i class="far fa-user mr-1"></i>{{ $expense->user->name }}
                        </span>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="text-xs font-bold text-rose-400">{{ $company->curency_code }}</span>
                        <span
                            class="text-base font-black text-rose-600">{{ number_format($expense->total_amount, 2) }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div>
                        <span class="text-slate-400 font-medium block">Caja / Método:</span>
                        <span class="font-bold text-slate-700 block mt-0.5">{{ $expense->cashRegister->name }}</span>
                        <span
                            class="text-[10px] text-slate-400 font-bold uppercase block">{{ $expense->paymentMethod->name }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-medium block">Fecha y Hora:</span>
                        <span class="font-bold text-slate-600 block mt-0.5">
                            <i class="far fa-calendar-alt mr-1 text-slate-400"></i>
                            {{ $expense->expense_date->format('d/m/Y H:i') }}
                        </span>
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button wire:click="edit({{ $expense->id }})"
                        class="p-2 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-600 transition-colors"
                        title="Editar">
                        <i class="fas fa-edit text-sm"></i>
                    </button>
                    <button wire:click="deleteConfirm({{ $expense->id }})"
                        class="p-2 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 transition-colors"
                        title="Eliminar">
                        <i class="fas fa-trash-alt text-sm"></i>
                    </button>
                </div>
            </div>
        @empty
            <div class="bg-white p-8 rounded-xl border border-slate-200 text-center">
                <i class="fas fa-receipt text-slate-200 text-3xl mb-3 block"></i>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">No hay gastos registrados</p>
            </div>
        @endforelse
    </div>

    <div class="datatable-container hidden md:block overflow-x-auto">
        <table class="datatable w-full">
            <thead class="datatable-thead">
                <tr>
                    <th class="datatable-th">Concepto</th>
                    <th class="datatable-th">Caja / Método</th>
                    <th class="datatable-th">Monto</th>
                    <th class="datatable-th">Fecha / Usuario</th>
                    <th class="datatable-th text-right">Gestión</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse ($bills as $expense)
                    <tr class="datatable-tr" wire:key="expense-row-{{ $expense->id }}">
                        <td class="datatable-td">
                            <span class="text-sm font-bold text-slate-700 block">{{ $expense->concept }}</span>
                        </td>
                        <td class="datatable-td">
                            <span
                                class="text-xs font-bold text-slate-700 block">{{ $expense->cashRegister->name }}</span>
                            <span
                                class="text-[10px] text-slate-400 font-bold uppercase">{{ $expense->paymentMethod->name }}</span>
                        </td>
                        <td class="datatable-td">
                            <span class="text-xs font-bold text-rose-400">{{ $company->curency_code }}</span>
                            <span
                                class="text-sm font-black text-rose-600">{{ number_format($expense->total_amount, 2) }}</span>
                        </td>
                        <td class="datatable-td">
                            <span
                                class="text-xs font-bold text-slate-600 block">{{ $expense->expense_date->format('d/m/Y H:i') }}</span>
                            <span
                                class="text-[10px] text-slate-400 font-bold uppercase">{{ $expense->user->name }}</span>
                        </td>
                        <td class="datatable-td text-right">
                            <div class="action-group">
                                <button wire:click="edit({{ $expense->id }})"
                                    class="btn-action hover:bg-amber-50 hover:text-amber-600 border-r" title="Editar">
                                    <i class="fas fa-edit text-xs"></i>
                                </button>
                                <button wire:click="deleteConfirm({{ $expense->id }})"
                                    class="btn-action hover:bg-red-50 hover:text-red-600" title="Eliminar">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-16 text-center">
                            <i class="fas fa-receipt text-slate-200 text-3xl mb-3 block"></i>
                            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">No hay gastos
                                registrados</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 px-2">
        {{ $bills->links() }}
    </div>

    @if ($isOpen)
        <div class="modal-overlay">
            <div class="modal-backdrop" wire:click="closeModal"></div>

            <div class="modal-container max-w-2xl">
                <div class="modal-header">
                    <div class="flex items-center gap-3">
                        <div class="modal-icon-box">
                            <i class="fas {{ $expense_id ? 'fa-edit' : 'fa-receipt' }}"></i>
                        </div>
                        <h3 class="modal-title">{{ $expense_id ? 'Editar Gasto' : 'Nuevo Gasto' }}</h3>
                    </div>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="modal-label">Concepto</label>
                            <div class="modal-input-group">
                                <i class="fas fa-file-invoice modal-input-icon"></i>
                                <input type="text" wire:model="concept" class="modal-input"
                                    placeholder="Descripción del gasto">
                            </div>
                            @error('concept')
                                <span class="text-red-500 text-[10px] font-black mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="modal-label">Método de Pago</label>
                            <div class="modal-input-group">
                                <i class="fas fa-credit-card modal-input-icon"></i>
                                <select wire:model="payment_method_id" class="modal-input">
                                    <option value="">Seleccionar...</option>
                                    @foreach ($paymentMethods as $pm)
                                        <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('payment_method_id')
                                <span class="text-red-500 text-[10px] font-black mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="modal-label">Fecha</label>
                            <div class="modal-input-group">
                                <i class="fas fa-calendar-alt modal-input-icon"></i>
                                <input type="date" wire:model="expense_date" class="modal-input">
                            </div>
                            @error('expense_date')
                                <span class="text-red-500 text-[10px] font-black mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="modal-label">Monto Gravado</label>
                            <div class="modal-input-group">
                                <i class="fas fa-dollar-sign modal-input-icon"></i>
                                <input type="number" wire:model.live="amount" step="0.01" class="modal-input"
                                    placeholder="0.00">
                            </div>
                            @error('amount')
                                <span class="text-red-500 text-[10px] font-black mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="modal-label">Impuesto</label>
                            <div class="modal-input-group">
                                <i class="fas fa-percent modal-input-icon"></i>
                                <input type="number" wire:model.live="itbis" step="0.01" class="modal-input"
                                    placeholder="0.00">
                            </div>
                            @error('itbis')
                                <span class="text-red-500 text-[10px] font-black mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="modal-label">Otros Impuestos</label>
                            <div class="modal-input-group">
                                <i class="fas fa-plus-minus modal-input-icon"></i>
                                <input type="number" wire:model.live="other_taxes" step="0.01"
                                    class="modal-input" placeholder="0.00">
                            </div>
                            @error('other_taxes')
                                <span class="text-red-500 text-[10px] font-black mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="modal-label">Total</label>
                            <div class="modal-input-group">
                                <i class="fas fa-money-bill-wave modal-input-icon text-rose-500"></i>
                                <input type="number" wire:model="total_amount" step="0.01"
                                    class="modal-input bg-slate-50 font-black text-rose-600">
                            </div>
                            @error('total_amount')
                                <span class="text-red-500 text-[10px] font-black mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-span-2">
                            <label class="modal-label">Notas / Descripción adicional</label>
                            <textarea wire:model="description" class="modal-input min-h-[80px]"></textarea>
                            @error('description')
                                <span class="text-red-500 text-[10px] font-black mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button wire:click="closeModal" class="btn-modal-secondary">Cancelar</button>
                    <button wire:click="store" class="btn-modal-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="store">
                            {{ $expense_id ? 'Actualizar Gasto' : 'Guardar Gasto' }}
                        </span>
                        <span wire:loading wire:target="store">Procesando...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
