<?php

namespace App\Http\Controllers;

use App\Models\Formula;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ExcelFile;
use App\Models\Result;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Maatwebsite\Excel\Facades\Excel; // Assurez-vous d'importer la façade Excel
use App\Imports\ExcelImport; // Importez la classe ExcelImport
use App\Models\Calcul;

class FormulaController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $search = $request->input('search');

        if ($search) {
            $formulas = Formula::where('name', 'like', "%{$search}%")
                ->orWhere('expression', 'like', "%{$search}%")
                ->get();
        } else {
            $formulas = Formula::all();
        }

        return view('formulas.index', compact('formulas'));
    }

    public function create()
    {
        return view('formulas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'expression' => 'required|string',
        ]);

        $formula = new Formula();
        $formula->name = $request->name;
        $formula->expression = $request->expression;
        $formula->user_id = Auth::id();
        $formula->save();

        return redirect()->route('formulas.index')->with('success', 'Formule ajoutée avec succès!');
    }

    public function edit($id)
    {
        $formula = Formula::findOrFail($id);
        $this->authorize('update', $formula);
        return view('formulas.edit', compact('formula'));
    }

    public function update(Request $request, $id)
    {
        $formula = Formula::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'expression' => 'required|string|max:255',
        ]);

        $isExpressionChanged = $formula->expression !== $request->expression;

        if ($isExpressionChanged) {
            $formula->results()->delete();
        }

        $formula->update($request->only('name', 'expression'));

        return redirect()->route('formulas.index')->with('success', 'Formule mise à jour avec succès.');
    }

    public function destroy($id)
    {
        $formula = Formula::findOrFail($id);
        $this->authorize('delete', $formula);

        // Supprimer toutes les données liées à la formule
        $formula->results()->delete();
        $formula->delete();

        return redirect()->route('formulas.index')->with('success', 'Formule supprimée avec succès.');
    }

    public function import(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx',
            'reference' => 'required|string',
        ]);

        $formula = Formula::find($id);
        if (!$formula) {
            return redirect()->back()->withErrors(['error' => 'Formule non trouvée.']);
        }

        try {
            DB::beginTransaction();

            $file = $request->file('file');
            $filePath = $file->store('excel_files');

            // Importer le fichier Excel sans créer l'enregistrement tout de suite
            $import = new ExcelImport($request->input('reference'), $id);
            Excel::import($import, $file);

            // Vérifier les erreurs d'importation
            if (!empty($import->getErrors())) {
                DB::rollBack();
                return redirect()->back()->withErrors(['error' => implode('; ', $import->getErrors())]);
            }

            // Créer l'enregistrement du fichier Excel seulement si l'importation a réussi
            $excelFile = ExcelFile::create([
                'path' => $filePath,
                'name' => $file->getClientOriginalName(),
            ]);

            // Enregistrez les résultats avec l'ID du fichier Excel
            $import->saveResults($excelFile->id);

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Erreur lors de l\'importation du fichier Excel.']);
        }

        return redirect()->back()->with('success', 'Fichier importé et calculé avec succès.');
    }

    
    public function analyze(Request $request)
    {
        $request->validate([
            'sentence' => 'required|string',
        ]);

        $sentence = $request->input('sentence');

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/completions', [
                'model' => 'text-davinci-003',
                'prompt' => "Translate this sentence into a formula: " . $sentence,
                'max_tokens' => 60,
            ]);

            $data = $response->json();

            if (isset($data['choices'][0]['text'])) {
                return response()->json([
                    'success' => true,
                    'expression' => trim($data['choices'][0]['text']),
                ]);
            } else {
                return response()->json(['success' => false, 'message' => 'No valid response from API'], 500);
            }

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'API request failed: ' . $e->getMessage()], 500);
        }
    }

    public function confirmDelete($id)
    {
        $formula = Formula::findOrFail($id);
        return view('formulas.confirm_delete', compact('formula'));
    }

    //public function showResults($id)
//{
   // $cal = Calcul::where('id',$id)->where('reference',$ref)->get();
    //$formula = Formula::find($cal->formula_id);
    //$results = Result::with('formula')->where('calcul_id', $id)->get();
    
    // Convertir les résultats JSON en tableau PHP pour l'affichage
    //$results = $results->map(function ($result) {
        //$data = json_decode($result->result_data, true);
        //return array_merge(['result_data' => $data], ['id' => $result->id]);
    //});
    
    //return view('formulas.results', compact('cal','formula', 'results'));
//}
}
