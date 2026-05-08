<x-app-layout>
    @php
        $title = 'CV';
        $description = 'Curriculum vitae de ' . $user->name . ' - ' . ($user->profile?->title ?? 'Desarrollador Web Fullstack');
        $contactEmail = $user->profile?->contact_email ?? $user->email;
    @endphp

    {{-- ── CV HEADER ───────────────────────────────────────────────────────── --}}
    <section class="bg-aurora py-16">
        <div class="max-w-5xl mx-auto px-4 sm:px-6">
            <div class="flex flex-col sm:flex-row items-center sm:items-end justify-between gap-6">
                <div class="text-white text-center sm:text-left">
                    <p class="text-white/70 text-sm font-semibold uppercase tracking-widest mb-2">Curriculum Vitae</p>
                    <h1 class="font-heading text-4xl lg:text-5xl font-black mb-2">{{ $user->name }}</h1>
                    <p class="text-white/90 text-lg font-medium">{{ $user->profile?->title }}</p>
                    @if($user->profile?->location)
                    <p class="text-white/70 text-sm mt-1 flex items-center gap-1 justify-center sm:justify-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        {{ $user->profile->location }}
                    </p>
                    @endif
                </div>
                <div class="flex gap-3 shrink-0">
                    <a href="{{ route('cv.print') }}" target="_blank" class="inline-flex items-center gap-2 bg-white/10 border border-white/30 text-white text-sm font-semibold px-4 py-2.5 rounded-xl hover:bg-white/20 transition-all duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Imprimir
                    </a>
                    <a href="{{ route('cv.download-pdf') }}" class="inline-flex items-center gap-2 bg-white text-brand-purple text-sm font-bold px-4 py-2.5 rounded-xl hover:bg-slate-100 transition-all duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        PDF
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ── CV BODY ───────────────────────────────────────────────────────────── --}}
    <section class="py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6">
            <div class="grid lg:grid-cols-3 gap-8">

                {{-- ── LEFT SIDEBAR ─────────────────────────────────────── --}}
                <aside class="space-y-8">
                    {{-- Avatar --}}
                    <div class="flex justify-center lg:justify-start">
                        <div class="w-24 h-24 rounded-2xl bg-aurora overflow-hidden flex items-center justify-center">
                            @if($user->profile?->avatar_path)
                            <img src="{{ asset('storage/' . $user->profile->avatar_path) }}"
                                 class="w-full h-full object-cover" alt="{{ $user->name }}">
                            @else
                            <span class="font-heading text-3xl font-black text-white">{{ $user->profile?->avatar_initials ?? 'JF' }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Contact --}}
                    <div class="card-glass rounded-2xl p-5 space-y-3">
                        <h3 class="font-heading font-bold text-sm uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-4">Contacto</h3>
                        <a href="mailto:{{ $contactEmail }}" class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 hover:text-brand-purple transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0 text-brand-coral" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            {{ $contactEmail }}
                        </a>
                        @if($user->profile?->github_url)
                        <a href="{{ $user->profile->github_url }}" target="_blank" rel="noopener" class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 hover:text-brand-purple transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0 text-slate-700 dark:text-slate-300" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/></svg>
                            GitHub
                        </a>
                        @endif
                        @if($user->profile?->linkedin_url)
                        <a href="{{ $user->profile->linkedin_url }}" target="_blank" rel="noopener" class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 hover:text-brand-blue transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0 text-brand-blue" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                            LinkedIn
                        </a>
                        @endif
                    </div>

                    {{-- Skills --}}
                    @if($user->skills->where('show_in_web', true)->isNotEmpty())
                    <div class="card-glass rounded-2xl p-5">
                        <h3 class="font-heading font-bold text-sm uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-4">Competencias</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($user->skills->where('show_in_web', true) as $skill)
                            <span class="badge badge-purple">{{ $skill->name }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Education --}}
                    @if($user->education->where('show_in_web', true)->isNotEmpty())
                    <div class="card-glass rounded-2xl p-5 space-y-4">
                        <h3 class="font-heading font-bold text-sm uppercase tracking-widest text-slate-500 dark:text-slate-400">Educacion</h3>
                        @foreach($user->education->where('show_in_web', true) as $edu)
                        <div>
                            <p class="font-heading font-bold text-sm">{{ $edu->title }}</p>
                            <p class="text-xs text-brand-amber dark:text-yellow-400 font-semibold mt-0.5">{{ $edu->institution }}</p>
                            @if($edu->location)
                            <p class="text-xs text-slate-500 dark:text-slate-500 mt-0.5">{{ $edu->location }}</p>
                            @endif
                            @if($edu->year)
                            <p class="text-xs text-slate-500 dark:text-slate-500 mt-0.5">{{ $edu->year }}</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @endif

                    {{-- Cursos --}}
                    @php $cvCourses = $user->courses->where('type', 'course')->where('show_in_web', true); @endphp
                    @if($cvCourses->isNotEmpty())
                    <div class="card-glass rounded-2xl p-5 space-y-4">
                        <h3 class="font-heading font-bold text-sm uppercase tracking-widest text-slate-500 dark:text-slate-400">Cursos</h3>
                        @foreach($cvCourses as $course)
                        <div>
                            <p class="font-heading font-bold text-sm">{{ $course->title }}</p>
                            <p class="text-xs text-brand-teal dark:text-teal-400 font-semibold mt-0.5">{{ $course->institution }}</p>
                            @if($course->year)
                            <p class="text-xs text-slate-500 dark:text-slate-500 mt-0.5">{{ $course->year }}</p>
                            @endif
                            @if($course->url)
                            <a href="{{ $course->url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 text-xs text-brand-blue hover:underline mt-0.5">
                                Ver curso
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </a>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @endif

                    {{-- Certificaciones --}}
                    @php $cvCertifications = $user->courses->where('type', 'certification')->where('show_in_web', true); @endphp
                    @if($cvCertifications->isNotEmpty())
                    <div class="card-glass rounded-2xl p-5 space-y-4">
                        <h3 class="font-heading font-bold text-sm uppercase tracking-widest text-slate-500 dark:text-slate-400">Certificaciones</h3>
                        @foreach($cvCertifications as $cert)
                        <div>
                            <p class="font-heading font-bold text-sm">{{ $cert->title }}</p>
                            <p class="text-xs text-brand-blue dark:text-blue-400 font-semibold mt-0.5">{{ $cert->institution }}</p>
                            @if($cert->year)
                            <p class="text-xs text-slate-500 dark:text-slate-500 mt-0.5">{{ $cert->year }}</p>
                            @endif
                            @if($cert->url)
                            <a href="{{ $cert->url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 text-xs text-brand-blue hover:underline mt-0.5">
                                Ver certificado
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </a>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @endif
                </aside>

                {{-- ── MAIN CONTENT ──────────────────────────────────────── --}}
                <div class="lg:col-span-2 space-y-10">
                    {{-- Bio --}}
                    @if($user->profile?->bio)
                    <div class="reveal">
                        <h2 class="font-heading text-xl font-black mb-4 flex items-center gap-3">
                            <span class="w-8 h-0.5 bg-aurora inline-block"></span>
                            Sobre mi
                        </h2>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed" style="white-space: pre-line">{{ $user->profile->bio }}</p>
                    </div>
                    @endif

                    {{-- Experience --}}
                    @if($user->experiences->where('show_in_web', true)->isNotEmpty())
                    <div class="reveal">
                        <h2 class="font-heading text-xl font-black mb-6 flex items-center gap-3">
                            <span class="w-8 h-0.5 bg-aurora inline-block"></span>
                            Experiencia
                        </h2>
                        <div class="space-y-6">
                            @foreach($user->experiences->where('show_in_web', true) as $exp)
                            <div class="card-glass rounded-2xl p-5 border-l-4 border-brand-purple">
                                <div class="flex flex-wrap items-start justify-between gap-2 mb-1">
                                    <h3 class="font-heading font-bold text-base">{{ $exp->role }}</h3>
                                    <span class="badge badge-purple shrink-0 text-xs">{{ $exp->period }}</span>
                                </div>
                                <p class="text-brand-purple dark:text-purple-400 text-sm font-semibold">{{ $exp->company }}</p>
                                @if($exp->location)
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    {{ $exp->location }}
                                </p>
                                @endif
                                @if($exp->description)
                                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed mt-2">{{ $exp->description }}</p>
                                @endif
                                @if($exp->competencies->isNotEmpty())
                                <div class="flex flex-wrap gap-1.5 mt-3">
                                    @foreach($exp->competencies as $comp)
                                    <span class="badge badge-purple text-xs opacity-80">{{ $comp->name }}</span>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

</x-app-layout>
