# Portfolio Personal — Javier Florido

Portfolio profesional construido con Laravel 11, PHP 8.3, MariaDB y Docker.

- **CV interactivo** descargable en PDF y optimizado para impresión
- **Blog** con soporte Markdown, categorías y tags
- **Panel de administración** para gestionar todo el contenido
- Dark mode, responsive, SEO optimizado

## Stack

| Capa | Tecnología |
|---|---|
| Backend | Laravel 11 (PHP 8.3) |
| Base de datos | MariaDB 11 |
| Frontend | Blade + Tailwind CSS + Vite |
| Autenticación | Laravel Breeze |
| Contenedores | Docker + Docker Compose |
| Tests | PHPUnit |
| Markdown | League/CommonMark |
| PDF | barryvdh/laravel-dompdf |

## Inicio rápido

Necesitas Docker y Docker Compose instalados.

```bash
git clone <url-del-repo>
cd WebPorfolioCV
cp .env.example .env
# Edita .env: cambia DB_PASSWORD, DB_ROOT_PASSWORD y APP_KEY como mínimo
docker compose up --build -d
docker compose exec app php artisan migrate:fresh --seed
```

Accede a http://localhost:8080  
Panel admin: http://localhost:8080/login — `admin@portfolio.local` / `password`

Para levantar phpMyAdmin:

```bash
docker compose --profile tools up -d
# http://localhost:8081
```

## Rutas principales

| URL | Descripción |
|---|---|
| `/` | Página de inicio |
| `/cv` | CV interactivo |
| `/cv/download-pdf` | Descarga el CV en PDF |
| `/blog` | Lista de posts |
| `/blog/{slug}` | Post individual |
| `/sitemap.xml` | Sitemap para SEO |
| `/admin/posts` | Gestión de posts (requiere login) |
| `/admin/cv` | Gestión de perfil y experiencias |

## Tests

```bash
docker compose exec app php artisan test
```

## Documentación

- [Guía del desarrollador](docs/guia-desarrollador.md) — arquitectura, flujo de peticiones, cómo añadir features, tests, diseño
- [Guía de despliegue](docs/despliegue.md) — desarrollo local, Docker Hub, Portainer, troubleshooting
- [Historial del proyecto](docs/historial.md) — checklist de funcionalidades implementadas

## Contacto

- Email: jflorido94@hotmail.com
- GitHub: https://github.com/jflorido94
- LinkedIn: https://linkedin.com/in/jflorido94
