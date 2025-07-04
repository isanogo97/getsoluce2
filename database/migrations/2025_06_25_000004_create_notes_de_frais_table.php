<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotesDeFraisTable extends Migration
{
    public function up(): void
    {
        Schema::create('notes_de_frais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->date('date');
            $table->decimal('montant', 8, 2);
            $table->string('description');
            $table->string('justificatif')->nullable(); // autorise l'upload optionnel
            $table->enum('statut', ['En attente', 'Accepté', 'Refusé'])
                  ->default('En attente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notes_de_frais');
    }
}
