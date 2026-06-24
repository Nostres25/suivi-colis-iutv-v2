@extends('base')

@section('header')
    <div class="orders-hero-content">
        <span class="hero-kicker">Tableau de bord</span>

        <h1>Bienvenue, {{ $user->getFirstname() ?? 'Agent' }} {{ $user->getLastName() ?? '' }}</h1>

        <p class="hero-role">Service financier</p>

        <p>Vue d’ensemble des commandes, devis et paiements à suivre.</p>
    </div>
@endsection

@section('content')
    @use(Database\Seeders\Status)

    <section class="dashboard-shell dashboard-shell-simple">
        <div class="dashboard-title-row">

            <div>
                <h2>Vue d’ensemble</h2>
                <p>Statistiques réelles des commandes accessibles à votre rôle.</p>
            </div>

            <div class="dashboard-header-actions">

                <form class="dashboard-search"
                      method="GET"
                      action="{{ route('orders.index') }}">

                    <input
                        type="text"
                        name="search"
                        placeholder="Rechercher une commande..."
                    >

                </form>

                <div class="dashboard-date">
                    {{ now()->format('d/m/Y') }}
                </div>

            </div>

        </div>


        <div class="dashboard-grid">
            <div class="dashboard-card dashboard-card-success">
                <span class="dashboard-icon">✓</span>
                <div>
                    <p>Traitées aujourd’hui</p>
                    <strong>{{ $dashboardStats['treatedToday'] }}</strong>
                    <small>Livrées et payées aujourd’hui</small>
                </div>
                <a href="{{ route('orders.index', [
                    'status' => Status::LIVRE_ET_PAYE->value,
                    'updated_from' => now()->toDateString()
                ]) }}">Voir</a>
            </div>

            <div class="dashboard-card dashboard-card-primary">
                <span class="dashboard-icon">✓</span>
                <div>
                    <p>Traitées cette semaine</p>
                    <strong>{{ $dashboardStats['treatedWeek'] }}</strong>
                    <small>Livrées et payées depuis lundi</small>
                </div>
                <a href="{{ route('orders.index', [
                    'status' => Status::LIVRE_ET_PAYE->value,
                    'updated_from' => now()->startOfWeek()->toDateString()
                ]) }}">Voir</a>
            </div>

            <div class="dashboard-card dashboard-card-warning">
                <span class="dashboard-icon">!</span>
                <div>
                    <p>À traiter</p>
                    <strong>{{ $dashboardStats['toProcess'] }}</strong>
                    <small>Devis ou paiements en attente</small>
                </div>
                <a href="{{ route('orders.index', ['dashboard_filter' => 'to_process']) }}">Voir</a>
            </div>

            <div class="dashboard-card dashboard-card-info">
                <span class="dashboard-icon">D</span>
                <div>
                    <p>Devis à contrôler</p>
                    <strong>{{ $dashboardStats['quotesToCheck'] }}</strong>
                    <small>Devis reçus à vérifier</small>
                </div>
                <a href="{{ route('orders.index', ['status' => Status::DEVIS->value]) }}">Voir</a>
            </div>

            <div class="dashboard-card dashboard-card-payment">
                <span class="dashboard-icon">€</span>
                <div>
                    <p>Paiements à faire</p>
                    <strong>{{ $dashboardStats['paymentsPending'] }}</strong>
                    <small>Services faits à régler</small>
                </div>
                <a href="{{ route('orders.index', ['status' => Status::SERVICE_FAIT->value]) }}">Voir</a>
            </div>
        </div>

        <div class="dashboard-panels">
            <div class="dashboard-panel">
                <div class="panel-title">
                    <h3>Commandes à traiter</h3>
                    <span>{{ $dashboardPriorities->count() }}</span>
                </div>

                @forelse ($dashboardPriorities as $priorityOrder)
                    <div class="priority-item">
                        <div>
                            <strong>{{ $priorityOrder->getOrderNumber() }}</strong>
                            <p>{{ $priorityOrder->getTitle() }}</p>
                        </div>

                        <a href="{{ route('orders.index', ['search' => $priorityOrder->getOrderNumber()]) }}">
                            Ouvrir
                        </a>
                    </div>
                @empty
                    <p class="empty-dashboard">Aucune commande à traiter.</p>
                @endforelse
            </div>

            <div class="dashboard-panel">
                <div class="panel-title">
                    <h3>Activité récente</h3>
                    <span>{{ $dashboardRecent->count() }}</span>
                </div>

                @forelse ($dashboardRecent as $recentOrder)
                    <div class="activity-item">
                        <span class="activity-dot"></span>
                        <div>
                            <strong>{{ $recentOrder->getOrderNumber() }}</strong>
                            <p>
                                Mise à jour :
                                {{ $recentOrder->updated_at?->format('d/m/Y') ?? 'Non renseignée' }}
                            </p>
                        </div>

                        <a href="{{ route('orders.index', ['search' => $recentOrder->getOrderNumber()]) }}">
                            Ouvrir
                        </a>
                    </div>
                @empty
                    <p class="empty-dashboard">Aucune activité récente.</p>
                @endforelse
            </div>
        </div>
    </section>
@endsection
