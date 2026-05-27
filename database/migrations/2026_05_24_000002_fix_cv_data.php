<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Nombre completo
        DB::table('users')->where('id', 1)->update([
            'name' => 'Javier Florido Pavon',
        ]);

        // WifiBlaster (id=2): fechas reales y periodo corregido
        DB::table('experiences')->where('id', 2)->update([
            'started_at' => '2015-08-10',
            'ended_at'   => '2015-09-18',
            'period'     => 'Ago 2015 - Sep 2015',
        ]);

        // PC Blaster (id=4): fechas reales (alta dic 2014 - abr 2015, empresa desde 2012)
        DB::table('experiences')->where('id', 4)->update([
            'started_at' => '2014-12-15',
            'ended_at'   => '2015-04-30',
            'period'     => '2012 - 2015',
        ]);

        // Bachillerato en educacion
        DB::table('education')->insertOrIgnore([
            [
                'user_id'     => 1,
                'title'       => 'Bachillerato',
                'institution' => 'IES La Palma',
                'location'    => 'La Palma del Condado, Huelva',
                'year'        => 2017,
                'show_in_web' => true,
                'show_in_pdf' => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);

        // Telefono en el perfil (idiomas gestionados via tabla languages)
        DB::table('profiles')->where('id', 1)->update([
            'phone' => '+34 635 751 965',
        ]);
    }

    public function down(): void
    {
        DB::table('users')->where('id', 1)->update(['name' => 'Javier Florido']);

        DB::table('experiences')->where('id', 2)->update([
            'started_at' => '2020-06-01',
            'ended_at'   => '2022-01-10',
            'period'     => '2015 - 2016',
        ]);

        DB::table('experiences')->where('id', 4)->update([
            'started_at' => null,
            'ended_at'   => null,
            'period'     => '2012 - 2015',
        ]);

        DB::table('education')->where('institution', 'IES La Palma')->delete();

        DB::table('profiles')->where('id', 1)->update([
            'phone' => null,
        ]);
    }
};
