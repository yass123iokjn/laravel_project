<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCalculIdToResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('results', function (Blueprint $table) {
            // Ajouter la colonne calcul_id
            $table->unsignedBigInteger('calcul_id')->after('id');

            // Ajouter la contrainte de clé étrangère pour calcul_id
            $table->foreign('calcul_id')->references('id')->on('calculs')->onDelete('cascade');

            // Supprimer la clé étrangère de formula_id si elle existe
            $table->dropForeign(['formula_id']);
            
            // Supprimer la colonne formula_id
            $table->dropColumn('formula_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('results', function (Blueprint $table) {
            // Ajouter à nouveau la colonne formula_id
            $table->unsignedBigInteger('formula_id')->after('id');
            
            // Ajouter la contrainte de clé étrangère pour formula_id
            $table->foreign('formula_id')->references('id')->on('formulas')->onDelete('cascade');

            // Supprimer la clé étrangère de calcul_id
            $table->dropForeign(['calcul_id']);
            
            // Supprimer la colonne calcul_id
            $table->dropColumn('calcul_id');
        });
    }
}
