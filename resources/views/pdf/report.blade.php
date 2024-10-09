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
            margin: 0;
            padding: 20px; /* Ajout d'un peu de padding pour le corps */
            border: 2px solid black; /* Bordure noire pour la page */
        }

        .title {
            font-size: 20px; /* Taille du titre */
            font-style: italic; /* Italique */
            color: #3A8EBA; /* Couleur rouge */
            text-align: center; /* Centré */
            margin: 20px 0; /* Marges autour du titre */
        }

        .section-title {
            font-size: 16px; /* Taille des sous-titres */
            font-weight: bold; /* Gras */
            margin-top: 20px; /* Marge au-dessus des sous-titres */
        }

        p {
            font-size: 14px; /* Taille pour tous les paragraphes */
            margin: 10px 0; /* Marges autour des paragraphes */
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

        .chart-container {
            text-align: center; /* Centre l'image du graphique */
            margin: 20px 0; /* Marge autour du graphique */
        }

        .chart {
            border: 2px solid black; /* Bordure noire autour de l'image */
            max-width: 90%; /* Taille maximale */
            height: auto; /* Hauteur automatique */
            margin: 0 auto; /* Centrer l'image */
            display: block; /* Bloquer l'image pour centrer */
        }

        .caption {
            font-size: 12px; /* Taille de la légende */
            text-align: center; /* Centré */
            margin-top: 5px; /* Marge au-dessus de la légende */
        }

        .page-number {
            position: absolute; /* Positionnement absolu pour la numérotation de la page */
            bottom: 20px; /* Position par rapport au bas de la page */
            right: 20px; /* Position par rapport à la droite de la page */
            font-size: 12px; /* Taille de la police */
            border-top: 1px solid #ddd; /* Bordure supérieure */
            padding-top: 5px; /* Espacement au-dessus du texte */
        }
    </style>
</head>

<body>
    <h1 class="title">Rapport de la Formule : {{ $formula->name }}</h1>
    <p class="section-title">Expression :<strong style="color:#000080">{{ $formula->expression }}</strong></p>
    
    <p class="section-title">Fichier Excel utilisé :<strong style="color:#000080">{{ $excelFile->name }}</strong></p>
    

    <h2 class="section-title">Résultats du Calcul :</h2>
    <table class="table">
        <caption>Tableau des résultats</caption> <!-- Légende pour le tableau -->
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
                <td>{{ is_array($operand) ? implode(', ', $operand) : $operand }}</td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>

    <h2 class="section-title">Affichage graphique :</h2>
    <div class="chart-container">
        @if($chartImage)
            <img src="{{ $chartImage }}" alt="Graphique" class="chart">
            <div class="caption">Graphique représentant les résultats</div> <!-- Légende pour l'image -->
        @endif
    </div>

    
</body>

</html>
