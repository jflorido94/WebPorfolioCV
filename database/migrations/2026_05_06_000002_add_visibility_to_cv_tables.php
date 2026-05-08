<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['experiences', 'education', 'courses', 'skills'] as $tbl) {
            Schema::table($tbl, function (Blueprint $table) {
                $table->boolean('show_in_web')->default(true)->after('sort_order');
                $table->boolean('show_in_pdf')->default(true)->after('show_in_web');
            });
        }
    }

    public function down(): void
    {
        foreach (['experiences', 'education', 'courses', 'skills'] as $tbl) {
            Schema::table($tbl, function (Blueprint $table) {
                $table->dropColumn(['show_in_web', 'show_in_pdf']);
            });
        }
    }
};
