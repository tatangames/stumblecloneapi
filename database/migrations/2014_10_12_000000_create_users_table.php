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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->dateTime('fecha_creacion');
            $table->string('nombre', 30);
            $table->integer('experiencia');
            $table->boolean('nombre_cambio');
            $table->string('token',100);
            $table->string('id_facebook', 100)->nullable();
            $table->string('id_android', 100)->nullable();
            $table->string('id_apple',100)->nullable();
            $table->string('pais', 20);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
