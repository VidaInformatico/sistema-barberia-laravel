<div>
    <div class="page-container">
        <header class="page-header">
            <div class="flex items-center gap-3">
                <div class="header-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black tracking-tight text-slate-800 leading-none">Usuarios y Clientes</h2>
                    <p class="text-slate-400 text-[11px] font-bold uppercase tracking-wider mt-1">
                        Gestión y control de accesos de la plataforma
                    </p>
                </div>
            </div>
    
            <button wire:click="create" class="btn-primary">
                <i class="fas fa-plus"></i>
                Nuevo Registro
            </button>
        </header>
    
        <div class="flex flex-wrap border-b border-slate-200 mb-6 gap-y-2">
            <button wire:click="setTab('users')" 
                class="flex-1 sm:flex-initial py-3 px-5 border-b-2 font-semibold text-sm transition-all flex items-center justify-center sm:justify-start gap-2 {{ $activeTab === 'users' ? 'border-indigo-600 text-indigo-600 bg-indigo-50/50 rounded-t-lg' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                <i class="fas fa-user-shield"></i> 
                <span>Usuarios <span class="hidden md:inline text-xs opacity-75">(Admin / Barberos)</span></span>
            </button>
        
            <button wire:click="setTab('clients')" 
                class="flex-1 sm:flex-initial py-3 px-5 border-b-2 font-semibold text-sm transition-all flex items-center justify-center sm:justify-start gap-2 {{ $activeTab === 'clients' ? 'border-indigo-600 text-indigo-600 bg-indigo-50/50 rounded-t-lg' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                <i class="fas fa-user"></i> 
                <span>Clientes</span>
            </button>
        </div>
    
        <div class="toolbar-container">
            <div class="search-input-group relative w-full lg:flex-1">
                <input type="text" wire:model.live.debounce.500ms="search" class="search-input"
                    placeholder="Buscar por nombre, correo o teléfono...">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                    <i class="fas fa-search text-sm"></i>
                </div>
            </div>
    
            <div class="flex flex-wrap items-center justify-between w-full lg:w-auto gap-3">
                <div class="per-page-select flex items-center bg-slate-50 px-3 py-2 rounded-lg border border-slate-100">
                    <label class="text-[10px] font-black text-slate-400 uppercase border-r border-slate-200 pr-3 mr-3 leading-none">
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
    
        <div class="block md:hidden space-y-3">
            @forelse ($users as $item)
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-3" wire:key="user-card-{{ $item->id }}">
                    <div class="flex items-start justify-between border-b border-slate-100 pb-2.5">
                        <div class="flex items-center gap-3">
                            <img src="{{ $item->avatar_url }}" alt="{{ $item->full_name }}" class="w-10 h-10 rounded-full object-cover border border-slate-200 shrink-0">
                            <div class="flex flex-col">
                                <span class="text-sm font-black text-slate-800">{{ $item->full_name }}</span>
                                <span class="text-[10px] text-slate-400 font-semibold">
                                    ID: <span class="badge-id">#{{ $item->id }}</span>
                                </span>
                            </div>
                        </div>
        
                        <div>
                            @if ($item->isAdmin())
                                <span class="px-2.5 py-0.5 text-[10px] font-black bg-purple-100 text-purple-700 rounded-full uppercase">Admin</span>
                            @elseif($item->isBarber())
                                <span class="px-2.5 py-0.5 text-[10px] font-black bg-blue-100 text-blue-700 rounded-full uppercase">Barbero</span>
                            @else
                                <span class="px-2.5 py-0.5 text-[10px] font-black bg-amber-100 text-amber-700 rounded-full uppercase">Cliente</span>
                            @endif
                        </div>
                    </div>
        
                    <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-100 space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-600 font-bold flex items-center gap-1.5 truncate mr-2">
                                <i class="far fa-envelope text-slate-400 shrink-0"></i>
                                <span class="truncate">{{ $item->email }}</span>
                            </span>
        
                            @if ($item->is_active)
                                <span class="px-2 py-0.5 text-[10px] font-black bg-emerald-100 text-emerald-700 rounded-full uppercase shrink-0">Activo</span>
                            @else
                                <span class="px-2 py-0.5 text-[10px] font-black bg-slate-100 text-slate-400 rounded-full uppercase shrink-0">Inactivo</span>
                            @endif
                        </div>
        
                        <div class="flex items-center text-slate-500 text-[11px]">
                            <i class="fas fa-phone text-slate-400 mr-1.5 shrink-0"></i>
                            <span>{{ $item->phone ?? 'Sin teléfono' }}</span>
                        </div>
        
                        @if ($activeTab === 'users' && $item->branch)
                            <div class="flex items-center text-slate-500 text-[11px] pt-1 border-t border-slate-200">
                                <i class="fas fa-store text-slate-400 mr-1.5 shrink-0"></i>
                                <span>{{ $item->branch->name }}</span>
                            </div>
                        @endif
        
                        @if ($activeTab === 'users')
                            <div class="pt-2 border-t border-slate-200 space-y-1.5">
                                <!-- Tamaños configurados -->
                                <div class="flex flex-wrap gap-1.5">
                                    @if ($item->bluetooth_print_size)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-50 border border-blue-200 text-blue-700 text-[10px] font-bold">
                                            <i class="fab fa-bluetooth-b"></i> BT Size: {{ $item->bluetooth_print_size }}mm
                                        </span>
                                    @endif
        
                                    @if ($item->pc_print_size)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-indigo-50 border border-indigo-200 text-indigo-700 text-[10px] font-bold" title="{{ $item->pc_printer_name }}">
                                            <i class="fas fa-print"></i> PC Size: {{ $item->pc_print_size }}mm
                                        </span>
                                    @endif
        
                                    @if (!$item->bluetooth_print_size && !$item->pc_print_size)
                                        <span class="text-[10px] text-slate-400 italic">Sin tamaños configurados</span>
                                    @endif
                                </div>
        
                                <!-- Estados de activación de impresiones -->
                                <div class="flex flex-wrap gap-1.5 pt-1">
                                    @if ($item->bluetooth_printing)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-700 text-[10px] font-bold">
                                            <i class="fas fa-check-circle"></i> BT Activo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 border border-slate-200 text-slate-500 text-[10px] font-semibold">
                                            <i class="fas fa-times-circle"></i> BT Inactivo
                                        </span>
                                    @endif
        
                                    @if ($item->direct_pc_printing)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-700 text-[10px] font-bold">
                                            <i class="fas fa-check-circle"></i> PC Directo Activo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 border border-slate-200 text-slate-500 text-[10px] font-semibold">
                                            <i class="fas fa-times-circle"></i> PC Directo Inactivo
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
        
                    <div class="flex items-center justify-end gap-2 pt-1 border-t border-slate-50">
                        <button wire:click="edit({{ $item->id }})" class="p-2 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-600 transition-colors" title="Editar">
                            <i class="fas fa-edit text-sm"></i>
                        </button>
                        <button wire:click="deleteConfirm({{ $item->id }})" class="p-2 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 transition-colors" title="Eliminar">
                            <i class="fas fa-trash-alt text-sm"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="bg-white p-8 rounded-xl border border-slate-200 text-center">
                    <i class="fas fa-users text-slate-200 text-3xl mb-3 block"></i>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">No hay registros</p>
                </div>
            @endforelse
        </div>
        
        <div class="datatable-container hidden md:block overflow-x-auto">
            <table class="datatable w-full">
                <thead class="datatable-thead">
                    <tr>
                        <th class="datatable-th">Usuario / Contacto</th>
                        <th class="datatable-th">Rol</th>
                        @if ($activeTab === 'users')
                            <th class="datatable-th">Sucursal</th>
                            <th class="datatable-th">Impresión</th>
                        @endif
                        <th class="datatable-th">Estado</th>
                        <th class="datatable-th text-right w-28">Gestión</th>
                    </tr>
                </thead>
        
                <tbody class="divide-y divide-slate-50">
                    @forelse ($users as $item)
                        <tr class="datatable-tr" wire:key="user-row-{{ $item->id }}">
                            <td class="datatable-td">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $item->avatar_url }}" alt="{{ $item->full_name }}" class="w-10 h-10 rounded-full object-cover border border-slate-200 shrink-0">
                                    <div class="flex flex-col">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-bold text-slate-800 leading-tight">{{ $item->full_name }}</span>
                                            <span class="text-[10px] text-slate-400 font-semibold">#{{ $item->id }}</span>
                                        </div>
                                        <div class="text-xs text-slate-500 mt-0.5 space-y-0.5">
                                            <div class="flex items-center gap-1.5">
                                                <i class="far fa-envelope text-slate-400 w-3 text-center"></i>
                                                <span>{{ $item->email }}</span>
                                            </div>
                                            <div class="flex items-center gap-1.5">
                                                <i class="fas fa-phone text-slate-400 w-3 text-center"></i>
                                                <span>{{ $item->phone ?? 'Sin teléfono' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
        
                            <td class="datatable-td">
                                @if ($item->isAdmin())
                                    <span class="px-2.5 py-1 text-[10px] font-black bg-purple-100 text-purple-700 rounded-full uppercase">Administrador</span>
                                @elseif($item->isBarber())
                                    <span class="px-2.5 py-1 text-[10px] font-black bg-blue-100 text-blue-700 rounded-full uppercase">Barbero</span>
                                @else
                                    <span class="px-2.5 py-1 text-[10px] font-black bg-amber-100 text-amber-700 rounded-full uppercase">Cliente</span>
                                @endif
                            </td>
        
                            @if ($activeTab === 'users')
                                <td class="datatable-td">
                                    @if ($item->branch)
                                        <span class="text-xs font-bold text-slate-600 flex items-center gap-1">
                                            <i class="fas fa-store text-slate-400"></i>{{ $item->branch->name }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">-</span>
                                    @endif
                                </td>
        
                                <td class="datatable-td">
                                    <div class="flex flex-col gap-1.5">
                                        <!-- Tamaños -->
                                        <div class="flex flex-wrap gap-1">
                                            @if ($item->bluetooth_print_size)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-50 border border-blue-200 text-blue-700 text-[10px] font-bold w-fit">
                                                    <i class="fab fa-bluetooth-b"></i> BT: {{ $item->bluetooth_print_size }}mm
                                                </span>
                                            @endif
        
                                            @if ($item->pc_print_size)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-indigo-50 border border-indigo-200 text-indigo-700 text-[10px] font-bold w-fit" title="{{ $item->pc_printer_name }}">
                                                    <i class="fas fa-print"></i> PC: {{ $item->pc_print_size }}mm
                                                </span>
                                            @endif
        
                                            @if (!$item->bluetooth_print_size && !$item->pc_print_size)
                                                <span class="text-xs text-slate-400 italic">Sin tamaños</span>
                                            @endif
                                        </div>
        
                                        <!-- Estados de Activación -->
                                        <div class="flex flex-wrap gap-1">
                                            @if ($item->bluetooth_printing)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-700 text-[10px] font-bold">
                                                    <i class="fas fa-check-circle"></i> BT Activo
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 border border-slate-200 text-slate-500 text-[10px] font-semibold">
                                                    <i class="fas fa-times-circle"></i> BT Inactivo
                                                </span>
                                            @endif
        
                                            @if ($item->direct_pc_printing)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-700 text-[10px] font-bold">
                                                    <i class="fas fa-check-circle"></i> PC Activo
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 border border-slate-200 text-slate-500 text-[10px] font-semibold">
                                                    <i class="fas fa-times-circle"></i> PC Inactivo
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            @endif
        
                            <td class="datatable-td">
                                @if ($item->is_active)
                                    <span class="px-2.5 py-0.5 text-[10px] font-black bg-emerald-100 text-emerald-700 rounded-full uppercase">Activo</span>
                                @else
                                    <span class="px-2.5 py-0.5 text-[10px] font-black bg-slate-100 text-slate-400 rounded-full uppercase">Inactivo</span>
                                @endif
                            </td>
        
                            <td class="datatable-td text-right">
                                <div class="action-group inline-flex">
                                    <button wire:click="edit({{ $item->id }})" class="btn-action bg-[#fff8e1] hover:bg-[#ffecb3] text-[#f57f17] border-r" title="Editar">
                                        <i class="fas fa-edit text-xs"></i>
                                    </button>
                                    <button wire:click="deleteConfirm({{ $item->id }})" class="btn-action bg-[#ffebee] hover:bg-[#ffcdd2] text-[#c62828]" title="Eliminar">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $activeTab === 'users' ? '6' : '4' }}" class="py-16 text-center">
                                <i class="fas fa-users text-slate-200 text-3xl mb-3 block"></i>
                                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">No hay registros</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    
        <div class="mt-4 px-2">
            {{ $users->links() }}
        </div>
    </div>

    @if ($isOpen)
        <div class="modal-overlay">
            <div class="modal-backdrop" wire:click="closeModal"></div>

            <div class="modal-container max-w-2xl">
                <div class="modal-header">
                    <div class="flex items-center gap-3">
                        <div class="modal-icon-box">
                            <i class="fas {{ $user_id ? 'fa-edit' : 'fa-plus' }}"></i>
                        </div>
                        <h3 class="modal-title">
                            {{ $user_id ? 'Editar Usuario' : 'Nuevo Usuario' }}
                        </h3>
                    </div>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-1">
                            <label class="modal-label">Nombre</label>
                            <div class="modal-input-group">
                                <i class="fas fa-user modal-input-icon"></i>
                                <input type="text" wire:model="name" class="modal-input" placeholder="Ej: Juan">
                            </div>
                            @error('name')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="md:col-span-1">
                            <label class="modal-label">Apellido</label>
                            <div class="modal-input-group">
                                <i class="fas fa-user modal-input-icon"></i>
                                <input type="text" wire:model="last_name" class="modal-input"
                                    placeholder="Ej: Pérez">
                            </div>
                            @error('last_name')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="md:col-span-1">
                            <label class="modal-label">Correo Electrónico</label>
                            <div class="modal-input-group">
                                <i class="far fa-envelope modal-input-icon"></i>
                                <input type="email" wire:model="email" class="modal-input"
                                    placeholder="Ej: juan.perez@example.com">
                            </div>
                            @error('email')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="md:col-span-1">
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

                        <div class="md:col-span-1">
                            <label class="modal-label">
                                Contraseña {{ $user_id ? '(Opcional si no se cambia)' : '' }}
                            </label>
                            <div class="modal-input-group">
                                <i class="fas fa-lock modal-input-icon"></i>
                                <input type="password" wire:model="password" class="modal-input"
                                    placeholder="Mínimo 8 caracteres">
                            </div>
                            @error('password')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="md:col-span-1">
                            <label class="modal-label">Rol del Sistema</label>
                            <div class="modal-input-group">
                                <i class="fas fa-user-shield modal-input-icon"></i>
                                <select wire:model.live="role" class="modal-input">
                                    <option value="client">Cliente</option>
                                    <option value="barber">Barbero</option>
                                    <option value="admin">Administrador</option>
                                </select>
                            </div>
                            @error('role')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        @if ($role !== 'client')
                            <div class="col-span-1 md:col-span-2">
                                <label class="modal-label">Sucursal</label>
                                <div class="modal-input-group">
                                    <i class="fas fa-store modal-input-icon"></i>
                                    <select wire:model="branch_id" class="modal-input">
                                        <option value="">Seleccione una sucursal</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('branch_id')
                                    <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-span-1 md:col-span-2 py-2">
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <div class="relative">
                                        <input type="checkbox" wire:model="bluetooth_printing" class="sr-only peer">
                                        <div
                                            class="w-10 h-5 bg-slate-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600">
                                        </div>
                                    </div>
                                    <span
                                        class="text-xs font-bold text-slate-600 uppercase group-hover:text-indigo-600 transition-colors">Impresión
                                        Bluetooth</span>
                                </label>
                                @error('bluetooth_printing')
                                    <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="md:col-span-1">
                                <label class="modal-label">Tamaño Impresión Bluetooth</label>
                                <div class="modal-input-group">
                                    <i class="fas fa-ruler modal-input-icon"></i>
                                    <select wire:model="bluetooth_print_size" class="modal-input">
                                        <option value="">Seleccione tamaño</option>
                                        <option value="58">58 mm</option>
                                        <option value="80">80 mm</option>
                                    </select>
                                </div>
                                @error('bluetooth_print_size')
                                    <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="md:col-span-1">
                                <label class="modal-label">Tamaño Impresión PC</label>
                                <div class="modal-input-group">
                                    <i class="fas fa-ruler modal-input-icon"></i>
                                    <select wire:model="pc_print_size" class="modal-input">
                                        <option value="">Seleccione tamaño</option>
                                        <option value="58">58 mm</option>
                                        <option value="80">80 mm</option>
                                    </select>
                                </div>
                                @error('pc_print_size')
                                    <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-span-1 md:col-span-2 py-2">
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <div class="relative">
                                        <input type="checkbox" wire:model.live="direct_pc_printing"
                                            class="sr-only peer">
                                        <div
                                            class="w-10 h-5 bg-slate-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600">
                                        </div>
                                    </div>
                                    <span
                                        class="text-xs font-bold text-slate-600 uppercase group-hover:text-indigo-600 transition-colors">Impresión
                                        Directa PC</span>
                                </label>
                                @error('direct_pc_printing')
                                    <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            @if ($direct_pc_printing)
                                <div class="col-span-1 md:col-span-2">
                                    <label class="modal-label">Nombre de la Impresora PC</label>
                                    <div class="modal-input-group">
                                        <i class="fas fa-print modal-input-icon"></i>
                                        <input type="text" wire:model="pc_printer_name" class="modal-input"
                                            placeholder="Ej: EPSON TM-T20II">
                                    </div>
                                    @error('pc_printer_name')
                                        <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                        @endif

                        <div class="col-span-1 md:col-span-2">
                            <label class="modal-label">Imagen de Perfil / Avatar (Opcional)</label>
                            <div class="flex items-center gap-4 mt-1">
                                <div class="shrink-0">
                                    @if ($avatar && is_object($avatar))
                                        <img src="{{ $avatar->temporaryUrl() }}"
                                            class="w-14 h-14 rounded-full object-cover border-2 border-indigo-500 shadow-sm">
                                    @elseif ($avatar && is_string($avatar))
                                        <img src="{{ asset('storage/' . $avatar) }}"
                                            class="w-14 h-14 rounded-full object-cover border-2 border-slate-200 shadow-sm">
                                    @else
                                        <div
                                            class="w-14 h-14 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 border border-slate-200">
                                            <i class="fas fa-user text-xl"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="w-full">
                                    <input type="file" wire:model="avatar" accept="image/*"
                                        class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                                    <span class="text-[10px] text-slate-400 mt-1 block">PNG, JPG, GIF de hasta
                                        2MB.</span>
                                </div>
                            </div>
                            @error('avatar')
                                <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-span-1 md:col-span-2 py-2">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <div class="relative">
                                    <input type="checkbox" wire:model="is_active" class="sr-only peer">
                                    <div
                                        class="w-10 h-5 bg-slate-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600">
                                    </div>
                                </div>
                                <span
                                    class="text-xs font-bold text-slate-600 uppercase group-hover:text-indigo-600 transition-colors">¿Usuario
                                    activo?</span>
                            </label>
                            @error('is_active')
                                <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button wire:click="closeModal" class="btn-modal-secondary">Cancelar</button>
                    <button wire:click="store" class="btn-modal-primary" wire:loading.attr="disabled">
                        <span class="relative z-10" wire:loading.remove wire:target="store">
                            {{ $user_id ? 'Actualizar' : 'Guardar' }}
                        </span>
                        <span class="relative z-10" wire:loading wire:target="store">
                            Procesando...
                        </span>
                        <i
                            class="fas fa-check-circle text-indigo-400 group-hover:text-white transition-colors text-xs relative z-10"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
