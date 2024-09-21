<x-app-layout>
    <div class="bg-custom">
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
                @foreach($result as $operand) <!-- Assurez-vous que $operand est une chaîne -->
                    <td class="border px-4 py-2">{{ is_array($operand) ? implode(', ', $operand) : $operand }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>



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
