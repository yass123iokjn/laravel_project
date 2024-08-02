<!-- resources/views/formulas/confirm_delete.blade.php -->
<x-app-layout>
    <div class="bg-custom">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <h2 class="font-extrabold text-5xl text-gray-800 leading-tight text-center mb-8">
                    Supprimer la Formule
                </h2>

                <div class="bg-white p-6 rounded shadow-md">
                    <p class="text-lg">Êtes-vous sûr de vouloir supprimer la formule suivante ?</p>
                    <p class="font-bold">{{ $formula->name }}</p>
                    <p>Cette action est irréversible et toutes les données associées seront supprimées.</p>

                    <form action="{{ route('formulas.destroy', $formula->id) }}" method="POST" class="mt-4">
                        @csrf
                        @method('DELETE')
                        <div class="flex justify-between">
                            <a href="{{ route('formulas.index') }}" class="btn-cancel">Annuler</a>
                            <button type="submit" class="btn-delete">Supprimer</button>
                        </div>
                    </form>
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

    .btn-cancel, .btn-delete {
        display: inline-block;
        padding: 10px 20px;
        font-weight: bold;
        border-radius: 5px;
        text-align: center;
        transition: background-color 0.3s;
    }

    .btn-cancel {
        background-color: #d1d5db;
        color: #000;
    }

    .btn-cancel:hover {
        background-color: #9ca3af;
    }

    .btn-delete {
        background-color: #e3342f;
        color: #fff;
    }

    .btn-delete:hover {
        background-color: #cc1f1a;
    }
</style>
