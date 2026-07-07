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
        Schema::create('usuario', function (Blueprint $table) {
            $table->id("id_usuario");
            $table->string('nom_usuario');
            $table->string('ape_usuario');
            $table->string('ema_usuario')->unique();
            $table->string('doc_usuario')->unique();
            $table->string('pas_usuario');
            $table->tinyInteger("est_usuario");
            $table->rememberToken();

            $table->foreignId("id_rol")
                ->nullable()
                ->constrained(table: "rol", column: "id_rol")
                ->onDelete("set null");

            $table->foreignId("id_genero")
                ->nullable()
                ->constrained(table: "genero", column: "id_genero")
                ->onDelete("set null");

            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('ema_usuario')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('id_usuario')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuario');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
