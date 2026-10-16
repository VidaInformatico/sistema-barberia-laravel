<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? env('APP_NAME') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('css')
</head>

<body>

    <livewire:front.navigation />

    <main>
        {{ $slot }}
    </main>

    <x-footer />

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('js')

</body>

</html>
