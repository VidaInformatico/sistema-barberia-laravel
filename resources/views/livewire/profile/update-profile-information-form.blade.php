<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Intervention\Image\ImageManager;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public string $name = '';
    public string $last_name = '';
    public string $email = '';
    public string $phone = '';
    public $avatar = null;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name ?? '';
        $this->last_name = $user->last_name ?? '';
        $this->email = $user->email ?? '';
        $this->phone = $user->phone ?? '';
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($this->avatar) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $filename = 'avatars/' . Str::uuid() . '.webp';

            $manager = ImageManager::gd();
            $imageConverted = $manager->read($this->avatar->getRealPath())->toWebp(80);

            Storage::disk('public')->put($filename, (string) $imageConverted);

            $validated['avatar'] = $filename;
        } else {
            unset($validated['avatar']);
        }

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->reset('avatar');

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section class="w-full">
    <div class="flex items-center space-x-3 border-b border-slate-100 pb-4 mb-6">
        <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
            <i class="fas fa-id-card text-sm"></i>
        </div>
        <div>
            <h2 class="text-sm font-bold text-slate-800">{{ __('Información del Perfil') }}</h2>
            <p class="text-xs text-slate-500">
                {{ __('Actualiza la información de tu cuenta, datos personales y fotografía de perfil.') }}
            </p>
        </div>
    </div>

    <form wire:submit="updateProfileInformation" class="space-y-4">
        <!-- Campo para Avatar -->
        <div class="col-span-2 space-y-2">
            <label class="block text-xs font-semibold text-slate-700">
                {{ __('Foto de Perfil (PNG, JPG, JPEG - Máx. 2MB)') }}
            </label>
            <div class="flex items-center gap-4">
                <div
                    class="relative w-20 h-20 rounded-full overflow-hidden border-2 border-slate-200 bg-slate-50 flex-shrink-0">
                    <div wire:loading.remove wire:target="avatar" class="w-full h-full">
                        @if ($avatar)
                            <img src="{{ $avatar->temporaryUrl() }}" class="w-full h-full object-cover">
                        @elseif (auth()->user()->avatar)
                            <img src="{{ Storage::url(auth()->user()->avatar) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-400 bg-slate-100">
                                <i class="fas fa-user text-2xl"></i>
                            </div>
                        @endif
                    </div>

                    <div wire:loading wire:target="avatar"
                        class="w-full h-full flex flex-col items-center justify-center bg-slate-900/50 text-white">
                        <i class="fas fa-spinner fa-spin text-lg"></i>
                    </div>
                </div>

                <div class="flex-1">
                    <div
                        class="relative flex flex-col items-center justify-center border-2 border-dashed border-slate-200 rounded-xl p-3 bg-slate-50/50 hover:bg-slate-50 transition-colors">
                        <input type="file" wire:model="avatar" accept="image/*"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                        <div class="text-center" wire:loading.remove wire:target="avatar">
                            <i class="fas fa-cloud-upload-alt text-lg text-slate-400 mb-1"></i>
                            <p class="text-xs font-bold text-slate-600">Haz clic o arrastra para seleccionar un avatar
                            </p>
                            <p class="text-[10px] text-slate-400">Se convertirá automáticamente a WebP</p>
                        </div>

                        <div class="text-center" wire:loading wire:target="avatar">
                            <p class="text-xs font-bold text-indigo-600">Procesando imagen...</p>
                            <p class="text-[10px] text-slate-400">Por favor espera un momento</p>
                        </div>
                    </div>
                </div>
            </div>
            <x-input-error :messages="$errors->get('avatar')" class="text-[11px] text-red-500 mt-1" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-1">
                <label for="name" class="block text-xs font-semibold text-slate-700">
                    {{ __('Nombre') }}
                </label>
                <div class="relative">
                    <span
                        class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-xs pointer-events-none">
                        <i class="fas fa-user"></i>
                    </span>
                    <input wire:model="name" id="name" name="name" type="text"
                        class="w-full pl-8 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 outline-none transition-all"
                        required autofocus autocomplete="given-name" />
                </div>
                <x-input-error :messages="$errors->get('name')" class="text-[11px] text-red-500 mt-1" />
            </div>

            <div class="space-y-1">
                <label for="last_name" class="block text-xs font-semibold text-slate-700">
                    {{ __('Apellido') }}
                </label>
                <div class="relative">
                    <span
                        class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-xs pointer-events-none">
                        <i class="fas fa-user"></i>
                    </span>
                    <input wire:model="last_name" id="last_name" name="last_name" type="text"
                        class="w-full pl-8 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 outline-none transition-all"
                        required autocomplete="family-name" />
                </div>
                <x-input-error :messages="$errors->get('last_name')" class="text-[11px] text-red-500 mt-1" />
            </div>

            <div class="space-y-1">
                <label for="email" class="block text-xs font-semibold text-slate-700">
                    {{ __('Correo Electrónico') }}
                </label>
                <div class="relative">
                    <span
                        class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-xs pointer-events-none">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input wire:model="email" id="email" name="email" type="email"
                        class="w-full pl-8 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 outline-none transition-all"
                        required autocomplete="username" />
                </div>
                <x-input-error :messages="$errors->get('email')" class="text-[11px] text-red-500 mt-1" />
            </div>

            <div class="space-y-1">
                <label for="phone" class="block text-xs font-semibold text-slate-700">
                    {{ __('Teléfono') }}
                </label>
                <div class="relative">
                    <span
                        class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-xs pointer-events-none">
                        <i class="fas fa-phone"></i>
                    </span>
                    <input wire:model="phone" id="phone" name="phone" type="text"
                        class="w-full pl-8 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 outline-none transition-all"
                        autocomplete="tel" />
                </div>
                <x-input-error :messages="$errors->get('phone')" class="text-[11px] text-red-500 mt-1" />
            </div>
        </div>

        @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !auth()->user()->hasVerifiedEmail())
            <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg mt-2">
                <p class="text-xs text-amber-800 flex items-center gap-2">
                    <i class="fas fa-triangle-exclamation text-amber-600"></i>
                    <span>{{ __('Tu dirección de correo electrónico no está verificada.') }}</span>
                    <button wire:click.prevent="sendVerification"
                        class="underline font-semibold text-amber-900 hover:text-amber-950 focus:outline-none ml-1">
                        {{ __('Haz clic aquí para reenviar el correo de verificación.') }}
                    </button>
                </p>

                @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 font-semibold text-xs text-emerald-700 flex items-center gap-1">
                        <i class="fas fa-circle-check"></i>
                        {{ __('Se ha enviado un nuevo enlace de verificación a tu correo electrónico.') }}
                    </p>
                @endif
            </div>
        @endif

        <div class="flex items-center justify-between pt-4 border-t border-slate-100 mt-4">
            <x-action-message class="text-xs text-emerald-600 font-medium flex items-center gap-1"
                on="profile-updated">
                <i class="fas fa-check-circle"></i> {{ __('Guardado correctamente.') }}
            </x-action-message>

            <button type="submit"
                class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-lg transition-colors flex items-center gap-2">
                <i class="fas fa-save"></i>
                <span>{{ __('Guardar Cambios') }}</span>
            </button>
        </div>
    </form>
</section>
