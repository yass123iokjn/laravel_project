<?php
// database/migrations/xxxx_xx_xx_create_calculs_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCalculsTable extends Migration
{
    public function up()
    {
        Schema::create('calculs', function (Blueprint $table) {
            $table->id();
            $table->string('nom_calcul');
            $table->string('reference')->unique();
            $table->foreignId('excel_file_id')->constrained()->onDelete('cascade');
            $table->foreignId('formula_id')->constrained()->onDelete('cascade');
            $table->foreignId('result_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('calculs');
    }
}
