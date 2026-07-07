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
        Schema::create('doctor', function (Blueprint $table) {
            $table->id("id_doctor");
            $table->string("cop_num_doctor")->unique(); // Número de colegiatura
            $table->text("bio_doctor")->nullable(); // Biografía
            $table->text("img_doctor")->nullable();
            
            $table->foreignId("id_usuario")
                ->constrained(table: "usuario", column: "id_usuario")
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
        Schema::dropIfExists('doctor');
    }
};
