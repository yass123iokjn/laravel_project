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
    protected $headers = [];

    public function __construct($reference, $formulaId)
    {
        $this->reference = $reference;
        $this->formulaId = $formulaId;
    }

    public function model(array $row)
{
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

    // Stocker uniquement les valeurs d'entrée et le résultat
    $this->results[] = array_merge(
        array_values($row), // Valeurs des colonnes Ex, F, G
        [$resultData['result']] // Ajouter le résultat
    );

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

    Log::info('Expression avant évaluation : ' . $expression);
    $result = 0;

    try {
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
        'result_data' => json_encode([
            'headers' => $this->headers,
            'results' => $this->results,
        ]), // Encode the array to a JSON string
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
