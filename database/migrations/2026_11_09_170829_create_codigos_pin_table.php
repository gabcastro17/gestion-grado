<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('codigos_pin', function (Blueprint $table) {

            $table->id();

            // usuario al que pertenece el PIN
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // código PIN almacenado como hash
            $table->string('codigo');

            // fecha y hora en que vence el PIN
            $table->timestamp('expiracion');

            // indica si el PIN ya fue utilizado
            $table->boolean('usado')->default(false);

            // fecha de creación y actualización
            $table->timestamps();

            // facilita la búsqueda de códigos activos de un usuario
            $table->index(['user_id', 'usado']);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('codigos_pin');
    }
};