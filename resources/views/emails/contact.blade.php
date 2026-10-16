<x-mail::message>
# Nuevo mensaje de contacto

Has recibido un nuevo mensaje desde el sitio web:

- **Nombre:** {{ $name }}
- **Teléfono:** {{ $phone }}
- **Asunto:** {{ $subject }}

**Mensaje:**
{{ $messageContent }}

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>