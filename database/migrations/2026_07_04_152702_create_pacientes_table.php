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
        Schema::create('paciente', function (Blueprint $table) {
            $table->id("id_paciente");
            $table->string("tel_paciente", 9);
            $table->date("fch_nac_paciente"); 
            $table->decimal("sld_fav_paciente", 10, 2)->default(0.00); // Saldo a favor por cancelaciones a tiempo
                
            $table->foreignId("id_usuario")
                ->constrained(table: "usuario", column: "id_usuario")
                ->onDelete("cascade");

            $table->foreignId("id_usuario_responsable")
                ->constrained(table: "usuario", column: "id_usuario")
                ->restrictOnDelete();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paciente');
    }
};
