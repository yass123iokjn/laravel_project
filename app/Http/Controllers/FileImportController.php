<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ExcelImport;
use App\Models\Formula;
use App\Models\Result;
use App\Models\ExcelFile;
use App\Models\Calcul;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FileImportController extends Controller
{
    public function create($formulaId)
    {
        $formula = Formula::findOrFail($formulaId);
        $calculations = Calcul::where('formula_id', $formulaId)->get(); // Récupérer les calculs pour cette formule
        return view('formulas.import', compact('formula', 'calculations'));
    }

    public function store(Request $request, $formulaId)
{
    $request->validate([
        'file' => 'required|mimes:xlsx',
        'reference' => 'required|string|unique:calculs,reference', // Vérification de l'unicité
        'nom_calcul' => 'required|string|max:255', // Add validation for nom_calcul
    ]);

    $file = $request->file('file');
    $reference = $request->input('reference');
    $nomCalcul = $request->input('nom_calcul'); // Get nom_calcul from request

    try {
        DB::beginTransaction();

        $filePath = $file->store('excel_files');

        $import = new ExcelImport($reference, $formulaId, $nomCalcul);
        Excel::import($import, $file);

        if (!empty($import->getErrors())) {
            DB::rollBack();
            return redirect()->route('formulas.importFile', $formulaId)
                ->withErrors(['error' => implode('. ', $import->getErrors())]);
        }

        $excelFile = ExcelFile::create([
            'user_id' => Auth::id(),
            'file_path' => $filePath,
            'uploaded_at' => Carbon::now(),
        ]);

        $import->saveResults($excelFile->id);

        // Create a new calculation record
        $calcul = Calcul::create([
            'nom_calcul' => $nomCalcul, // Use the value from the request
            'reference' => $reference,
            'excel_file_id' => $excelFile->id,
            'formula_id' => $formulaId,
            'result_id' => null, // Set to null initially
        ]);

        // Update the result with the newly created calculation ID
        $result = Result::where('formula_id', $formulaId)->latest()->first();
        if ($result) {
            $result->update(['calcul_id' => $calcul->id]);
        }

        // Update the calculation record with the result ID
        $calcul->update(['result_id' => $result ? $result->id : null]);

        // Add the calculation to the session
        $calculations = Calcul::where('formula_id', $formulaId)->get(); // Récupérer les calculs pour cette formule
        session(['calculations' => $calculations]);

        DB::commit();

        return redirect()->route('formulas.importFile', $formulaId)
            ->with('success', 'Fichier importé et calculs effectués avec succès.');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Erreur lors de l\'importation ou du calcul : ' . $e->getMessage()); // Log the error message
        return redirect()->route('formulas.importFile', $formulaId)
            ->withErrors(['error' => 'Erreur lors de l\'importation ou du calcul : ' . $e->getMessage()]);
    }
}


    public function results()
    {
        $calculations = Calcul::with('formula', 'result')->get();
        return view('formulas.results', compact('calculations'));
    }

    public function destroy($id)
    {
        $calculation = Calcul::findOrFail($id);
        
        // Remove the associated Result if needed
        if ($calculation->result_id) {
            Result::destroy($calculation->result_id);
        }
        
        $calculation->delete();

        return redirect()->route('formulas.importFile', $calculation->formula_id)
            ->with('success', 'Importation supprimée avec succès.');
    }
}
