<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCongesTable extends Migration
{
    public function up(): void
    {
        Schema::create('conges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->date('date_debut');
            $table->date('date_fin');
            $table->enum('type',['CP','RTT','Maladie']);
            $table->text('motif')->nullable();
            $table->string('justificatif')->nullable();
            $table->enum('statut',['En attente','Accepté','Refusé'])
                  ->default('En attente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conges');
    }
}
