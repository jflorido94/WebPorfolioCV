<x-admin-layout>
    @php $title = 'Posts'; @endphp

    <div class="flex items-center justify-between mb-8">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-1">Gestion</p>
            <h1 class="font-heading text-2xl font-black">Posts del blog</h1>
        </div>
        <a href="{{ route('admin.posts.create') }}" class="btn-primary text-sm py-2.5 px-5">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo post
        </a>
    </div>

    <div class="card-glass rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-light-border dark:border-dark-border">
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500">Titulo</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500 hidden sm:table-cell">Categoria</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500">Estado</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500 hidden md:table-cell">Fecha</th>
                    <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-light-border dark:divide-dark-border">
                @forelse($posts as $post)
                <tr class="hover:bg-slate-50 dark:hover:bg-dark-elevated transition-colors">
                    <td class="px-6 py-4 font-semibold max-w-xs">
                        <span class="line-clamp-1">{{ $post->title }}</span>
                    </td>
                    <td class="px-6 py-4 hidden sm:table-cell">
                        <span class="badge badge-purple text-xs">{{ $post->category }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @if($post->published)
                        <span class="badge badge-teal">Publicado</span>
                        @else
                        <span class="badge bg-slate-100 dark:bg-dark-elevated text-slate-500 dark:text-slate-400">Borrador</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 hidden md:table-cell text-xs text-slate-400 dark:text-slate-500">
                        {{ $post->published_at?->format('d M Y') ?? '-' }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="p-1.5 rounded-lg text-slate-400 hover:text-brand-blue hover:bg-brand-blue/10 transition-all" title="Ver">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </a>
                            <a href="{{ route('admin.posts.edit', $post) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-brand-purple hover:bg-brand-purple/10 transition-all" title="Editar">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="inline" x-data @submit.prevent="confirm('Eliminar este post?') && $el.submit()">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-brand-coral hover:bg-brand-coral/10 transition-all cursor-pointer" title="Eliminar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-16 text-center text-slate-400 dark:text-slate-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mx-auto mb-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                        </svg>
                        <p class="font-semibold">No hay posts todavia</p>
                        <p class="text-xs mt-1">Crea tu primer articulo</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($posts->hasPages())
    <div class="mt-6">{{ $posts->links() }}</div>
    @endif

</x-admin-layout>
