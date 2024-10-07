<?php
// routes/web.php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FormulaController;
use App\Http\Controllers\FileImportController;
use App\Http\Controllers\CalculController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/formulas', [FormulaController::class, 'index'])->name('formulas.index');
    // Routes pour les formules
    Route::resource('formulas', FormulaController::class);
    Route::get('/formulas/{formula}/confirm_delete', [FormulaController::class, 'confirmDelete'])->name('formulas.confirmDelete');


    // Routes pour le profil utilisateur
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    });

// Routes pour l'importation des fichiers
Route::get('/formulas/{formula}/import', [FileImportController::class, 'create'])->name('formulas.importFile');
Route::post('/formulas/{formula}/import', [FileImportController::class, 'store'])->name('formulas.import');

Route::post('/formulas/analyze', [FormulaController::class, 'analyzeAndTranslate'])->name('formulas.analyze');

Route::get('formulas/{id}/results', [FormulaController::class, 'showResults'])->name('formulas.results');

Route::get('formulas/{id}/graph', [FormulaController::class, 'showGraph'])->name('formulas.graph');


Route::delete('/formulas/import/{id}', [FileImportController::class, 'destroy'])->name('formulas.deleteImport');

require __DIR__.'/auth.php';
