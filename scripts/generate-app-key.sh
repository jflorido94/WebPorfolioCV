#!/bin/bash

# Script para generar una APP_KEY válida para Portainer
# Uso: bash scripts/generate-app-key.sh

echo "=== Generador de APP_KEY para Laravel ==="
echo ""
echo "Nota: Para generar APP_KEY manualmente sin Docker:"
echo "1. En Portainer, abre Console del contenedor portfolio_app"
echo "2. Ejecuta: php artisan key:generate --show"
echo "3. Copia el valor (sin 'base64:' si lo muestra)"
echo ""
echo "O ejecuta desde tu máquina:"
echo "  docker compose exec app php artisan key:generate --show"
