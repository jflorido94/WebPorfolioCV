<x-admin-layout>
    @php $title = 'Visitas'; @endphp

    <div class="flex items-center justify-between mb-8">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-1">Estadisticas</p>
            <h1 class="font-heading text-2xl font-black">Visitas al portfolio</h1>
        </div>
        <div class="text-right">
            <p class="text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-1">Total</p>
            <p class="font-heading text-2xl font-black">{{ $metrics->total() }}</p>
        </div>
    </div>

    <div class="card-glass rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-light-border dark:border-dark-border">
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500">Pagina</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500 hidden sm:table-cell">IP</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500 hidden md:table-cell">Ubicacion</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500 hidden xl:table-cell">ISP</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500 hidden lg:table-cell">Indicadores</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500">Fecha</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-light-border dark:divide-dark-border">
                @forelse($metrics as $metric)
                <tr class="hover:bg-slate-50 dark:hover:bg-dark-elevated transition-colors">
                    <td class="px-6 py-4">
                        @if($metric->page === 'home')
                            <span class="badge badge-purple text-xs">Inicio</span>
                        @else
                            <span class="badge badge-teal text-xs">CV</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 font-mono text-xs text-slate-500 dark:text-slate-400 hidden sm:table-cell">
                        {{ $metric->ip }}
                    </td>
                    <td class="px-6 py-4 hidden md:table-cell">
                        @if($metric->city || $metric->country)
                            <span class="font-medium">{{ implode(', ', array_filter([$metric->city, $metric->state, $metric->country])) }}</span>
                            @if($metric->zipcode)
                                <span class="text-xs text-slate-400 dark:text-slate-500 ml-1">({{ $metric->zipcode }})</span>
                            @endif
                            @if($metric->latitude && $metric->longitude)
                                <span class="block text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                                    {{ number_format((float) $metric->latitude, 4) }}, {{ number_format((float) $metric->longitude, 4) }}
                                </span>
                            @endif
                        @else
                            <span class="text-slate-400 dark:text-slate-500">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 hidden xl:table-cell text-xs text-slate-500 dark:text-slate-400 max-w-48">
                        <span class="line-clamp-1">{{ $metric->isp ?? '—' }}</span>
                    </td>
                    <td class="px-6 py-4 hidden lg:table-cell">
                        <div class="flex flex-wrap gap-1">
                            @if($metric->is_vpn)
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">VPN</span>
                            @endif
                            @if($metric->is_tor)
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">TOR</span>
                            @endif
                            @if($metric->is_proxy)
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400">PROXY</span>
                            @endif
                            @if($metric->is_datacenter)
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">DC</span>
                            @endif
                            @if($metric->is_mobile)
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold bg-teal-100 text-teal-700 dark:bg-teal-900/30 dark:text-teal-400">MOB</span>
                            @endif
                            @if(! $metric->is_vpn && ! $metric->is_tor && ! $metric->is_proxy && ! $metric->is_datacenter && ! $metric->is_mobile)
                                <span class="text-slate-300 dark:text-slate-600">—</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 text-xs text-slate-400 dark:text-slate-500 whitespace-nowrap">
                        {{ $metric->created_at->format('d M Y, H:i') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center text-slate-400 dark:text-slate-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mx-auto mb-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <p class="font-semibold">Sin visitas registradas</p>
                        <p class="text-xs mt-1">Las visitas aparecen aqui automaticamente</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($metrics->hasPages())
    <div class="mt-6">{{ $metrics->links() }}</div>
    @endif

</x-admin-layout>
