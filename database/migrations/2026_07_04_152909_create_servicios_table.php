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
        Schema::create('servicio', function (Blueprint $table) {
            $table->id("id_servicio");
            $table->string("nom_servicio");
            $table->string("des_servicio", 200)->nullable();
            $table->tinyInteger("dur_min_servicio"); // duracion en minutos
            $table->decimal("prc_servicio", 8, 2);
            $table->tinyInteger("est_servicio")->default(1);

            $table->foreignId("id_tipo_servicio")
                ->constrained(table: "tipo_servicio", column: "id_tipo_servicio")
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
        Schema::dropIfExists('servicio');
    }
};
