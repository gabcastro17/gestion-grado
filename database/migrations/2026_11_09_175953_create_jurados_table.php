<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurados', function (Blueprint $table) {

            $table->id();

            // proyecto evaluado
            $table->foreignId('proyecto_id')
                ->constrained('proyectos')
                ->cascadeOnDelete();

            // profesor asignado como jurado
            $table->foreignId('profesor_id')
                ->constrained('users')
                ->restrictOnDelete();

            // fecha de asignación
            $table->timestamp('fecha_asignacion');

            $table->timestamps();

            // Un profesor no puede repetirse en el mismo proyecto
            $table->unique(['proyecto_id', 'profesor_id']);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurados');
    }
};