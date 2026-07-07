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
        Schema::create('horario_atencion', function (Blueprint $table) {
            $table->id("id_horario_atencion");
            $table->string("dia_sem_horario_atencion", 50);
            $table->string("hor_ini_horario_atencion", 50);
            $table->string("hor_fin_horario_atencion", 50);
            $table->tinyInteger("est_horario_atencion")->default(1);

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
        Schema::dropIfExists('horario_atencion');
    }
};
