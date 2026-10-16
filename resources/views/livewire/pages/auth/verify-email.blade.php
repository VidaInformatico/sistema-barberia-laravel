<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('client.dashboard', absolute: false), navigate: true);

            return;
        }

        Auth::user()->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
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
            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight text-center">Verifica tu Correo</h2>
            <p class="mt-2 text-sm text-gray-500 text-center">
                {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
            </p>

            @if (session('status') == 'verification-link-sent')
                <div
                    class="mt-4 p-4 bg-green-50 border-l-4 border-green-500 text-sm font-medium text-green-700 rounded-r-lg">
                    <i class="fa-solid fa-circle-check mr-2"></i>
                    {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                </div>
            @endif

            <div class="mt-8 space-y-4">
                <div>
                    <x-primary-button wire:click="sendVerification"
                        class="w-full h-11 justify-center bg-gray-950 hover:bg-amber-600 text-white rounded-lg shadow-md transition duration-200 ease-in-out font-semibold text-sm tracking-wide">
                        <i class="fa-solid fa-envelope-circle-check mr-2"></i>
                        {{ __('Resend Verification Email') }}
                    </x-primary-button>
                </div>

                <div class="text-center pt-2">
                    <button wire:click="logout" type="button"
                        class="text-sm font-semibold text-gray-500 hover:text-red-600 transition inline-flex items-center">
                        <i class="fa-solid fa-right-from-bracket mr-2"></i>
                        {{ __('Log Out') }}
                    </button>
                </div>
            </div>
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
