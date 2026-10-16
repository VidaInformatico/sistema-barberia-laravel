<?php
use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component {
    public function leave(): void
    {
        $logout = new Logout();
        $logout();
        $this->redirect('/', navigate: false);
    }
}; ?>

<div x-data="{ open: false }">
    <button type="button" @click="open = true"
        class="w-full flex items-center space-x-2 px-4 py-2.5 text-red-500 hover:bg-red-50 font-semibold transition-colors text-left text-sm">
        <i class="fas fa-sign-out-alt w-4 text-center"></i>
        <span>Cerrar Sesión</span>
    </button>

    <template x-teleport="body">
        <div x-show="open" class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="display: none;">
            <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" class="fixed inset-0 bg-gray-950/60 backdrop-blur-sm" @click="open = false"></div>

            <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
                class="relative bg-white border border-gray-100 rounded-2xl max-w-sm w-full p-6 shadow-2xl z-10 text-center">
                
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-red-100 text-red-500 mb-4">
                    <i class="fa-solid fa-right-from-bracket text-xl"></i>
                </div>

                <h3 class="text-lg font-black text-gray-900">¿Deseas salir?</h3>
                <p class="mt-2 text-sm text-gray-500 font-medium">Tu sesión actual finalizará inmediatamente.</p>

                <div class="mt-6 flex gap-3">
                    <button type="button" @click="open = false"
                        class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-bold text-sm transition">
                        Cancelar
                    </button>
                    <button type="button" @click="open = false; $wire.leave()"
                        class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold text-sm rounded-xl transition shadow-lg shadow-red-500/20">
                        Sí, salir
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>