<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFormulaIdToResultsInResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('results', function (Blueprint $table) {
            // Ajoutez la colonne formula_id
            $table->unsignedBigInteger('formula_id')->after('id');

            // Définir la clé étrangère
            $table->foreign('formula_id')->references('id')->on('formulas')->onDelete('cascade');
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
            // Supprimer la clé étrangère
            $table->dropForeign(['formula_id']);

            // Supprimer la colonne
            $table->dropColumn('formula_id');
        });
    }
}
