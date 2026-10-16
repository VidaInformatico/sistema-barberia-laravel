<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

<section class="w-full">
    <div class="relative bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-2xl p-6 sm:p-8 text-white shadow-xl overflow-hidden border border-slate-800 mb-8">
        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-start space-x-4">
                <div class="p-3 bg-indigo-500/10 border border-indigo-500/20 rounded-xl text-indigo-400 shrink-0">
                    <i class="fas fa-shield-halved text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold tracking-tight text-white flex items-center gap-2">
                        {{ __('Actualizar Contraseña') }}
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                            <i class="fas fa-lock text-[10px] mr-1"></i> {{ __('Seguridad') }}
                        </span>
                    </h2>
                    <p class="text-sm text-slate-400 mt-1 max-w-2xl leading-relaxed">
                        {{ __('Asegúrate de que tu cuenta utilice una contraseña larga y aleatoria para mantener la máxima seguridad.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <form wire:submit="updatePassword" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="space-y-1.5">
                <x-input-label for="update_password_current_password" class="text-xs font-semibold uppercase tracking-wider text-slate-700" :value="__('Contraseña Actual')" />
                <div class="relative group" x-data="{ show: false }">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-600 transition-colors">
                        <i class="fas fa-key text-sm"></i>
                    </div>
                    <x-text-input 
                        wire:model="current_password" 
                        id="update_password_current_password" 
                        name="current_password" 
                        x-bind:type="show ? 'text' : 'password'" 
                        class="w-full pl-10 pr-10 py-2.5 text-sm bg-slate-50 border-slate-200 rounded-xl focus:bg-white focus:border-indigo-600 focus:ring-4 focus:ring-indigo-600/10 transition-all font-medium text-slate-800 placeholder-slate-400" 
                        autocomplete="current-password" 
                        placeholder="••••••••••••" 
                    />
                    <button type="button" x-on:click="show = !show" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                        <i class="fas" x-bind:class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('current_password')" class="mt-1.5 text-xs text-rose-600 font-medium" />
            </div>

            <div class="space-y-1.5">
                <x-input-label for="update_password_password" class="text-xs font-semibold uppercase tracking-wider text-slate-700" :value="__('Nueva Contraseña')" />
                <div class="relative group" x-data="{ show: false }">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-600 transition-colors">
                        <i class="fas fa-lock text-sm"></i>
                    </div>
                    <x-text-input 
                        wire:model="password" 
                        id="update_password_password" 
                        name="password" 
                        x-bind:type="show ? 'text' : 'password'" 
                        class="w-full pl-10 pr-10 py-2.5 text-sm bg-slate-50 border-slate-200 rounded-xl focus:bg-white focus:border-indigo-600 focus:ring-4 focus:ring-indigo-600/10 transition-all font-medium text-slate-800 placeholder-slate-400" 
                        autocomplete="new-password" 
                        placeholder="••••••••••••" 
                    />
                    <button type="button" x-on:click="show = !show" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                        <i class="fas" x-bind:class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-rose-600 font-medium" />
            </div>

            <div class="space-y-1.5">
                <x-input-label for="update_password_password_confirmation" class="text-xs font-semibold uppercase tracking-wider text-slate-700" :value="__('Confirmar Contraseña')" />
                <div class="relative group" x-data="{ show: false }">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-600 transition-colors">
                        <i class="fas fa-circle-check text-sm"></i>
                    </div>
                    <x-text-input 
                        wire:model="password_confirmation" 
                        id="update_password_password_confirmation" 
                        name="password_confirmation" 
                        x-bind:type="show ? 'text' : 'password'" 
                        class="w-full pl-10 pr-10 py-2.5 text-sm bg-slate-50 border-slate-200 rounded-xl focus:bg-white focus:border-indigo-600 focus:ring-4 focus:ring-indigo-600/10 transition-all font-medium text-slate-800 placeholder-slate-400" 
                        autocomplete="new-password" 
                        placeholder="••••••••••••" 
                    />
                    <button type="button" x-on:click="show = !show" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                        <i class="fas" x-bind:class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5 text-xs text-rose-600 font-medium" />
            </div>
        </div>

        <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
            <div class="flex items-center text-xs text-slate-500 gap-2">
                <i class="fas fa-circle-info text-indigo-500"></i>
                <span>{{ __('Se cerrarán las sesiones activas en otros dispositivos al guardar.') }}</span>
            </div>

            <div class="flex items-center gap-4">
                <x-action-message class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-semibold border border-emerald-200/60" on="password-updated">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ __('Guardado correctamente.') }}</span>
                </x-action-message>

                <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-xs font-semibold uppercase tracking-wider rounded-xl shadow-lg shadow-indigo-600/20 hover:shadow-indigo-600/30 transition-all focus:outline-none focus:ring-4 focus:ring-indigo-600/20">
                    <i class="fas fa-floppy-disk"></i>
                    <span>{{ __('Guardar') }}</span>
                </button>
            </div>
        </div>
    </form>
</section>
