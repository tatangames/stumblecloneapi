<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configuraciones', function (Blueprint $table) {
            $table->id();
            // cuando usuario cambia nombre por segunda vez, se cobran usando gemas
            $table->integer('precio_nombre');
            $table->integer('version_android');
            $table->integer('version_apple');

            // nuevas noticias se debera aumentar el contador para que usuario al cargar el juego
            // aparesca icono que hay una nueva noticia
            $table->integer('nueva_noticia');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('configuraciones');
    }
};
