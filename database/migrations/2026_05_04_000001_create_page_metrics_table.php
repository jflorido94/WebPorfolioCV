<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('page', 20);
            $table->string('ip', 45);
            $table->string('country', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('isp', 200)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->boolean('geo_resolved')->default(false);
            $table->timestamps();

            $table->index('page');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_metrics');
    }
};
