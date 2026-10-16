<?php

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    #[Locked]
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Mount the component.
     */
    public function mount(string $token): void
    {
        $this->token = $token;

        $this->email = request()->string('email');
    }

    /**
     * Reset the password for the given user.
     */
    public function resetPassword(): void
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset($this->only('email', 'password', 'password_confirmation', 'token'), function ($user) {
            $user
                ->forceFill([
                    'password' => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])
                ->save();

            event(new PasswordReset($user));
        });

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        if ($status != Password::PASSWORD_RESET) {
            $this->addError('email', __($status));

            return;
        }

        Session::flash('status', __($status));

        $this->redirectRoute('login', navigate: true);
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
            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight text-center">Restablecer Contraseña</h2>
            <p class="mt-2 text-sm text-gray-500 text-center">Ingresa tu nueva contraseña para actualizar las
                credenciales de tu cuenta.</p>

            <form wire:submit="resetPassword" class="mt-8 space-y-5">
                <div>
                    <x-input-label for="email" :value="__('Email')" class="mb-1 text-gray-700 font-medium" />
                    <div class="relative">
                        <i class="fa-solid fa-envelope absolute left-3 top-3.5 text-gray-400"></i>
                        <x-text-input wire:model="email" id="email"
                            class="pl-10 block w-full h-11 border-gray-300 focus:border-amber-500 focus:ring-amber-500 rounded-lg shadow-sm"
                            type="email" name="email" required autofocus autocomplete="username"
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
                    <x-primary-button
                        class="w-full h-11 justify-center bg-gray-950 hover:bg-amber-600 text-white rounded-lg shadow-md transition duration-200 ease-in-out font-semibold text-sm tracking-wide">
                        <i class="fa-solid fa-key mr-2"></i>
                        {{ __('Reset Password') }}
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
