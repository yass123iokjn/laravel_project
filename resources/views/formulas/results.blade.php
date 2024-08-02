<!-- resources/views/formulas/results.blade.php -->
<x-app-layout>
    <div class="bg-custom">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <h2 class="font-extrabold text-5xl text-gray-800 leading-tight text-center mb-8">
                    Résultats pour la Formule : {{ $formula->name }}
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

                <div class="bg-white p-6 rounded shadow-md">
                    <h3 class="text-xl font-bold mb-4">Résultats Calculés</h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                @foreach($results->first() ? array_keys($results->first()['result_data']) : [] as $header)
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ ucfirst($header) }}
                                    </th>
                                @endforeach
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Résultat
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($results as $result)
                                <tr>
                                    @foreach($result['result_data'] as $data)
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $data }}
                                        </td>
                                    @endforeach
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $result['result_data']['result'] ?? 'N/A' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
