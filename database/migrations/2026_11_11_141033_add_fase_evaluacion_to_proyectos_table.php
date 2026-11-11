<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {
            $table->string('fase_evaluacion')
                ->default('pendiente_evaluacion_1');

            $table->date('fecha_limite_correccion')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {
            $table->dropColumn([
                'fase_evaluacion',
                'fecha_limite_correccion',
            ]);
        });
    }
};