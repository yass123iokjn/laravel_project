<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Créer une nouvelle formule') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form id="create-formula-form" action="{{ route('formulas.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="sentence" class="block font-medium text-sm text-gray-700">{{ __('Phrase décrivant la Formule') }}</label>
                            <input id="sentence" class="block mt-1 w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" type="text" name="sentence" required autofocus />
                        </div>

                        <div class="mb-4">
                            <button id="analyze-btn" type="button" class="w-full flex items-center justify-center px-4 py-2 bg-black-500 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-grey-700 focus:outline-none focus:border-black-700 focus:ring focus:ring-green-200 active:bg-grey-600 disabled:opacity-25 transition">
                                {{ __('Analyser et Traduire') }}
                            </button>
                        </div>

                        <div class="mb-4">
                            <label for="name" class="block font-medium text-sm text-gray-700">{{ __('Nom de la Formule') }}</label>
                            <input id="name" class="block mt-1 w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" type="text" name="name" required />
                        </div>

                        <div class="mb-4">
                            <label for="expression" class="block font-medium text-sm text-gray-700">{{ __('Expression de la Formule') }}</label>
                            <input id="expression" class="block mt-1 w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" type="text" name="expression" required />
                        </div>

                        <div class="mb-4">
                            <button id="add-formula-btn" type="submit" class="w-full flex items-center justify-center px-4 py-2 bg-black-500 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-black-700 focus:outline-none focus:border-black-700 focus:ring focus:ring-blue-200 active:bg-black-600 disabled:opacity-25 transition">
                                {{ __('Ajouter la Formule') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .py-12 {
            background-image: url('{{ asset('images/board-5599231_1280.png') }}');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .max-w-4xl {
            max-width: 32rem;
            width: 100%;
        }

        .px-4 {
            padding-left: 1rem;
            padding-right: 1rem;
        }
    </style>

    <script>
        document.getElementById('analyze-btn').addEventListener('click', function() {
    const sentence = document.getElementById('sentence').value;

    // Envoyer la phrase à l'API via une requête POST
    fetch("{{ route('formulas.analyze') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ sentence: sentence })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Erreur du serveur: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.error) {
            console.error('Erreur:', data.error);
            alert(`Erreur: ${data.error}`);
        } else {
            // Mettre à jour le champ "Expression de la Formule" avec la formule générée
            document.getElementById('expression').value = data.formula;
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert(`Erreur: ${error.message}`);
    });
});

    </script>
</x-app-layout>
