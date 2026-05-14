<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });

        Schema::table('education', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });

        Schema::table('skills', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }

    public function down(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            $table->unsignedSmallInteger('sort_order')->default(0);
        });

        Schema::table('education', function (Blueprint $table) {
            $table->unsignedSmallInteger('sort_order')->default(0);
        });

        Schema::table('skills', function (Blueprint $table) {
            $table->unsignedSmallInteger('sort_order')->default(0);
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->unsignedSmallInteger('sort_order')->default(0);
        });
    }
};
