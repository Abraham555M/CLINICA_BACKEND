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
        Schema::create('condicion_dental', function (Blueprint $table) {
            $table->id("id_condicion_dental");
            $table->string("nom_condicion_dental");
            $table->string("cod_col_condicion_dental", 20); // codigo color hexadecimal
            $table->tinyInteger("est_condicion_dental")->default(1);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('condicion_dentals');
    }
};
