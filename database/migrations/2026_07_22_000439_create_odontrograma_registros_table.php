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
        Schema::create('odontrograma_registro', function (Blueprint $table) {
            $table->id('id_odontograma_registro');
            $table->dateTime('fch_registro_odontograma');
            $table->text("obs_registro_odontograma")->nullable();

            $table->foreignId("id_paciente")
                ->constrained(table: "paciente", column: "id_paciente")
                ->onDelete("cascade");
            
            $table->foreignId("id_pieza_dental")
                ->constrained(table: "pieza_dental", column: "id_pieza_dental")
                ->onDelete("cascade");
            
            $table->foreignId("id_condicion_dental")
                ->constrained(table: "condicion_dental", column: "id_condicion_dental")
                ->onDelete("cascade");

            $table->foreignId("id_consulta")
                ->constrained(table: "consulta", column: "id_consulta")
                ->onDelete("cascade");

             $table->foreignId("id_doctor")
                ->constrained(table: "doctor", column: "id_doctor")
                ->onDelete("cascade");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('odontrograma_registros');
    }
};
