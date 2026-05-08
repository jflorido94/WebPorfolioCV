# Guía del desarrollador — Portfolio CV

> Referencia completa para mantener, corregir y ampliar este proyecto.  
> Stack: Laravel 11 · PHP 8.3 · MariaDB 11 · Docker · Blade + Tailwind CSS

---

## Indice

1. [Cómo levantar el proyecto](#1-cómo-levantar-el-proyecto)
2. [Mapa del proyecto](#2-mapa-del-proyecto)
3. [Cómo funciona una petición HTTP](#3-cómo-funciona-una-petición-http)
4. [Base de datos y modelos](#4-base-de-datos-y-modelos)
5. [Sistema de autenticación y roles](#5-sistema-de-autenticación-y-roles)
6. [Servicios externos (Markdown y PDF)](#6-servicios-externos-markdown-y-pdf)
7. [Flujo completo de una funcionalidad](#7-flujo-completo-de-una-funcionalidad)
8. [Tareas habituales de mantenimiento](#8-tareas-habituales-de-mantenimiento)
9. [Cómo añadir una feature nueva](#9-cómo-añadir-una-feature-nueva)
10. [Tests](#10-tests)
11. [Sistema de diseño](#11-sistema-de-diseño)
12. [Reglas que no se rompen](#12-reglas-que-no-se-rompen)
13. [Dónde está cada cosa](#13-dónde-está-cada-cosa)

---

## 1. Cómo levantar el proyecto

```bash
# Primera vez
cp .env.example .env
# Edita .env: cambia DB_PASSWORD, DB_ROOT_PASSWORD y APP_KEY

docker compose up --build -d
docker compose exec app php artisan migrate:fresh --seed

# Acceso
# Web:   http://localhost:8080
# Admin: http://localhost:8080/login  →  admin@portfolio.local / password
# phpMyAdmin (opcional):
docker compose --profile tools up -d  # http://localhost:8081
```

**Comandos del día a día:**

```bash
docker compose up -d                          # levantar sin reconstruir
docker compose down                           # apagar
docker compose exec app php artisan tinker    # REPL de Laravel
docker compose exec app php artisan test      # ejecutar todos los tests
docker compose logs -f app                    # ver logs en tiempo real
docker compose exec app bash                  # entrar al contenedor
```

---

## 2. Mapa del proyecto

```
WebPorfolioCV/
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Public/          ← rutas públicas (/  /cv  /blog)
│   │   │   │   ├── HomeController.php
│   │   │   │   ├── CvController.php
│   │   │   │   └── BlogController.php
│   │   │   ├── Admin/           ← rutas protegidas (/admin/*)
│   │   │   │   ├── PostController.php
│   │   │   │   └── CvController.php
│   │   │   ├── SitemapController.php
│   │   │   └── Concerns/
│   │   │       └── AuthorizesOwnership.php  ← trait reutilizable
│   │   ├── Middleware/
│   │   │   └── EnsureIsAdmin.php   ← bloquea si no es admin
│   │   └── Requests/Admin/          ← validación de formularios
│   │       ├── StorePostRequest.php
│   │       ├── UpdatePostRequest.php
│   │       ├── UpdateProfileRequest.php
│   │       └── StoreExperienceRequest.php
│   ├── Models/
│   │   ├── User.php         ← usuario + relaciones CV + scope withCv()
│   │   ├── Profile.php
│   │   ├── Experience.php
│   │   ├── Education.php
│   │   ├── Skill.php
│   │   ├── Post.php         ← auto-slug, scope published(), tag_list
│   │   └── PostTag.php
│   ├── Services/
│   │   ├── MarkdownService.php  ← convierte Markdown a HTML seguro
│   │   └── PdfService.php       ← genera PDF del CV con DomPDF
│   └── Providers/
│       └── AppServiceProvider.php  ← registra MarkdownService como singleton
│
├── resources/views/
│   ├── layouts/
│   │   ├── app.blade.php      ← layout público (nav + Tailwind)
│   │   └── admin.blade.php    ← layout admin (sidebar + nav)
│   ├── public/
│   │   ├── home.blade.php
│   │   ├── cv.blade.php
│   │   ├── cv-print.blade.php  ← versión limpia para PDF/impresión
│   │   └── blog/
│   │       ├── index.blade.php
│   │       └── show.blade.php
│   ├── admin/
│   │   ├── posts/
│   │   │   ├── index.blade.php
│   │   │   ├── create.blade.php
│   │   │   └── edit.blade.php
│   │   └── cv/
│   │       └── index.blade.php
│   └── components/           ← componentes Breeze reutilizables
│
├── database/
│   ├── migrations/           ← 10 migraciones del proyecto
│   ├── factories/
│   └── seeders/
│       └── AdminSeeder.php   ← crea admin + datos de ejemplo
│
├── routes/
│   ├── web.php               ← TODAS las rutas están aquí
│   └── auth.php              ← rutas de Breeze (login, register...)
│
├── tests/
│   ├── Unit/PostTest.php
│   └── Feature/
│       ├── PublicBlogTest.php
│       ├── AdminPostTest.php
│       └── AuthTest.php
│
├── docker-compose.yml        ← único archivo Docker
├── Dockerfile                ← imagen multistage (composer → php-fpm)
├── .env.example              ← plantilla de variables (sí va a git)
└── .env                      ← variables reales (NO va a git)
```

---

## 3. Cómo funciona una petición HTTP

Ejemplo: el visitante entra en `/blog/mi-post-slug`

```
Navegador: GET /blog/mi-post-slug
    │
    ▼
Nginx (docker: puerto 8080)
    │  fastcgi_pass app:9000
    ▼
PHP-FPM → public/index.php
    │
    ▼
routes/web.php
    │  Route::get('/blog/{slug}', [BlogController::class, 'show'])
    ▼
(no hay middleware aquí, es ruta pública)
    │
    ▼
BlogController::show(string $slug, MarkdownService $markdown)
    │  Post::published()->where('slug', $slug)->firstOrFail()
    │  $post->content_html = $markdown->toHtml($post->content)
    ▼
Vista: resources/views/public/blog/show.blade.php
    │  @extends('layouts.app')
    ▼
Respuesta HTML → Nginx → Navegador
```

Ejemplo con ruta protegida: `POST /admin/posts` (crear un post)

```
Navegador: POST /admin/posts  (formulario)
    │
    ▼
routes/web.php
    │  Route::middleware(['auth', 'verified'])->group(...)
    │      Route::middleware('admin')->group(...)
    │          Route::resource('admin/posts', PostController::class)
    ▼
Middleware: auth        → ¿está autenticado? Si no → redirige a /login
Middleware: verified    → ¿email verificado? (Breeze, normalmente pasa)
Middleware: admin       → ¿$user->isAdmin()? Si no → abort(403)
    │
    ▼
StorePostRequest (Form Request)
    │  authorize() → verifica is_admin de nuevo
    │  rules()     → valida title, content, category...
    │  Si falla → redirige con errores en sesión
    ▼
PostController::store(StorePostRequest $request)
    │  DB::transaction(...)
    │      Auth::user()->posts()->create([...])
    │      $this->syncTags($post, $request->input('tags'))
    ▼
redirect()->route('admin.posts.index')->with('success', '...')
```

---

## 4. Base de datos y modelos

### Esquema de relaciones

```
users
  │ id, name, email, password, is_admin
  │
  ├─── profiles (1:1)
  │      user_id, title, bio, location, github_url, linkedin_url, avatar_initials
  │
  ├─── experiences (1:N)  ordenado por sort_order
  │      user_id, role, company, period, description, started_at, ended_at, sort_order
  │
  ├─── education (1:N)  ordenado por sort_order
  │      user_id, title, institution, year, sort_order
  │
  ├─── skills (1:N)  ordenado por sort_order
  │      user_id, name, category, sort_order
  │
  └─── posts (1:N)
         user_id, title, slug, summary, content(Markdown), category,
         published, published_at
           │
           └─── post_tags (1:N)
                  post_id, tag
```

### Características especiales de los modelos

**User** (`app/Models/User.php`)
- `User::CV_RELATIONS` — constante con las relaciones del CV para eager loading
- `scopeWithCv()` — carga perfil, experiencias, educación y skills de golpe
- `getYearsOfExperienceAttribute()` — calcula años de experiencia desde `started_at` más antigua
- `isAdmin(): bool` — comprueba el campo `is_admin`

```php
// Cómo se usa en los controllers:
$user = User::query()->withCv()->first();          // público
$user = User::query()->withCv()->whereKey(Auth::id())->firstOrFail(); // admin
```

**Post** (`app/Models/Post.php`)
- El `slug` se genera automáticamente desde `title` al crear (boot/creating)
- `scopePublished()` — filtra publicados con `published=true` y `published_at` no nulo, ordenados por fecha
- `scopeLatestPublished(int $limit)` — los N más recientes publicados
- `getTagListAttribute(): array` — devuelve tags como array de strings

```php
// Uso habitual:
Post::published()->paginate(10);          // blog público
Post::query()->latestPublished(3)->get(); // últimos 3 para la home
```

**Modelos de CV** (Experience, Education, Skill)
- Todos tienen scope `ordered()` que ordena por `sort_order ASC`
- El `sort_order` determina el orden de aparición en la página

---

## 5. Sistema de autenticación y roles

### Cómo funciona

Laravel Breeze gestiona login/logout/registro. El proyecto añade un campo `is_admin` a la tabla `users`.

```
¿Está autenticado?  →  middleware 'auth'   (Laravel built-in)
¿Es admin?          →  middleware 'admin'  (EnsureIsAdmin.php)
```

`EnsureIsAdmin.php` hace exactamente esto:
```php
if (! $request->user()?->isAdmin()) {
    abort(403);
}
```

El alias `'admin'` se registra en `bootstrap/app.php`:
```php
$middleware->alias(['admin' => EnsureIsAdmin::class]);
```

### Regla importante: doble verificación

Las rutas admin tienen **dos capas** de protección:
1. El middleware `admin` en la ruta
2. `authorize()` en cada Form Request: `return auth()->check() && auth()->user()->isAdmin()`

Si alguien pasara el middleware (no debería), el Form Request lo detiene igualmente.

### Verificar propiedad de un recurso

Cuando un admin intenta editar/eliminar un post o experiencia que no es suyo:

```php
// El trait AuthorizesOwnership hace esto en una línea:
$this->authorizeOwnership($post); // compara $post->user_id con Auth::id()
// Si no coincide → abort(403)
```

Los controllers que lo usan: `Admin\PostController` y `Admin\CvController`.

---

## 6. Servicios externos (Markdown y PDF)

### MarkdownService

Convierte Markdown en HTML. Está registrado como singleton en `AppServiceProvider` para no reinstanciar el conversor en cada petición.

```php
// Se inyecta automáticamente en el controller:
public function show(string $slug, MarkdownService $markdown): View
{
    $post->content_html = $markdown->toHtml($post->content);
    return view('public.blog.show', compact('post'));
}
```

El HTML que genera tiene `html_input => 'escape'` para evitar XSS: cualquier `<script>` en el contenido se escapa, no se ejecuta.

### PdfService

Usa `barryvdh/laravel-dompdf`. Renderiza la vista `public.cv-print` a HTML y la convierte a PDF A4.

```php
// Uso en CvController:
public function downloadPdf(PdfService $pdfService): Response
{
    return $pdfService->generateCvPdf($this->loadCvUser());
}
```

La vista `cv-print.blade.php` tiene estilos `@media print` optimizados para A4. Si cambias el diseño del CV, esa vista es la que afecta al PDF descargable.

---

## 7. Flujo completo de una funcionalidad

### Ejemplo: crear un post desde el admin

```
1. Usuario rellena el formulario en /admin/posts/create
   (vista: resources/views/admin/posts/create.blade.php)

2. POST /admin/posts  →  StorePostRequest valida los campos:
   - title: required, max 255
   - content: required
   - category: required, max 60
   - summary: nullable
   - tags: nullable string ("php, laravel, docker")
   - publish: nullable boolean (checkbox)

3. PostController::store() ejecuta en transacción:
   a. Crea el post con Auth::user()->posts()->create([...])
      - published=true si el checkbox "publish" está marcado
      - published_at=now() si se publica ahora
      - El slug se genera solo desde el título (boot en Post)
   b. syncTags() borra los tags existentes y los recrea

4. Redirige a /admin/posts con mensaje de éxito en sesión
   (la vista lo muestra con @if(session('success')))
```

### Ejemplo: ver el CV público

```
1. GET /cv  →  CvController::show()

2. $user = User::query()->withCv()->firstOrFail()
   withCv() carga: profile, experiences, education, skills
   (solo hace 1 query con eager loading, no N+1)

3. Vista: resources/views/public/cv.blade.php
   - $user->profile->title, bio, location, etc.
   - $user->experiences (ordenadas por sort_order)
   - $user->education (ordenadas por sort_order)
   - $user->skills (ordenadas por sort_order)
   - Botón "Descargar PDF" → enlace a /cv/download-pdf
```

---

## 8. Tareas habituales de mantenimiento

### Cambiar datos del perfil (nombre, bio, skills...)

Los datos se gestionan desde el panel admin: `http://localhost:8080/admin/cv`

Si necesitas cambiarlo directamente en la base de datos:

```bash
docker compose exec app php artisan tinker
```

```php
// En tinker:
$user = App\Models\User::first();
$user->profile->update(['bio' => 'Nuevo texto de bio']);
$user->experiences()->create(['role' => 'Senior Dev', 'company' => 'Acme', 'period' => '2024 - Actual', 'sort_order' => 0]);
```

### Cambiar el orden de experiencias/skills

El campo `sort_order` controla el orden. 0 = primero. Cámbialo desde `/admin/cv` o con tinker.

### Añadir un post

Desde `/admin/posts` → Crear Post. Soporta Markdown con preview en tiempo real.

### Resetear la base de datos con datos de ejemplo

```bash
docker compose exec app php artisan migrate:fresh --seed
```

Esto borra todo y vuelve a crear el admin con los datos del `AdminSeeder`.

### Reconstruir assets (CSS/JS) tras cambios de diseño

```bash
docker compose exec app npm run build
# o en modo desarrollo con hot reload:
docker compose exec app npm run dev
```

### Limpiar cachés de Laravel

```bash
docker compose exec app php artisan config:clear
docker compose exec app php artisan route:clear
docker compose exec app php artisan view:clear
# O todo de golpe:
docker compose exec app php artisan optimize:clear
```

### Ver las rutas registradas

```bash
docker compose exec app php artisan route:list
```

---

## 9. Cómo añadir una feature nueva

Sigue siempre este orden. Saltarte pasos rompe el proyecto.

### Paso 1: migración (si necesitas nueva tabla o columna)

```bash
docker compose exec app php artisan make:migration add_website_to_profiles_table
```

```php
// database/migrations/xxxx_add_website_to_profiles_table.php
public function up(): void
{
    Schema::table('profiles', function (Blueprint $table) {
        $table->string('website_url')->nullable()->after('linkedin_url');
    });
}

public function down(): void
{
    Schema::table('profiles', function (Blueprint $table) {
        $table->dropColumn('website_url');
    });
}
```

```bash
docker compose exec app php artisan migrate
```

### Paso 2: actualizar el modelo

```php
// app/Models/Profile.php — añade el campo a $fillable
protected $fillable = [
    'user_id', 'title', 'bio', 'location',
    'github_url', 'linkedin_url', 'avatar_initials',
    'website_url',  // ← nuevo
];
```

### Paso 3: actualizar el Form Request

```php
// app/Http/Requests/Admin/UpdateProfileRequest.php
public function rules(): array
{
    return [
        // ... reglas existentes ...
        'website_url' => 'nullable|url|max:255',  // ← nuevo
    ];
}
```

### Paso 4: actualizar la vista del formulario admin

```html
<!-- resources/views/admin/cv/index.blade.php -->
<div>
    <label for="website_url">Sitio web</label>
    <input type="url" name="website_url" id="website_url"
           value="{{ old('website_url', $user->profile?->website_url) }}">
    @error('website_url') <span>{{ $message }}</span> @enderror
</div>
```

### Paso 5: actualizar la vista pública si aplica

```html
<!-- resources/views/public/cv.blade.php -->
@if($user->profile?->website_url)
    <a href="{{ $user->profile->website_url }}">Sitio web</a>
@endif
```

### Paso 6: escribir el test

```php
// tests/Feature/AdminCvTest.php (créalo si no existe)
public function test_admin_can_update_website_url(): void
{
    $admin = User::factory()->admin()->create();
    
    $response = $this->actingAs($admin)
        ->put(route('admin.cv.profile.update'), [
            'title' => 'Dev',
            'website_url' => 'https://misite.com',
        ]);
    
    $response->assertRedirect(route('admin.cv.index'));
    $this->assertDatabaseHas('profiles', ['website_url' => 'https://misite.com']);
}
```

```bash
docker compose exec app php artisan test
```

---

## 10. Tests

### Estructura

```
tests/
├── Unit/
│   └── PostTest.php          ← prueba lógica del modelo (sin BD real)
└── Feature/
    ├── PublicBlogTest.php     ← prueba el blog público (HTTP requests)
    ├── AdminPostTest.php      ← prueba CRUD de posts con auth
    └── AuthTest.php           ← prueba login correcto e incorrecto
```

### Configuración

Los tests usan **SQLite en memoria** (definido en `phpunit.xml`). No tocan la base de datos MariaDB de desarrollo.

```xml
<!-- phpunit.xml -->
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

Todos los Feature tests usan `RefreshDatabase` para limpiar la BD entre tests.

### Cómo ejecutar

```bash
# Todos los tests
docker compose exec app php artisan test

# Solo un archivo
docker compose exec app php artisan test tests/Feature/AdminPostTest.php

# Solo un test concreto
docker compose exec app php artisan test --filter=test_admin_can_create_post

# Con detalle de lo que ejecuta cada test
docker compose exec app php artisan test --verbose
```

### Patrón de los tests de Feature

```php
// Estructura típica de un test admin
public function test_admin_can_delete_own_post(): void
{
    // Arrange: crea datos
    $admin = User::factory()->admin()->create();
    $post  = Post::factory()->for($admin)->published()->create();

    // Act: hace la petición HTTP como ese usuario
    $response = $this->actingAs($admin)
        ->delete(route('admin.posts.destroy', $post));

    // Assert: comprueba el resultado
    $response->assertRedirect(route('admin.posts.index'));
    $this->assertModelMissing($post);
}
```

---

## 11. Sistema de diseño

### Paleta de colores (definida en tailwind.config.js)

| Variable | Hex | Uso |
|---|---|---|
| purple | `#533AB7` | Color principal, botones, acentos |
| purple-light | `#EEEDFE` | Fondos de badges, avatars |
| teal | `#1D9E75` | Acento secundario |
| coral | `#D85A30` | Acento terciario |
| pink | `#D4537E` | Acento cuaternario |
| amber | `#BA7517` | Acento quinto |
| blue | `#378ADD` | Acento sexto |

Los skills, stats, y puntos del timeline rotan los colores en orden: purple → teal → coral → pink → amber → blue.

### Tipografía

- Títulos: fuente `Syne` (Google Fonts), weight 700
- Cuerpo y UI: fuente `Inter` (Google Fonts), weight 400/500

### Convenciones

- Todo estilos con clases Tailwind. Sin CSS custom salvo `@media print`.
- Dark mode con `dark:` de Tailwind. Configurado para activarse con la clase `dark` en `<html>`.
- El CV imprimible (`/cv/print` y PDF) usa la vista `cv-print.blade.php` sin nav ni sidebar.

---

## 12. Reglas que no se rompen

Estas reglas son parte del contrato del proyecto. Romperlas introduce bugs difíciles de rastrear.

| Regla | Razón |
|---|---|
| Nunca `$guarded = []` en modelos | Previene asignación masiva de campos sensibles |
| Nunca `$request->validate()` en controllers | La validación va en Form Requests, siempre |
| Nunca queries dentro de vistas Blade | Las vistas no acceden a la BD directamente |
| Siempre `abort_unless` antes de editar/borrar | Cualquier usuario autenticado podría manipular IDs ajenos |
| Siempre `DB::transaction()` al escribir en más de una tabla | Si syncTags falla a mitad, no queda el post a medio guardar |
| Typed properties, parámetros y retornos siempre | PHP 8.3 con tipos estrictos — facilita encontrar errores |
| Todo código nuevo necesita su test | El proyecto tiene cobertura completa, mantenerla |
| Actualizar CLAUDE.md ante decisiones arquitectónicas | El contexto documentado aquí es el que tiene el asistente de IA |

---

## 13. Dónde está cada cosa

Si buscas algo concreto:

| ¿Qué? | ¿Dónde? |
|---|---|
| Todas las URLs de la app | `routes/web.php` |
| URLs de login/registro/logout | `routes/auth.php` |
| Lógica de una página pública | `app/Http/Controllers/Public/` |
| Lógica del panel admin | `app/Http/Controllers/Admin/` |
| Validación de un formulario | `app/Http/Requests/Admin/` |
| Estructura de la BD | `database/migrations/` |
| Datos de ejemplo (seeder) | `database/seeders/AdminSeeder.php` |
| Layouts (cabecera, nav, footer) | `resources/views/layouts/` |
| Componentes reutilizables Blade | `resources/views/components/` |
| Página de inicio pública | `resources/views/public/home.blade.php` |
| Página del CV | `resources/views/public/cv.blade.php` |
| Vista usada para el PDF | `resources/views/public/cv-print.blade.php` |
| Cómo se convierte Markdown a HTML | `app/Services/MarkdownService.php` |
| Cómo se genera el PDF | `app/Services/PdfService.php` |
| Registro del singleton de Markdown | `app/Providers/AppServiceProvider.php` |
| Middleware que bloquea no-admins | `app/Http/Middleware/EnsureIsAdmin.php` |
| Registro de alias de middleware | `bootstrap/app.php` |
| Colores y tipografía Tailwind | `tailwind.config.js` |
| Variables de entorno (ejemplo) | `.env.example` |
| Docker: servicios y puertos | `docker-compose.yml` |
| Configuración de PHP-FPM | `docker/php/local.ini` |
| Configuración de Nginx | `docker/nginx/default.conf` |
| Guía de despliegue completa | `docs/despliegue.md` |
