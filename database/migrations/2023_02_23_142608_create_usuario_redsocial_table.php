<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * REGISTRO DEL ID DE LAS REDES SOCIALES PARA INICIAR SESION SEGUN TIPO
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuario_redsocial', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_users')->unsigned();
            $table->bigInteger('id_tiporedsocial')->unsigned();
            $table->string('id_redsocial', 100); // el id que retorna red social al iniciar sesion
            $table->string('token_redsocial', 100);

            $table->foreign('id_users')->references('id')->on('users');
            $table->foreign('id_tiporedsocial')->references('id')->on('tipo_redsocial');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usuario_redsocial');
    }
};
