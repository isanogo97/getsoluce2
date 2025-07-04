<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyUsersTable extends Migration
{
    /**
     * Add enterprise_id and role to the users table.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('enterprise_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();
            $table->enum('role', ['employee', 'admin', 'creator'])
                  ->default('employee');
        });
    }

    /**
     * Drop enterprise_id and role from the users table.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['enterprise_id']);
            $table->dropColumn(['enterprise_id', 'role']);
        });
    }
}
