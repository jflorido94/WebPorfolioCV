<x-admin-layout>
    @php $title = 'Editar post'; @endphp

    <div class="max-w-4xl">
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('admin.posts.index') }}" class="p-2 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-dark-elevated transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h1 class="font-heading text-2xl font-black">Editar post</h1>
        </div>

        <form action="{{ route('admin.posts.update', $post) }}" method="POST"
              x-data="{
                  content: @js(old('content', $post->content)),
                  preview: '',
                  showPreview: false,
                  loading: false,
                  renderPreview() {
                      this.preview = window.marked ? marked.parse(this.content) : this.content;
                  }
              }">
            @csrf
            @method('PUT')
            <div class="space-y-6">

                <div class="card-glass rounded-2xl p-6">
                    <label class="input-label" for="title">Titulo *</label>
                    <input type="text" id="title" name="title" value="{{ old('title', $post->title) }}" required
                           class="input-field text-lg font-heading font-bold" placeholder="El titulo del articulo">
                    @error('title') <p class="text-brand-coral text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div class="grid md:grid-cols-3 gap-6">
                    <div class="md:col-span-2 card-glass rounded-2xl p-6">
                        <label class="input-label" for="summary">Resumen</label>
                        <textarea id="summary" name="summary" rows="3" class="input-field resize-none" placeholder="Breve descripcion">{{ old('summary', $post->summary) }}</textarea>
                        @error('summary') <p class="text-brand-coral text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>
                    <div class="card-glass rounded-2xl p-6">
                        <label class="input-label" for="category">Categoria *</label>
                        <input type="text" id="category" name="category" value="{{ old('category', $post->category) }}" required maxlength="60" class="input-field">
                        @error('category') <p class="text-brand-coral text-xs mt-1.5">{{ $message }}</p> @enderror

                        <label class="input-label mt-4" for="tags">Tags</label>
                        <input type="text" id="tags" name="tags" value="{{ old('tags', implode(', ', $post->tag_list)) }}" class="input-field" placeholder="php, laravel, docker">
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Separados por comas</p>
                    </div>
                </div>

                <div class="card-glass rounded-2xl overflow-hidden">
                    <div class="flex items-center justify-between px-6 py-3 border-b border-light-border dark:border-dark-border">
                        <label class="input-label mb-0">Contenido (Markdown) *</label>
                        <button type="button" @click="showPreview = !showPreview; if(showPreview) renderPreview()"
                                class="text-xs font-semibold text-brand-purple hover:text-brand-pink transition-colors cursor-pointer"
                                x-text="showPreview ? 'Editar' : 'Preview'">
                        </button>
                    </div>
                    <div x-show="!showPreview" class="p-6">
                        <textarea name="content" rows="16" required
                                  class="input-field font-mono text-sm resize-y"
                                  x-model="content">{{ old('content', $post->content) }}</textarea>
                        @error('content') <p class="text-brand-coral text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>
                    <div x-show="showPreview" class="p-6 prose-portfolio min-h-48" x-html="preview"></div>
                </div>

                <div class="card-glass rounded-2xl p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <label class="flex items-center gap-3 cursor-pointer select-none">
                        <div class="relative">
                            <input type="checkbox" name="publish" value="1" {{ old('publish', $post->published) ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-10 h-6 bg-slate-200 dark:bg-dark-elevated rounded-full peer-checked:bg-brand-purple transition-colors"></div>
                            <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-4"></div>
                        </div>
                        <span class="text-sm font-semibold">{{ $post->published ? 'Publicado' : 'Publicar ahora' }}</span>
                    </label>
                    <div class="flex gap-3">
                        <a href="{{ route('admin.posts.index') }}" class="btn-secondary text-sm py-2.5 px-5">Cancelar</a>
                        <button type="submit" class="btn-primary text-sm py-2.5 px-5" :disabled="loading" @click="loading=true">
                            <svg x-show="loading" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span>Guardar cambios</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

</x-admin-layout>
