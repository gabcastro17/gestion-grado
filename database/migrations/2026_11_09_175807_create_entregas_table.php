<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entregas', function (Blueprint $table) {

            $table->id();

            // proyecto al que pertenece la entrega
            $table->foreignId('proyecto_id')
                ->constrained('proyectos')
                ->cascadeOnDelete();

            // información de la entrega
            $table->string('titulo');
            $table->text('descripcion')->nullable();

            // orden de las entregas
            $table->unsignedInteger('numero_orden')->default(1);

            // fechas
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_limite')->nullable();

            // estado de la entrega
            $table->string('estado')->default('pendiente');

            $table->timestamps();

            $table->index(['proyecto_id', 'estado']);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entregas');
    }
};
