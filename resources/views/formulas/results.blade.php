<x-app-layout>
    <div class="bg-custom">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <h2 class="font-extrabold text-5xl text-gray-800 leading-tight text-center mb-8">
                    Résultats pour le Calcul : {{ $id }}
                </h2>

                <!-- Afficher les messages de succès -->
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Afficher les messages d'erreur -->
                @if($errors->any())
                    <div class="alert alert-danger">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <!-- Tableau des résultats -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    @if(!empty($resultsData) && is_array($resultsData))
                        <!-- Trouver le nombre maximum d'opérandes pour définir les colonnes -->
                        @php
                            $maxOperands = max(array_map(function($result) {
                                return count($result['operands']);
                            }, $resultsData));
                        @endphp

                        <table class="table-auto w-full">
                            <thead>
                                <tr>
                                    @for($i = 0; $i < $maxOperands; $i++)
                                        <th class="border px-4 py-2">Opérande {{ $i + 1 }}</th>
                                    @endfor
                                    <th class="border px-4 py-2">Résultat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($resultsData as $result)
                                    @if(is_array($result) && isset($result['operands']) && isset($result['result']))
                                        <tr>
                                            @foreach($result['operands'] as $operand)
                                                <td class="border px-4 py-2">{{ $operand }}</td>
                                            @endforeach
                                            @for($i = count($result['operands']); $i < $maxOperands; $i++)
                                                <td class="border px-4 py-2"></td>
                                            @endfor
                                            <td class="border px-4 py-2">{{ $result['result'] }}</td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td class="border px-4 py-2" colspan="{{ $maxOperands + 1 }}">Données non disponibles</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Ajouter un bouton pour l'affichage graphique -->
                        <div class="mt-6">
                            <a href="{{ route('formulas.graph', ['id' => $id]) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Afficher le Graphique
                            </a>
                        </div>
                    @else
                        <p>Aucun résultat trouvé pour ce calcul.</p>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
