<div>
    @if ($open)
        <div class="modal-overlay">
            <div class="modal-backdrop" wire:click="closeModal"></div>

            <div class="modal-container max-w-2xl">
                <div class="modal-header flex items-center justify-between p-4 border-b border-slate-100">
                    <div class="inline-flex p-1 bg-slate-100 rounded-xl gap-1">
                        <button type="button" 
                            wire:click="$set('tab', 'search')"
                            class="flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ $tab === 'search' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50' }}">
                            <i class="fas fa-search text-xs"></i>
                            <span>Buscar Cliente</span>
                        </button>
                
                        <button type="button" 
                            wire:click="$set('tab', 'create')"
                            class="flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ $tab === 'create' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50' }}">
                            <i class="fas fa-user-plus text-xs"></i>
                            <span>Registrar Nuevo</span>
                        </button>
                    </div>
                
                    <button wire:click="closeModal" type="button" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-full transition-colors">
                        <i class="fas fa-times text-sm block w-4 h-4 text-center leading-none"></i>
                    </button>
                </div>

                <div class="modal-body p-4">
                    @if ($tab === 'search')
                        <div class="space-y-4">
                            <div>
                                <label class="modal-label">Búsqueda rápida</label>
                                <div class="modal-input-group">
                                    <i class="fas fa-search modal-input-icon"></i>
                                    <input type="text" wire:model.live.debounce.300ms="search"
                                        placeholder="Buscar por Nombre, Apellido, Email o Teléfono..." class="modal-input">
                                </div>
                            </div>

                            <div class="overflow-x-auto rounded-lg border border-slate-200">
                                <table class="w-full text-left text-sm text-slate-600">
                                    <thead class="bg-slate-50 text-xs uppercase text-slate-700 border-b border-slate-200">
                                        <tr>
                                            <th class="px-3 py-2.5">Cliente</th>
                                            <th class="px-3 py-2.5">Email</th>
                                            <th class="px-3 py-2.5">Teléfono</th>
                                            <th class="px-3 py-2.5 text-right">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @forelse($clients as $c)
                                            <tr class="hover:bg-slate-50/80 transition-colors">
                                                <td class="px-3 py-2 font-semibold text-slate-800">
                                                    {{ $c->name }} {{ $c->last_name }}
                                                </td>
                                                <td class="px-3 py-2 text-slate-500">
                                                    {{ $c->email ?? '-' }}
                                                </td>
                                                <td class="px-3 py-2 text-slate-500">
                                                    {{ $c->phone ?? '-' }}
                                                </td>
                                                <td class="px-3 py-2 text-right">
                                                    <button type="button" wire:click="selectClient({{ $c->id }})"
                                                        class="btn-modal-primary group">
                                                        <i class="fas fa-check"></i> Seleccionar
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-6 text-slate-400">
                                                    <i class="fas fa-user-slash text-2xl mb-1 block"></i>
                                                    No se encontraron clientes registrados
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                {{ $clients->links() }}
                            </div>
                        </div>
                    @endif

                    @if ($tab === 'create')
                        <form wire:submit="saveClient" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="modal-label">Nombre(s)</label>
                                <div class="modal-input-group">
                                    <i class="fas fa-user modal-input-icon"></i>
                                    <input type="text" wire:model="name" class="modal-input"
                                        placeholder="Ej: Juan Carlos">
                                </div>
                                @error('name')
                                    <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="modal-label">Apellido(s)</label>
                                <div class="modal-input-group">
                                    <i class="fas fa-user modal-input-icon"></i>
                                    <input type="text" wire:model="last_name" class="modal-input"
                                        placeholder="Ej: Pérez Gómez">
                                </div>
                                @error('last_name')
                                    <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="modal-label">Teléfono</label>
                                <div class="modal-input-group">
                                    <i class="fas fa-phone modal-input-icon"></i>
                                    <input type="text" wire:model="phone" class="modal-input"
                                        placeholder="Ej: 987654321">
                                </div>
                                @error('phone')
                                    <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="modal-label">Correo Electrónico</label>
                                <div class="modal-input-group">
                                    <i class="fas fa-envelope modal-input-icon"></i>
                                    <input type="email" wire:model="email" class="modal-input"
                                        placeholder="ejemplo@correo.com">
                                </div>
                                @error('email')
                                    <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="modal-label">Contraseña</label>
                                <div class="modal-input-group">
                                    <i class="fas fa-lock modal-input-icon"></i>
                                    <input type="password" wire:model="password" class="modal-input"
                                        placeholder="Mínimo 6 caracteres (opcional)">
                                </div>
                                @error('password')
                                    <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="modal-label">Avatar (URL)</label>
                                <div class="modal-input-group">
                                    <i class="fas fa-image modal-input-icon"></i>
                                    <input type="text" wire:model="avatar" class="modal-input"
                                        placeholder="URL de la imagen">
                                </div>
                                @error('avatar')
                                    <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="md:col-span-2 flex justify-end gap-2 pt-2">
                                <button type="button" wire:click="closeModal" class="btn-modal-secondary">
                                    Cancelar
                                </button>
                                <button type="submit" class="btn-modal-primary group">
                                    <span class="relative z-10">Guardar y Seleccionar</span>
                                    <i class="fas fa-check-circle text-indigo-400 group-hover:text-white transition-colors text-xs relative z-10"></i>
                                </button>
                            </div>
                        </form>
                    @endif
                </div>

                @if ($tab === 'search')
                    <div class="modal-footer p-4 border-t border-slate-100 flex justify-end">
                        <button wire:click="closeModal" class="btn-modal-secondary">Cerrar</button>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>