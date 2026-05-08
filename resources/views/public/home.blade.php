<x-app-layout>
    @php
        $title = 'Mi Portfolio';
        $description = 'Portfolio profesional de Javier Florido - Desarrollador Web Fullstack PHP/Laravel especializado en soluciones robustas y escalables.';
    @endphp

    {{-- ── HERO ──────────────────────────────────────────────────────────── --}}
    <section class="relative overflow-hidden">
        {{-- Aurora background --}}
        <div class="absolute inset-0 bg-aurora opacity-10 dark:opacity-20"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-light-base dark:to-dark-base"></div>

        <div class="relative max-w-6xl mx-auto px-4 sm:px-6 py-24 lg:py-32">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                {{-- Text --}}
                <div class="animate-fade-in-up">
                    <span class="section-label">Bienvenido</span>
                    <h1 class="font-heading text-5xl lg:text-6xl font-black leading-tight mb-6">
                        {{ $user->name }}<br>
                        <span class="gradient-text">{{ $user->profile?->title ?? 'Desarrollador Web' }}</span>
                    </h1>
                    <p class="text-lg text-slate-600 dark:text-slate-400 leading-relaxed mb-8 max-w-lg">
                        {!! nl2br(e($user->profile?->bio ?? 'Desarrollador apasionado por construir soluciones robustas y escalables.')) !!}
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('blog.index') }}" class="btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                            Ver proyectos
                        </a>
                        <a href="{{ route('cv.show') }}" class="btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Mi CV
                        </a>
                    </div>
                    {{-- Social links --}}
                    <div class="flex items-center gap-4 mt-8">
                        @if($user->profile?->github_url)
                        <a href="{{ $user->profile->github_url }}" target="_blank" rel="noopener" class="text-slate-400 hover:text-brand-purple dark:hover:text-brand-purple transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/></svg>
                        </a>
                        @endif
                        @if($user->profile?->linkedin_url)
                        <a href="{{ $user->profile->linkedin_url }}" target="_blank" rel="noopener" class="text-slate-400 hover:text-brand-blue dark:hover:text-brand-blue transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                        </a>
                        @endif
                    </div>
                </div>
                {{-- Avatar --}}
                <div class="flex justify-center lg:justify-end animate-fade-in-up animation-delay-200">
                    <div class="relative">
                        <div class="w-52 h-52 lg:w-64 lg:h-64 rounded-full bg-aurora overflow-hidden flex items-center justify-center animate-pulse-glow">
                            @if($user->profile?->avatar_path)
                            <img src="{{ asset('storage/' . $user->profile->avatar_path) }}"
                                 class="w-full h-full object-cover object-center"
                                 alt="{{ $user->name }}">
                            @else
                            <span class="font-heading text-7xl lg:text-8xl font-black text-white">
                                {{ $user->profile?->avatar_initials ?? 'JF' }}
                            </span>
                            @endif
                        </div>
                        {{-- Decorative ring --}}
                        <div class="absolute -inset-3 rounded-full border-2 border-brand-purple/30 dark:border-brand-purple/50"></div>
                        <div class="absolute -inset-6 rounded-full border border-brand-teal/20 dark:border-brand-teal/30"></div>
                        {{-- Location badge --}}
                        @if($user->profile?->location)
                        <div class="absolute -bottom-4 left-1/2 -translate-x-1/2 whitespace-nowrap card-glass rounded-full px-4 py-1.5 text-xs font-semibold text-slate-700 dark:text-slate-200 flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-brand-coral shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ $user->profile->location }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── STATS BAR ───────────────────────────────────────────────────────── --}}
    <section class="bg-aurora py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-white text-center">
                <div class="reveal">
                    <p class="font-heading text-3xl font-black">{{ max(1, $user->years_of_experience) }}+</p>
                    <p class="text-sm text-white/80 mt-1">Anos de experiencia</p>
                </div>
                <div class="reveal animation-delay-100">
                    <p class="font-heading text-3xl font-black">{{ $user->skills->count() }}+</p>
                    <p class="text-sm text-white/80 mt-1">Tecnologias</p>
                </div>
                <div class="reveal animation-delay-200">
                    <p class="font-heading text-3xl font-black">{{ $user->experiences->count() }}</p>
                    <p class="text-sm text-white/80 mt-1">Empresas</p>
                </div>
                <div class="reveal animation-delay-300">
                    <p class="font-heading text-3xl font-black">100%</p>
                    <p class="text-sm text-white/80 mt-1">Compromiso</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ── LATEST POSTS ─────────────────────────────────────────────────────── --}}
    @if($posts->isNotEmpty())
    <section class="py-20">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="flex items-end justify-between mb-12 reveal">
                <div>
                    <span class="section-label">Blog</span>
                    <h2 class="font-heading text-3xl font-black">Ultimos articulos</h2>
                </div>
                <a href="{{ route('blog.index') }}" class="text-sm font-semibold text-brand-purple hover:text-brand-pink transition-colors hidden sm:inline-flex items-center gap-1">
                    Ver todos
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            </div>
            <div class="grid md:grid-cols-3 gap-6">
                @foreach($posts as $index => $post)
                @php
                    $badgeColors = ['badge-purple','badge-teal','badge-coral','badge-blue','badge-pink','badge-amber'];
                    $badgeColor = $badgeColors[$index % count($badgeColors)];
                @endphp
                <a href="{{ route('blog.show', $post->slug) }}" class="reveal animation-delay-{{ $index * 100 }} group card-glass rounded-2xl p-6 flex flex-col hover:border-brand-purple/40 dark:hover:border-brand-purple/50 transition-all duration-200 hover:-translate-y-1 hover:shadow-lg">
                    <span class="badge {{ $badgeColor }} mb-4 self-start">{{ $post->category }}</span>
                    <h3 class="font-heading font-bold text-lg leading-snug mb-3 group-hover:text-brand-purple transition-colors">{{ $post->title }}</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed mb-4 flex-1 line-clamp-3">{{ $post->summary }}</p>
                    <div class="flex items-center justify-between text-xs text-slate-400 dark:text-slate-500 mt-auto pt-4 border-t border-light-border dark:border-dark-border">
                        <time>{{ $post->published_at->format('d M Y') }}</time>
                        <span class="text-brand-purple group-hover:translate-x-1 transition-transform inline-block">Leer →</span>
                    </div>
                </a>
                @endforeach
            </div>
            <div class="text-center mt-8 sm:hidden">
                <a href="{{ route('blog.index') }}" class="btn-secondary text-sm py-2.5 px-5">Ver todos los articulos</a>
            </div>
        </div>
    </section>
    @endif

    {{-- ── EXPERIENCE ───────────────────────────────────────────────────────── --}}
    @if($user->experiences->where('show_in_web', true)->isNotEmpty())
    <section class="py-20 bg-light-surface dark:bg-dark-surface">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="reveal mb-12">
                <span class="section-label">Trayectoria</span>
                <h2 class="font-heading text-3xl font-black">Experiencia profesional</h2>
            </div>
            <div class="relative">
                {{-- Vertical line --}}
                <div class="absolute left-6 top-2 bottom-2 w-0.5 bg-gradient-to-b from-brand-purple via-brand-teal to-brand-blue hidden md:block"></div>
                <div class="space-y-8">
                    @foreach($user->experiences->where('show_in_web', true) as $index => $exp)
                    @php
                        $dotColors = ['bg-brand-purple','bg-brand-teal','bg-brand-coral','bg-brand-blue'];
                        $dotColor = $dotColors[$index % count($dotColors)];
                    @endphp
                    <div class="reveal animation-delay-{{ $index * 100 }} flex gap-6 md:gap-8">
                        {{-- Dot --}}
                        <div class="hidden md:flex flex-col items-center shrink-0">
                            <div class="w-3 h-3 rounded-full {{ $dotColor }} mt-1.5 ring-4 ring-light-surface dark:ring-dark-surface shrink-0"></div>
                        </div>
                        {{-- Content --}}
                        <div class="card-glass rounded-2xl p-6 flex-1">
                            <div class="flex flex-wrap items-start justify-between gap-2 mb-2">
                                <h3 class="font-heading text-xl font-bold">{{ $exp->role }}</h3>
                                <span class="badge badge-purple shrink-0">{{ $exp->period }}</span>
                            </div>
                            <p class="text-brand-purple dark:text-purple-400 font-semibold text-sm">{{ $exp->company }}</p>
                            @if($exp->location)
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 mb-3 flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ $exp->location }}
                            </p>
                            @else
                            <div class="mb-3"></div>
                            @endif
                            @if($exp->description)
                            <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">{{ $exp->description }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- ── SKILLS ───────────────────────────────────────────────────────────── --}}
    @if($user->skills->where('show_in_web', true)->isNotEmpty())
    <section class="py-20">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="reveal mb-12">
                <span class="section-label">Competencias</span>
                <h2 class="font-heading text-3xl font-black">Stack tecnologico</h2>
            </div>
            @php
                $skillsByCategory = $user->skills->where('show_in_web', true)->groupBy('category');
                $categoryColors = ['Backend' => 'badge-purple', 'Frontend' => 'badge-blue', 'DevOps' => 'badge-teal', 'Mobile' => 'badge-pink', 'Database' => 'badge-amber', 'general' => 'badge-coral'];
            @endphp
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($skillsByCategory as $category => $skills)
                <div class="reveal card-glass rounded-2xl p-6">
                    <span class="badge {{ $categoryColors[$category] ?? 'badge-purple' }} mb-4">{{ ucfirst($category) }}</span>
                    <div class="flex flex-wrap gap-2 mt-2">
                        @foreach($skills as $skill)
                        <span class="px-3 py-1.5 rounded-lg text-sm font-semibold bg-slate-100 dark:bg-dark-elevated text-slate-700 dark:text-slate-300">
                            {{ $skill->name }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ── CONTACT CTA ──────────────────────────────────────────────────────── --}}
    <section class="py-20 bg-aurora">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 text-center text-white">
            <h2 class="font-heading text-4xl font-black mb-4">Trabajemos juntos</h2>
            <p class="text-white/80 mb-8 text-lg">Siempre abierto a nuevas oportunidades y proyectos interesantes.</p>
            <div class="flex flex-wrap gap-4 justify-center">
                <a href="mailto:jflorido94@hotmail.com" class="inline-flex items-center gap-2 bg-white text-brand-purple font-bold px-6 py-3 rounded-xl hover:bg-slate-100 transition-all duration-200 hover:scale-105">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Contactar
                </a>
                <a href="{{ route('cv.show') }}" class="inline-flex items-center gap-2 bg-white/10 border border-white/30 text-white font-bold px-6 py-3 rounded-xl hover:bg-white/20 transition-all duration-200 hover:scale-105">
                    Ver mi CV completo
                </a>
            </div>
        </div>
    </section>

</x-app-layout>
