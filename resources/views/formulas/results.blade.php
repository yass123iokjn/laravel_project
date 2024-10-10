<x-app-layout>
    <div class="bg-custom bg-cover bg-center">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <h2 class="font-extrabold text-5xl text-gray-800 leading-tight text-center mb-8">
                    Résultats pour le Calcul : {{ $id }}
                </h2>

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    @if(!empty($resultsData) && is_array($resultsData))

                    <table class="table-auto w-full">
                        <thead>
                            <tr>
                                @if(!empty($headers) && is_array($headers))
                                    @foreach($headers as $header)
                                        <th class="border px-4 py-2">{{ ucfirst($header) }}</th>
                                    @endforeach
                                    <th class="border px-4 py-2">Résultat</th>
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

                        <!-- Bouton pour afficher le graphique -->
                        <div class="mt-6 text-center">
                            <a href="{{ route('formulas.graph', ['id' => $id]) }}" 
                               class="inline-block bg-blue-500 hover:bg-blue-700 text-white py-3 px-6 rounded-lg shadow-md transition-transform transform hover:scale-105">
                                Show Graph
                            </a>
                        </div>
                    @else
                        <p>Aucun résultat trouvé pour ce calcul.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-custom {
            background-image: url('{{ asset('images/board-5599231_1280.png') }}');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
</x-app-layout>
