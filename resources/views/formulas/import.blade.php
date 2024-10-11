<x-app-layout>
    <div class="bg-custom">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Afficher les messages de succès -->
                @if(session('success'))
                    <div class="bg-white border border-green-400 text-green-700 px-4 py-6 rounded-lg relative mb-4 text-lg alert-width" role="alert" id="success-alert">
                        <span class="mr-2">&#10003;</span>
                        <span>{{ session('success') }}</span>
                        <button type="button" class="absolute top-0 right-0 mt-2 mr-4 text-green-500" onclick="this.parentElement.style.display='none'">&times;</button>
                    </div>
                    <script>
                        setTimeout(() => {
                            const alert = document.getElementById('success-alert');
                            if (alert) {
                                alert.style.transition = 'opacity 0.5s ease';
                                alert.style.opacity = '0';
                                setTimeout(() => alert.style.display = 'none', 200);
                            }
                        }, 2500);
                    </script>
                @endif

                <!-- Afficher les messages d'erreur -->
                @if($errors->any() && !($errors->has('file') && $errors->first('file') === 'The file field is required.') && !($errors->has('reference') && $errors->first('reference') === 'The reference field is required.'))
                    <div class="bg-white border border-red-400 text-red-700 px-4 py-6 rounded-lg relative mb-4 text-lg alert-width" role="alert" id="error-alert">
                        <span class="mr-2">&#10060;</span>
                        <span>
                            @foreach($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </span>
                        <button type="button" class="absolute top-0 right-0 mt-2 mr-4 text-red-500" onclick="this.parentElement.style.display='none'">&times;</button>
                    </div>
                    <script>
                        setTimeout(() => {
                            const alert = document.getElementById('error-alert');
                            if (alert) {
                                alert.style.transition = 'opacity 0.5s ease';
                                alert.style.opacity = '0';
                                setTimeout(() => alert.style.display = 'none', 500);
                            }
                        }, 3000);
                    </script>
                @endif

                <h1 class="font-bold text-5xl text-white leading-tight text-center mb-4 font-serif">
                    Importer un fichier Excel
                </h1>

                <form action="{{ route('formulas.import', $formula->id) }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded shadow-md">
    @csrf
    <div class="mb-4">
        <label for="file" class="block text-gray-700 text-sm font-bold mb-2">Sélectionner un fichier :</label>
        <input type="file" name="file" id="file-input" class="block w-full border border-gray-300 rounded-lg text-sm focus:z-10 focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none
            file:bg-black-50 file:border-0
            file:me-4
            file:py-3 file:px-4">
        @error('file')
            <span class="text-red-500 text-xs">{{ $message }}</span>
        @enderror
    </div>
    <div class="mb-4">
        <label for="nom_calcul" class="block text-gray-700 text-sm font-bold mb-2">Nom de la Calcul :</label>
        <input type="text" name="nom_calcul" id="nom_calcul" class="border border-gray-300 p-2 w-full">
        @error('nom_calcul')
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
    
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
        @if($calculations->count())
            @foreach($calculations as $calculation)
                <div class="bg-white rounded-lg shadow p-4 flex items-center border justify-between">
                    <div class="flex-1">
                        <h4 class="text-lg font-semibold text-gray-800">{{ $calculation->nom_calcul }}</h4> <!-- Affichage du nom de calcul -->
                        <p class="text-gray-600">Référence de Calcul: {{ $calculation->reference }}</p>
                    </div>
                    <div class="flex items-center">
                        <a href="{{ route('formulas.results', ['id' => $calculation->id]) }}" class="btn-view-result">Voir Résultat</a>
                        <form action="{{ route('formulas.deleteImport', $calculation->id) }}" method="POST" onsubmit="return confirmDelete();" class="ml-4">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="focus:outline-none">
                                <img src="{{ asset('images/trash-red.png') }}" alt="Supprimer" class="w-6 h-6">
                            </button>
                        </form>
                    </div>
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

    <script>
        function confirmDelete() {
            return confirm('Êtes-vous sûr de vouloir supprimer cet import ? Cette action est irréversible.');
        }
    </script>
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

    .alert-width {
        max-width: 600px; /* Ajustez la largeur maximale ici */
        margin: 0 auto; /* Centre l'alerte */
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
