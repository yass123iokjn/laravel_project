<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('formula_id');
            $table->unsignedBigInteger('excel_file_id');
            $table->json('result_data');
            $table->timestamp('calculated_at')->nullable();
            $table->timestamps();
            $table->string('reference')->nullable();

            $table->foreign('formula_id')->references('id')->on('formulas')->onDelete('cascade');
            $table->foreign('excel_file_id')->references('id')->on('excel_files')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
