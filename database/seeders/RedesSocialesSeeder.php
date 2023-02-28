<?php

namespace Database\Seeders;

use App\Models\TipoRedSocial;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RedesSocialesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TipoRedSocial::create([
            'nombre' => 'Facebook',
        ]);

        TipoRedSocial::create([
            'nombre' => 'Android',
        ]);

        TipoRedSocial::create([
            'nombre' => 'Apple',
        ]);
    }
}
