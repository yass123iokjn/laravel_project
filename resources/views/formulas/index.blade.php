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
                        <a href="{{ route('formulas.create') }}" class="btn-create-formula">
                            Créer une nouvelle formule
                        </a>
                    </div>
                </div>

                <!-- Search Bar with Search Type Selection -->
                <div class="flex justify-center mb-16">
                    <form action="{{ route('formulas.index') }}" method="GET" class="relative w-full max-w-2xl flex items-center">
                        <!-- Dropdown for Search Type -->
                        <div class="relative mr-4">
                            <select name="search_type" class="w-56 px-4 py-3 border border-gray-300 rounded-full shadow-lg focus:outline-none focus:ring-2 focus:ring-gray-300">
                                <option value="name" {{ request('search_type') == 'name' ? 'selected' : '' }}> Nom</option>
                                <option value="expression" {{ request('search_type') == 'expression' ? 'selected' : '' }}>Expression</option>
                            </select>
                        </div>
                        <!-- Search Input -->
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
                        <!-- Icone Favoris (Positionné à droite) -->
                        <div class="absolute top-4 right-4">
                            <button class="favorite-btn" onclick="toggleFavorite(this)">
                                <!-- Icône de cœur par défaut (vide) -->
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-8 h-8 text-gray-400 transition duration-300 ease-in-out hover:text-gray-500 heart-icon" data-filled="false">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                                </svg>
                            </button>
                        </div>

                        <!-- Image with floating effect on hover (reduced size) -->
                        <div class="relative mx-auto mt-4 overflow-hidden rounded-full h-32 w-32 transition-transform duration-500 hover:scale-110 border border-black">
                            <img src="{{ asset('images/default-formula.png') }}" alt="card-image" class="object-cover w-full h-full rounded-full" />
                        </div>
                        <!-- Card Content -->
                        <div class="p-4 text-center">
                            <!-- Formula Name (Larger and Bold) -->
                            <h1 class="font-bold italic text-xl text-blue-gray-800 mb-1">
                                {{ $formula->name }}
                            </h1>
                            <!-- Formula Expression (Smaller size) -->
                            <p class="font-bold text-lg text-gray-600 opacity-75">
                                {{ $formula->expression }}
                            </p>
                        </div>
                        <!-- Buttons Section (Reduced padding) -->
                        <div class="p-4 pt-0 flex justify-between space-x-4">
                            <!-- Edit Button -->
                            <a href="{{ route('formulas.edit', $formula) }}" class="text-blue-600 font-bold hover:underline hover:-translate-y-1 transition-transform duration-300 text-sm">✏️ Modifier</a>
                            <!-- Calculate Button -->
                            <a href="{{ route('formulas.importFile', $formula->id) }}" class="text-green-600 font-bold hover:underline hover:-translate-y-1 transition-transform duration-300 text-sm">📐 Calculer</a>
                            <!-- Delete Button -->
                            <form action="{{ route('formulas.destroy', $formula) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 font-bold hover:underline hover:-translate-y-1 transition-transform duration-300 text-sm">🗑️ Supprimer</button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="flex justify-center mt-8">
                    @if ($formulas->hasPages())
                    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center">
                        <!-- Bouton de flèche gauche -->
                        @if ($formulas->onFirstPage())
                        <span class="px-6 py-3 text-gray-500 text-lg font-bold">
                            << 
                        @else
                        <a href="{{ $formulas->previousPageUrl() }}" class="px-6 py-3 text-black text-lg font-bold hover:bg-gray-300 rounded-full">
                            << 
                        @endif

                        <!-- Nombres de pages -->
                        @foreach ($formulas->links()->elements[0] as $page => $url)
                            @if ($page == $formulas->currentPage())
                                <span class="mx-2 px-6 py-3 text-white bg-black text-lg font-bold rounded-full">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="mx-2 px-6 py-3 text-black text-lg font-bold hover:bg-gray-300 rounded-full">{{ $page }}</a>
                            @endif
                        @endforeach

                        <!-- Bouton de flèche droite -->
                        @if ($formulas->hasMorePages())
                            <a href="{{ $formulas->nextPageUrl() }}" class="px-6 py-3 text-black text-lg font-bold hover:bg-gray-300 rounded-full">>></a>
                        @else
                            <span class="px-6 py-3 text-gray-500 text-lg font-bold">>></span>
                        @endif
                    </nav>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<!-- Tailwind CSS in Action -->
<style>
    .bg-custom {
        background-image: url('https://as2.ftcdn.net/v2/jpg/00/92/09/67/1000_F_92096720_BEfbFVfNCrWL6sogJYQ4Qt5Oq8rFNrGO.jpg');
        background-size: cover;
        background-position: center;
    }

    /* Create Formula Button Custom Style */
    .btn-create-formula {
        display: inline-block;
        padding: 10px 20px;
        background-color: #fff;
        color: #000;
        font-weight: bold;
        border-radius: 5px;
        text-align: center;
        transition: background-color 0.3s, color 0.3s, transform 0.3s;
        text-decoration: none;
    }

    .btn-create-formula:hover {
        background-color: #000;
        color: #fff;
        transform: scale(1.05);
    }
</style>
<script>
    function toggleFavorite(element) {
        const svgElement = element.querySelector('.heart-icon');

        // Vérifier si le cœur est déjà rempli
        if (svgElement.dataset.filled === "false") {
            // Passer à cœur rempli
            svgElement.setAttribute("fill", "red");
            svgElement.setAttribute("stroke", "red");
            svgElement.dataset.filled = "true"; // Mettre à jour l'état
        } else {
            // Passer à cœur vide
            svgElement.setAttribute("fill", "none");
            svgElement.setAttribute("stroke", "currentColor");
            svgElement.dataset.filled = "false"; // Mettre à jour l'état
        }
    }
</script>
