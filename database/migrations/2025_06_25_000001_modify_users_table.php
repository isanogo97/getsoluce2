<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyUsersTable extends Migration
{
    /**
     * Ajoute enterprise_id et role à la table users.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('enterprise_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();
            $table->enum('role', ['salarié', 'admin'])
                  ->default('salarié');
        });
    }

    /**
     * Supprime enterprise_id et role de la table users.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['enterprise_id']);
            $table->dropColumn(['enterprise_id', 'role']);
        });
    }
}
