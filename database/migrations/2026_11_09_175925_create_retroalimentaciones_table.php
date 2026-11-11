<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('retroalimentaciones', function (Blueprint $table) {

            $table->id();

            // entrega que está siendo revisada
            $table->foreignId('entrega_id')
                ->constrained('entregas')
                ->cascadeOnDelete();

            // profesor que realiza la revisión
            $table->foreignId('profesor_id')
                ->constrained('users')
                ->restrictOnDelete();

            // comentario del profesor
            $table->text('comentario');

            // decisión de la revisión
            $table->string('estado')->default('observacion');

            $table->timestamps();

            $table->index(['entrega_id', 'profesor_id']);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retroalimentaciones');
    }
};