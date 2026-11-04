<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            // Agregamos las columnas que tu controlador está buscando
            if (!Schema::hasColumn('password_reset_tokens', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable();
            }
            if (!Schema::hasColumn('password_reset_tokens', 'expiry_date')) {
                $table->timestamp('expiry_date')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'expiry_date']);
        });
    }
};