<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

        // rol que tendrá el usuario
        $table->string('role')->default('estudiante');

        // identificador de Telegram
        $table->string('telegram_chat_id')->nullable()->unique();

        // indica si el usuario está activo
        $table->boolean('activo')->default(true);

    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropUnique(['telegram_chat_id']);

            $table->dropColumn([
                'role',
                'telegram_chat_id',
                'activo'
            ]);
        });
    }
};
