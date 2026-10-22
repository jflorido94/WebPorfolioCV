<?php

use Illuminate\Database\Migrations\Migration;

/**
 * Migración vaciada intencionadamente.
 *
 * Antes corregía datos personales del CV (nombre, fechas de experiencias,
 * Bachillerato y teléfono) apuntando a ids fijos (user_id = 1). Eso rompía
 * cualquier base de datos vacía (tests con SQLite en memoria, instalaciones
 * nuevas) por violación de clave foránea.
 *
 * - Producción: ya está ejecutada y registrada en la tabla `migrations`,
 *   así que vaciarla no cambia nada.
 * - Instalaciones nuevas: esos datos los crea AdminSeeder.
 *
 * Regla del proyecto: las migraciones solo tocan esquema, nunca datos.
 * Se mantiene el fichero para no dejar un hueco en el historial de migraciones.
 */
return new class extends Migration
{
    public function up(): void
    {
        //
    }

    public function down(): void
    {
        //
    }
};
