<x-app-layout>
    <div class="bg-custom">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Sélecteur pour choisir l'opérande et le type de graphique -->
                <div class="flex flex-col sm:flex-row mb-4 space-y-4 sm:space-y-0 sm:space-x-4">
                    <div class="flex-1">
                        <label for="operandSelect" class="text-gray-100 font-bold block mb-1">Sélectionner une colonne pour tracer :</label>
                        <select id="operandSelect" class="form-control w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @foreach($headers as $header)
                                <option value="{{ $header }}">{{ $header }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1">
                        <label for="chartTypeSelect" class="text-gray-100 font-bold block mb-1">Sélectionner le type de graphique :</label>
                        <select id="chartTypeSelect" class="form-control w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="line">Graphique en ligne</option>
                            <option value="bar">Graphique à barres</option>
                            <option value="pie">Graphique circulaire</option>
                            <option value="area">Graphique en aires</option>
                        </select>
                    </div>
                </div>

                <!-- Bouton pour générer le graphique -->
                <div class="flex justify-center">
                    <button id="generateGraphBtn" class="btn-create-formula mt-8 px-8 py-4">
                        Tracer le graphique
                    </button>
                </div>

                <!-- Card blanche contenant le graphique -->
                <div class="mt-4 bg-white rounded-lg shadow-lg p-6">
                    <canvas id="chartCanvas" class="canvas-size"></canvas>
                </div>

                <!-- Champ caché pour l'image du graphique -->
                <input type="hidden" id="chartImage" name="chartImage">
            </div>
        </div>
        <div class="flex justify-center mt-8">
        <form action="{{ route('pdf.report', $formula->id) }}" method="POST">
    @csrf
    <input type="hidden" name="chartImage" id="chartImageHidden"> <!-- Champ caché pour l'image Base64 -->
    <button type="submit" class="btn-create-formula mt-8 px-8 py-4">Télécharger le PDF</button>
</form>

        </div>
    </div>
</x-app-layout>
<style>
    .bg-custom {
        background-image: url('https://as2.ftcdn.net/v2/jpg/00/92/09/67/1000_F_92096720_BEfbFVfNCrWL6sogJYQ4Qt5Oq8rFNrGO.jpg');
        background-size: cover;
        background-position: center;
        position: relative;
        overflow: hidden;
        height: 100vh;
    }

    .btn-create-formula {
        display: inline-block;
        background-color: #fff;
        color: #000;
        font-weight: bold;
        border-radius: 5px;
        text-align: center;
        transition: background-color 0.3s, color 0.3s, transform 0.3s;
        min-width: 250px;
        font-size: 1.25rem;
    }

    .btn-create-formula:hover {
        background-color: #000;
        color: #fff;
        transform: scale(1.05);
    }

    .form-control {
        padding: 10px;
        border-radius: 5px;
        background-color: white;
    }

    .text-gray-100 {
        color: white;
    }

    canvas {
        display: block;
        margin: 0 auto;
        width: 600px;  
        height: 300px;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let chart; // Variable to hold the chart instance

    document.getElementById('generateGraphBtn').addEventListener('click', function() {
        const operand = document.getElementById('operandSelect').value; // Get the selected operand
        const chartType = document.getElementById('chartTypeSelect').value; // Get the selected chart type

        const data = @json($resultsData); // Data extracted from import results
        const headers = {!! json_encode($headers) !!}; // Encode headers as JavaScript array

        // Get the index of the selected operand
        const operandIndex = headers.indexOf(operand);
        
        // Validate operand index
        if (operandIndex === -1) {
            console.error("Operand not found in headers.");
            return; // Exit if the operand is not found
        }

        // Update labels to use the selected operand instead of the first column
        const labels = data.map(row => row[operandIndex]); // Use the selected operand values for the x-axis
        const operandData = data.map(row => row[operandIndex]); // Get values for the selected operand
        const resultData = data.map(row => row[3]); // Assuming "Résultat" is always in the fourth column

        // Generate random pastel colors for the chart
        function getRandomPastelColor() {
            const r = Math.floor(Math.random() * 127 + 127);
            const g = Math.floor(Math.random() * 127 + 127);
            const b = Math.floor(Math.random() * 127 + 127);
            return `rgba(${r}, ${g}, ${b}, 0.7)`; // Opacité à 0.7
        }

        const backgroundColors = operandData.map(() => getRandomPastelColor());
        const borderColors = chartType === 'line' ? operandData.map(() => getRandomPastelColor()) : [];

        const ctx = document.getElementById('chartCanvas').getContext('2d');

        // Clear the existing chart instance if it exists
        if (chart) {
            chart.destroy(); // Destroy the existing chart instance
        }

        let chartConfig = {
            type: chartType === 'area' ? 'line' : chartType, // Use 'line' for area charts
            data: {
                labels: labels, // Using the selected operand values for X-axis
                datasets: [{
                    label: `Graphique de ${operand}`,
                    data: resultData, // Always show results on the Y-axis
                    backgroundColor: backgroundColors,
                    borderColor: chartType === 'line' ? borderColors : [],
                    borderWidth: chartType === 'line' ? 3 : 0,
                    fill: chartType === 'area' // Fill area if the chart type is 'area'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // Ensures the canvas maintains the correct aspect ratio
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: operand // Label for x-axis
                        }
                    },
                    y: {
                        beginAtZero: false,
                        title: {
                            display: true,
                            text: 'Résultat' // Label for y-axis
                        }
                    }
                }
            }
        };

        // Chart type-specific configuration
        if (chartType === 'pie') {
            chartConfig.options.plugins = {
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            const total = tooltipItem.dataset.data.reduce((a, b) => a + b, 0);
                            const currentValue = tooltipItem.raw;
                            const percentage = Math.floor((currentValue / total) * 100 + 0.5);         
                            return currentValue + ' (' + percentage + '%)'; // Show percentage
                        }
                    }
                }
            };
        }

        chart = new Chart(ctx, chartConfig); // Create the new chart instance

        // Attendre un moment pour s'assurer que le graphique est entièrement chargé
        setTimeout(() => {
            // Utiliser toBase64Image() pour convertir le graphique en image
            var image = chart.toBase64Image();
            document.getElementById('chartImage').value = image; // Store base64 image in hidden input
            document.getElementById('chartImageHidden').value = image; // Ajout dans le champ caché pour l'envoi du formulaire
            console.log("Base64 Image: ", image);
        }, 1000); // 1000 ms pour s'assurer que le rendu est terminé
    });
</script>
