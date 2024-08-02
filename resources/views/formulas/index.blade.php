<x-app-layout>

    <div class="bg-custom">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-8">
                    <x-slot name="header">
                        <h2 class="font-semibold text-xl text-gray-100 leading-tight">
                            {{ __('Liste des formules') }}
                        </h2>
                    </x-slot>
                    <h2 class="font-extrabold text-5xl text-gray-800 leading-tight text-center italic">

                    </h2>
                    <div>
                        <a href="{{ route('formulas.create') }}" class="btn-create-formula">
                            Créer une nouvelle formule
                        </a>
                    </div>

                </div>
                <div>
                    <div class="flex justify-center mb-16">
                        <form action="{{ route('formulas.index') }}" method="GET" class="relative w-full max-w-2xl">
                            <input type="text" name="search" placeholder="Rechercher une formule par expression ou nom" value="{{ request('search') }}" class="w-full px-6 py-3 border border-gray-300 rounded-full shadow-lg focus:outline-none focus:ring-2 focus:ring-gray-300" />
                            <button type="submit" class="absolute top-1/2 right-4 transform -translate-y-1/2 bg-black text-white px-4 py-2 rounded-full hover:bg-gray-800 focus:outline-none">
                                Search
                            </button>
                        </form>
                    </div>
                </div>

                <div>
                    <div class="flex justify-center mb-16">
                        <form action="{{ route('formulas.index') }}" method="GET" class="relative w-full max-w-2xl">
                            <br />

                        </form>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($formulas as $formula)
                    <div class="relative flex flex-col bg-gray-100 text-gray-800 shadow-lg rounded-xl transform transition duration-500 hover:scale-105 hover:shadow-xl">
                        <div class="relative mx-4 mt-4 overflow-hidden rounded-full h-48 w-48 mx-auto transition duration-500 hover:scale-110">
                            <img src="{{ asset('images/default-formula.png') }}" alt="card-image" class="object-cover w-full h-full rounded-full" />
                        </div>
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-2">
                                <h1 class="block font-sans text-base font-medium leading-relaxed text-blue-gray-800">
                                    {{ $formula->name }}
                                </h1>
                            </div>
                            <p class="block font-sans text-sm leading-normal text-gray-600 opacity-75">
                                {{ $formula->expression }}
                            </p>
                        </div>
                        <div class="p-6 pt-0 flex justify-between space-x-8">
                            <a href="{{ route('formulas.edit', $formula) }}" class="btn-edit">✏️ Modifier</a>
                            <a href="{{ route('formulas.importFile', $formula->id) }}" class="btn-calculate">📐 Calculer</a>
                            <form action="{{ route('formulas.destroy', $formula) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete">🗑️ Supprimer</button>
                            </form>
                        </div>
                    </div>
                    @endforeach
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

    .btn-edit,
    .btn-delete,
    .btn-calculate {
        display: inline-block;
        padding: 5px 15px;
        background-color: white;
        border-radius: 10%;
        transition: background-color 0.3s, transform 0.3s;
        text-decoration: none;
    }

    .btn-edit {
        color: #3490dc;
        border: 1px solid #3490dc;
    }

    .btn-edit:hover {
        background-color: #3490dc;
        color: white;
        transform: scale(1.05);
    }

    .btn-delete {
        color: #e3342f;
        border: 1px solid #e3342f;
        cursor: pointer;
    }

    .btn-delete:hover {
        background-color: #e3342f;
        color: white;
        transform: scale(1.05);
    }

    .btn-calculate {
        color: #38c172;
        border: 1px solid #38c172;
    }

    .btn-calculate:hover {
        background-color: #38c172;
        color: white;
        transform: scale(1.05);
    }
</style>