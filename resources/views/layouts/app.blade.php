<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $description ?? 'Portfolio profesional de Javier Florido - Desarrollador Web Fullstack PHP/Laravel' }}">
    <meta name="keywords" content="PHP, Laravel, desarrollo web, fullstack, Docker">
    <meta name="author" content="Javier Florido">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ ($title ?? 'Mi Portfolio') . ' | ' . config('app.name') }}">
    <meta property="og:description" content="{{ $description ?? 'Portfolio profesional de Javier Florido - Desarrollador Web Fullstack PHP/Laravel' }}">
    <meta property="og:image" content="{{ asset('og-image.jpg') }}">
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="{{ ($title ?? 'Mi Portfolio') . ' | ' . config('app.name') }}">
    <meta property="twitter:description" content="{{ $description ?? 'Portfolio profesional de Javier Florido - Desarrollador Web Fullstack PHP/Laravel' }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <title>{{ ($title ?? 'Mi Portfolio') . ' | ' . config('app.name') }}</title>

    {{-- Anti-FOUC: apply dark mode class before paint --}}
    <x-dark-mode-preload />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-dvh flex flex-col">

    {{-- ── Navbar ──────────────────────────────────────────────────────── --}}
    <header class="sticky top-0 z-50 backdrop-blur-md bg-light-surface/80 dark:bg-dark-base/80 border-b border-light-border dark:border-dark-border">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4" x-data="mobileNav()">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="font-heading text-2xl font-black gradient-text shrink-0">{{ $siteInitials }}</a>

            {{-- Desktop nav --}}
            <nav class="hidden md:flex items-center gap-8 text-sm font-semibold">
                <a href="{{ route('home') }}"
                   class="relative text-slate-700 dark:text-slate-300 hover:text-brand-purple dark:hover:text-brand-purple transition-colors after:absolute after:bottom-[-4px] after:left-0 after:w-0 after:h-0.5 after:bg-brand-purple after:transition-all hover:after:w-full {{ request()->routeIs('home') ? 'text-brand-purple after:w-full' : '' }}">
                    Inicio
                </a>
                <a href="{{ route('cv.show') }}"
                   class="relative text-slate-700 dark:text-slate-300 hover:text-brand-purple dark:hover:text-brand-purple transition-colors after:absolute after:bottom-[-4px] after:left-0 after:w-0 after:h-0.5 after:bg-brand-purple after:transition-all hover:after:w-full {{ request()->routeIs('cv.*') ? 'text-brand-purple after:w-full' : '' }}">
                    CV
                </a>
                <a href="{{ route('blog.index') }}"
                   class="relative text-slate-700 dark:text-slate-300 hover:text-brand-purple dark:hover:text-brand-purple transition-colors after:absolute after:bottom-[-4px] after:left-0 after:w-0 after:h-0.5 after:bg-brand-purple after:transition-all hover:after:w-full {{ request()->routeIs('blog.*') ? 'text-brand-purple after:w-full' : '' }}">
                    Blog
                </a>
            </nav>

            {{-- Right controls --}}
            <div class="flex items-center gap-3">
                {{-- Dark mode toggle --}}
                <x-dark-mode-toggle class="p-2 rounded-lg text-slate-500 dark:text-slate-400 hover:text-brand-purple dark:hover:text-brand-purple hover:bg-slate-100 dark:hover:bg-dark-elevated transition-all duration-200 cursor-pointer" />

                @auth
                    <a href="{{ route('admin.cv.index') }}" class="hidden md:inline-flex btn-primary text-sm py-2 px-4">Admin</a>
                    <form method="POST" action="{{ route('logout') }}" class="hidden md:block">
                        @csrf
                        <button type="submit" class="text-sm text-slate-500 dark:text-slate-400 hover:text-brand-coral transition-colors cursor-pointer">Logout</button>
                    </form>
                @endauth

                {{-- Hamburger --}}
                <button @click="toggle()" class="md:hidden p-2 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-dark-elevated transition-colors cursor-pointer" aria-label="Open menu">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" @click.outside="close()" class="md:hidden border-t border-light-border dark:border-dark-border bg-light-surface dark:bg-dark-surface px-4 py-4 space-y-3">
            <a href="{{ route('home') }}" @click="close()" class="block py-2 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:text-brand-purple transition-colors">Inicio</a>
            <a href="{{ route('cv.show') }}" @click="close()" class="block py-2 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:text-brand-purple transition-colors">CV</a>
            <a href="{{ route('blog.index') }}" @click="close()" class="block py-2 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:text-brand-purple transition-colors">Blog</a>
            @auth
                <a href="{{ route('admin.cv.index') }}" @click="close()" class="block py-2 text-sm font-semibold text-brand-purple">Admin</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block py-2 text-sm text-slate-500 dark:text-slate-400 hover:text-brand-coral transition-colors cursor-pointer">Logout</button>
                </form>
            @endauth
        </div>
    </header>

    {{-- ── Main content ─────────────────────────────────────────────────── --}}
    <main class="flex-1">{{ $slot }}</main>

    {{-- ── Footer ───────────────────────────────────────────────────────── --}}
    <footer class="border-t border-light-border dark:border-dark-border bg-light-surface dark:bg-dark-surface mt-20">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-12">
            <div class="grid md:grid-cols-3 gap-8">
                {{-- Brand --}}
                <div>
                    <span class="font-heading text-2xl font-black gradient-text">{{ $siteInitials }}</span>
                    <p class="mt-3 text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                        Desarrollador Web Fullstack especializado en PHP/Laravel.<br>Construyendo software robusto y escalable.
                    </p>
                </div>
                {{-- Nav --}}
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-4">Navegacion</p>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('home') }}" class="text-slate-600 dark:text-slate-400 hover:text-brand-purple transition-colors">Inicio</a></li>
                        <li><a href="{{ route('cv.show') }}" class="text-slate-600 dark:text-slate-400 hover:text-brand-purple transition-colors">CV</a></li>
                        <li><a href="{{ route('blog.index') }}" class="text-slate-600 dark:text-slate-400 hover:text-brand-purple transition-colors">Blog</a></li>
                    </ul>
                </div>
                {{-- Social --}}
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-4">Contacto</p>
                    <div class="flex gap-4">
                        @php $profile = auth()->user()?->profile; @endphp
                        @if($profile?->github_url)
                        <a href="{{ $profile->github_url }}" target="_blank" rel="noopener" aria-label="GitHub" class="text-slate-500 dark:text-slate-400 hover:text-brand-purple dark:hover:text-brand-purple transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/></svg>
                        </a>
                        @endif
                        @if($profile?->linkedin_url)
                        <a href="{{ $profile->linkedin_url }}" target="_blank" rel="noopener" aria-label="LinkedIn" class="text-slate-500 dark:text-slate-400 hover:text-brand-blue dark:hover:text-brand-blue transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                        </a>
                        @endif
                        <a href="mailto:jflorido94@hotmail.com" aria-label="Email" class="text-slate-500 dark:text-slate-400 hover:text-brand-coral dark:hover:text-brand-coral transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            <div class="mt-10 pt-6 border-t border-light-border dark:border-dark-border text-center text-xs text-slate-400 dark:text-slate-500">
                &copy; {{ date('Y') }} Javier Florido. Hecho con Laravel + Tailwind CSS.
            </div>
        </div>
    </footer>


</body>
</html>
