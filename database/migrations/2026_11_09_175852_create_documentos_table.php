<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos', function (Blueprint $table) {

            $table->id();

            // entrega a la que pertenece el documento
            $table->foreignId('entrega_id')
                ->constrained('entregas')
                ->cascadeOnDelete();

            // estudiante que realizó el envío
            $table->foreignId('estudiante_id')
                ->constrained('users')
                ->restrictOnDelete();

            // nombre original del archivo
            $table->string('nombre_archivo');

            // ubicación del archivo en el almacenamiento
            $table->string('ruta_archivo')->nullable();

            // enlace externo opcional
            $table->text('enlace')->nullable();

            // tipo de documento
            $table->string('tipo_archivo');

            $table->timestamps();

            $table->index(['entrega_id', 'estudiante_id']);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};