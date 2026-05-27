<?php

namespace Database\Seeders;

use App\Models\Education;
use App\Models\Experience;
use App\Models\Language;
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
        $admin = User::updateOrCreate(
            ['email' => 'admin@portfolio.local'],
            [
                'name' => 'Javier Florido Pavon',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        Profile::updateOrCreate(
            ['user_id' => $admin->id],
            [
                'title' => 'Técnico Superior en Desarrollo de Aplicaciones Web',
                'bio' => "Desarrollador web con más de 3 años de experiencia profesional en PHP y Laravel. Especializado en el desarrollo de aplicaciones web robustas con MariaDB, Docker y arquitecturas orientadas a servicios. Apasionado por las buenas prácticas, el código limpio y la mejora continua.",
                'contact_email' => 'jflorido94@hotmail.com',
                'phone' => '+34 635 751 965',
                'location' => 'Torres de la Alameda, Madrid',
                'github_url' => 'https://github.com/jflorido94',
                'linkedin_url' => 'https://linkedin.com/in/jflorido94',
                'avatar_initials' => 'JFP',
            ]
        );

        if ($admin->experiences()->doesntExist()) {
            Experience::create([
                'user_id' => $admin->id,
                'role' => 'Programador Web',
                'company' => 'Unex Group / BM S.L',
                'period' => '2022 - Actual',
                'location' => 'Huelva',
                'description' => "Desarrollo y mantenimiento de aplicaciones web internas con PHP y Laravel.\nDiseño e implementación de bases de datos relacionales con MariaDB.\nDespliegue y gestión de entornos Docker en producción.\nDesarrollo de APIs REST e integraciones con sistemas externos.",
                'started_at' => '2022-01-15',
                'ended_at' => null,
                'show_in_web' => true,
                'show_in_pdf' => true,
            ]);

            Experience::create([
                'user_id' => $admin->id,
                'role' => 'Técnico informático',
                'company' => 'WifiBlaster',
                'period' => 'Ago 2015 - Sep 2015',
                'location' => 'Huelva',
                'description' => 'Instalación y configuración de equipos y redes WiFi para clientes residenciales y empresas.',
                'started_at' => '2015-08-10',
                'ended_at' => '2015-09-18',
                'show_in_web' => true,
                'show_in_pdf' => true,
            ]);

            Experience::create([
                'user_id' => $admin->id,
                'role' => 'Técnico informático',
                'company' => 'PC Blaster',
                'period' => '2012 - 2015',
                'location' => 'Huelva',
                'description' => 'Reparación y mantenimiento de equipos informáticos. Instalación de sistemas operativos, software y periféricos.',
                'started_at' => '2014-12-15',
                'ended_at' => '2015-04-30',
                'show_in_web' => true,
                'show_in_pdf' => true,
            ]);

            Experience::create([
                'user_id' => $admin->id,
                'role' => 'Técnico en Prácticas',
                'company' => 'Tecinet',
                'period' => '2022',
                'location' => 'Huelva',
                'description' => 'Prácticas de empresa del ciclo formativo de Grado Superior en Desarrollo de Aplicaciones Web.',
                'started_at' => '2022-03-01',
                'ended_at' => '2022-06-30',
                'show_in_web' => true,
                'show_in_pdf' => true,
            ]);
        }

        if ($admin->education()->doesntExist()) {
            Education::create([
                'user_id' => $admin->id,
                'title' => 'Técnico Superior en Desarrollo de Aplicaciones Web',
                'institution' => 'IES San Sebastián',
                'location' => 'Huelva',
                'year' => 2022,
                'show_in_web' => true,
                'show_in_pdf' => true,
            ]);

            Education::create([
                'user_id' => $admin->id,
                'title' => 'Bachillerato',
                'institution' => 'IES La Palma',
                'location' => 'La Palma del Condado, Huelva',
                'year' => 2017,
                'show_in_web' => true,
                'show_in_pdf' => true,
            ]);
        }

        if ($admin->skills()->doesntExist()) {
            $skills = [
                ['name' => 'PHP',          'category' => 'Backend'],
                ['name' => 'Laravel',      'category' => 'Backend'],
                ['name' => 'MySQL / MariaDB', 'category' => 'Base de datos'],
                ['name' => 'Docker',       'category' => 'DevOps'],
                ['name' => 'Git',          'category' => 'DevOps'],
                ['name' => 'JavaScript',   'category' => 'Frontend'],
                ['name' => 'TypeScript',   'category' => 'Frontend'],
                ['name' => 'Angular',      'category' => 'Frontend'],
                ['name' => 'Tailwind CSS', 'category' => 'Frontend'],
                ['name' => 'C#',           'category' => 'Lenguajes'],
                ['name' => 'HTML / CSS',   'category' => 'Frontend'],
                ['name' => 'Nginx',        'category' => 'DevOps'],
            ];

            foreach ($skills as $skill) {
                Skill::create([
                    'user_id' => $admin->id,
                    'show_in_web' => true,
                    'show_in_pdf' => true,
                    ...$skill,
                ]);
            }
        }

        if ($admin->languages()->doesntExist()) {
            Language::create(['user_id' => $admin->id, 'name' => 'Español', 'level' => 'Nativo',         'show_in_web' => true, 'show_in_pdf' => true]);
            Language::create(['user_id' => $admin->id, 'name' => 'Inglés',  'level' => 'Nivel técnico', 'show_in_web' => true, 'show_in_pdf' => true]);
        }

        if ($admin->posts()->doesntExist()) {
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

## Configuración de Docker

Una de las ventajas de usar Docker es que garantiza que tu aplicación funcione en cualquier máquina.

```dockerfile
FROM php:8.3-fpm-alpine
RUN docker-php-ext-install pdo pdo_mysql mbstring
COPY . /var/www
WORKDIR /var/www
```

Laravel 11 es un excelente framework para construir aplicaciones web profesionales. Con Docker, garantizamos reproducibilidad y facilidad de despliegue.',
                'category' => 'tutorial',
                'published' => true,
                'published_at' => now()->subDays(15),
            ]);
        }
    }
}
