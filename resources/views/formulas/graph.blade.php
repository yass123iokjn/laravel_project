<x-app-layout>
    <div class="bg-custom">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <h2 class="font-extrabold text-5xl text-gray-800 leading-tight text-center mb-8">
                    Graphique des résultats pour le Calcul 
                </h2>

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <canvas id="resultGraph"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Script pour afficher le graphique avec Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('resultGraph').getContext('2d');
        const resultGraph = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [@foreach($resultsData as $result) "{{ explode('=', $result)[0] }}", @endforeach],
                datasets: [{
                    label: 'Résultats',
                    data: [@foreach($resultsData as $result) {{ explode('=', $result)[1] }}, @endforeach],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</x-app-layout>
