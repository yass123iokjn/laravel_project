<x-app-layout>
    <div class="bg-custom">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <h2 class="font-extrabold text-5xl text-gray-800 leading-tight text-center mb-8">
                    Importer un fichier Excel
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

                <form action="{{ route('formulas.import', $formula->id) }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded shadow-md">
                    @csrf
                    <div class="mb-4">
                        <label for="file" class="block text-gray-700 text-sm font-bold mb-2">Sélectionner un fichier :</label>
                        <input type="file" name="file" id="file" class="border border-gray-300 p-2 w-full">
                        @error('file')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="reference" class="block text-gray-700 text-sm font-bold mb-2">Référence de Calcul :</label>
                        <input type="text" name="reference" id="reference" class="border border-gray-300 p-2 w-full">
                        @error('reference')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="btn-submit">Importer et Calculer</button>
                    </div>
                </form>

                <div class="mt-8">
                    <h2 class="text-xl font-bold leading-none text-gray-900 dark:text-white">Calculs Effectués</h2>
                    <div class="grid grid-cols-1 gap-4">
                        @if($calculations->count())
                            @foreach($calculations as $calculation)
                                <div class="bg-white rounded-lg shadow p-4 flex items-center border">
                                    <div class="flex-1">
                                        <h4 class="text-lg font-semibold text-gray-800">{{ $formula->name }}</h4>
                                        <p class="text-gray-600">Référence de Calcul: {{ $calculation->reference }}</p>
                                    </div>
                                    <a href="{{ route('formulas.results', ['id' => $calculation->id]) }}" class="btn-view-result">Voir Résultat</a>
                                </div>
                            @endforeach
                        @else
                            <p>Aucun calcul n'a été importé pour le moment.</p>
                        @endif
                    </div>
                </div>
            </div>
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

    .btn-submit {
        display: inline-block;
        padding: 10px 20px;
        background-color: #3490dc;
        color: #fff;
        font-weight: bold;
        border-radius: 5px;
        text-align: center;
        transition: background-color 0.3s;
    }

    .btn-submit:hover {
        background-color: #2779bd;
    }

    .btn-view-result {
        background-color: #3490dc;
        color: #fff;
        padding: 10px 20px;
        border-radius: 5px;
        font-weight: bold;
        transition: transform 0.3s, background-color 0.3s;
    }

    .btn-view-result:hover {
        background-color: #2779bd;
        transform: scale(1.05);
    }
</style>
