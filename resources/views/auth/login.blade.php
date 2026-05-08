<x-guest-layout>

    {{-- Cabecera --}}
    <div class="mb-8 text-center">
        <h1 class="font-heading text-2xl font-bold text-slate-900 dark:text-white mb-1">
            Acceso al <span class="gradient-text">panel</span>
        </h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">
            Solo para el propietario del portfolio
        </p>
    </div>

    {{-- Estado de sesión (ej. enlace de reseteo enviado) --}}
    @if (session('status'))
        <div class="mb-6 px-4 py-3 rounded-xl bg-brand-teal/15 border border-brand-teal/30 text-brand-teal dark:text-teal-300 text-sm font-medium">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5" x-data="{ loading: false }" @submit="loading = true">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="input-label">
                Correo electrónico
            </label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
                class="input-field @error('email') border-red-500 focus:ring-red-500/50 focus:border-red-500 @enderror"
                placeholder="admin@portfolio.local"
            />
            @error('email')
                <p class="mt-1.5 text-sm text-red-500 dark:text-red-400 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Contraseña --}}
        <div x-data="{ show: false }">
            <label for="password" class="input-label">
                Contraseña
            </label>
            <div class="relative">
                <input
                    id="password"
                    :type="show ? 'text' : 'password'"
                    name="password"
                    required
                    autocomplete="current-password"
                    class="input-field pr-11 @error('password') border-red-500 focus:ring-red-500/50 focus:border-red-500 @enderror"
                    placeholder="••••••••"
                />
                <button
                    type="button"
                    @click="show = !show"
                    class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-brand-purple dark:hover:text-purple-400 transition-colors duration-200"
                    :aria-label="show ? 'Ocultar contraseña' : 'Mostrar contraseña'"
                >
                    <template x-if="!show">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </template>
                    <template x-if="show">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21"/>
                        </svg>
                    </template>
                </button>
            </div>
            @error('password')
                <p class="mt-1.5 text-sm text-red-500 dark:text-red-400 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Recuérdame + olvidaste contraseña --}}
        <div class="flex items-center justify-between">
            <label for="remember_me" class="flex items-center gap-2 cursor-pointer select-none">
                <input
                    id="remember_me"
                    type="checkbox"
                    name="remember"
                    class="w-4 h-4 rounded border-light-border dark:border-dark-border bg-light-surface dark:bg-dark-elevated text-brand-purple focus:ring-brand-purple/50 focus:ring-2 transition-colors duration-200"
                />
                <span class="text-sm text-slate-600 dark:text-slate-400">Recuérdame</span>
            </label>

            @if (Route::has('password.request'))
                <a
                    href="{{ route('password.request') }}"
                    class="text-sm text-brand-purple dark:text-purple-400 hover:underline transition-colors duration-200"
                >
                    ¿Olvidaste la contraseña?
                </a>
            @endif
        </div>

        {{-- Botón submit --}}
        <button
            type="submit"
            class="btn-primary w-full justify-center mt-2"
        >
            <template x-if="loading">
                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                </svg>
            </template>
            <template x-if="!loading">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
            </template>
            <span x-text="loading ? 'Accediendo...' : 'Acceder al panel'"></span>
        </button>
    </form>

</x-guest-layout>
