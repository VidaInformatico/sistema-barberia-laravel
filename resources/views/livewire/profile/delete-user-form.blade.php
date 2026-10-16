<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<section class="w-full space-y-4">
    <!-- Encabezado de la sección con ícono de advertencia -->
    <header class="flex items-start space-x-3 border-b border-red-100 pb-3">
        <div class="p-2 bg-red-100 text-red-600 rounded-lg shrink-0 mt-0.5">
            <i class="fas fa-user-xmark text-base"></i>
        </div>
        <div>
            <h2 class="text-sm font-bold text-gray-800">
                {{ __('Eliminar Cuenta') }}
            </h2>
            <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">
                {{ __('Una vez eliminada tu cuenta, todos sus recursos y datos se borrarán de forma permanente. Antes de proceder, descarga cualquier información que desees conservar.') }}
            </p>
        </div>
    </header>

    <!-- Botón de Acción Principal -->
    <div class="pt-1">
        <x-danger-button
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="w-full sm:w-auto justify-center text-xs px-4 py-2 flex items-center space-x-2 shadow-sm"
        >
            <i class="fas fa-trash-can"></i>
            <span>{{ __('Eliminar Cuenta') }}</span>
        </x-danger-button>
    </div>

    <!-- Modal de Confirmación -->
    <x-modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="deleteUser" class="p-6 w-full">

            <!-- Cabecera del Modal -->
            <div class="flex items-center space-x-3 border-b border-gray-100 pb-3 mb-4">
                <div class="p-2 bg-red-100 text-red-600 rounded-full shrink-0">
                    <i class="fas fa-triangle-exclamation text-base"></i>
                </div>
                <h2 class="text-base font-bold text-gray-800">
                    {{ __('¿Estás seguro de que deseas eliminar tu cuenta?') }}
                </h2>
            </div>

            <!-- Descripción de Advertencia -->
            <p class="text-xs text-gray-600 mb-4 leading-relaxed">
                {{ __('Esta acción no se puede deshacer. Por favor, ingresa tu contraseña para confirmar la eliminación definitiva de tu cuenta.') }}
            </p>

            <!-- Campo de Contraseña con Ícono -->
            <div class="space-y-1">
                <x-input-label for="password" value="{{ __('Contraseña') }}" class="sr-only" />

                <div class="relative w-full">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 text-xs">
                        <i class="fas fa-lock"></i>
                    </span>
                    <x-text-input
                        wire:model="password"
                        id="password"
                        name="password"
                        type="password"
                        class="w-full pl-8 pr-3 py-1.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:border-red-500 focus:bg-white transition-colors"
                        placeholder="{{ __('Ingresa tu contraseña para confirmar') }}"
                    />
                </div>

                <x-input-error :messages="$errors->get('password')" class="mt-1 text-[11px] text-red-500" />
            </div>

            <!-- Botones de Acción del Modal -->
            <div class="mt-6 flex flex-col-reverse sm:flex-row justify-end gap-2 pt-3 border-t border-gray-100">
                <x-secondary-button 
                    x-on:click="$dispatch('close')" 
                    class="w-full sm:w-auto justify-center text-xs py-2 flex items-center space-x-1"
                >
                    <i class="fas fa-xmark"></i>
                    <span>{{ __('Cancelar') }}</span>
                </x-secondary-button>

                <x-danger-button class="w-full sm:w-auto justify-center text-xs py-2 flex items-center space-x-2">
                    <i class="fas fa-trash-can"></i>
                    <span>{{ __('Eliminar Cuenta Definitivamente') }}</span>
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
