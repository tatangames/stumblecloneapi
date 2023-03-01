<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * las banderas de todos los paises
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banderas_paises', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 10);
            $table->string('urlimagen', 100);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('banderas_paises');
    }
};
