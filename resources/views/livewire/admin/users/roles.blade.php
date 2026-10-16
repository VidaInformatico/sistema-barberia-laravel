<div class="page-container">
    <header class="page-header">
        <div class="flex items-center gap-3">
            <div class="header-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div>
                <h2 class="text-2xl font-black tracking-tight text-slate-800 leading-none">Roles y Permisos</h2>
                <p class="text-slate-400 text-[11px] font-bold uppercase tracking-wider mt-1">
                    Control de acceso y seguridad del sistema
                </p>
            </div>
        </div>

        {{-- @can('roles.crear') --}}
        <button wire:click="create" class="btn-primary">
            <i class="fas fa-plus"></i>
            Nuevo Rol
        </button>
        {{-- @endcan --}}
    </header>

    <div class="toolbar-container">
        <div class="search-input-group relative w-full lg:flex-1">
            <input type="text" wire:model.live.debounce.500ms="search" class="search-input"
                placeholder="Buscar rol...">
            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                <i class="fas fa-search text-sm"></i>
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-between w-full lg:w-auto gap-3">
            <div class="per-page-select flex items-center bg-slate-50 px-3 py-2 rounded-lg border border-slate-100">
                <label
                    class="text-[10px] font-black text-slate-400 uppercase border-r border-slate-200 pr-3 mr-3 leading-none">
                    Mostrar
                </label>
                <select wire:model.live="perPage"
                    class="bg-transparent border-none p-0 pr-8 text-sm font-bold text-slate-700 focus:ring-0 cursor-pointer">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>
    </div>

    <div class="datatable-container">
        <table class="datatable">
            <thead class="datatable-thead">
                <tr>
                    <th class="datatable-th">Nombre del Rol</th>
                    <th class="datatable-th">Permisos Asignados</th>
                    <th class="datatable-th text-right">Gestión</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($roles as $role)
                    <tr class="datatable-tr">
                        <td class="datatable-td">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 group-hover:bg-orange-100 group-hover:text-orange-600 transition-colors">
                                    <i class="fa-solid fa-user-shield text-xs"></i>
                                </div>
                                <span class="text-sm font-bold text-slate-700">{{ $role->name }}</span>
                            </div>
                        </td>
                        <td class="datatable-td">
                            <div class="flex flex-wrap gap-1.5 max-w-md">
                                @foreach ($role->permissions as $perm)
                                    <span
                                        class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-orange-50 text-orange-600 border border-orange-100/50 uppercase">
                                        {{ $perm->name }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="datatable-td text-right">
                            <div class="action-group">
                                @can('roles.editar')
                                    <button wire:click="edit({{ $role->id }})"
                                        class="btn-action hover:bg-amber-50 hover:text-amber-600 border-r" title="Editar">
                                        <i class="fas fa-edit text-xs"></i>
                                    </button>
                                @endcan
                                @can('roles.eliminar')
                                    <button wire:click="deleteConfirm({{ $role->id }})"
                                        class="btn-action hover:bg-red-50 hover:text-red-600" title="Eliminar">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="py-16 text-center">
                            <i class="fas fa-shield-alt text-slate-200 text-3xl mb-3 block"></i>
                            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">No hay roles
                                registrados</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 px-2">
        {{ $roles->links() }}
    </div>

    @if ($isOpen)
        <div class="modal-overlay">
            <div class="modal-backdrop" wire:click="closeModal"></div>

            <div class="modal-container max-w-2xl">
                <div class="modal-header">
                    <div class="flex items-center gap-3">
                        <div class="modal-icon-box">
                            <i class="fas {{ $role_id ? 'fa-edit' : 'fa-plus' }}"></i>
                        </div>
                        <h3 class="modal-title">
                            {{ $role_id ? 'Editar Rol' : 'Nuevo Rol' }}
                        </h3>
                    </div>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="modal-label">Nombre del Rol</label>
                            <div class="modal-input-group">
                                <i class="fas fa-shield-alt modal-input-icon"></i>
                                <input type="text" wire:model="name" class="modal-input"
                                    placeholder="Ej: Administrador, Cajero...">
                            </div>
                            @error('name')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="py-2">
                            <label class="modal-label mb-3 block">Asignar Permisos al Rol</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-64 overflow-y-auto pr-1">
                                @foreach ($permissions as $permission)
                                    <label
                                        class="group relative flex items-center gap-3 p-3 rounded-xl border border-slate-100 bg-slate-50/50 cursor-pointer hover:bg-white hover:border-orange-300 hover:shadow-sm transition-all">
                                        <div class="relative flex items-center">
                                            <input type="checkbox" wire:model="selectedPermissions"
                                                value="{{ $permission->name }}"
                                                class="w-4 h-4 rounded border-slate-300 text-orange-600 focus:ring-orange-500/20 transition-all cursor-pointer">
                                        </div>
                                        <div class="flex flex-col">
                                            <span
                                                class="text-xs font-bold text-slate-700 group-hover:text-orange-600 transition-colors uppercase">
                                                {{ strtoupper(str_replace('.', ' ', $permission->name)) }}
                                            </span>
                                            <span class="text-[10px] text-slate-400 font-medium">Acceso a módulo</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error('selectedPermissions')
                                <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button wire:click="closeModal" class="btn-modal-secondary">Cancelar</button>
                    <button wire:click="store" class="btn-modal-primary" wire:loading.attr="disabled">
                        <span class="relative z-10" wire:loading.remove wire:target="store">
                            {{ $role_id ? 'Actualizar' : 'Guardar' }}
                        </span>
                        <span class="relative z-10" wire:loading wire:target="store">
                            Procesando...
                        </span>
                        <i
                            class="fas fa-check-circle text-orange-400 group-hover:text-white transition-colors text-xs relative z-10"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
