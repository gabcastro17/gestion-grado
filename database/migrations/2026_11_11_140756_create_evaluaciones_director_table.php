<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


//aca se guardaran todas las calificaciones del director, incluyendo las que no han sido aprobadas
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluaciones_director', function (Blueprint $table) {
            $table->id();

            $table->foreignId('proyecto_id')
                ->constrained('proyectos')
                ->cascadeOnDelete();

            $table->foreignId('profesor_id')
                ->constrained('users');

            $table->decimal('nota', 3, 2);

            $table->text('observaciones')->nullable();

            $table->date('nueva_fecha_limite')->nullable();

            $table->unsignedInteger('numero_intento');

            $table->timestamps();

            $table->unique(['proyecto_id', 'numero_intento']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluaciones_director');
    }
};