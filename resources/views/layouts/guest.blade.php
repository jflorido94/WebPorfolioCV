<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Portfolio') }} — Acceso</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {{-- Anti-FOUC: aplica dark antes del primer render --}}
        <x-dark-mode-preload />
    </head>
    <body
        x-data="{}"
        class="min-h-dvh bg-light-base dark:bg-dark-base antialiased"
    >
        {{-- Fondo Aurora --}}
        <div class="fixed inset-0 -z-10 bg-aurora opacity-90 dark:opacity-70"></div>
        <div class="fixed inset-0 -z-10 bg-dark-base/30 dark:bg-dark-base/60"></div>

        {{-- Centering wrapper --}}
        <div class="min-h-dvh flex flex-col items-center justify-center px-4 py-12">

            {{-- Logo / branding --}}
            <a href="{{ route('home') }}" class="mb-8 flex flex-col items-center gap-2 group">
                <div class="w-14 h-14 rounded-2xl bg-white/20 dark:bg-white/10 backdrop-blur-sm border border-white/30 flex items-center justify-center shadow-lg group-hover:scale-105 transition-transform duration-200">
                    <span class="font-heading font-black text-white text-xl">JF</span>
                </div>
                <span class="text-white/90 text-sm font-medium tracking-wide">Volver al portfolio</span>
            </a>

            {{-- Card --}}
            <div class="w-full max-w-md bg-white/90 dark:bg-dark-surface/90 backdrop-blur-md border border-white/50 dark:border-dark-border rounded-2xl shadow-2xl px-8 py-10">
                {{ $slot }}
            </div>

            {{-- Dark toggle --}}
            <x-dark-mode-toggle class="mt-6 flex items-center gap-2 text-white/70 hover:text-white text-sm transition-colors duration-200" />
        </div>
    </body>
</html>
