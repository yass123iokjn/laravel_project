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
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;
use Exception;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

class FormulaController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $search = $request->input('search');
        $searchType = $request->input('search_type', 'expression');

        $formulas = Formula::query();

        if ($search) {
            if ($searchType == 'name') {
                $formulas = $formulas->where('name', 'like', '%' . $search . '%');
            } else {
                $formulas = $formulas->where('expression', 'like', '%' . $search . '%');
            }
        }

        $formulas = $formulas->paginate(6);

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
            'nom_calcul' => 'required|string|max:255', // Validation pour le nom de calcul
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
            $import = new ExcelImport($request->input('reference'), $id, $request->input('nom_calcul')); // Passer le nom_calcul
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

    public function analyzeAndTranslate(Request $request)
    {
        $sentence = $request->input('sentence');
        $request->validate([
            'sentence' => 'required|string',
        ]);

        // Validation de l'entrée
        if (empty($sentence)) {
            return response()->json(['error' => 'La phrase ne peut pas être vide.'], 400);
        }

        $appId = config('services.wolfram.alpha.appid');
        if (empty($appId)) {
            return response()->json(['error' => 'Clé API Wolfram Alpha non configurée.'], 400);
        }

        $url = "https://api.wolframalpha.com/v2/query?input=" . urlencode($sentence) . "&format=plaintext&output=JSON&appid=" . $appId;

        try {
            $response = file_get_contents($url);
            Log::info('Response from Wolfram Alpha: ', ['response' => $response]); // Log de la réponse brute
            $data = json_decode($response, true);
            Log::info('Decoded response from Wolfram Alpha: ', $data);

            if (!isset($data['queryresult'])) {
                return response()->json(['error' => 'Aucune réponse valide reçue.'], 400);
            }

            if ($data['queryresult']['success']) {
                // Vérifiez si le pod de résultat contient des données
                if (isset($data['queryresult']['pods']) && count($data['queryresult']['pods']) > 0) {
                    foreach ($data['queryresult']['pods'] as $pod) {
                        if (isset($pod['subpods'][0]['plaintext'])) {
                            $formula = $pod['subpods'][0]['plaintext'];
                            // Simplify the formula
                            $simplifiedFormula = $this->simplifyFormula($formula);
                            return response()->json(['formula' => $simplifiedFormula]);
                        }
                    }
                }
                return response()->json(['error' => 'Aucune formule trouvée.'], 400);
            } else {
                return response()->json(['error' => 'Aucune formule trouvée.'], 400);
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'analyse avec Wolfram Alpha : ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de l\'analyse.'], 500);
        }
    }

    // Function to simplify the formula
    private function simplifyFormula($formula)
    {
        $replacements = [
            'sum()' => '+',
            'sqrt' => '√',
            'abs' => '|',
            'log10(' => 'log(',
            'log(' => 'ln(',
            'sin' => 'sin',
            'cos' => 'cos',
            'tan' => 'tan',
            '^' => '**',
            'exp(' => 'exp(',
        ];

        // Perform the replacements
        return str_replace(array_keys($replacements), array_values($replacements), $formula);
    }







    public function showResults($id)
    {
        $calculation = Calcul::with('results')->findOrFail($id);

        $resultsData = [];
        $headers = [];

        foreach ($calculation->results as $result) {
            // Supposons que result_data est toujours un tableau
            $resultData = $result->result_data;

            if (is_array($resultData)) {
                // Vérifiez si les en-têtes sont présents
                if (isset($resultData['headers'])) {
                    $headers = $resultData['headers']; // Stocker les en-têtes
                }

                // Ajoutez les résultats
                if (isset($resultData['results'])) {
                    foreach ($resultData['results'] as $item) {
                        if (is_array($item)) {
                            $resultsData[] = $item; // Ajouter les résultats
                        }
                    }
                }
            }
        }

        return view('formulas.results', compact('resultsData', 'id', 'headers'));
    }




    public function showGraph($id)
    {
        $calculation = Calcul::with('results')->findOrFail($id);
        $formula = $calculation->formula;
        $resultsData = [];
        $headers = [];

        foreach ($calculation->results as $result) {
            $resultData = $result->result_data;

            // Vérifiez que $resultData est un tableau et contient les clés attendues
            if (is_array($resultData)) {
                if (isset($resultData['headers'])) {
                    $headers = $resultData['headers']; // En-têtes des colonnes
                }

                if (isset($resultData['results'])) {
                    foreach ($resultData['results'] as $item) {
                        // Ajoutez une vérification ici
                        if (is_array($item) && !empty($item)) {
                            $resultsData[] = $item; // Ajouter les résultats
                        } else {
                            Log::info('Item is not an array or is empty', ['item' => $item]);
                        }
                    }
                } else {
                    Log::info('No results found in result_data', ['resultData' => $resultData]);
                }
            } else {
                Log::info('result_data is not an array', ['resultData' => $resultData]);
            }
        }

        // Vérifiez le contenu de $resultsData
        Log::info('Results Data:', ['resultsData' => $resultsData]);

        return view('formulas.graph', compact('formula', 'headers', 'resultsData'));
    }



    public function generatePdf(Request $request, $id)
    {
        $formula = Formula::findOrFail($id);
        $calcul = Calcul::where('formula_id', $id)->latest()->first();

        if (!$calcul) {
            return redirect()->back()->withErrors(['error' => 'Aucun calcul trouvé pour cette formule.']);
        }

        // Récupérer le fichier Excel et les résultats associés
        $excelFile = ExcelFile::find($calcul->excel_file_id);
        $results = Result::where('formula_id', $id)->get();

        // Préparer les données pour $resultsData et $headers
        $resultsData = [];
        $headers = [];

        if ($results->isNotEmpty()) {
            foreach ($results as $result) {
                $resultData = $result->result_data;

                if (is_array($resultData)) {
                    if (isset($resultData['headers'])) {
                        $headers = $resultData['headers'];
                    }
                    if (isset($resultData['results'])) {
                        foreach ($resultData['results'] as $item) {
                            if (is_array($item) && !in_array($item, $resultsData)) {
                                $resultsData[] = $item;
                            }
                        }
                    }
                }
            }
        }

        // Récupérer l'image du graphique (base64) depuis la requête
        $chartImage = $request->input('chartImage'); // L'image est en format Base64

        // Générer le PDF en incluant l'image du graphique
        $pdf = Pdf::loadView('pdf.report', compact('formula', 'excelFile', 'resultsData', 'headers', 'chartImage'));

        return $pdf->download('rapport.pdf');
    }
}
