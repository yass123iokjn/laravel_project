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
            'reference' => 'required|string',
        ]);

        $file = $request->file('file');
        $reference = $request->input('reference');

        try {
            DB::beginTransaction();

            $filePath = $file->store('excel_files');

            
            $import = new ExcelImport($reference, $formulaId);
            Excel::import($import, $file);

            if (!empty($import->getErrors())) {
                DB::rollBack();
                return redirect()->route('formulas.importFile', $formulaId)
                    ->withErrors(['error' => implode('. ', $import->getErrors())]);
            }

            
            Result::where('formula_id', $formulaId)->delete();

            $excelFile = ExcelFile::create([
                'user_id' => Auth::id(),
                'file_path' => $filePath,
                'uploaded_at' => Carbon::now(),
            ]);

            $import->saveResults($excelFile->id);

            // Create a new calculation entry and get the ID
            $calcul = Calcul::create([
                'nom_calcul' => 'formule',
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

            // Update the calculation entry with the result ID
            $calcul->update(['result_id' => $result ? $result->id : null]);

            // Add calculation to the session
            $calculations = Calcul::where('formula_id', $formulaId)->get(); // Retrieve calculations for this formula
            session(['calculations' => $calculations]);

            DB::commit();

            return redirect()->route('formulas.importFile', $formulaId)
                ->with('success', 'File imported and calculations performed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('formulas.importFile', $formulaId)
                ->withErrors(['error' => 'Error importing or calculating: ' . $e->getMessage()]);
        }
    }

    public function results()
{
    $calculations = Calcul::with('formula', 'result')->get();
    return view('formulas.results', compact('calculations'));
}

}
