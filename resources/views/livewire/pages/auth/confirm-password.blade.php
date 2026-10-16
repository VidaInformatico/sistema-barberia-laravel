<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public string $password = '';

    /**
     * Confirm the current user's password.
     */
    public function confirmPassword(): void
    {
        $this->validate([
            'password' => ['required', 'string'],
        ]);

        if (
            !Auth::guard('web')->validate([
                'email' => Auth::user()->email,
                'password' => $this->password,
            ])
        ) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        session(['auth.password_confirmed_at' => time()]);

        $url = match ($user->role) {
            'admin' => route('admin.dashboard', absolute: false),
            'barber' => route('barber.dashboard', absolute: false),
            default => route('client.dashboard', absolute: false),
        };

        $this->redirectIntended(session()->pull('url.intended', $url), navigate: true);

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
            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight text-center">Confirmar Contraseña</h2>
            <p class="mt-2 text-sm text-gray-500 text-center">
                {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
            </p>

            <form wire:submit="confirmPassword" class="mt-8 space-y-5">
                <div>
                    <x-input-label for="password" :value="__('Password')" class="mb-1 text-gray-700 font-medium" />
                    <div class="relative">
                        <i class="fa-solid fa-lock absolute left-3 top-3.5 text-gray-400"></i>
                        <x-text-input wire:model="password" id="password"
                            class="pl-10 block w-full h-11 border-gray-300 focus:border-amber-500 focus:ring-amber-500 rounded-lg shadow-sm"
                            type="password" name="password" required autocomplete="current-password"
                            placeholder="••••••••" />
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                <div class="pt-2">
                    <x-primary-button
                        class="w-full h-11 justify-center bg-gray-950 hover:bg-amber-600 text-white rounded-lg shadow-md transition duration-200 ease-in-out font-semibold text-sm tracking-wide">
                        <i class="fa-solid fa-check-double mr-2"></i>
                        {{ __('Confirm') }}
                    </x-primary-button>
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
                lugar con nuestra plataforma premium.</p>
        </div>
    </div>

</div>
