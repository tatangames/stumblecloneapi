<?php

namespace Database\Seeders;

use App\Models\NivelExperiencia;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NivelesExpeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        NivelExperiencia::create([
            'nivel' => 1,
            'experiencia' => 200
        ]);

        NivelExperiencia::create([
            'nivel' => 2,
            'experiencia' => 500
        ]);

        NivelExperiencia::create([
            'nivel' => 3,
            'experiencia' => 800
        ]);

        NivelExperiencia::create([
            'nivel' => 4,
            'experiencia' => 1500
        ]);
    }
}
