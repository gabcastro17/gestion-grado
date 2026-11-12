<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('telegram_link_codes', function (Blueprint $table) {
            if (!Schema::hasColumn('telegram_link_codes', 'user_id')) {
                $table->foreignId('user_id')
                    ->nullable()
                    ->constrained('users')
                    ->cascadeOnDelete();
            }

            if (!Schema::hasColumn('telegram_link_codes', 'code_hash')) {
                $table->string('code_hash', 255)->nullable();
            }

            if (!Schema::hasColumn('telegram_link_codes', 'expires_at')) {
                $table->timestamp('expires_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        // No eliminamos columnas para proteger los datos existentes.
    }
};
