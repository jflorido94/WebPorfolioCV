<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Las migraciones solo deben crear/modificar esquema. Si alguna inserta datos
 * (por ejemplo datos personales del CV), este test falla. Los datos van en
 * seeders: AdminSeeder (reales) o TestCvSeeder (ficticios, para tests).
 */
class MigrationsAreSchemaOnlyTest extends TestCase
{
    use RefreshDatabase;

    public function test_migrations_leave_content_tables_empty(): void
    {
        $tables = ['users', 'profiles', 'experiences', 'education', 'skills', 'courses', 'languages', 'posts'];

        foreach ($tables as $table) {
            $this->assertSame(0, DB::table($table)->count(), "La tabla {$table} no está vacía tras migrar.");
        }
    }
}
