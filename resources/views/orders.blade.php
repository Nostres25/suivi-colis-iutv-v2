@extends('base')

@section('header')
    <div class="container d-block">
        <h1 class="h1"><svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-box-seam-fill" preserveAspectRatio="xMidYMid meet" width="32" height="32" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15.528 2.973a.75.75 0 0 1 .472.696v8.662a.75.75 0 0 1-.472.696l-7.25 2.9a.75.75 0 0 1-.557 0l-7.25-2.9A.75.75 0 0 1 0 12.331V3.669a.75.75 0 0 1 .471-.696L7.443.184l.01-.003.268-.108a.75.75 0 0 1 .558 0l.269.108.01.003zM10.404 2 4.25 4.461 1.846 3.5 1 3.839v.4l6.5 2.6v7.922l.5.2.5-.2V6.84l6.5-2.6v-.4l-.846-.339L8 5.961 5.596 5l6.154-2.461z"/>
            </svg> Mon Espace - Commandes</h1>
        <p class="mb-0 opacity-100">Liste et gestion des commandes</p>
    </div>
@endsection

@section('content')
    @use(Database\Seeders\Status)
    @use(Database\Seeders\PermissionValue)
    @use(App\Models\Role)
    @use(App\Models\Order)

    <section class="mb-4">
    <form method="GET" action="{{ url('/orders') }}">
        <div class="row justify-content-center">
            <div class="search-container flex-column flex-sm-row">
                <div class="search-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search search-icon" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                    </svg>
                    <input type="text" name="search" class="form-control search-input"
                           placeholder="Rechercher une commande..."
                           autocomplete="off"
                           value="{{ @$options['search'] ?? '' }}">
                </div>
                <button type="submit" class="btn btn-outline-primary search-button" style="display:none">Rechercher</button>
                @if(@$options['search'])
                    <a href="{{ url('/orders') }}" class="btn btn-secondary ms-2">Effacer</a>
                @endif
            </div>
        </div>
    </form>
</section>

{{-- TODO Peut-être afficher un aperçu de ce qu'il y a dans la commande (colis) --}}
{{-- TODO format mobile : afficher les commandes comme la solution 1 ou 2 mais juste cliquer dessus ça fonctionne donc pas prioritaire : https://www.behance.net/gallery/95240691/Responsive-Data-Table-Designs# --}}
<section class="table-section table-responsive">

    {{--Pour pouvoir créer une commande il faut appartenir à un département et avoir la permission de créer une commande--}}
    @if ($user->hasPermission(PermissionValue::CREER_COMMANDES) && !empty($userDepartments))
        <button type="button" class="btn btn-primary erasure-alert" style="display: table-row" data-bs-toggle="modal"
                data-bs-target="#createOrderModal" id="createOrderButton">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle-fill"
                 viewBox="0 0 16 16">
                <path
                    d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3z"/>
            </svg>
            Ajouter une commande
        </button>
        <x-orders.modal.orderCreationModal :user="$user" :userDepartments="$userDepartments" :validSupplierNames="$validSupplierNames"/>
    @endif

    <div class="table-header mt-4">
        <h2 class="h3"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-list-ul" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m-3 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2m0 4a1 1 0 1 0 0-2 1 1 0 0 0 0 2m0 4a1 1 0 1 0 0-2 1 1 0 0 0 0 2"/>
            </svg> Commandes</h2>
        <p>Devis et bons de commandes</p>
    </div>

    <div id="orders-table-container" data-url="{{ route('orders.fetch.table', $options) }}">
        <x-orders.orders-table :orders="$orders" :user="$user" :userDepartments="$userDepartments"></x-orders.orders-table>
    </div>
@endsection

@section('javascript')
    <script src="{{asset('js/orders.js')}}"></script>
@endsection
