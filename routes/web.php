<?php
// routes/web.php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FormulaController;
use App\Http\Controllers\FileImportController;
use App\Http\Controllers\CalculController;
use Illuminate\Support\Facades\Route;


use Illuminate\Support\Facades\Auth;

// Redirect to login page when visiting the root URL
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('formulas.index'); // Redirect to formulas index if logged in
    }
    return redirect()->route('login'); // Redirect to login if not authenticated
});

// Grouping routes that require authentication
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/formulas', [FormulaController::class, 'index'])->name('formulas.index');
    Route::resource('formulas', FormulaController::class);
    Route::get('/formulas/{formula}/confirm_delete', [FormulaController::class, 'confirmDelete'])->name('formulas.confirmDelete');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/formulas/{formula}/import', [FileImportController::class, 'create'])->name('formulas.importFile');
Route::post('/formulas/{formula}/import', [FileImportController::class, 'store'])->name('formulas.import');

Route::post('/formulas/analyze', [FormulaController::class, 'analyzeAndTranslate'])->name('formulas.analyze');
Route::get('formulas/{id}/results', [FormulaController::class, 'showResults'])->name('formulas.results');
Route::get('formulas/{id}/graph', [FormulaController::class, 'showGraph'])->name('formulas.graph');

Route::delete('/formulas/import/{id}', [FileImportController::class, 'destroy'])->name('formulas.deleteImport');
Route::post('/formulas/{id}/generatePdf', [FormulaController::class, 'generatePdf'])->name('pdf.report');

// Require authentication routes
require __DIR__.'/auth.php';
