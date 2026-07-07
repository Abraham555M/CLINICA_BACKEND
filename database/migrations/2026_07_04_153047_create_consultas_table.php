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
        Schema::create('consulta', function (Blueprint $table) {
            $table->id("id_consulta");
            $table->date("fch_consulta");
            $table->text("mot_consulta");
            $table->string("dig_consulta");
            $table->string("tra_consulta");
            $table->text("obs_consulta")->nullable();
            $table->tinyInteger("est_consulta")->default(1); 

            $table->foreignId("id_reserva")
                ->constrained(table: "reserva", column: "id_reserva")
                ->onDelete("cascade");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consulta');
    }
};
