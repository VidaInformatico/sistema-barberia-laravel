<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        // 1. Obtenemos el usuario que se acaba de loguear
        $user = auth()->user();

        $url = match ($user->role) {
            'admin' => route('admin.dashboard', absolute: false),
            'barber' => route('barber.dashboard', absolute: false),
            default => route('client.dashboard', absolute: false),
        };

        $this->redirect(session()->pull('url.intended', $url), navigate: true);
    }
}; ?>

<div class="min-h-screen grid grid-cols-1 md:grid-cols-2">
    <div
        class="flex flex-col justify-center px-6 py-12 sm:px-16 lg:px-24 bg-white relative border-t-8 md:border-t-0 md:border-l-8 border-amber-500">
        <div class="absolute top-8 left-1/2 -translate-x-1/2">
            <a href="/" wire:navigate>
                <x-application-logo class="w-12 h-12 text-gray-900" />
            </a>
        </div>

        <div class="mx-auto w-full max-w-md pt-12">
            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight text-center">Bienvenido, Barbero</h2>
            <p class="mt-2 text-sm text-gray-500 text-center">Ingresa tus credenciales para acceder al sistema.</p>

            <x-auth-session-status class="mt-4 mb-4" :status="session('status')" />

            <form wire:submit="login" class="mt-8 space-y-5">
                @csrf
                <div>
                    <x-input-label for="email" :value="__('Email')" class="mb-1 text-gray-700 font-medium" />
                    <div class="relative">
                        <i class="fa-solid fa-envelope absolute left-3 top-3.5 text-gray-400"></i>
                        <x-text-input wire:model="form.email" id="email"
                            class="pl-10 block w-full h-11 border-gray-300 focus:border-amber-500 focus:ring-amber-500 rounded-lg shadow-sm"
                            type="email" placeholder="correo@ejemplo.com" required />
                    </div>
                    <x-input-error :messages="$errors->get('form.email')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="password" :value="__('Password')" class="mb-1 text-gray-700 font-medium" />
                    <div class="relative">
                        <i class="fa-solid fa-lock absolute left-3 top-3.5 text-gray-400"></i>
                        <x-text-input wire:model="form.password" id="password"
                            class="pl-10 block w-full h-11 border-gray-300 focus:border-amber-500 focus:ring-amber-500 rounded-lg shadow-sm"
                            type="password" placeholder="••••••••" required />
                    </div>
                    <x-input-error :messages="$errors->get('form.password')" class="mt-1" />
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label for="remember" class="inline-flex items-center">
                        <input wire:model="form.remember" id="remember" type="checkbox"
                            class="rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                        <span class="ms-2 text-sm text-gray-600">{{ __('Recuérdame') }}</span>
                    </label>
                </div>

                <div class="pt-2">
                    <x-primary-button wire:loading.attr="disabled"
                        class="w-full h-11 justify-center bg-gray-950 hover:bg-amber-600 text-white rounded-lg shadow-md transition duration-200 ease-in-out font-semibold text-sm tracking-wide disabled:opacity-50 disabled:cursor-not-allowed">

                        <i wire:loading.remove class="fa-solid fa-right-to-bracket mr-2"></i>

                        <i wire:loading class="fa-solid fa-circle-notch animate-spin mr-2"></i>

                        <span>{{ __('Iniciar Sesión') }}</span>

                    </x-primary-button>
                </div>

                <div class="flex flex-col space-y-3 pt-2 text-center">
                    @if (Route::has('password.request'))
                        <a class="text-sm font-medium text-gray-500 hover:text-amber-600 transition"
                            href="{{ route('password.request') }}" wire:navigate>
                            ¿Olvidaste tu contraseña?
                        </a>
                    @endif

                    @if (Route::has('register'))
                        <p class="text-sm text-gray-500">
                            ¿No tienes cuenta?
                            <a class="font-semibold text-amber-600 hover:text-amber-700 transition"
                                href="{{ route('register') }}" wire:navigate>
                                Regístrate aquí
                            </a>
                        </p>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="hidden md:block relative bg-gray-950">
        <img src="https://images.unsplash.com/photo-1503951914875-452162b0f3f1?q=80&w=1000&auto=format&fit=crop"
            alt="Barber Shop" class="absolute inset-0 w-full h-full object-cover opacity-75 mix-blend-luminosity">
        <div class="absolute inset-0 bg-gradient-to-t from-gray-950 via-gray-950/30 to-transparent"></div>
        <div class="absolute bottom-0 left-0 p-16 text-white z-10 max-w-xl">
            <p class="text-amber-500 font-semibold tracking-widest uppercase text-xs mb-3">Estilo & Precisión</p>
            <h3 class="text-3xl font-bold leading-tight mb-4">"El cabello es la joya de la corona, y el barbero su
                artesano."</h3>
            <p class="text-gray-400 text-base leading-relaxed">Gestiona tus citas, clientes y herramientas en un solo
                lugar
                con nuestra plataforma premium.</p>
        </div>
    </div>
</div>
