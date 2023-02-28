<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * se ingresa la noticia en todos los idiomas que se da soporte
     *
     * @return void
     */
    public function up()
    {
        Schema::create('noticia_novedades', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_regionesapp')->unsigned();
            $table->bigInteger('id_imagen')->unsigned();

            $table->string('titulo', 100);
            $table->date('fecha');
            $table->text('descripcion')->nullable();
            $table->boolean('redireccionar');
            $table->string('link_url', 500)->nullable();
            $table->integer('posicion');

            $table->foreign('id_regionesapp')->references('id')->on('regiones_app');
            $table->foreign('id_imagen')->references('id')->on('noticia_imagen');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('noticia_novedades');
    }
};
