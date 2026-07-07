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
        Schema::create('pieza_dental', function (Blueprint $table) {
            $table->id("id_pieza_dental");
            $table->integer("num_pieza_dental");
            $table->string("nom_pieza_dental");
            $table->string('tip_det_pieza_dental', 30); 
            $table->tinyInteger("est_pieza_dental")->default(1);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pieza_dentals');
    }
};
