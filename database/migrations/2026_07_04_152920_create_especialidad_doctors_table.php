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
        Schema::create('especialidad_doctor', function (Blueprint $table) {
            $table->id("id_especialidad_doctor");

            $table->foreignId("id_doctor")
                ->constrained(table: "doctor", column: "id_doctor")
                ->onDelete("cascade");

            $table->foreignId("id_especialidad")
                ->constrained(table: "especialidad", column: "id_especialidad")
                ->onDelete("cascade");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('especialidad_doctor');
    }
};
