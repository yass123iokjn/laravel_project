<?php
namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Result;
use App\Models\Formula;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ExcelImport implements ToModel, WithHeadingRow
{
    protected $reference;
    protected $formulaId;
    protected $errors = [];
    protected $results = [];
    protected $evaluatedExpressions = [];
    protected $headers = []; // Nouvelle propriété pour stocker les en-têtes

    public function __construct($reference, $formulaId)
    {
        $this->reference = $reference;
        $this->formulaId = $formulaId;
    }

    public function model(array $row)
    {
        // Assurez-vous que les en-têtes sont capturés avant le traitement des lignes
        if (empty($this->headers)) {
            $this->headers = array_keys($row);
        }

        $formula = Formula::find($this->formulaId);
        if (!$formula) {
            $this->addError('Formule non trouvée.');
            return null;
        }

        $resultData = $this->applyFormula($formula->expression, $row);

        if (isset($resultData['error'])) {
            $this->addError($resultData['error']);
            return null;
        }

        $this->results[] = [
            'result_data' => $resultData['expression'], // Stocker l'expression avec le résultat
        ];

        // Log des résultats avant sauvegarde
        Log::info('Résultat calculé :', [
            'result_data' => $resultData['expression'], // Log l'expression avec le résultat
        ]);

        return null;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    protected function applyFormula($expression, $row)
    {
        $data = array_map('floatval', $row);
        foreach ($data as $key => $value) {
            $expression = str_replace($key, $value, $expression);
        }
        $this->evaluatedExpressions[] = $expression;

        Log::info('Expression avant évaluation : ' . $expression);
        Log::info($this->evaluatedExpressions);

        $error = null;
        $result = 0;

        try {
            $result = eval('return ' . $expression . ';');
        } catch (\Throwable $e) {
            Log::error('Erreur d\'évaluation : ' . $e->getMessage());
            $error = 'Erreur lors de l\'évaluation de l\'expression.';
        }

        if ($error) {
            return ['error' => $error];
        }

        // Construire l'expression évaluée avec le résultat
        $expressionWithResult = $expression . ' = ' . $result;

        return ['result' => $result, 'expression' => $expressionWithResult];
    }

    public function saveResults($excelFileId)
    {
        Log::info('Résultats à sauvegarder :', ['results' => $this->results]);

        $resultsGroupedByFormula = [];

        foreach ($this->results as $result) {
            $formulaId = $this->formulaId;

            if (!isset($resultsGroupedByFormula[$formulaId])) {
                $resultsGroupedByFormula[$formulaId] = [];
            }

            $resultsGroupedByFormula[$formulaId][] = $result['result_data'];
        }

        foreach ($resultsGroupedByFormula as $formulaId => $results) {
            Log::info('Sauvegarde des résultats pour la formule ID : ' . $formulaId, ['results' => $results]);

            Result::create([
                'excel_file_id' => $excelFileId,
                'result_data' => json_encode($results),
                'calculated_at' => Carbon::now(),
                'formula_id' => $this->formulaId,
            ]);
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }

    protected function addError($error)
    {
        if (!in_array($error, $this->errors)) {
            $this->errors[] = $error;
        }
    }
}
