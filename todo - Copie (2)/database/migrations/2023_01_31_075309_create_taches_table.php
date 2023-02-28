<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTachesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taches', function (Blueprint $table) {
            $table->id(); // ID
            $table->string('titre', 100); // Titre de max 100 caractères
            $table->text('contenu')->nullable(); // Contenu détaillé, peut être NULL
            $table->integer('ordre')->default(0); // Ordre, par défaut 0
            $table->boolean('complet')->default(0); // Tâche terminée, par défaut 0 (false)
            $table->dateTime('date_fin')->useCurrent(); // Date de fin, par défaut heure et date actuelle
            $table->timestamps(); // Ajoute le champs mouchards created_at et updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('taches');
    }
}
