<x-barber-layout>
    <x-slot name="title">Mi Perfil</x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 w-full space-y-6 max-w-5xl mx-auto">
        <div class="flex items-center space-x-3 border-b border-slate-200 pb-4">
            <div class="p-2.5 bg-indigo-50 text-indigo-600 rounded-xl">
                <i class="fas fa-user-gear text-lg"></i>
            </div>
            <div>
                <h1 class="text-lg font-bold text-slate-800">Ajustes de Mi Perfil</h1>
                <p class="text-xs text-slate-500">Gestiona tu información personal y seguridad de la cuenta</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <livewire:profile.update-profile-information-form />
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <livewire:profile.update-password-form />
        </div>

        <div class="bg-white rounded-xl border border-red-100 shadow-sm p-6">
            <livewire:profile.delete-user-form />
        </div>
    </div>
</x-barber-layout>