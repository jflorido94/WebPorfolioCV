<x-app-layout>
    @php
        $title = $post->title;
        $description = $post->summary ?? substr(strip_tags($post->content), 0, 160);
    @endphp

    {{-- Article header --}}
    <section class="relative overflow-hidden py-16">
        <div class="absolute inset-0 bg-aurora opacity-10 dark:opacity-20"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-transparent to-light-base dark:to-dark-base"></div>
        <div class="relative max-w-3xl mx-auto px-4 sm:px-6">
            <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-brand-purple hover:text-brand-pink transition-colors mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver al blog
            </a>
            @php
                $badgeColors = ['blog' => 'badge-purple','laravel' => 'badge-coral','php' => 'badge-blue','devops' => 'badge-teal','general' => 'badge-amber'];
                $badgeColor = $badgeColors[strtolower($post->category)] ?? 'badge-purple';
            @endphp
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <span class="badge {{ $badgeColor }}">{{ $post->category }}</span>
                <time class="text-sm text-slate-500 dark:text-slate-400">{{ $post->published_at->format('d M Y') }}</time>
            </div>
            <h1 class="font-heading text-4xl lg:text-5xl font-black leading-tight mb-4">{{ $post->title }}</h1>
            @if($post->summary)
            <p class="text-lg text-slate-600 dark:text-slate-400 leading-relaxed">{{ $post->summary }}</p>
            @endif
        </div>
    </section>

    {{-- Article body --}}
    <section class="py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6">
            <div class="prose-portfolio">
                {!! $post->content_html !!}
            </div>

            {{-- Tags --}}
            @if($post->tag_list)
            <div class="mt-12 pt-8 border-t border-light-border dark:border-dark-border">
                <p class="text-xs font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-3">Tags</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($post->tag_list as $tag)
                    <span class="badge badge-coral">#{{ $tag }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Back link --}}
            <div class="mt-12">
                <a href="{{ route('blog.index') }}" class="btn-secondary inline-flex">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver al blog
                </a>
            </div>
        </div>
    </section>

</x-app-layout>
