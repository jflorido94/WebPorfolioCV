<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('page_metrics', function (Blueprint $table) {
            $table->string('state', 100)->nullable()->after('country');
            $table->string('zipcode', 20)->nullable()->after('city');
            $table->decimal('latitude', 10, 7)->nullable()->after('zipcode');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->boolean('is_mobile')->nullable()->after('isp');
            $table->boolean('is_vpn')->nullable()->after('is_mobile');
            $table->boolean('is_tor')->nullable()->after('is_vpn');
            $table->boolean('is_proxy')->nullable()->after('is_tor');
            $table->boolean('is_datacenter')->nullable()->after('is_proxy');
        });
    }

    public function down(): void
    {
        Schema::table('page_metrics', function (Blueprint $table) {
            $table->dropColumn([
                'state', 'zipcode', 'latitude', 'longitude',
                'is_mobile', 'is_vpn', 'is_tor', 'is_proxy', 'is_datacenter',
            ]);
        });
    }
};
