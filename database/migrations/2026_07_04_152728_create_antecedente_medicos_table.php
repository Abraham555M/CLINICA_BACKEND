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
        Schema::create('antecedente_medico', function (Blueprint $table) {
            $table->id("id_antecedente");
            $table->text("des_antecedente")->nullable(); // Descripción antecedente
            $table->dateTime("fch_antecedente"); 
            $table->tinyInteger("est_antecedente")->default(1); 

            $table->foreignId("id_paciente")
                ->constrained(table: "paciente", column: "id_paciente")
                ->onDelete("cascade");
            
            $table->foreignId("id_tipo_antecedente")
                ->constrained(table: "tipo_antecedente", column: "id_tipo_antecedente")
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
        Schema::dropIfExists('antecedente_medico');
    }
};
