<x-admin-layout>
    @php $title = 'Configuracion'; @endphp

    <div class="max-w-2xl space-y-8">

        <h1 class="font-heading text-2xl font-black">Configuracion de cuenta</h1>

        {{-- Cambiar email --}}
        <div class="card-glass rounded-2xl p-6 space-y-5">
            <h2 class="font-heading font-bold text-sm uppercase tracking-widest text-slate-400">Email de acceso</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">Email actual: <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $user->email }}</span></p>

            <form action="{{ route('admin.settings.email.update') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="input-label" for="email">Nuevo email *</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required class="input-field" placeholder="nuevo@email.com">
                    @error('email') <p class="text-brand-coral text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="btn-primary">Actualizar email</button>
                </div>
            </form>
        </div>

        {{-- Cambiar contraseña --}}
        <div class="card-glass rounded-2xl p-6 space-y-5">
            <h2 class="font-heading font-bold text-sm uppercase tracking-widest text-slate-400">Contraseña</h2>

            <form action="{{ route('admin.settings.password.update') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="input-label" for="current_password">Contraseña actual *</label>
                    <input type="password" id="current_password" name="current_password" required class="input-field" autocomplete="current-password">
                    @error('current_password') <p class="text-brand-coral text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="input-label" for="password">Nueva contraseña *</label>
                    <input type="password" id="password" name="password" required class="input-field" autocomplete="new-password" minlength="8">
                    @error('password') <p class="text-brand-coral text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="input-label" for="password_confirmation">Confirmar nueva contraseña *</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required class="input-field" autocomplete="new-password">
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="btn-primary">Cambiar contraseña</button>
                </div>
            </form>
        </div>

        {{-- Cerrar sesion --}}
        <div class="card-glass rounded-2xl p-6 space-y-5">
            <h2 class="font-heading font-bold text-sm uppercase tracking-widest text-slate-400">Sesion</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">Cierra la sesion activa en este dispositivo.</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold bg-brand-coral/10 text-brand-coral hover:bg-brand-coral hover:text-white transition-all duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Cerrar sesion
                </button>
            </form>
        </div>

    </div>
</x-admin-layout>
