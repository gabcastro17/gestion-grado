<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes', function (Blueprint $table) {

            $table->id();

            // estudiante que realiza la solicitud
            $table->foreignId('estudiante_id')
                ->constrained('users')
                ->restrictOnDelete();

            // propuesta seleccionada
            $table->foreignId('propuesta_id')
                ->constrained('propuestas')
                ->restrictOnDelete();

            // estado de la solicitud
            $table->string('estado')->default('pendiente');

            // mensaje opcional del estudiante
            $table->text('mensaje')->nullable();

            // fechas de solicitud y respuesta
            $table->timestamp('fecha_solicitud');
            $table->timestamp('fecha_respuesta')->nullable();

            $table->timestamps();

            $table->index(['estudiante_id', 'estado']);
            $table->index(['propuesta_id', 'estado']);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes');
    }
};