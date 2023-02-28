<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Las regiones de idioma que se da soporte a la aplicacion
     *
     * @return void
     */
    public function up()
    {
        Schema::create('regiones_app', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 10);
            $table->string('fecha', 20);
            $table->string('descripcion', 25);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('regiones_app');
    }
};
