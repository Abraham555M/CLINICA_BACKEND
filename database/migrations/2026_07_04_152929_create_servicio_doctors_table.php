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
        Schema::create('servicio_doctor', function (Blueprint $table) {
            $table->id("id_servicio_doctor");

            $table->foreignId("id_servicio")
                ->constrained(table: "servicio", column: "id_servicio")
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
        Schema::dropIfExists('servicio_doctor');
    }
};
