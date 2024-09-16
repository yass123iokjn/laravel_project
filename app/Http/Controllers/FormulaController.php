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
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;
use Exception;

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

    // Vérifiez que vous passez bien la variable $formulas à la vue
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

        // Importer le fichier Excel
        $import = new ExcelImport($request->input('reference'), $id);
        Excel::import($import, $file);

        // Obtenez les en-têtes
        $headers = $import->getHeaders();
        Log::info('En-têtes du fichier Excel : ', ['headers' => $headers]);

        // Vérifiez les erreurs d'importation
        if (!empty($import->getErrors())) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => implode('; ', $import->getErrors())]);
        }

        // Créez l'enregistrement du fichier Excel seulement si l'importation a réussi
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

    public function analyzeAndTranslate(Request $request)
    {
        try {
            // Log de début de la méthode
            Log::info('Début de la méthode analyzeAndTranslate');

            $sentence = $request->input('sentence');

            // Log de la phrase reçue
            Log::info('Phrase reçue : ' . $sentence);

            // Valider que la phrase est bien envoyée
            if (!$sentence) {
                Log::error('La phrase est manquante');
                return response()->json(['error' => 'La phrase est manquante.'], 400);
            }

            // Appelez l'API OpenAI pour analyser et traduire la phrase
            $response = OpenAI::completions()->create([
                'model' => 'text-davinci-003',
                'prompt' => "Analyser la phrase suivante et traduisez-la en une formule mathématique: {$sentence}",
                'max_tokens' => 100,
                'temperature' => 0.5,
            ]);

            // Log de la réponse de l'API OpenAI
            Log::info('Réponse OpenAI : ' . json_encode($response));

            // Extraire la formule de la réponse OpenAI
            if (isset($response['choices'][0]['text'])) {
                $formula = $response['choices'][0]['text'];
                Log::info('Formule générée : ' . $formula);
                return response()->json(['formula' => trim($formula)]);
            } else {
                Log::error('Aucune formule générée par OpenAI');
                return response()->json(['error' => 'Aucune formule générée.'], 500);
            }
        } catch (Exception $e) {
            // Enregistrer l'erreur dans les logs
            Log::error('Erreur dans analyzeAndTranslate : ' . $e->getMessage());

            // Retourner une réponse JSON avec l'erreur
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function confirmDelete($id)
    {
        $formula = Formula::findOrFail($id);
        return view('formulas.confirm_delete', compact('formula'));
    }

    public function showResults($id)
{
    // Récupérer le calcul avec l'ID spécifié et charger ses résultats
    $calculation = Calcul::with('results')->findOrFail($id);

    // Initialiser un tableau vide pour les données de résultats
    $resultsData = [];

    // Parcourir les résultats associés au calcul
    foreach ($calculation->results as $result) {
        $resultData = $result->result_data; // Ce champ est un JSON, donc on le décode

        // Vérifier si $resultData est une chaîne JSON ou déjà un tableau
        if (is_string($resultData)) {
            $resultData = json_decode($resultData, true); // Décoder le JSON en tableau
        }

        if (is_array($resultData)) {
            foreach ($resultData as $item) {
                // Supposons que $item est une chaîne de caractères au format "op1 op2 ... = result"
                // Diviser la chaîne en utilisant le signe "=" pour séparer les opérandes du résultat
                $parts = explode('=', $item);
                if (count($parts) === 2) {
                    $operands = trim($parts[0]);
                    $result = trim($parts[1]);

                    // Supprimer les signes d'opération et conserver uniquement les nombres
                    $operandParts = preg_split('/\D+/', $operands, -1, PREG_SPLIT_NO_EMPTY);

                    // Ajouter au tableau des résultats
                    $resultsData[] = [
                        'operands' => $operandParts,
                        'result' => $result,
                    ];
                }
            }
        }
    }

    // Retourner la vue avec les données de résultats et l'ID de calcul
    return view('formulas.results', compact('resultsData', 'id'));
}







public function showGraph($id)
{
    $calcul = Calcul::with('result')->findOrFail($id);
    
    // Vérifiez si result_data est déjà un tableau
    if (is_string($calcul->result->result_data)) {
        $resultsData = json_decode($calcul->result->result_data, true);
    } else {
        $resultsData = $calcul->result->result_data; // Si c'est déjà un tableau
    }
 
    return view('formulas.graph', compact('resultsData', 'calcul'));
}




    
}
