<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proyectos', function (Blueprint $table) {

            $table->id();

            // solicitud que originó el proyecto
            $table->foreignId('solicitud_id')
                ->unique()
                ->constrained('solicitudes')
                ->restrictOnDelete();

            // estudiante responsable
            $table->foreignId('estudiante_id')
                ->constrained('users')
                ->restrictOnDelete();

            // profesor director
            $table->foreignId('profesor_id')
                ->constrained('users')
                ->restrictOnDelete();

            // propuesta de origen
            $table->foreignId('propuesta_id')
                ->constrained('propuestas')
                ->restrictOnDelete();

            // estado del proyecto
            $table->string('estado')->default('activo');

            // fechas del proyecto
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_finalizacion')->nullable();

            $table->timestamps();

            $table->index(['estudiante_id', 'estado']);
            $table->index(['profesor_id', 'estado']);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proyectos');
    }
};