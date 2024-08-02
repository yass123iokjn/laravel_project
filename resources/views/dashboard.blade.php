<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
        <a href="{{ route('formulas.create') }}" class="btn-create-formula">
            Créer une nouvelle formule
        </a>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="mt-8 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                @foreach($formulas as $formula)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <img src="{{ asset('public\images/default-formula.png') }}" class="w-full mb-4" alt="Image par défaut">
                        <h3 class="text-lg font-semibold text-gray-800">{{ $formula->name }}</h3>
                        <p class="text-gray-600 mb-4">{{ $formula->expression }}</p>
                        <div class="flex justify-end">
                            <a href="{{ route('formulas.edit', $formula) }}" class="text-blue-500 hover:text-blue-700 mr-2">Modifier</a>
                            <form action="{{ route('formulas.destroy', $formula) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700">Supprimer</button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>

<style>
    .btn-create-formula {
        display: inline-block;
        padding: 10px 20px;
        background-color: #3490dc;
        color: white;
        font-weight: bold;
        border-radius: 5px;
        text-align: center;
        float: right;
        margin-top: -40px;
        transition: background-color 0.3s;
        text-decoration: none;
    }

    .btn-create-formula:hover {
        background-color: #2779bd;
    }

    @media (max-width: 768px) {
        .btn-create-formula {
            float: none;
            display: block;
            margin: 10px auto;
        }
    }
</style>