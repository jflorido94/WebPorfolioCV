# Guía de despliegue

Portfolio CV — Laravel 11 + Docker

---

## Desarrollo local (inicio rápido)

```bash
# 1. Clona el repositorio
git clone <url-del-repo>
cd WebPorfolioCV

# 2. Configura las variables de entorno
cp .env.example .env
# Edita .env y cambia al menos APP_KEY, DB_PASSWORD y DB_ROOT_PASSWORD

# 3. Genera APP_KEY si no lo tienes
#    (opción A — antes de levantar Docker, si tienes PHP instalado)
php artisan key:generate

# 4. Levanta los contenedores construyendo la imagen localmente
docker compose up --build -d

# 5. Ejecuta migraciones y seeders
docker compose exec app php artisan migrate:fresh --seed

# 6. Accede a la aplicación
#    Web:   http://localhost:8080
#    Admin: http://localhost:8080/login
#           admin@portfolio.local / password
```

### phpMyAdmin (solo en desarrollo)

```bash
docker compose --profile tools up -d
# Acceso: http://localhost:8081
```

### Comandos útiles

```bash
# Ver logs
docker compose logs -f app

# Ejecutar artisan
docker compose exec app php artisan tinker

# Ejecutar tests
docker compose exec app php artisan test

# Reconstruir imagen tras cambios en Dockerfile
docker compose up --build -d
```

---

## Producción con Docker Hub

### Paso 1: construir y subir imágenes

Desde tu máquina local:

```bash
# Construye las imágenes
docker compose build

# Etiqueta para Docker Hub
docker tag webporfoliocv-app:latest jflorido94/webportfolio-cv:latest
docker tag webporfoliocv-nginx:latest jflorido94/webportfolio-cv-nginx:latest

# Sube
docker login
docker push jflorido94/webportfolio-cv:latest
docker push jflorido94/webportfolio-cv-nginx:latest
```

### Paso 2: desplegar en el servidor

En el servidor de producción (o en Portainer):

```bash
# Descarga las imágenes actualizadas
docker compose pull

# Levanta los servicios
docker compose up -d
```

Las imágenes en producción se identifican automáticamente como `jflorido94/webportfolio-cv:latest` porque ese es el valor por defecto de `APP_IMAGE` en el compose. No hace falta `--build`.

---

## Despliegue en Portainer

### Opción A: Stack con web editor (recomendada)

1. En Portainer, ve a **Stacks** → **+ Add stack**
2. Nombre: `webportfolio-cv`
3. Pega el contenido de `docker-compose.yml` en el editor
4. En **Environment variables**, añade las variables obligatorias (ver tabla más abajo)
5. Clic en **Deploy the stack**

### Opción B: subir el archivo compose

1. En Portainer, ve a **Stacks** → **+ Add stack**
2. Selecciona **Upload** y sube `docker-compose.yml`
3. Añade las variables de entorno
4. Clic en **Deploy**

### Variables de entorno para Portainer

| Variable | Obligatoria | Descripción |
|---|---|---|
| `DB_PASSWORD` | Sí | Contraseña del usuario `portfolio` en MariaDB |
| `DB_ROOT_PASSWORD` | Sí | Contraseña root de MariaDB |
| `APP_KEY` | Sí | Clave de cifrado de Laravel (`base64:XXXXX`) |
| `APP_URL` | Sí | URL pública, p. ej. `https://tu.dominio.com` |
| `APP_ENV` | No | `production` (por defecto `local`) |
| `APP_DEBUG` | No | `false` (por defecto `true`) |
| `LOG_LEVEL` | No | `info` (por defecto `debug`) |
| `PORT` | No | Puerto expuesto por nginx (por defecto `8080`) |
| `DB_USERNAME` | No | Usuario de BD (por defecto `portfolio`) |
| `DB_DATABASE` | No | Nombre de la BD (por defecto `portfolio`) |

### Generar APP_KEY

```bash
# Opción A — local antes del deploy
php artisan key:generate --show
# Copia el valor (empieza por base64:) y ponlo en Portainer

# Opción B — en el contenedor tras el deploy
# Containers → portfolio_app → Console → Connect
php artisan key:generate --show
# Actualiza la variable en el Stack y haz Re-deploy
```

Si dejas `APP_KEY` vacío, el entrypoint lo genera automáticamente al arrancar. Guarda ese valor para futuros redeploys.

---

## Lo que hace el contenedor al arrancar (entrypoint.sh)

El proceso de inicialización es idempotente: puedes reiniciar sin perder datos.

1. Si `APP_KEY` está vacío, lo genera
2. Espera a que la BD esté disponible (hasta 30 intentos)
3. Ejecuta `php artisan migrate --force` (Laravel rastrea migraciones ya aplicadas)
4. Si no hay usuarios admin, ejecuta los seeders
5. Calienta cachés (`config:cache`, `route:cache`)
6. Inicia PHP-FPM

---

## Actualizar la aplicación en producción

```bash
# 1. Construye y sube las nuevas imágenes (desde tu máquina)
docker compose build
docker push jflorido94/webportfolio-cv:latest
docker push jflorido94/webportfolio-cv-nginx:latest

# 2. En el servidor (o desde Portainer → Stack → Re-deploy)
docker compose pull
docker compose up -d
```

Los volúmenes (`db_data`, `storage_data`, `public_data`, `cache_data`) persisten entre actualizaciones. No se pierden datos.

---

## Volúmenes persistentes

| Volumen | Contenido |
|---|---|
| `db_data` | Base de datos MariaDB |
| `storage_data` | Uploads y archivos generados por la app |
| `public_data` | Assets estáticos compilados (CSS, JS) |
| `cache_data` | Caché de Bootstrap de Laravel |

---

## Resolución de problemas comunes

| Síntoma | Causa probable | Solución |
|---|---|---|
| Contenedor `app` no arranca | BD no está lista | Espera 30–60 s; revisa logs con `docker compose logs db` |
| `SQLSTATE[HY000] [1045]` | `DB_PASSWORD` no coincide con `MYSQL_PASSWORD` | Verifica que sean iguales en `.env` |
| `APP_KEY` inválido | Formato incorrecto | Debe empezar por `base64:` — genera uno nuevo |
| Assets no cargan (404) | Vite no compiló | Ejecuta `docker compose exec app npm run build` |
| Permisos denegados en storage | Owner incorrecto | `docker compose exec app chown -R www-data:www-data storage bootstrap/cache` |
| Datos perdidos tras redeploy | Volúmenes borrados | En Portainer usa **Pull & redeploy**, no **Delete & redeploy** |

---

## Diferencias entre desarrollo y producción

| Aspecto | Desarrollo | Producción |
|---|---|---|
| Imagen app | Build local (`--build`) | Docker Hub (`pull`) |
| `APP_ENV` | `local` | `production` |
| `APP_DEBUG` | `true` | `false` |
| `LOG_LEVEL` | `debug` | `info` |
| phpMyAdmin | Sí (`--profile tools`) | No |
| Puerto por defecto | 8080 | 80 (cambia `PORT=80` en `.env` o Portainer) |
