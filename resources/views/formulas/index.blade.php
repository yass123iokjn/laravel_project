<x-app-layout>
    <div class="bg-custom">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-8">
                    <x-slot name="header">
                        <h2 class="font-semibold text-xl text-black-100 leading-tight">
                            {{ __('Liste des formules') }}
                        </h2>
                    </x-slot>
                    <div class="ml-auto">
                        <a href="{{ route('formulas.create') }}" class="inline-block bg-white text-black px-4 py-2 rounded-full hover:bg-black hover:text-white transition duration-300">
                            Créer une nouvelle formule
                        </a>
                    </div>
                </div>

                <!-- Search Bar -->
                <div class="flex justify-center mb-16">
                    <form action="{{ route('formulas.index') }}" method="GET" class="relative w-full max-w-2xl flex items-center">
                        <div class="relative mr-4">
                            <select name="search_type" class="w-56 px-4 py-3 border border-gray-300 rounded-full shadow-lg focus:outline-none focus:ring-2 focus:ring-gray-300">
                                <option value="name" {{ request('search_type') == 'name' ? 'selected' : '' }}> Nom</option>
                                <option value="expression" {{ request('search_type') == 'expression' ? 'selected' : '' }}>Expression</option>
                            </select>
                        </div>
                        <input type="text" name="search" placeholder="Rechercher une formule" value="{{ request('search') }}" class="w-full px-6 py-3 border border-gray-300 rounded-full shadow-lg focus:outline-none focus:ring-2 focus:ring-gray-300" />
                        <button type="submit" class="absolute top-1/2 right-4 transform -translate-y-1/2 bg-black text-white px-4 py-2 rounded-full hover:bg-gray-800 focus:outline-none">
                            Search
                        </button>
                    </form>
                </div>

                <!-- Cards Grid -->
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($formulas as $formula)
                    <div class="relative flex flex-col bg-gray-100 text-gray-800 shadow-lg rounded-xl transition-transform duration-500 hover:scale-105 hover:shadow-2xl p-4">
                        <div class="absolute top-4 right-4">
                            <button class="favorite-btn" onclick="toggleFavorite(this)">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-8 h-8 text-gray-400 transition duration-300 ease-in-out hover:text-gray-500 heart-icon" data-filled="false">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                                </svg>
                            </button>
                        </div>

                        <div class="relative mx-auto mt-4 overflow-hidden rounded-full h-32 w-32 transition-transform duration-500 hover:scale-110 border border-black">
                            <img src="{{ asset('images/default-formula.png') }}" alt="card-image" class="object-cover w-full h-full rounded-full" />
                        </div>
                        <div class="p-4 text-center">
                            <h1 class="font-bold italic text-xl text-blue-gray-800 mb-1">{{ $formula->name }}</h1>
                            <p class="font-bold text-lg text-gray-600 opacity-75">{{ $formula->expression }}</p>
                        </div>
                        <div class="p-4 pt-0 flex justify-between space-x-4">
                            <a href="{{ route('formulas.edit', $formula) }}" class="text-blue-600 font-bold hover:underline hover:-translate-y-1 transition-transform duration-300 text-sm">✏️ Modifier</a>
                            <a href="{{ route('formulas.importFile', $formula->id) }}" class="text-green-600 font-bold hover:underline hover:-translate-y-1 transition-transform duration-300 text-sm">📐 Calculer</a>
                            <button type="button" onclick="confirmDelete({{ $formula->id }})" class="text-red-600 font-bold hover:underline hover:-translate-y-1 transition-transform duration-300 text-sm">🗑️ Supprimer</button>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="flex justify-center mt-8">
                    <!-- Pagination logic... -->
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmationModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-red-600">Confirmer la suppression</h2>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">✖</button>
            </div>
            <p class="text-gray-600 mb-4">Voulez-vous vraiment supprimer cette formule ? Cette action est irréversible.</p>
            <div class="flex justify-end space-x-4">
                <button onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Annuler</button>
                <form id="deleteForm" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    function confirmDelete(formulaId) {
        const deleteForm = document.getElementById('deleteForm');
        deleteForm.action = `/formulas/${formulaId}`;
        document.getElementById('confirmationModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('confirmationModal').classList.add('hidden');
    }
</script>

<style>
    .bg-custom {
        background-image: url('https://as2.ftcdn.net/v2/jpg/00/92/09/67/1000_F_92096720_BEfbFVfNCrWL6sogJYQ4Qt5Oq8rFNrGO.jpg');
        background-size: cover;
        background-position: center;
    }
</style>
