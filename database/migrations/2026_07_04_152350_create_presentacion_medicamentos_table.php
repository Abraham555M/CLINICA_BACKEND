<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('presentacion_medicamento', function (Blueprint $table) {
            $table->id("id_presentacion");
            $table->string("nom_presentacion", 150)->unique();
            $table->tinyInteger("est_presentacion")->default(1); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presentacion_medicamento');
    }
};
