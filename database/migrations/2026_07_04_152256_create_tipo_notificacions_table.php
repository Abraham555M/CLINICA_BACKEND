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
        Schema::create('tipo_notificacion', function (Blueprint $table) {
            $table->id("id_tipo_notificacion");
            $table->string("nom_tipo_notificacion", 100)->unique();
            $table->tinyInteger("est_tipo_notificacion")->default(1); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipo_notificacion');
    }
};
