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
        Schema::create('panel_videos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 100);
            $table->date('fecha');
            $table->string('imagen', 100);
            $table->text('descripcion')->nullable();
            $table->string('link_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('panel_videos');
    }
};
