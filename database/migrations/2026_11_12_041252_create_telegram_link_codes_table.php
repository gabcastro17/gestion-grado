
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('telegram_link_codes')) {
            Schema::create('telegram_link_codes', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // No eliminamos la tabla porque otra migración
        // también es responsable de crearla.
    }
};
