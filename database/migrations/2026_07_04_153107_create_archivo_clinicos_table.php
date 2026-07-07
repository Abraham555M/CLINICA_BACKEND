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
        Schema::create('archivo_clinico', function (Blueprint $table) {
            $table->id("id_archivo_clinico");
            $table->string("nom_archivo"); 
            $table->text("url_archivo"); 
            $table->date("fch_crg_archivo");
            $table->string("tip_archivo"); // Tipo de archivo: imagen, pdf, laboratorio 
            
            $table->foreignId("id_consulta")
                ->constrained(table: "consulta", column: "id_consulta")
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
        Schema::dropIfExists('archivo_clinico');
    }
};
