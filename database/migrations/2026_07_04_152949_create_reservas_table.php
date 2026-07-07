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
        Schema::create('reserva', function (Blueprint $table) {
            $table->id("id_reserva");
            $table->date("fch_reserva");
            $table->string("hor_reserva");
            $table->string("tok_cancelacion")->nullable(); // token de cancelación
            $table->date("fch_con_reserva")->nullable(); // fecha de confirmación 
            $table->date("fch_can_reserva")->nullable(); // fecha de cancelación

            $table->foreignId("id_paciente")
                ->constrained(table: "paciente", column: "id_paciente")
                ->onDelete("cascade");

            $table->foreignId("id_doctor")
                ->constrained(table: "doctor", column: "id_doctor")
                ->onDelete("cascade");

            $table->foreignId("id_servicio")
                ->constrained(table: "servicio", column: "id_servicio")
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
        Schema::dropIfExists('reserva');
    }
};
