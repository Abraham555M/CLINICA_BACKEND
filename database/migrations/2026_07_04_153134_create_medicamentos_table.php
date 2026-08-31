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
        Schema::create('medicamento', function (Blueprint $table) {
            $table->id("id_medicamento");
            $table->string("nom_medicamento");
            $table->string("con_medicamento"); // concentracion - 500 mg 
            $table->tinyInteger("est_medicamento")->default(1);

            $table->foreignId("id_presentacion")
                ->constrained(table: "presentacion_medicamento", column: "id_presentacion")
                ->onDelete("cascade");

            $table->foreignId("id_unidad_medida")
                ->constrained(table: "unidad_medida", column: "id_unidad_medida")
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
        Schema::dropIfExists('medicamento');
    }
};
