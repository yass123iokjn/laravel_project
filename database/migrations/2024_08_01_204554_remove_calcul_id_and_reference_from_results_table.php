<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveCalculIdAndReferenceFromResultsTable extends Migration
{
    public function up()
    {
        Schema::table('results', function (Blueprint $table) {
            if (Schema::hasColumn('results', 'calcul_id')) {
                $table->dropForeign(['calcul_id']); // Supprimez d'abord la clé étrangère
                $table->dropColumn('calcul_id'); // Puis la colonne elle-même
            }
            if (Schema::hasColumn('results', 'reference')) {
                $table->dropColumn('reference');
            }
        });
    }

    public function down()
    {
        Schema::table('results', function (Blueprint $table) {
 
            $table->unsignedBigInteger('calcul_id')->after('excel_file_id');
            $table->string('reference')->after('calcul_id');
            
      
            $table->foreign('calcul_id')->references('id')->on('calculus')->onDelete('cascade');
        });
    }
}
