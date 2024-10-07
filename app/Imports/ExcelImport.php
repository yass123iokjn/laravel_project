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
    protected $nomCalcul; // Consider using or removing if unnecessary
    protected $errors = [];
    protected $results = [];
    protected $evaluatedExpressions = [];
    protected $headers = [];

    public function __construct($reference, $formulaId, $nomCalcul)
    {
        $this->reference = $reference;
        $this->formulaId = $formulaId;
        $this->nomCalcul = $nomCalcul;
    }

    public function model(array $row)
    {
        // Initialize headers on the first row
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

        // Store only input values and the result
        $this->results[] = array_merge(
            array_values($row), // Column values
            [$resultData['result']] // Add the result
        );

        return null; // Indicate no model to return
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    protected function applyFormula($expression, $row)
    {
        // Replace each header in the expression with its corresponding value in the row
        foreach ($this->headers as $header) {
            if (array_key_exists($header, $row)) {
                $expression = str_replace($header, floatval($row[$header]), $expression);
            }
        }

        Log::info('Expression avant évaluation : ' . $expression);
        $result = 0;

        try {
            // Evaluate the expression
            $result = eval('return ' . $expression . ';');
        } catch (\Throwable $e) {
            Log::error('Erreur d\'évaluation : ' . $e->getMessage());
            return ['error' => 'Erreur lors de l\'évaluation de l\'expression.'];
        }

        return ['result' => $result, 'expression' => $expression];
    }

    public function saveResults($excelFileId)
    {
        Log::info('Résultats à sauvegarder :', ['results' => $this->results]);

        Result::create([
            'excel_file_id' => $excelFileId,
            'result_data' => json_encode([ // Encode array to JSON
                'headers' => $this->headers,
                'results' => $this->results,
            ]),
            'calculated_at' => Carbon::now(),
            'formula_id' => $this->formulaId,
        ]);
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
