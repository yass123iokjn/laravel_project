<!-- report.blade.php -->
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport de Formule</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
        }

        .title {
            font-size: 20px;
            font-weight: bold;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .table th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <h1 class="title">Rapport de la Formule : {{ $formula->name }}</h1>
    <p>Expression : {{ $formula->expression }}</p>
    <p>Fichier Excel utilisé : {{ $excelFile->name }}</p>

    <h2>Résultats du Calcul :</h2>
    <table class="table">
        <thead>
            <tr>
                @if(!empty($headers) && is_array($headers))
                @foreach($headers as $header)
                <th>{{ ucfirst($header) }}</th>
                @endforeach
                <th>Résultat</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($resultsData as $result)
            <tr>
                @foreach($result as $operand)
                <td class="border px-4 py-2">{{ is_array($operand) ? implode(', ', $operand) : $operand }}</td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Affichage graphique</h2>
@if($chartImage)
    <img src="{{ $chartImage }}" alt="Graphique" style="max-width: 100%; height: auto;">
@endif


</body>

</html>