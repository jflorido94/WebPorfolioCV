<x-admin-layout>
    @php $title = 'Mi CV'; @endphp

    <div x-data="adminTabs('profile')" class="max-w-4xl">

        {{-- Tab navigation --}}
        <div class="flex gap-1 p-1 bg-light-surface dark:bg-dark-elevated rounded-2xl mb-8 overflow-x-auto">
            @foreach([
                ['key' => 'profile',    'label' => 'Perfil'],
                ['key' => 'experience', 'label' => 'Experiencia'],
                ['key' => 'education',  'label' => 'Educacion'],
                ['key' => 'skills',     'label' => 'Habilidades'],
            ] as $tab)
            <button @click="setActive('{{ $tab['key'] }}')"
                    :class="isActive('{{ $tab['key'] }}') ? 'bg-brand-purple text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100'"
                    class="flex-1 min-w-max px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 cursor-pointer whitespace-nowrap">
                {{ $tab['label'] }}
            </button>
            @endforeach
        </div>

        {{-- ── PERFIL ────────────────────────────────────────────────── --}}
        <div x-show="isActive('profile')" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            <form action="{{ route('admin.cv.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="card-glass rounded-2xl p-6 space-y-5">
                    <h2 class="font-heading font-bold text-sm uppercase tracking-widest text-slate-400">Informacion personal</h2>

                    <div>
                        <label class="input-label" for="title">Titulo profesional *</label>
                        <input type="text" id="title" name="title" value="{{ old('title', $user->profile?->title) }}" required class="input-field" placeholder="Ej: Desarrollador Web Fullstack">
                        @error('title') <p class="text-brand-coral text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="input-label" for="bio">Bio</label>
                        <textarea id="bio" name="bio" rows="4" class="input-field resize-none" placeholder="Breve descripcion profesional...">{{ old('bio', $user->profile?->bio) }}</textarea>
                        @error('bio') <p class="text-brand-coral text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="input-label" for="contact_email">Email de contacto publico</label>
                        <input type="email" id="contact_email" name="contact_email" value="{{ old('contact_email', $user->profile?->contact_email) }}" class="input-field" placeholder="tu@email.com">
                        @error('contact_email') <p class="text-brand-coral text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="input-label" for="location">Ubicacion</label>
                        <input type="text" id="location" name="location" value="{{ old('location', $user->profile?->location) }}" class="input-field" placeholder="Madrid, Spain">
                        @error('location') <p class="text-brand-coral text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>

                    {{-- Avatar --}}
                    <div x-data="{
                            removing: false,
                            savedUrl: @js($user->profile?->avatar_path ? asset('storage/' . $user->profile->avatar_path) : null),
                            previewUrl: @js($user->profile?->avatar_path ? asset('storage/' . $user->profile->avatar_path) : null),
                            selectFile(e) {
                                const file = e.target.files[0];
                                if (!file) { this.previewUrl = this.savedUrl; return; }
                                const reader = new FileReader();
                                reader.onload = ev => { this.previewUrl = ev.target.result; };
                                reader.readAsDataURL(file);
                            },
                            toggleRemove(checked) {
                                this.removing = checked;
                                this.previewUrl = checked ? null : this.savedUrl;
                            }
                         }" class="space-y-3">
                        <p class="input-label">Foto de avatar (CV)</p>

                        {{-- Preview: foto guardada o previsualización de la nueva selección --}}
                        <div x-show="previewUrl && !removing" class="flex items-center gap-4">
                            <img :src="previewUrl"
                                 class="w-16 h-16 rounded-xl object-cover ring-2 ring-brand-purple/30"
                                 alt="Vista previa avatar">
                            @if($user->profile?->avatar_path)
                            <label class="flex items-center gap-2 text-sm cursor-pointer" x-show="!removing">
                                <input type="checkbox" name="remove_avatar" value="1"
                                       @change="toggleRemove($event.target.checked)"
                                       class="rounded text-brand-coral focus:ring-brand-coral">
                                <span class="text-brand-coral">Eliminar foto actual</span>
                            </label>
                            @endif
                        </div>
                        <p x-show="removing" class="text-xs text-slate-500 italic">La foto se eliminara al guardar.</p>

                        {{-- Input de archivo (oculto cuando se va a borrar) --}}
                        <div x-show="!removing">
                            <input type="file" name="avatar" accept="image/*"
                                   @change="selectFile($event)"
                                   class="block w-full text-sm text-slate-500 dark:text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-brand-purple/10 file:text-brand-purple hover:file:bg-brand-purple/20 cursor-pointer">
                            <p class="text-xs text-slate-400 mt-1">JPG, PNG o WebP. Max 2 MB. Reemplaza las iniciales en el CV.</p>
                        </div>
                        @error('avatar') <p class="text-brand-coral text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="input-label" for="avatar_initials">Iniciales avatar (max 4, se usan si no hay foto)</label>
                        <input type="text" id="avatar_initials" name="avatar_initials" value="{{ old('avatar_initials', $user->profile?->avatar_initials) }}" maxlength="4" class="input-field" placeholder="JF">
                        @error('avatar_initials') <p class="text-brand-coral text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label class="input-label" for="github_url">GitHub URL</label>
                            <input type="url" id="github_url" name="github_url" value="{{ old('github_url', $user->profile?->github_url) }}" class="input-field" placeholder="https://github.com/usuario">
                            @error('github_url') <p class="text-brand-coral text-xs mt-1.5">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="input-label" for="linkedin_url">LinkedIn URL</label>
                            <input type="url" id="linkedin_url" name="linkedin_url" value="{{ old('linkedin_url', $user->profile?->linkedin_url) }}" class="input-field" placeholder="https://linkedin.com/in/usuario">
                            @error('linkedin_url') <p class="text-brand-coral text-xs mt-1.5">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="btn-primary">Guardar perfil</button>
                </div>
            </form>
        </div>

        {{-- ── EXPERIENCIA ───────────────────────────────────────────── --}}
        <div x-show="isActive('experience')" x-data="{ addOpen: false }" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">

            <div>
                <button @click="addOpen = !addOpen" class="btn-primary text-sm py-2.5 px-5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Agregar experiencia
                </button>
            </div>

            <div x-show="addOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <form action="{{ route('admin.cv.experience.store') }}" method="POST" class="card-glass rounded-2xl p-6 space-y-4">
                    @csrf
                    <h3 class="font-heading font-bold">Nueva experiencia</h3>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="input-label">Rol / Puesto *</label>
                            <input type="text" name="role" class="input-field" placeholder="Desarrollador Backend" required>
                        </div>
                        <div>
                            <label class="input-label">Empresa *</label>
                            <input type="text" name="company" class="input-field" placeholder="Acme Corp" required>
                        </div>
                        <div>
                            <label class="input-label">Ubicacion</label>
                            <input type="text" name="location" class="input-field" placeholder="Madrid, Spain">
                        </div>
                        <div>
                            <label class="input-label">Periodo *</label>
                            <input type="text" name="period" class="input-field" placeholder="2022 - actual" required>
                        </div>
                        <div>
                            <label class="input-label">Orden</label>
                            <input type="number" name="sort_order" class="input-field" placeholder="0" min="0">
                        </div>
                    </div>
                    <div>
                        <label class="input-label">Descripcion</label>
                        <textarea name="description" rows="3" class="input-field resize-none" placeholder="Descripcion del puesto y responsabilidades..."></textarea>
                    </div>
                    <div>
                        <label class="input-label">Competencias (separadas por comas)</label>
                        <input type="text" name="competencies" class="input-field" placeholder="Laravel, Docker, MySQL, Vue.js">
                    </div>
                    <div class="flex flex-wrap items-center gap-x-6 gap-y-2">
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="checkbox" name="show_in_web" value="1" checked class="rounded text-brand-purple focus:ring-brand-purple">
                            <span>Visible en web</span>
                        </label>
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="checkbox" name="show_in_pdf" value="1" checked class="rounded text-brand-teal focus:ring-brand-teal">
                            <span>Visible en PDF / impresion</span>
                        </label>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="addOpen = false" class="btn-secondary text-sm py-2 px-4">Cancelar</button>
                        <button type="submit" class="btn-primary text-sm py-2 px-4">Guardar</button>
                    </div>
                </form>
            </div>

            @forelse($user->experiences as $exp)
            <div class="card-glass rounded-2xl p-5" x-data="{ editOpen: false }">

                {{-- Vista normal --}}
                <div x-show="!editOpen">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <h3 class="font-heading font-bold">{{ $exp->role }}</h3>
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
                            <span class="badge badge-purple mt-1">{{ $exp->period }}</span>
                            @if($exp->description)
                            <p class="mt-2 text-sm text-slate-600 dark:text-slate-400 leading-relaxed">{{ $exp->description }}</p>
                            @endif
                            @if($exp->competencies->isNotEmpty())
                            <div class="flex flex-wrap gap-1.5 mt-3">
                                @foreach($exp->competencies as $comp)
                                <span class="badge badge-teal text-xs">{{ $comp->name }}</span>
                                @endforeach
                            </div>
                            @endif
                            @if(!$exp->show_in_web || !$exp->show_in_pdf)
                            <div class="flex gap-1 mt-2">
                                @if(!$exp->show_in_web)<span class="badge badge-coral text-xs">Oculto web</span>@endif
                                @if(!$exp->show_in_pdf)<span class="badge badge-amber text-xs">Oculto PDF</span>@endif
                            </div>
                            @endif
                        </div>
                        <div class="flex gap-1 shrink-0">
                            <button @click="editOpen = true" class="p-2 rounded-xl text-slate-400 hover:text-brand-purple hover:bg-brand-purple/10 transition-all cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <form action="{{ route('admin.cv.experience.destroy', $exp) }}" method="POST" x-data @submit.prevent="confirm('Eliminar esta experiencia?') && $el.submit()">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 rounded-xl text-slate-400 hover:text-brand-coral hover:bg-brand-coral/10 transition-all cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Formulario de edicion inline --}}
                <div x-show="editOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                    <form action="{{ route('admin.cv.experience.update', $exp) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <h3 class="font-heading font-bold text-sm">Editar experiencia</h3>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="input-label">Rol / Puesto *</label>
                                <input type="text" name="role" value="{{ $exp->role }}" class="input-field" required>
                            </div>
                            <div>
                                <label class="input-label">Empresa *</label>
                                <input type="text" name="company" value="{{ $exp->company }}" class="input-field" required>
                            </div>
                            <div>
                                <label class="input-label">Ubicacion</label>
                                <input type="text" name="location" value="{{ $exp->location }}" class="input-field" placeholder="Madrid, Spain">
                            </div>
                            <div>
                                <label class="input-label">Periodo *</label>
                                <input type="text" name="period" value="{{ $exp->period }}" class="input-field" required>
                            </div>
                            <div>
                                <label class="input-label">Orden</label>
                                <input type="number" name="sort_order" value="{{ $exp->sort_order }}" class="input-field" min="0">
                            </div>
                        </div>
                        <div>
                            <label class="input-label">Descripcion</label>
                            <textarea name="description" rows="3" class="input-field resize-none">{{ $exp->description }}</textarea>
                        </div>
                        <div>
                            <label class="input-label">Competencias (separadas por comas)</label>
                            <input type="text" name="competencies" value="{{ $exp->competencies->pluck('name')->implode(', ') }}" class="input-field" placeholder="Laravel, Docker, MySQL">
                        </div>
                        <div class="flex flex-wrap items-center gap-x-6 gap-y-2">
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="checkbox" name="show_in_web" value="1"
                                       {{ $exp->show_in_web ? 'checked' : '' }}
                                       class="rounded text-brand-purple focus:ring-brand-purple">
                                <span>Visible en web</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="checkbox" name="show_in_pdf" value="1"
                                       {{ $exp->show_in_pdf ? 'checked' : '' }}
                                       class="rounded text-brand-teal focus:ring-brand-teal">
                                <span>Visible en PDF / impresion</span>
                            </label>
                        </div>
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="editOpen = false" class="btn-secondary text-sm py-2 px-4">Cancelar</button>
                            <button type="submit" class="btn-primary text-sm py-2 px-4">Guardar cambios</button>
                        </div>
                    </form>
                </div>

            </div>
            @empty
            <div class="card-glass rounded-2xl p-12 text-center text-slate-400 dark:text-slate-500">
                <p class="font-semibold">No hay experiencias</p>
                <p class="text-xs mt-1">Agrega tu primer puesto de trabajo</p>
            </div>
            @endforelse
        </div>

        {{-- ── EDUCACION ─────────────────────────────────────────────── --}}
        <div x-show="isActive('education')" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-8">

            {{-- Formacion academica --}}
            <div x-data="{ addEduOpen: false }" class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-heading font-bold text-base">Formacion academica</h3>
                    <button @click="addEduOpen = !addEduOpen" class="btn-primary text-sm py-2 px-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        Agregar
                    </button>
                </div>

                <div x-show="addEduOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                    <form action="{{ route('admin.cv.education.store') }}" method="POST" class="card-glass rounded-2xl p-5 space-y-4">
                        @csrf
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="input-label">Titulo / Grado *</label>
                                <input type="text" name="title" class="input-field" placeholder="Grado en Informatica" required>
                            </div>
                            <div>
                                <label class="input-label">Centro *</label>
                                <input type="text" name="institution" class="input-field" placeholder="Universidad de Malaga" required>
                            </div>
                            <div>
                                <label class="input-label">Ubicacion</label>
                                <input type="text" name="location" class="input-field" placeholder="Malaga, Spain">
                            </div>
                            <div>
                                <label class="input-label">Año</label>
                                <input type="number" name="year" class="input-field" placeholder="2016" min="1900" max="2099">
                            </div>
                            <div>
                                <label class="input-label">Orden</label>
                                <input type="number" name="sort_order" class="input-field" placeholder="0" min="0">
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-x-6 gap-y-2">
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="checkbox" name="show_in_web" value="1" checked class="rounded text-brand-purple focus:ring-brand-purple">
                                <span>Visible en web</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="checkbox" name="show_in_pdf" value="1" checked class="rounded text-brand-teal focus:ring-brand-teal">
                                <span>Visible en PDF / impresion</span>
                            </label>
                        </div>
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="addEduOpen = false" class="btn-secondary text-sm py-2 px-4">Cancelar</button>
                            <button type="submit" class="btn-primary text-sm py-2 px-4">Guardar</button>
                        </div>
                    </form>
                </div>

                @forelse($user->education as $edu)
                <div class="card-glass rounded-2xl p-5" x-data="{ editOpen: false }">
                    <div x-show="!editOpen">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <h4 class="font-heading font-bold text-sm">{{ $edu->title }}</h4>
                                <p class="text-brand-amber dark:text-yellow-400 text-sm font-semibold">{{ $edu->institution }}</p>
                                @if($edu->location)
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $edu->location }}</p>
                                @endif
                                @if($edu->year)
                                <span class="badge badge-amber mt-1">{{ $edu->year }}</span>
                                @endif
                                @if(!$edu->show_in_web || !$edu->show_in_pdf)
                                <div class="flex gap-1 mt-2">
                                    @if(!$edu->show_in_web)<span class="badge badge-coral text-xs">Oculto web</span>@endif
                                    @if(!$edu->show_in_pdf)<span class="badge badge-amber text-xs">Oculto PDF</span>@endif
                                </div>
                                @endif
                            </div>
                            <div class="flex gap-1 shrink-0">
                                <button @click="editOpen = true" class="p-2 rounded-xl text-slate-400 hover:text-brand-purple hover:bg-brand-purple/10 transition-all cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <form action="{{ route('admin.cv.education.destroy', $edu) }}" method="POST" x-data @submit.prevent="confirm('Eliminar esta formacion?') && $el.submit()">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-xl text-slate-400 hover:text-brand-coral hover:bg-brand-coral/10 transition-all cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div x-show="editOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                        <form action="{{ route('admin.cv.education.update', $edu) }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')
                            <div class="grid sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="input-label">Titulo / Grado *</label>
                                    <input type="text" name="title" value="{{ $edu->title }}" class="input-field" required>
                                </div>
                                <div>
                                    <label class="input-label">Centro *</label>
                                    <input type="text" name="institution" value="{{ $edu->institution }}" class="input-field" required>
                                </div>
                                <div>
                                    <label class="input-label">Ubicacion</label>
                                    <input type="text" name="location" value="{{ $edu->location }}" class="input-field" placeholder="Malaga, Spain">
                                </div>
                                <div>
                                    <label class="input-label">Año</label>
                                    <input type="number" name="year" value="{{ $edu->year }}" class="input-field" min="1900" max="2099">
                                </div>
                                <div>
                                    <label class="input-label">Orden</label>
                                    <input type="number" name="sort_order" value="{{ $edu->sort_order }}" class="input-field" min="0">
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-x-6 gap-y-2">
                                <label class="flex items-center gap-2 text-sm cursor-pointer">
                                    <input type="checkbox" name="show_in_web" value="1"
                                           {{ $edu->show_in_web ? 'checked' : '' }}
                                           class="rounded text-brand-purple focus:ring-brand-purple">
                                    <span>Visible en web</span>
                                </label>
                                <label class="flex items-center gap-2 text-sm cursor-pointer">
                                    <input type="checkbox" name="show_in_pdf" value="1"
                                           {{ $edu->show_in_pdf ? 'checked' : '' }}
                                           class="rounded text-brand-teal focus:ring-brand-teal">
                                    <span>Visible en PDF / impresion</span>
                                </label>
                            </div>
                            <div class="flex justify-end gap-3">
                                <button type="button" @click="editOpen = false" class="btn-secondary text-sm py-2 px-4">Cancelar</button>
                                <button type="submit" class="btn-primary text-sm py-2 px-4">Guardar cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
                @empty
                <div class="card-glass rounded-2xl p-8 text-center text-slate-400 dark:text-slate-500">
                    <p class="text-sm">No hay formacion academica registrada</p>
                </div>
                @endforelse
            </div>

            <hr class="border-border dark:border-white/10">

            {{-- Cursos --}}
            @php
                $courses = $user->courses->where('type', 'course');
                $certifications = $user->courses->where('type', 'certification');
            @endphp

            <div x-data="{ addCourseOpen: false }" class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-heading font-bold text-base">Cursos</h3>
                    <button @click="addCourseOpen = !addCourseOpen" class="btn-primary text-sm py-2 px-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        Agregar
                    </button>
                </div>

                <div x-show="addCourseOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                    <form action="{{ route('admin.cv.course.store') }}" method="POST" class="card-glass rounded-2xl p-5 space-y-4">
                        @csrf
                        <input type="hidden" name="type" value="course">
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="input-label">Nombre del curso *</label>
                                <input type="text" name="title" class="input-field" placeholder="React - The Complete Guide" required>
                            </div>
                            <div>
                                <label class="input-label">Plataforma *</label>
                                <input type="text" name="institution" class="input-field" placeholder="Udemy" required>
                            </div>
                            <div>
                                <label class="input-label">Año</label>
                                <input type="number" name="year" class="input-field" placeholder="2024" min="1900" max="2099">
                            </div>
                            <div>
                                <label class="input-label">Orden</label>
                                <input type="number" name="sort_order" class="input-field" placeholder="0" min="0">
                            </div>
                        </div>
                        <div>
                            <label class="input-label">Enlace al curso (opcional)</label>
                            <input type="url" name="url" class="input-field" placeholder="https://udemy.com/course/...">
                        </div>
                        <div class="flex flex-wrap items-center gap-x-6 gap-y-2">
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="checkbox" name="show_in_web" value="1" checked class="rounded text-brand-purple focus:ring-brand-purple">
                                <span>Visible en web</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="checkbox" name="show_in_pdf" value="1" checked class="rounded text-brand-teal focus:ring-brand-teal">
                                <span>Visible en PDF / impresion</span>
                            </label>
                        </div>
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="addCourseOpen = false" class="btn-secondary text-sm py-2 px-4">Cancelar</button>
                            <button type="submit" class="btn-primary text-sm py-2 px-4">Guardar</button>
                        </div>
                    </form>
                </div>

                @forelse($courses as $course)
                <div class="card-glass rounded-2xl p-5" x-data="{ editOpen: false }">
                    <div x-show="!editOpen">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <h4 class="font-heading font-bold text-sm">{{ $course->title }}</h4>
                                <p class="text-brand-teal dark:text-teal-400 text-sm font-semibold">{{ $course->institution }}</p>
                                @if($course->year) <span class="badge badge-teal mt-1">{{ $course->year }}</span> @endif
                                @if($course->url)
                                <a href="{{ $course->url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 text-xs text-brand-blue hover:underline mt-1">
                                    Ver curso
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </a>
                                @endif
                                @if(!$course->show_in_web || !$course->show_in_pdf)
                                <div class="flex gap-1 mt-2">
                                    @if(!$course->show_in_web)<span class="badge badge-coral text-xs">Oculto web</span>@endif
                                    @if(!$course->show_in_pdf)<span class="badge badge-amber text-xs">Oculto PDF</span>@endif
                                </div>
                                @endif
                            </div>
                            <div class="flex gap-1 shrink-0">
                                <button @click="editOpen = true" class="p-2 rounded-xl text-slate-400 hover:text-brand-purple hover:bg-brand-purple/10 transition-all cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <form action="{{ route('admin.cv.course.destroy', $course) }}" method="POST" x-data @submit.prevent="confirm('Eliminar este curso?') && $el.submit()">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 rounded-xl text-slate-400 hover:text-brand-coral hover:bg-brand-coral/10 transition-all cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div x-show="editOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                        <form action="{{ route('admin.cv.course.update', $course) }}" method="POST" class="space-y-4">
                            @csrf @method('PUT')
                            <input type="hidden" name="type" value="course">
                            <div class="grid sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="input-label">Nombre del curso *</label>
                                    <input type="text" name="title" value="{{ $course->title }}" class="input-field" required>
                                </div>
                                <div>
                                    <label class="input-label">Plataforma *</label>
                                    <input type="text" name="institution" value="{{ $course->institution }}" class="input-field" required>
                                </div>
                                <div>
                                    <label class="input-label">Año</label>
                                    <input type="number" name="year" value="{{ $course->year }}" class="input-field" min="1900" max="2099">
                                </div>
                                <div>
                                    <label class="input-label">Orden</label>
                                    <input type="number" name="sort_order" value="{{ $course->sort_order }}" class="input-field" min="0">
                                </div>
                            </div>
                            <div>
                                <label class="input-label">Enlace al curso (opcional)</label>
                                <input type="url" name="url" value="{{ $course->url }}" class="input-field" placeholder="https://...">
                            </div>
                            <div class="flex flex-wrap items-center gap-x-6 gap-y-2">
                                <label class="flex items-center gap-2 text-sm cursor-pointer">
                                    <input type="checkbox" name="show_in_web" value="1"
                                           {{ $course->show_in_web ? 'checked' : '' }}
                                           class="rounded text-brand-purple focus:ring-brand-purple">
                                    <span>Visible en web</span>
                                </label>
                                <label class="flex items-center gap-2 text-sm cursor-pointer">
                                    <input type="checkbox" name="show_in_pdf" value="1"
                                           {{ $course->show_in_pdf ? 'checked' : '' }}
                                           class="rounded text-brand-teal focus:ring-brand-teal">
                                    <span>Visible en PDF / impresion</span>
                                </label>
                            </div>
                            <div class="flex justify-end gap-3">
                                <button type="button" @click="editOpen = false" class="btn-secondary text-sm py-2 px-4">Cancelar</button>
                                <button type="submit" class="btn-primary text-sm py-2 px-4">Guardar cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
                @empty
                <div class="card-glass rounded-2xl p-6 text-center text-slate-400 dark:text-slate-500 text-sm">
                    No hay cursos registrados
                </div>
                @endforelse
            </div>

            <hr class="border-border dark:border-white/10">

            {{-- Certificaciones --}}
            <div x-data="{ addCertOpen: false }" class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-heading font-bold text-base">Certificaciones</h3>
                    <button @click="addCertOpen = !addCertOpen" class="btn-primary text-sm py-2 px-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        Agregar
                    </button>
                </div>

                <div x-show="addCertOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                    <form action="{{ route('admin.cv.course.store') }}" method="POST" class="card-glass rounded-2xl p-5 space-y-4">
                        @csrf
                        <input type="hidden" name="type" value="certification">
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="input-label">Nombre de la certificacion *</label>
                                <input type="text" name="title" class="input-field" placeholder="AWS Certified Developer" required>
                            </div>
                            <div>
                                <label class="input-label">Organismo emisor *</label>
                                <input type="text" name="institution" class="input-field" placeholder="Amazon Web Services" required>
                            </div>
                            <div>
                                <label class="input-label">Año</label>
                                <input type="number" name="year" class="input-field" placeholder="2024" min="1900" max="2099">
                            </div>
                            <div>
                                <label class="input-label">Orden</label>
                                <input type="number" name="sort_order" class="input-field" placeholder="0" min="0">
                            </div>
                        </div>
                        <div>
                            <label class="input-label">URL del certificado (opcional)</label>
                            <input type="url" name="url" class="input-field" placeholder="https://aws.amazon.com/verification/...">
                        </div>
                        <div class="flex flex-wrap items-center gap-x-6 gap-y-2">
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="checkbox" name="show_in_web" value="1" checked class="rounded text-brand-purple focus:ring-brand-purple">
                                <span>Visible en web</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="checkbox" name="show_in_pdf" value="1" checked class="rounded text-brand-teal focus:ring-brand-teal">
                                <span>Visible en PDF / impresion</span>
                            </label>
                        </div>
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="addCertOpen = false" class="btn-secondary text-sm py-2 px-4">Cancelar</button>
                            <button type="submit" class="btn-primary text-sm py-2 px-4">Guardar</button>
                        </div>
                    </form>
                </div>

                @forelse($certifications as $cert)
                <div class="card-glass rounded-2xl p-5" x-data="{ editOpen: false }">
                    <div x-show="!editOpen">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <h4 class="font-heading font-bold text-sm">{{ $cert->title }}</h4>
                                <p class="text-brand-blue dark:text-blue-400 text-sm font-semibold">{{ $cert->institution }}</p>
                                @if($cert->year) <span class="badge badge-blue mt-1">{{ $cert->year }}</span> @endif
                                @if($cert->url)
                                <a href="{{ $cert->url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 text-xs text-brand-blue hover:underline mt-1">
                                    Ver certificado
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </a>
                                @endif
                                @if(!$cert->show_in_web || !$cert->show_in_pdf)
                                <div class="flex gap-1 mt-2">
                                    @if(!$cert->show_in_web)<span class="badge badge-coral text-xs">Oculto web</span>@endif
                                    @if(!$cert->show_in_pdf)<span class="badge badge-amber text-xs">Oculto PDF</span>@endif
                                </div>
                                @endif
                            </div>
                            <div class="flex gap-1 shrink-0">
                                <button @click="editOpen = true" class="p-2 rounded-xl text-slate-400 hover:text-brand-purple hover:bg-brand-purple/10 transition-all cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <form action="{{ route('admin.cv.course.destroy', $cert) }}" method="POST" x-data @submit.prevent="confirm('Eliminar esta certificacion?') && $el.submit()">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 rounded-xl text-slate-400 hover:text-brand-coral hover:bg-brand-coral/10 transition-all cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div x-show="editOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                        <form action="{{ route('admin.cv.course.update', $cert) }}" method="POST" class="space-y-4">
                            @csrf @method('PUT')
                            <input type="hidden" name="type" value="certification">
                            <div class="grid sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="input-label">Nombre de la certificacion *</label>
                                    <input type="text" name="title" value="{{ $cert->title }}" class="input-field" required>
                                </div>
                                <div>
                                    <label class="input-label">Organismo emisor *</label>
                                    <input type="text" name="institution" value="{{ $cert->institution }}" class="input-field" required>
                                </div>
                                <div>
                                    <label class="input-label">Año</label>
                                    <input type="number" name="year" value="{{ $cert->year }}" class="input-field" min="1900" max="2099">
                                </div>
                                <div>
                                    <label class="input-label">Orden</label>
                                    <input type="number" name="sort_order" value="{{ $cert->sort_order }}" class="input-field" min="0">
                                </div>
                            </div>
                            <div>
                                <label class="input-label">URL del certificado (opcional)</label>
                                <input type="url" name="url" value="{{ $cert->url }}" class="input-field" placeholder="https://...">
                            </div>
                            <div class="flex flex-wrap items-center gap-x-6 gap-y-2">
                                <label class="flex items-center gap-2 text-sm cursor-pointer">
                                    <input type="checkbox" name="show_in_web" value="1"
                                           {{ $cert->show_in_web ? 'checked' : '' }}
                                           class="rounded text-brand-purple focus:ring-brand-purple">
                                    <span>Visible en web</span>
                                </label>
                                <label class="flex items-center gap-2 text-sm cursor-pointer">
                                    <input type="checkbox" name="show_in_pdf" value="1"
                                           {{ $cert->show_in_pdf ? 'checked' : '' }}
                                           class="rounded text-brand-teal focus:ring-brand-teal">
                                    <span>Visible en PDF / impresion</span>
                                </label>
                            </div>
                            <div class="flex justify-end gap-3">
                                <button type="button" @click="editOpen = false" class="btn-secondary text-sm py-2 px-4">Cancelar</button>
                                <button type="submit" class="btn-primary text-sm py-2 px-4">Guardar cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
                @empty
                <div class="card-glass rounded-2xl p-6 text-center text-slate-400 dark:text-slate-500 text-sm">
                    No hay certificaciones registradas
                </div>
                @endforelse
            </div>

        </div>

        {{-- ── HABILIDADES ───────────────────────────────────────────── --}}
        <div x-show="isActive('skills')" x-data="{ addOpen: false }" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">

            <div>
                <button @click="addOpen = !addOpen" class="btn-primary text-sm py-2.5 px-5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Agregar habilidad
                </button>
            </div>

            <div x-show="addOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <form action="{{ route('admin.cv.skill.store') }}" method="POST" class="card-glass rounded-2xl p-5 space-y-4">
                    @csrf
                    <div class="grid sm:grid-cols-3 gap-4">
                        <div class="sm:col-span-2">
                            <label class="input-label">Nombre *</label>
                            <input type="text" name="name" class="input-field" placeholder="Laravel" required maxlength="60">
                        </div>
                        <div>
                            <label class="input-label">Orden</label>
                            <input type="number" name="sort_order" class="input-field" placeholder="0" min="0">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="input-label">Categoria *</label>
                            <input type="text" name="category" class="input-field" placeholder="Backend" required maxlength="40">
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-x-6 gap-y-2">
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="checkbox" name="show_in_web" value="1" checked class="rounded text-brand-purple focus:ring-brand-purple">
                            <span>Visible en web</span>
                        </label>
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="checkbox" name="show_in_pdf" value="1" checked class="rounded text-brand-teal focus:ring-brand-teal">
                            <span>Visible en PDF / impresion</span>
                        </label>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="addOpen = false" class="btn-secondary text-sm py-2 px-4">Cancelar</button>
                        <button type="submit" class="btn-primary text-sm py-2 px-4">Guardar</button>
                    </div>
                </form>
            </div>

            @php
                $skillsByCategory = $user->skills->groupBy('category');
                $paletteSkill = ['purple', 'teal', 'coral', 'pink', 'amber', 'blue'];
                $catIndex = 0;
            @endphp

            @forelse($skillsByCategory as $category => $categorySkills)
            @php $color = $paletteSkill[$catIndex % count($paletteSkill)]; $catIndex++; @endphp
            <div class="space-y-3">
                <h3 class="font-heading font-bold text-sm uppercase tracking-widest text-slate-400">{{ $category }}</h3>
                <div class="space-y-2">
                    @foreach($categorySkills as $skill)
                    <div class="card-glass rounded-xl px-4 py-3" x-data="{ editOpen: false }">
                        <div x-show="!editOpen" class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="badge badge-{{ $color }} text-sm">{{ $skill->name }}</span>
                                @if(!$skill->show_in_web || !$skill->show_in_pdf)
                                    @if(!$skill->show_in_web)<span class="badge badge-coral text-xs">Oculto web</span>@endif
                                    @if(!$skill->show_in_pdf)<span class="badge badge-amber text-xs">Oculto PDF</span>@endif
                                @endif
                            </div>
                            <div class="flex gap-1 shrink-0">
                                <button @click="editOpen = true" class="p-1.5 rounded-lg text-slate-400 hover:text-brand-purple hover:bg-brand-purple/10 transition-all cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <form action="{{ route('admin.cv.skill.destroy', $skill) }}" method="POST" x-data @submit.prevent="confirm('Eliminar esta habilidad?') && $el.submit()">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-brand-coral hover:bg-brand-coral/10 transition-all cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div x-show="editOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                            <form action="{{ route('admin.cv.skill.update', $skill) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="grid sm:grid-cols-3 gap-3 mb-3">
                                    <div class="sm:col-span-1">
                                        <input type="text" name="name" value="{{ $skill->name }}" class="input-field text-sm" required maxlength="60" placeholder="Nombre">
                                    </div>
                                    <div class="sm:col-span-1">
                                        <input type="text" name="category" value="{{ $skill->category }}" class="input-field text-sm" required maxlength="40" placeholder="Categoria">
                                    </div>
                                    <div>
                                        <input type="number" name="sort_order" value="{{ $skill->sort_order }}" class="input-field text-sm" min="0" placeholder="Orden">
                                    </div>
                                </div>
                                <div class="flex flex-wrap items-center gap-x-6 gap-y-2 mb-3">
                                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                                        <input type="checkbox" name="show_in_web" value="1"
                                               {{ $skill->show_in_web ? 'checked' : '' }}
                                               class="rounded text-brand-purple focus:ring-brand-purple">
                                        <span>Visible en web</span>
                                    </label>
                                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                                        <input type="checkbox" name="show_in_pdf" value="1"
                                               {{ $skill->show_in_pdf ? 'checked' : '' }}
                                               class="rounded text-brand-teal focus:ring-brand-teal">
                                        <span>Visible en PDF / impresion</span>
                                    </label>
                                </div>
                                <div class="flex justify-end gap-2">
                                    <button type="button" @click="editOpen = false" class="btn-secondary text-xs py-1.5 px-3">Cancelar</button>
                                    <button type="submit" class="btn-primary text-xs py-1.5 px-3">Guardar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @empty
            <div class="card-glass rounded-2xl p-12 text-center text-slate-400 dark:text-slate-500">
                <p class="font-semibold">No hay habilidades</p>
                <p class="text-xs mt-1">Agrega tus tecnologias y competencias</p>
            </div>
            @endforelse

        </div>

    </div>
</x-admin-layout>
