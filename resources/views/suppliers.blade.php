@extends('base')


@section('header')
    <div class="container d-block">
        <h1 class="h1">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-truck"
                 viewBox="0 0 16 16">
                <path
                    d="M0 3.5A1.5 1.5 0 0 1 1.5 2h9A1.5 1.5 0 0 1 12 3.5V5h1.02a1.5 1.5 0 0 1 1.17.563l1.481 1.85a1.5 1.5 0 0 1 .329.938V10.5a1.5 1.5 0 0 1-1.5 1.5H14a2 2 0 1 1-4 0H5a2 2 0 1 1-3.998-.085A1.5 1.5 0 0 1 0 10.5zm1.294 7.456A2 2 0 0 1 4.732 11h5.536a2 2 0 0 1 .732-.732V3.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .294.456M12 10a2 2 0 0 1 1.732 1h.768a.5.5 0 0 0 .5-.5V8.35a.5.5 0 0 0-.11-.312l-1.48-1.85A.5.5 0 0 0 13.02 6H12zm-9 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2m9 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2"/>
            </svg>
            Gestion des Fournisseurs
        </h1>
        <p class="mb-0 opacity-100">Liste et gestion des fournisseurs</p>
    </div>
@endsection

@section('content')
    @use(Database\Seeders\PermissionValue)
    @use(App\Models\Role)
    @use(App\Models\Supplier)

    {{-- Section Recherche Fonctionnelle --}}
    <section class="mb-4">
        <form method="GET" action="{{ url('/suppliers') }}">
            <div class="row justify-content-center">
                <div class="search-container flex-column flex-sm-row">
                    <div class="search-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                             class="bi bi-search search-icon" viewBox="0 0 16 16">
                            <path
                                d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                        </svg>
                        <input type="text" name="search" class="form-control search-input"
                               placeholder="Rechercher un fournisseur..."
                               autocomplete="off"
                               value="{{ $search ?? '' }}">
                    </div>
                    <button type="submit" class="btn btn-outline-primary search-button" style="display:none;">Rechercher
                    </button>
                    @if(isset($search) && $search)
                        <a href="{{ url('/suppliers') }}" class="btn btn-secondary ms-2">Effacer</a>
                    @endif
                </div>
            </div>
        </form>
    </section>

    <!-- Tableau des fournisseurs -->
    <section class="table-section table-responsive">
        {{--Pour pouvoir ajouter un fournisseur il faut avoir la permission de demander l'ajout d'un fournisseur ou de gérer les fournisseur--}}
        @if ($user->hasPermission(PermissionValue::GERER_FOURNISSEURS) || $user->hasPermission(PermissionValue::DEMANDER_AJOUT_FOURNISSEUR) )
            <button type="button" class="btn btn-primary erasure-alert" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle-fill"
                     viewBox="0 0 16 16">
                    <path
                        d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3z"/>
                </svg>
                Ajouter un fournisseur
            </button>
            <x-suppliers.modal.supplierCreationModal :user="$user"/>
        @endif

        <div class="table-header mt-4">
            <h2 class="h3">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor"
                     class="bi bi-building-fill" viewBox="0 0 16 16">
                    <path
                        d="M3 0a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h3v-3.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 .5.5V16h3a1 1 0 0 0 1-1V1a1 1 0 0 0-1-1zm1 2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3.5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5M4 5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zM7.5 5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5m2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zM4.5 8h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5m2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3.5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5"/>
                </svg>
                Fournisseurs
            </h2>
            <p>Liste des fournisseurs référencés</p>
        </div>

        <div id="suppliers-table-container" data-url="{{ route('suppliers.fetch.table', ['search' => $search, 'page' => $suppliers->currentPage()]) }}" >
            <x-suppliers.suppliers-table :suppliers="$suppliers"></x-suppliers.suppliers-table>
        </div>

    </section>

@endsection


@section('javascript')
    <script src="{{ asset('js/suppliers.js') }}"></script>
@endsection
