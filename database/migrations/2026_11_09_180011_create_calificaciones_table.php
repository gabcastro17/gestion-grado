<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calificaciones', function (Blueprint $table) {

            $table->id();

            // asignación del jurado que realiza la evaluación
            $table->foreignId('jurado_id')
                ->constrained('jurados')
                ->cascadeOnDelete();

            // nota otorgada
            $table->decimal('nota', 5, 2);

            // observaciones de la evaluación
            $table->text('observaciones')->nullable();

            $table->timestamps();

            // cada jurado tendrá una calificación por asignación
            $table->unique('jurado_id');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calificaciones');
    }
};
