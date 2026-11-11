<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('propuestas', function (Blueprint $table) {

            $table->id();

            // profesor que publica la propuesta
            $table->foreignId('profesor_id')
                ->constrained('users')
                ->restrictOnDelete();

            // información de la propuesta
            $table->string('titulo');
            $table->text('descripcion');
            $table->text('introduccion')->nullable();
            $table->text('objetivos')->nullable();

            // estado de publicación
            $table->string('estado')->default('borrador');

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('propuestas');
    }
};