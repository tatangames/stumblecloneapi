<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Se registran las imagenes para cada noticia
     *
     * @return void
     */
    public function up()
    {
        Schema::create('noticia_imagen', function (Blueprint $table) {
            $table->id();
            $table->string('imagen', 100);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('noticia_imagen');
    }
};
