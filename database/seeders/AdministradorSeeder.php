<?php

namespace Database\Seeders;

use App\Models\Administrador;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdministradorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Administrador::create([
            'nombre' => 'Jonathan',
            'usuario' => 'tatan',
            'password' => bcrypt('1234'),
        ])->assignRole('admin');
    }
}
