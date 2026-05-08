<x-app-layout>
    @php
        $title = 'Blog';
        $description = 'Blog de Javier Florido con articulos sobre desarrollo web, Laravel, PHP y tecnologias modernas.';
    @endphp

    {{-- Header --}}
    <section class="relative overflow-hidden py-16">
        <div class="absolute inset-0 bg-aurora opacity-10 dark:opacity-20"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-transparent to-light-base dark:to-dark-base"></div>
        <div class="relative max-w-6xl mx-auto px-4 sm:px-6">
            <span class="section-label">Blog</span>
            <h1 class="font-heading text-4xl lg:text-5xl font-black mb-4">Articulos y proyectos</h1>
            <p class="text-slate-600 dark:text-slate-400 max-w-xl">Notas tecnicas, proyectos y reflexiones sobre desarrollo web.</p>
        </div>
    </section>

    {{-- Posts grid --}}
    <section class="py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            @forelse($posts as $index => $post)
            @php
                $badgeColors = ['badge-purple','badge-teal','badge-coral','badge-blue','badge-pink','badge-amber'];
                $badgeColor = $badgeColors[crc32($post->category) % count($badgeColors)];
            @endphp
            <article class="reveal group card-glass rounded-2xl p-6 mb-6 flex flex-col sm:flex-row gap-6 hover:border-brand-purple/40 dark:hover:border-brand-purple/50 transition-all duration-200">
                <div class="flex-1">
                    <div class="flex flex-wrap items-center gap-3 mb-3">
                        <span class="badge {{ $badgeColor }}">{{ $post->category }}</span>
                        <time class="text-xs text-slate-400 dark:text-slate-500">{{ $post->published_at->format('d M Y') }}</time>
                    </div>
                    <h2 class="font-heading text-xl font-bold mb-2 group-hover:text-brand-purple transition-colors">
                        <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                    </h2>
                    @if($post->summary)
                    <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed line-clamp-2">{{ $post->summary }}</p>
                    @endif
                    @if($post->tag_list)
                    <div class="flex flex-wrap gap-1.5 mt-3">
                        @foreach(array_slice($post->tag_list, 0, 4) as $tag)
                        <span class="px-2 py-0.5 rounded text-xs bg-slate-100 dark:bg-dark-elevated text-slate-600 dark:text-slate-400">#{{ $tag }}</span>
                        @endforeach
                    </div>
                    @endif
                </div>
                <div class="sm:self-center shrink-0">
                    <a href="{{ route('blog.show', $post->slug) }}" class="inline-flex items-center gap-1 text-sm font-semibold text-brand-purple hover:text-brand-pink transition-colors group-hover:translate-x-1 transition-transform">
                        Leer
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                </div>
            </article>
            @empty
            <div class="text-center py-20">
                <p class="text-slate-400 dark:text-slate-500 text-lg mb-2">Aun no hay articulos publicados.</p>
                <p class="text-slate-400 dark:text-slate-600 text-sm">Vuelve pronto.</p>
            </div>
            @endforelse

            @if($posts->hasPages())
            <div class="mt-12 flex justify-center">
                {{ $posts->links() }}
            </div>
            @endif
        </div>
    </section>

</x-app-layout>
