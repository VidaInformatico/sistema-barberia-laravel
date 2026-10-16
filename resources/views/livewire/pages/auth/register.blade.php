<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public string $name = '';
    public string $last_name = '';
    public string $phone = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = 'client';

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);

        $this->redirect(route('client.dashboard', absolute: false), navigate: true);
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
            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight text-center">Crea tu Cuenta</h2>
            <p class="mt-2 text-sm text-gray-500 text-center">Únete a nuestra red premium de barberos.</p>

            <form wire:submit="register" class="mt-8 space-y-5">
                <div>
                    <x-input-label for="name" :value="__('Name')" class="mb-1 text-gray-700 font-medium" />
                    <div class="relative">
                        <i class="fa-solid fa-user absolute left-3 top-3.5 text-gray-400"></i>
                        <x-text-input wire:model="name" id="name"
                            class="pl-10 block w-full h-11 border-gray-300 focus:border-amber-500 focus:ring-amber-500 rounded-lg shadow-sm"
                            type="text" name="name" required autofocus autocomplete="name"
                            placeholder="Tu nombre completo" />
                    </div>
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="last_name" :value="__('Apellido')" class="mb-1 text-gray-700 font-medium" />
                    <div class="relative">
                        <i class="fa-solid fa-user absolute left-3 top-3.5 text-gray-400"></i>
                        <x-text-input wire:model="last_name" id="last_name"
                            class="pl-10 block w-full h-11 border-gray-300 focus:border-amber-500 focus:ring-amber-500 rounded-lg shadow-sm"
                            type="text" name="last_name" required placeholder="Apellido" />
                    </div>
                    <x-input-error :messages="$errors->get('last_name')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="phone" :value="__('Teléfono')" class="mb-1 text-gray-700 font-medium" />
                    <div class="relative">
                        <i class="fa-solid fa-phone absolute left-3 top-3.5 text-gray-400"></i>
                        <x-text-input wire:model="phone" id="phone"
                            class="pl-10 block w-full h-11 border-gray-300 focus:border-amber-500 focus:ring-amber-500 rounded-lg shadow-sm"
                            type="text" name="phone" required placeholder="51900897537" />
                    </div>
                    <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="email" :value="__('Email')" class="mb-1 text-gray-700 font-medium" />
                    <div class="relative">
                        <i class="fa-solid fa-envelope absolute left-3 top-3.5 text-gray-400"></i>
                        <x-text-input wire:model="email" id="email"
                            class="pl-10 block w-full h-11 border-gray-300 focus:border-amber-500 focus:ring-amber-500 rounded-lg shadow-sm"
                            type="email" name="email" required autocomplete="username"
                            placeholder="correo@ejemplo.com" />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="password" :value="__('Password')" class="mb-1 text-gray-700 font-medium" />
                    <div class="relative">
                        <i class="fa-solid fa-lock absolute left-3 top-3.5 text-gray-400"></i>
                        <x-text-input wire:model="password" id="password"
                            class="pl-10 block w-full h-11 border-gray-300 focus:border-amber-500 focus:ring-amber-500 rounded-lg shadow-sm"
                            type="password" name="password" required autocomplete="new-password"
                            placeholder="••••••••" />
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')"
                        class="mb-1 text-gray-700 font-medium" />
                    <div class="relative">
                        <i class="fa-solid fa-shield absolute left-3 top-3.5 text-gray-400"></i>
                        <x-text-input wire:model="password_confirmation" id="password_confirmation"
                            class="pl-10 block w-full h-11 border-gray-300 focus:border-amber-500 focus:ring-amber-500 rounded-lg shadow-sm"
                            type="password" name="password_confirmation" required autocomplete="new-password"
                            placeholder="••••••••" />
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                </div>

                <div class="pt-2">
                    <x-primary-button wire:loading.attr="disabled" wire:target="register"
                        class="w-full h-11 justify-center bg-gray-950 hover:bg-amber-600 text-white rounded-lg shadow-md transition duration-200 ease-in-out font-semibold text-sm tracking-wide disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="register">
                            <i class="fa-solid fa-user-plus mr-2"></i>
                            {{ __('Register') }}
                        </span>
                        <span wire:loading wire:target="register" class="inline-flex items-center">
                            <i class="fa-solid fa-circle-notch fa-spin mr-2"></i>
                            Procesando...
                        </span>
                    </x-primary-button>
                </div>

                <div class="text-center pt-2">
                    <p class="text-sm text-gray-500">
                        ¿Ya tienes una cuenta?
                        <a class="font-semibold text-amber-600 hover:text-amber-700 transition"
                            href="{{ route('login') }}" wire:navigate>
                            Inicia sesión aquí
                        </a>
                    </p>
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
