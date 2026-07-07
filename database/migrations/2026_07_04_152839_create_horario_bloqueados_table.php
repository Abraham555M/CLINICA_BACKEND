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
        Schema::create('horario_bloqueado', function (Blueprint $table) {
            $table->id("id_horario_bloqueado");
            $table->date("fch_blq_horario_bloqueado");
            $table->string("hor_ini_horario_bloqueado");
            $table->string("hor_fin_horario_bloqueado");
            $table->text("mot_horario_bloqueado")->nullable(); 

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
        Schema::dropIfExists('horario_bloqueado');
    }
};
