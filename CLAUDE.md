# CLAUDE.md

Eres un asistente experto en Laravel 11, PHP 8.3 y Docker trabajando sobre un portfolio personal ya construido y funcional. Tu tarea es mantenerlo, ampliarlo y mejorarlo siguiendo estrictamente las especificaciones de este documento.

**Pregunta antes de tomar decisiones de arquitectura no cubiertas aquí. Actualiza este archivo siempre que se tome una decisión importante.**

---

## Estado del proyecto

El proyecto está completado y en producción. Las 12 fases de construcción inicial están cerradas:

- CV interactivo con descarga PDF (barryvdh/laravel-dompdf)
- Blog con Markdown (league/commonmark), categorías, tags y paginación
- Panel de administración completo (perfil, experiencias, educación, skills, posts)
- SEO: meta tags dinámicos, sitemap.xml, robots.txt, Open Graph, Twitter Cards
- Dark mode, responsive, Tailwind CSS
- Autenticación con Laravel Breeze
- Tests PHPUnit (Unit + Feature) — todos en verde
- Docker unificado para dev y prod

Credenciales de prueba: `admin@portfolio.local` / `password`

---

## Descripción del proyecto

Portfolio personal con tres funciones:
- **CV interactivo** descargable/imprimible en PDF
- **Blog y bitácora** de proyectos técnicos con soporte Markdown
- **Panel de administración** privado para gestionar todo el contenido

Audiencia: reclutadores (parte pública) y el propietario (panel admin).

---

## Stack tecnológico — sin excepciones

| Capa | Tecnología |
|---|---|
| Lenguaje | PHP 8.3 |
| Framework | Laravel 11 |
| Base de datos | MariaDB 11 |
| ORM | Eloquent |
| Frontend | Blade + Vite |
| Estilos | Tailwind CSS (incluido con Breeze) |
| Auth scaffolding | Laravel Breeze (blade stack) |
| Servidor web | Nginx Alpine |
| Contenedores | Docker + Docker Compose |
| Tests | PHPUnit (Feature + Unit) |
| Markdown | league/commonmark |
| PDF | barryvdh/laravel-dompdf |

---

## Sistema de diseño

### Paleta de colores

```css
--purple:      #533AB7   /* color principal, acento hero */
--purple-light:#EEEDFE
--purple-dark: #3C3489
--teal:        #1D9E75
--teal-light:  #E1F5EE
--coral:       #D85A30
--coral-light: #FAECE7
--pink:        #D4537E
--pink-light:  #FBEAF0
--amber:       #BA7517
--amber-light: #FAEEDA
--blue:        #378ADD
--blue-light:  #E6F1FB
--bg:          #ffffff
--bg2:         #f7f7f5
--text:        #1a1a18
--muted:       #666666
--border:      rgba(0,0,0,0.1)
```

Skills, métricas, puntos de timeline y stats rotan por la paleta en este orden: purple → teal → coral → pink → amber → blue.

Dark mode: los colores `--bg`, `--bg2`, `--text` se invierten. Los colores de acento se mantienen pero sus variantes `-light` pasan a versiones oscuras (`opacity-20` sobre fondo oscuro).

### Tipografía

- Títulos (`h1`, `h2`): `Syne`, weight 700 — importada desde Google Fonts
- Cuerpo y UI: `Inter`, weight 400/500 — importada desde Google Fonts
- Tamaños: h1 hero 54px → h2 sección 28–30px → cuerpo 15px → meta/etiquetas 12–13px

### Componentes y comportamiento

- Cards: hover con `translateY(-4px)` + sombra suave
- Botón primario: fondo purple, texto blanco, `hover:bg-purple-dark`
- Botón secundario: borde, fondo transparente, `hover:bg-bg2`
- Avatar del perfil: fondo purple-light, iniciales en purple, shape circular
- Badge "disponible": pill redondeada, fondo purple-light, texto purple
- Timeline de experiencia: 3 columnas (fecha | línea+punto | contenido); punto de color rotativo por entrada
- Barras de skills: altura 7px, border-radius redondeado, color rotativo de la paleta
- Navbar: sticky, `backdrop-filter: blur`, fondo semitransparente al hacer scroll

### Convenciones de implementación

- Tailwind CSS para todos los estilos — sin CSS custom salvo `@media print` y casos inevitables
- Dark mode con clase `dark:` de Tailwind (configurado en `tailwind.config.js`)
- El CV imprimible (`/cv/print`) debe verse bien en A4 con `@media print`
- Sin emojis en el HTML salvo en el avatar o elementos decorativos explícitos

---

## Infraestructura Docker

### Archivo único: docker-compose.yml

Un solo archivo para todos los entornos. Ver documentación extendida en [`docs/despliegue.md`](docs/despliegue.md).

```
Desarrollo  → docker compose up --build -d
Producción  → docker compose pull && docker compose up -d
phpMyAdmin  → docker compose --profile tools up -d
```

**Servicios:**

| Servicio | Imagen | Puerto |
|---|---|---|
| app | PHP-FPM 8.3 Alpine (build local / Docker Hub) | — |
| nginx | Nginx Alpine | `${PORT:-8080}:80` |
| db | mariadb:11 | 3306 |
| phpmyadmin | phpmyadmin:latest (profile: tools) | 8081 |

Variables que deben estar en `.env`:
- `DB_PASSWORD` — obligatorio
- `DB_ROOT_PASSWORD` — obligatorio
- `APP_KEY` — obligatorio (genera con `php artisan key:generate`)
- `APP_URL` — por defecto `http://localhost:8080`

Variables opcionales para sobreescribir imágenes Docker Hub:
- `APP_IMAGE` — por defecto `jflorido94/webportfolio-cv:latest`
- `NGINX_IMAGE` — por defecto `jflorido94/webportfolio-cv-nginx:latest`
- `PORT` — por defecto `8080`

### Dockerfile (multistage)

**Stage 1** — `composer:2.7 AS vendor`
- Solo instala dependencias PHP (`composer install --no-dev --no-scripts`)

**Stage 2** — `php:8.3-fpm-alpine`
- Extensiones PHP requeridas: `pdo`, `pdo_mysql`, `mbstring`, `exif`, `pcntl`, `bcmath`, `gd`, `xml`
- Instalar Node.js + npm para compilar assets con Vite
- Copiar vendor del stage 1
- Ejecutar `npm ci && npm run build`
- Ejecutar `php artisan config:cache`, `route:cache`, `view:cache`
- Permisos: `chown www-data:www-data storage/ bootstrap/cache/`

### docker/nginx/default.conf
- `root /var/www/public`
- `try_files $uri $uri/ /index.php?$query_string`
- FastCGI hacia `app:9000`
- Cache de assets estáticos (css, js, imágenes) con `expires 1y`
- Denegar acceso a archivos ocultos (`/\.`)

### docker/php/local.ini
```
upload_max_filesize = 10M
post_max_size = 10M
memory_limit = 256M
max_execution_time = 60
```

---

## Base de datos — Migraciones

Crear en este orden. Todas deben incluir método `down()` correcto.

### tabla: users *(ajustar la existente)*
- Añadir columna: `is_admin` boolean, default false

### tabla: profiles
- `id`
- `user_id` → FK users, cascadeOnDelete
- `title` string
- `bio` text nullable
- `location` string nullable
- `github_url` string nullable
- `linkedin_url` string nullable
- `avatar_initials` string(4) nullable
- timestamps

### tabla: experiences
- `id`
- `user_id` → FK users, cascadeOnDelete
- `role` string
- `company` string
- `period` string
- `description` text nullable
- `started_at` date nullable
- `ended_at` date nullable
- `sort_order` unsignedSmallInteger, default 0
- timestamps

### tabla: education
- `id`
- `user_id` → FK users, cascadeOnDelete
- `title` string
- `institution` string
- `year` year nullable
- `sort_order` unsignedSmallInteger, default 0
- timestamps

### tabla: skills
- `id`
- `user_id` → FK users, cascadeOnDelete
- `name` string(60)
- `category` string(40), default 'general'
- `sort_order` unsignedSmallInteger, default 0
- timestamps

### tabla: posts
- `id`
- `user_id` → FK users, cascadeOnDelete
- `title` string
- `slug` string, unique
- `summary` text nullable
- `content` longText (Markdown raw)
- `category` string(60), default 'blog'
- `published` boolean, default false
- `published_at` timestamp nullable
- timestamps

### tabla: post_tag
- `id`
- `post_id` → FK posts, cascadeOnDelete
- `tag` string(60)
- índice en columna `tag`

---

## Modelos Eloquent

Reglas generales:
- Typed properties siempre
- Return types en todos los métodos
- `$fillable` explícito en todos los modelos (nunca `$guarded = []`)
- Casts apropiados para fechas y booleanos

### User
- Relaciones: `hasOne(Profile)`, `hasMany(Experience)` ordenado por `sort_order`, `hasMany(Education)` ordenado por `sort_order`, `hasMany(Skill)` ordenado por `sort_order`, `hasMany(Post)`
- Cast: `is_admin` → boolean, `password` → hashed
- Método: `isAdmin(): bool`

### Profile
- Relación: `belongsTo(User)`

### Experience
- Relación: `belongsTo(User)`
- Casts: `started_at` y `ended_at` → date

### Education
- Relación: `belongsTo(User)`

### Skill
- Relación: `belongsTo(User)`

### Post
- Relación: `belongsTo(User)`, `hasMany(PostTag)`
- Casts: `published` → boolean, `published_at` → datetime
- Boot: auto-generar `slug` desde `title` con `Str::slug()` si no se proporciona (en `creating`)
- Scope: `scopePublished()` → where published=true AND published_at NOT NULL, ordenado por published_at DESC
- Accessor: `getTagListAttribute(): array` → array de strings con los tags

### PostTag
- `$fillable = ['post_id', 'tag']`
- Relación: `belongsTo(Post)`

---

## Estructura de Controllers

Separación estricta en dos namespaces:

```
app/Http/Controllers/
├── Public/
│   ├── HomeController.php      (invokable)
│   ├── CvController.php
│   └── BlogController.php
└── Admin/
    ├── PostController.php
    └── CvController.php
```

### Public\HomeController (invokable)
- Carga User con eager loading: profile, experiences, education, skills
- Carga últimos 3 posts publicados
- Retorna vista `public.home`

### Public\CvController
- `show()` → vista `public.cv` con User eager loaded
- `print()` → vista `public.cv-print` (layout mínimo, optimizado para impresión)

### Public\BlogController
- `index()` → posts publicados paginados (10 por página) → vista `public.blog.index`
- `show(string $slug)` → post por slug usando scope published → vista `public.blog.show`

### Admin\PostController (resource completo)
- `index()` → posts del usuario autenticado, paginados (15)
- `create()` → vista formulario
- `store(StorePostRequest)` → usar `DB::transaction()`. Si `publish` es true, setear `published=true` y `published_at=now()`. Sincronizar tags.
- `edit(Post)` → verificar ownership con `abort_unless`
- `update(UpdatePostRequest, Post)` → usar `DB::transaction()`. Sincronizar tags.
- `destroy(Post)` → verificar ownership con `abort_unless`
- Método privado `syncTags(Post, string): void` → delete + recrear tags
- Método privado `authorizePost(Post): void` → `abort_unless($post->user_id === Auth::id(), 403)`

### Admin\CvController
- `index()` → User con eager loading → vista `admin.cv.index`
- `updateProfile(UpdateProfileRequest)` → `updateOrCreate` en profile
- `storeExperience(StoreExperienceRequest)` → crear en colección del usuario
- `destroyExperience(Experience)` → verificar ownership, eliminar

---

## Form Requests

Todos en `app/Http/Requests/Admin/`. Todos con `authorize(): bool { return auth()->check() && auth()->user()->isAdmin(); }`

### StorePostRequest
- `title`: required, string, max:255
- `summary`: nullable, string, max:500
- `content`: required, string
- `category`: required, string, max:60
- `tags`: nullable, string
- `publish`: nullable, boolean

### UpdatePostRequest
- Mismas reglas que Store

### UpdateProfileRequest
- `title`: required, string, max:255
- `bio`: nullable, string
- `location`: nullable, string, max:100
- `github_url`: nullable, url, max:255
- `linkedin_url`: nullable, url, max:255
- `avatar_initials`: nullable, string, max:4

### StoreExperienceRequest
- `role`: required, string, max:150
- `company`: required, string, max:150
- `period`: required, string, max:60
- `description`: nullable, string
- `sort_order`: nullable, integer, min:0

---

## Middleware

### app/Http/Middleware/EnsureIsAdmin.php
```php
if (! $request->user()?->isAdmin()) {
    abort(403);
}
return $next($request);
```

Registrar en `bootstrap/app.php` con alias `admin`.

---

## Rutas (routes/web.php)

```
GET  /                          → Public\HomeController          name: home
GET  /cv                        → Public\CvController@show       name: cv.show
GET  /cv/print                  → Public\CvController@print      name: cv.print
GET  /cv/download-pdf           → Public\CvController@downloadPdf name: cv.download-pdf
GET  /blog                      → Public\BlogController@index    name: blog.index
GET  /blog/{slug}               → Public\BlogController@show     name: blog.show
GET  /sitemap.xml               → SitemapController              name: sitemap

--- middleware: auth, admin ---
GET  /admin                     → redirect admin.posts.index     name: admin.dashboard
GET  /admin/cv                  → Admin\CvController@index       name: admin.cv.index
PUT  /admin/cv/profile          → Admin\CvController@updateProfile   name: admin.cv.profile.update
POST /admin/cv/experience       → Admin\CvController@storeExperience name: admin.cv.experience.store
DEL  /admin/cv/experience/{id}  → Admin\CvController@destroyExperience name: admin.cv.experience.destroy
RESOURCE /admin/posts           → Admin\PostController           name: admin.posts.*
```

---

## Vistas Blade

### Layouts
- `layouts/app.blade.php` — layout público con nav (Inicio, CV, Blog). Usado via `<x-app-layout>`
- `components/admin-layout.blade.php` — layout admin con sidebar. Usado via `<x-admin-layout>` (NO existe layouts/admin.blade.php)

### Vistas públicas
- `public/home.blade.php` — presentación, stats, últimos posts
- `public/cv.blade.php` — CV completo con botón "Imprimir PDF"
- `public/cv-print.blade.php` — layout mínimo sin nav, `@media print` optimizado
- `public/blog/index.blade.php` — lista de posts con paginación
- `public/blog/show.blade.php` — post individual con Markdown renderizado

### Vistas admin
- `admin/posts/index.blade.php` — tabla de posts con acciones
- `admin/posts/create.blade.php` — formulario con editor Markdown y preview en tiempo real (Alpine.js + marked.js vía CDN)
- `admin/posts/edit.blade.php` — igual que create pero pre-relleno
- `admin/cv/index.blade.php` — formularios de perfil, experiencia, educación y habilidades en una sola página

---

## Factories

### UserFactory
- Estado `admin()`: setea `is_admin = true`
- Estado normal: `is_admin = false`

### PostFactory
- Estado `published()`: `published = true`, `published_at = now()`
- Estado `draft()`: `published = false`, `published_at = null`
- Auto-generar `slug` desde `title`

---

## Seeders

### DatabaseSeeder
Llama únicamente a `AdminSeeder`.

### AdminSeeder
Crea un usuario administrador:
- name: `Admin`
- email: `admin@portfolio.local`
- password: `password` (hasheado)
- is_admin: `true`

Crea su perfil con datos de Javier Florido.

---

## Tests

### Configuración phpunit.xml
Base de datos de tests en SQLite en memoria:
```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

### tests/Unit/PostTest.php
- `test_slug_is_generated_from_title`
- `test_tag_list_returns_array`

### tests/Feature/PublicBlogTest.php
Usa `RefreshDatabase`.
- `test_blog_index_shows_published_posts`
- `test_draft_post_is_not_publicly_accessible`
- `test_published_post_is_accessible_by_slug`

### tests/Feature/AdminPostTest.php
Usa `RefreshDatabase`.
- `test_guest_cannot_access_admin`
- `test_admin_can_create_post`
- `test_admin_can_delete_own_post`
- `test_admin_cannot_delete_another_users_post`
- `test_store_post_requires_title_and_content`

### tests/Feature/AuthTest.php
Usa `RefreshDatabase`.
- `test_admin_can_login`
- `test_wrong_password_fails_login`

---

## Servicios

### MarkdownService (`app/Services/MarkdownService.php`)
- Método `toHtml(string $markdown): string`
- `CommonMarkConverter` con extensiones: `TableExtension`, `AutolinkExtension`, `FencedCodeExtension`
- Registrado como singleton en `AppServiceProvider`
- Inyectado en `Public\BlogController`

### PdfService (`app/Services/PdfService.php`)
- Usa barryvdh/laravel-dompdf
- Método `generateCvPdf(User $user): Response`
- Inyectado en `Public\CvController@downloadPdf`

### AppServiceProvider — View::composer
`app/Providers/AppServiceProvider.php` comparte estas variables con `layouts.app` y `components.admin-layout`:
- `$siteInitials` — `avatar_initials` del perfil admin (cadena vacía si no configurado)
- `$siteAvatarPath` — `avatar_path` del perfil admin
- `$siteBio` — `bio` del perfil admin (cadena vacía si no configurado)

Todas las vistas que usen el bio deben renderizarlo con `style="white-space: pre-line"` para respetar saltos de línea.

---

## Reglas que Claude Code debe respetar siempre

- Nunca usar `$guarded = []` en modelos
- Nunca poner lógica de negocio en controllers — usar Services si es necesario
- Nunca usar `$request->validate()` directamente en controllers — siempre Form Requests
- Nunca hacer queries dentro de las vistas Blade
- Siempre verificar ownership con `abort_unless` antes de modificar o eliminar recursos
- Siempre usar `DB::transaction()` cuando se escriba en más de una tabla
- Siempre tipar propiedades, parámetros y retornos
- Todo código nuevo necesita su test correspondiente
- Actualizar este CLAUDE.md ante cualquier decisión arquitectónica importante
