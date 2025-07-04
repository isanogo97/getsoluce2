<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyUsersTable extends Migration
{
    /**
     * Ajoute entreprise_id et role à la table users.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('entreprise_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();
            $table->enum('role', ['salarié', 'admin'])
                  ->default('salarié');
        });
    }

    /**
     * Supprime entreprise_id et role de la table users.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['entreprise_id']);
            $table->dropColumn(['entreprise_id', 'role']);
        });
    }
}
