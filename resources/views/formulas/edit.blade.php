<x-app-layout>
    <div class="bg-custom">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <h2 class="font-extrabold text-6xl text-gray-800 leading-tight text-right italic mb-8">
                
                </h2>

                <!-- Messages de succès ou d'erreur -->
                @if (session('success'))
                <div class="bg-teal-100 border-t-4 border-teal-500 text-teal-900 px-4 py-3 shadow-md rounded relative mb-4" role="alert">
                    <div class="flex">
                        <div class="py-1">
                            <svg class="fill-current h-6 w-6 text-teal-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold">Succès!</p>
                            <p class="text-sm">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
                @endif

                @if ($errors->any())
                <div class="bg-red-100 border-t-4 border-red-500 text-red-900 px-4 py-3 shadow-md rounded relative mb-4" role="alert">
                    <div class="flex">
                        <div class="py-1">
                            <svg class="fill-current h-6 w-6 text-red-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold">Erreur!</p>
                            <ul class="text-sm">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif

                <form id="edit-formula-form" action="{{ route('formulas.update', $formula->id) }}" method="POST" class="bg-white p-6 rounded shadow-md">
                    @csrf
                    @method('PATCH')
                    <div class="mb-4">
                        <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nom de la Formule :</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $formula->name) }}" class="border border-gray-300 p-2 w-full rounded">
                        @error('name')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="expression" class="block text-gray-700 text-sm font-bold mb-2">Expression de la Formule :</label>
                        <input type="text" name="expression" id="expression" value="{{ old('expression', $formula->expression) }}" class="border border-gray-300 p-2 w-full rounded">
                        @error('expression')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="flex justify-end">
                        <button type="button" id="submit-button" class="btn-submit">Sauvegarder les modifications</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de confirmation -->
    <div id="confirmationModal" class="fixed z-50 inset-0 overflow-y-auto hidden flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-lg max-w-xs w-full">
            <div class="bg-green-100 border-t-4 border-green-500 text-green-900 px-4 py-3 shadow-md rounded-t relative">
                <div class="flex">
                    <div class="py-1">
                        <svg class="fill-current h-6 w-6 text-green-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z" />
                        </svg>
                    </div>

                    <div>
                        <p class="font-bold">Confirmation</p>
                        <p class="text-sm">Vous avez modifié l'expression de la formule. Tous les calculs associés seront supprimés. Voulez-vous continuer ?</p>
                    </div>
                </div>
            </div>
            <div class="bg-green-100 border-t-4 border-green-500 rounded-b text-green-900 px-4 py-3 shadow-md" role="alert">
                <div class="px-4 py-3 flex justify-end">
                    <button type="button" class="bg-gray-200 text-gray-800 px-4 py-2 rounded ml-2 hover:bg-gray-300" onclick="confirmUpdate()">Oui</button>
                    <button type="button" class="bg-gray-200 text-gray-800 px-4 py-2 rounded ml-2 hover:bg-gray-300" onclick="closeModal()">Non</button>
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
</style>

<script>
    document.getElementById('submit-button').addEventListener('click', function() {
        const originalExpression = "{{ $formula->expression }}";
        const newExpression = document.getElementById('expression').value;

        if (originalExpression !== newExpression) {
            // Afficher le modal de confirmation
            document.getElementById('confirmationModal').classList.remove('hidden');
        } else {
            // Soumettre le formulaire directement
            document.getElementById('edit-formula-form').submit();
        }
    });

    function closeModal() {
        document.getElementById('confirmationModal').classList.add('hidden');
    }

    function confirmUpdate() {
        document.getElementById('edit-formula-form').submit();
    }
</script>
