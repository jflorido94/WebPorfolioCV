<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ ($title ?? 'Admin') . ' | ' . config('app.name') }}</title>

    {{-- Anti-FOUC --}}
    <x-dark-mode-preload />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light-base dark:bg-dark-base text-slate-900 dark:text-slate-100 min-h-dvh" x-data="adminSidebar()">

    <div class="flex h-dvh overflow-hidden">

        {{-- ── Sidebar overlay (mobile) ────────────────────────────────── --}}
        <div x-show="open" x-transition:enter="transition-opacity ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-20 bg-black/50 md:hidden" @click="close()"></div>

        {{-- ── Sidebar ───────────────────────────────────────────────── --}}
        <aside :class="open ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
               class="fixed md:relative z-30 inset-y-0 left-0 w-64 flex flex-col bg-dark-surface border-r border-dark-border transition-transform duration-200 ease-out shrink-0">

            {{-- Logo --}}
            <div class="h-16 flex items-center px-6 border-b border-dark-border shrink-0">
                <a href="{{ route('home') }}" class="font-heading text-2xl font-black gradient-text">JF</a>
                <span class="ml-3 text-xs font-semibold text-slate-500 uppercase tracking-widest">Admin</span>
            </div>

            {{-- User info --}}
            <div class="px-4 py-4 border-b border-dark-border">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-aurora flex items-center justify-center text-white text-xs font-bold shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-slate-200 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-slate-500 truncate">{{ Auth::user()->email }}</p>
                    </div>
                </div>
            </div>

            {{-- Nav links --}}
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                @php
                    $navItems = [
                        ['route' => 'admin.cv.index',      'label' => 'Mi CV',   'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                        ['route' => 'admin.posts.index',   'label' => 'Posts',   'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
                        ['route' => 'admin.metrics.index', 'label' => 'Visitas', 'icon' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z'],
                    ];
                @endphp
                @foreach($navItems as $item)
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition-all duration-150 cursor-pointer
                          {{ request()->routeIs($item['route'] . '*')
                             ? 'bg-brand-purple text-white'
                             : 'text-slate-400 hover:text-slate-100 hover:bg-dark-elevated' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                    </svg>
                    {{ $item['label'] }}
                </a>
                @endforeach
            </nav>

            {{-- Bottom actions --}}
            <div class="px-3 py-4 border-t border-dark-border space-y-1 shrink-0">
                {{-- Dark mode toggle --}}
                <x-dark-mode-toggle class="flex items-center gap-3 w-full px-3 py-2.5 rounded-lg text-sm font-semibold text-slate-400 hover:text-slate-100 hover:bg-dark-elevated transition-all duration-150 cursor-pointer" />
                {{-- View site --}}
                <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold text-slate-400 hover:text-slate-100 hover:bg-dark-elevated transition-all duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    Ver portfolio
                </a>
                {{-- Logout --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 w-full px-3 py-2.5 rounded-lg text-sm font-semibold text-slate-400 hover:text-brand-coral hover:bg-dark-elevated transition-all duration-150 cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Cerrar sesion
                    </button>
                </form>
            </div>
        </aside>

        {{-- ── Main area ─────────────────────────────────────────────── --}}
        <div class="flex-1 flex flex-col overflow-hidden">

            {{-- Top bar --}}
            <header class="h-16 shrink-0 flex items-center justify-between px-6 bg-light-surface dark:bg-dark-surface border-b border-light-border dark:border-dark-border">
                {{-- Hamburger --}}
                <button @click="toggle()" class="md:hidden p-2 rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-dark-elevated transition-colors cursor-pointer" aria-label="Open sidebar">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                {{-- Page title --}}
                <h1 class="font-heading text-lg font-bold text-slate-800 dark:text-slate-100">{{ $title ?? 'Admin' }}</h1>
                {{-- Spacer --}}
                <div class="w-9 md:hidden"></div>
            </header>

            {{-- Page content --}}
            <main class="flex-1 overflow-y-auto p-6 lg:p-8">
                @if(session('success'))
                <div class="mb-6 flex items-center gap-3 px-4 py-3 rounded-xl bg-brand-teal/15 border border-brand-teal/30 text-brand-teal text-sm font-semibold">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('success') }}
                </div>
                @endif

                @if(session('error'))
                <div class="mb-6 flex items-center gap-3 px-4 py-3 rounded-xl bg-brand-coral/15 border border-brand-coral/30 text-brand-coral text-sm font-semibold">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('error') }}
                </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

</body>
</html>
