<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * LOS TIPOS DE REDES SOCIALES CON CUAL PUEDE INICIAR SESION EL USUARIO
     *
     * 1- Facebook
     * 2- Android
     * 3- Apple
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_redsocial', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 25);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipo_redsocial');
    }
};
