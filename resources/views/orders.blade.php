@extends('base')
@section('header')
    <div class="orders-hero-content">
        <span class="hero-kicker">Commandes</span>

        <h1>Commandes</h1>

        <p>Liste des commandes, devis et bons de commande.</p>
    </div>
@endsection
@section('content')
    @use(Database\Seeders\Status)
    @use(Database\Seeders\PermissionValue)
    @use(App\Models\Role)
    @use(App\Models\Order)

    <section class="table-section table-responsive">

        @if ($user->hasPermission(PermissionValue::CREER_COMMANDES) && !empty($userDepartments))
            <button type="button" class="btn btn-primary erasure-alert" style="display: table-row" data-bs-toggle="modal"
                    data-bs-target="#createOrderModal" id="createOrderButton">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                     class="bi bi-plus-circle-fill" viewBox="0 0 16 16">
                    <path
                        d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3z"/>
                </svg>
                Ajouter une commande
            </button>

            <x-orders.modal.orderCreationModal
                :user="$user"
                :userDepartments="$userDepartments"
                :validSupplierNames="$validSupplierNames"
            />
        @endif

        <div class="table-header mt-4">
            <h2 class="h3">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor"
                     class="bi bi-list-ul" viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                          d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m-3 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2m0 4a1 1 0 1 0 0-2 1 1 0 0 0 0 2m0 4a1 1 0 1 0 0-2 1 1 0 0 0 0 2"/>
                </svg>
                Commandes
            </h2>
            <p>Devis et bons de commandes</p>
        </div>

            <div id="orders-table-container"
                 data-url="{{ route('orders.fetch.table', [
        'search' => $search,
        'status' => $status ?? null,
        'dashboard_filter' => $dashboardFilter ?? null,
        'updated_from' => $updatedFrom ?? null,
        'page' => $orders->currentPage()
     ]) }}">
                <x-orders.orders-table
                    :orders="$orders"
                    :user="$user"
                    :userDepartments="$userDepartments"
                />
            </div>
    </section>
@endsection

@section('javascript')
    <script src="{{ asset('js/orders.js') }}"></script>
@endsection
