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
                    @if(!empty($resultsData))
                        <table class="table-auto w-full">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2">Opérande 1</th>
                                    <th class="px-4 py-2">Opérande 2</th>
                                    <th class="px-4 py-2">Résultat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($resultsData as $result)
                                    @php
                                        // Diviser l'expression et le résultat
                                        $parts = preg_split('/\s*[\+=]\s*/', $result);
                                    @endphp
                                    <tr>
                                        <td class="border px-4 py-2">{{ $parts[0] ?? 'N/A' }}</td>
                                        <td class="border px-4 py-2">{{ $parts[1] ?? 'N/A' }}</td>
                                        <td class="border px-4 py-2">{{ $parts[2] ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p>Aucun résultat trouvé pour ce calcul.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
