<?php

namespace Database\Seeders;

use App\Models\Education;
use App\Models\Experience;
use App\Models\Post;
use App\Models\Profile;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Use firstOrCreate to make seeder idempotent (safe to run multiple times)
        $admin = User::firstOrCreate(
            ['email' => 'admin@portfolio.local'],
            [
                'name' => 'Javier Florido',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        // Only create profile if it doesn't exist
        Profile::firstOrCreate(
            ['user_id' => $admin->id],
            [
                'title' => 'Técnico Superior en Desarrollo de Aplicaciones Web',
                'bio' => 'Programador especializado en PHP/Laravel y desarrollo web fullstack. Apasionado por crear soluciones robustas y escalables usando tecnologías modernas.',
                'location' => 'Madrid, Spain',
                'github_url' => 'https://github.com/jflorido94',
                'linkedin_url' => 'https://linkedin.com/in/jflorido94',
                'avatar_initials' => 'JF',
            ]
        );

        Experience::create([
            'user_id' => $admin->id,
            'role' => 'Programador Senior',
            'company' => 'Unex Group / BM S.L',
            'period' => '2022 - Actual',
            'description' => 'Desarrollo y mantenimiento de aplicaciones web con PHP/Laravel. Arquitectura de bases de datos MariaDB. Implementación de APIs REST. Dockerización de proyectos.',
            'started_at' => '2022-01-15',
            'ended_at' => null,
            'sort_order' => 0,
        ]);

        Experience::create([
            'user_id' => $admin->id,
            'role' => 'Desarrollador Junior',
            'company' => 'Minas de Riotinto',
            'period' => '2020 - 2022',
            'description' => 'Desarrollo de aplicaciones web con PHP, JavaScript y Angular. Soporte técnico y mantenimiento de sistemas.',
            'started_at' => '2020-06-01',
            'ended_at' => '2022-01-10',
            'sort_order' => 1,
        ]);

        Experience::create([
            'user_id' => $admin->id,
            'role' => 'Prácticas Profesionales',
            'company' => 'Paterna del Campo',
            'period' => '2019 - 2020',
            'description' => 'Prácticas en desarrollo web durante el ciclo formativo superior.',
            'started_at' => '2019-09-01',
            'ended_at' => '2020-06-30',
            'sort_order' => 2,
        ]);

        Education::create([
            'user_id' => $admin->id,
            'title' => 'Técnico Superior en Desarrollo de Aplicaciones Web',
            'institution' => 'IES San Sebastián',
            'year' => 2022,
            'sort_order' => 0,
        ]);

        $skills = [
            ['name' => 'PHP', 'category' => 'Backend', 'sort_order' => 0],
            ['name' => 'Laravel', 'category' => 'Framework', 'sort_order' => 1],
            ['name' => 'Docker', 'category' => 'DevOps', 'sort_order' => 2],
            ['name' => 'JavaScript', 'category' => 'Frontend', 'sort_order' => 3],
            ['name' => 'TypeScript', 'category' => 'Frontend', 'sort_order' => 4],
            ['name' => 'Angular', 'category' => 'Framework', 'sort_order' => 5],
            ['name' => 'C#', 'category' => 'Lenguaje', 'sort_order' => 6],
            ['name' => 'MariaDB', 'category' => 'Base de Datos', 'sort_order' => 7],
            ['name' => 'Tailwind CSS', 'category' => 'Frontend', 'sort_order' => 8],
            ['name' => 'Git', 'category' => 'Herramientas', 'sort_order' => 9],
        ];

        foreach ($skills as $skill) {
            Skill::create([
                'user_id' => $admin->id,
                ...$skill,
            ]);
        }

        Post::create([
            'user_id' => $admin->id,
            'title' => 'Construyendo un Portfolio con Laravel 11 y Docker',
            'slug' => 'portfolio-laravel-docker',
            'summary' => 'En este artículo te muestro cómo construir un portfolio profesional fullstack usando Laravel 11, PHP 8.3, MariaDB y Docker.',
            'content' => '# Construyendo un Portfolio con Laravel 11 y Docker

En el mundo del desarrollo web, tener un portfolio profesional es esencial. En este artículo, te mostraré cómo construir un portfolio completo usando **Laravel 11**, **PHP 8.3**, **MariaDB** y **Docker**.

## Stack Tecnológico

- **Backend**: Laravel 11 con PHP 8.3
- **Base de Datos**: MariaDB 11
- **Frontend**: Blade + Tailwind CSS
- **Contenedores**: Docker + Docker Compose
- **Herramientas**: Vite para assets

## Estructura de la Aplicación

La aplicación consta de tres partes principales:

1. **Sección Pública** - Página de inicio, CV interactivo y blog
2. **Panel de Administración** - Gestión de contenido privada
3. **API** - Endpoints para futuras integraciones

## Configuración de Docker

Una de las ventajas de usar Docker es que garantiza que tu aplicación funcione en cualquier máquina.

```dockerfile
FROM php:8.3-fpm-alpine
RUN docker-php-ext-install pdo pdo_mysql mbstring
COPY . /var/www
WORKDIR /var/www
```

## Conclusión

Laravel 11 es un excelente framework para construir aplicaciones web profesionales. Con Docker, garantizamos reproducibilidad y facilidad de despliegue.',
            'category' => 'tutorial',
            'published' => true,
            'published_at' => now()->subDays(15),
        ]);

        Post::create([
            'user_id' => $admin->id,
            'title' => 'Guía Completa de Eloquent ORM en Laravel',
            'slug' => 'eloquent-orm-laravel',
            'summary' => 'Una guía profunda sobre Eloquent ORM, el sistema de mapeo relacional de objetos de Laravel.',
            'content' => '# Guía Completa de Eloquent ORM en Laravel

Eloquent es el ORM (Object Relational Mapping) incluido con Laravel y es uno de los más elegantes en el ecosistema PHP.

## Relaciones en Eloquent

### Relación HasMany

```php
class User extends Model
{
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
```

### Relación BelongsTo

```php
class Post extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

## Scopes

Los scopes te permiten reutilizar lógica de consultas en tu aplicación.

```php
class Post extends Model
{
    public function scopePublished(Builder $query): Builder
    {
        return $query->where("published", true);
    }
}

// Uso
Post::published()->get();
```

Eloquent simplifica enormemente el trabajo con bases de datos en Laravel.',
            'category' => 'blog',
            'published' => true,
            'published_at' => now()->subDays(8),
        ]);

        Post::create([
            'user_id' => $admin->id,
            'title' => 'Mi Nuevo Proyecto: Sistema de Gestión de Tareas',
            'slug' => 'proyecto-sistema-tareas',
            'summary' => 'Presentación de un nuevo proyecto personal: un sistema completo de gestión de tareas con Laravel y Angular.',
            'content' => '# Mi Nuevo Proyecto: Sistema de Gestión de Tareas

Acabo de terminar un nuevo proyecto que combina **Laravel** en el backend con **Angular** en el frontend.

## Características

- Crear, editar y eliminar tareas
- Asignación de tareas a otros usuarios
- Sistema de notificaciones en tiempo real
- Dashboard con estadísticas
- Autenticación JWT

## Tecnologías Usadas

| Tecnología | Uso |
|---|---|
| Laravel 11 | Backend |
| Angular 17 | Frontend |
| PostgreSQL | Base de datos |
| Docker | Contenedorización |
| WebSockets | Notificaciones en tiempo real |

Este proyecto me permitió explorar nuevas tecnologías y mejorar mis habilidades en arquitectura de sistemas.',
            'category' => 'proyecto',
            'published' => false,
            'published_at' => null,
        ]);
    }
}
