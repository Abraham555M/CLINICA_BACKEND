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
        Schema::create('notificacion', function (Blueprint $table) {
            $table->id("id_notificacion");
            $table->text("dat_notificacion");
            $table->tinyInteger("est_lei_notificacion")->default(1); // 1:Pendiente, 2:Leido
            $table->dateTime("fch_lei_notificacion")->nullable();   
            
            $table->foreignId("id_tipo_notificacion")
                ->constrained(table: "tipo_notificacion", column: "id_tipo_notificacion")
                ->onDelete("cascade");

            $table->foreignId("id_usuario")
                ->constrained(table: "usuario", column: "id_usuario")
                // Se elimina en cascada - si se elimina al usuario, afecta a la notificación
                ->onDelete("cascade")                 
                // Se actualiza en cascada - si se actualiza al usuario, afecta a la notificación
                ->onUpdate("cascade");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificacion');
    }
};
