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
        Schema::create('receta_detalle', function (Blueprint $table) {
            $table->id("id_receta_detalle");
            $table->string("dos_med_receta_detalle"); // dosis medicamento
            $table->string("fre_med_receta_detalle"); // frecuencia medicamento
            $table->string("dur_med_receta_detalle"); // duracion medicamento

            $table->foreignId("id_medicamento")
                ->constrained(table: "medicamento", column: "id_medicamento")
                ->onDelete("cascade");

            $table->foreignId("id_receta")
                ->constrained(table: "receta", column: "id_receta")
                ->onDelete("cascade");
                
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receta_detalle');
    }
};
