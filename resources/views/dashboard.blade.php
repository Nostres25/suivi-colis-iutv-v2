@extends('base')

@section('header')
    <div class="orders-hero-content">
        <span class="hero-kicker">Tableau de bord</span>
        <h1>Bonjour, {{ $user->getFirstname() ?? 'Agent' }} 👋</h1>
        <p>Vue rapide du suivi des commandes, colis et validations de l’IUT de Villetaneuse.</p>
    </div>
@endsection

@section('content')
    @use(Database\Seeders\PermissionValue)

    @php
        $dashboardTotal = method_exists($orders, 'total') ? $orders->total() : $orders->count();
        $dashboardPageCount = $orders->count();

        $dashboardToProcess = 0;
        $dashboardRefused = 0;
        $dashboardDelivered = 0;
        $dashboardRecent = $orders->take(4);
        $dashboardPriorities = $orders->take(3);

        foreach ($orders as $dashboardOrder) {
            $statusText = $dashboardOrder->getStatus()->value ?? $dashboardOrder->getStatus()->name ?? '';
            $statusText = strtolower((string) $statusText);

            if (
                str_contains($statusText, 'devis') ||
                str_contains($statusText, 'bon_de_commande') ||
                str_contains($statusText, 'commande')
            ) {
                $dashboardToProcess++;
            }

            if (
                str_contains($statusText, 'refus') ||
                str_contains($statusText, 'refusé') ||
                str_contains($statusText, 'refuse')
            ) {
                $dashboardRefused++;
            }

            if (
                str_contains($statusText, 'livre') ||
                str_contains($statusText, 'livré') ||
                str_contains($statusText, 'paye') ||
                str_contains($statusText, 'payé') ||
                str_contains($statusText, 'service_fait')
            ) {
                $dashboardDelivered++;
            }
        }
    @endphp

    <section class="dashboard-shell">
        <div class="dashboard-welcome">
            <div class="dashboard-heading">
                <span class="dashboard-label">Suivi colis IUTV</span>
                <h2>Vue d’ensemble</h2>
                <p>Retrouvez rapidement les commandes à suivre, les validations en attente et l’activité récente.</p>
            </div>

            <div class="dashboard-top-search">
                <form method="GET" action="{{ route('orders.index') }}">
                    <span>⌕</span>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search ?? '' }}"
                        placeholder="Rechercher une commande, devis, fournisseur..."
                        autocomplete="off"
                    >
                    <kbd>Ctrl K</kbd>
                </form>
            </div>

            <div class="dashboard-date">
                {{ now()->format('d/m/Y') }}
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="dashboard-card">
                <span class="dashboard-icon">📦</span>
                <div>
                    <p>Total commandes</p>
                    <strong>{{ $dashboardTotal }}</strong>
                </div>
            </div>

            <div class="dashboard-card">
                <span class="dashboard-icon green">✅</span>
                <div>
                    <p>Terminées</p>
                    <strong>{{ $dashboardDelivered }}</strong>
                </div>
            </div>

            <div class="dashboard-card">
                <span class="dashboard-icon orange">⏳</span>
                <div>
                    <p>À traiter</p>
                    <strong>{{ $dashboardToProcess }}</strong>
                </div>
            </div>

            <div class="dashboard-card">
                <span class="dashboard-icon red">⚠️</span>
                <div>
                    <p>Refusées / bloquées</p>
                    <strong>{{ $dashboardRefused }}</strong>
                </div>
            </div>
        </div>

        <div class="dashboard-actions">
            <div>
                <h3>Actions rapides</h3>
                <p>Accès direct aux tâches principales de suivi.</p>
            </div>

            <div class="dashboard-action-buttons">
                <a href="{{ route('orders.index') }}" class="btn btn-primary">
                    Voir les commandes
                </a>

                @if ($user->hasPermission(PermissionValue::CREER_COMMANDES) && !empty($userDepartments))
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-primary">
                        + Nouvelle commande
                    </a>
                @endif

                <a href="/suppliers" class="btn btn-outline-primary">
                    Fournisseurs
                </a>

                <a href="/about" class="btn btn-light">
                    À propos
                </a>
            </div>
        </div>

        <div class="dashboard-panels">
            <div class="dashboard-panel">
                <div class="panel-title">
                    <h3>Commandes prioritaires</h3>
                    <span>{{ $dashboardToProcess }}</span>
                </div>

                @forelse ($dashboardPriorities as $priorityOrder)
                    <div class="priority-item">
                        <div>
                            <strong>{{ $priorityOrder->getOrderNumber() }}</strong>
                            <p>{{ $priorityOrder->getTitle() }}</p>
                        </div>

                        <a href="{{ route('orders.index', ['search' => $priorityOrder->getOrderNumber()]) }}">
                            Voir
                        </a>
                    </div>
                @empty
                    <p class="empty-dashboard">Aucune commande prioritaire.</p>
                @endforelse
            </div>

            <div class="dashboard-panel">
                <div class="panel-title">
                    <h3>Activité récente</h3>
                    <span>{{ $dashboardPageCount }}</span>
                </div>

                @forelse ($dashboardRecent as $recentOrder)
                    <div class="activity-item">
                        <span class="activity-dot"></span>
                        <div>
                            <strong>{{ $recentOrder->getOrderNumber() }}</strong>
                            <p>
                                Dernière mise à jour :
                                {{ $recentOrder->updated_at?->format('d/m/Y') ?? 'Non renseignée' }}
                            </p>
                        </div>
                    </div>
                @empty
                    <p class="empty-dashboard">Aucune activité récente.</p>
                @endforelse
            </div>
        </div>

        <div class="dashboard-panels mt-4">
            <div class="dashboard-panel">
                <div class="panel-title">
                    <h3>Suivi opérationnel</h3>
                </div>

                <div class="activity-item">
                    <span class="activity-dot"></span>
                    <div>
                        <strong>Commandes en cours</strong>
                        <p>{{ $dashboardToProcess }} commande(s) nécessitent une action ou une vérification.</p>
                    </div>
                </div>

                <div class="activity-item">
                    <span class="activity-dot"></span>
                    <div>
                        <strong>Commandes finalisées</strong>
                        <p>{{ $dashboardDelivered }} commande(s) semblent terminées ou payées.</p>
                    </div>
                </div>

                <div class="activity-item">
                    <span class="activity-dot"></span>
                    <div>
                        <strong>Points bloquants</strong>
                        <p>{{ $dashboardRefused }} commande(s) refusées ou bloquées à surveiller.</p>
                    </div>
                </div>
            </div>

            <div class="dashboard-panel">
                <div class="panel-title">
                    <h3>Accès métier</h3>
                </div>

                <div class="priority-item">
                    <div>
                        <strong>Liste des commandes</strong>
                        <p>Consulter, filtrer et modifier les commandes existantes.</p>
                    </div>
                    <a href="{{ route('orders.index') }}">Ouvrir</a>
                </div>

                <div class="priority-item">
                    <div>
                        <strong>Fournisseurs</strong>
                        <p>Consulter les fournisseurs enregistrés dans l’application.</p>
                    </div>
                    <a href="/suppliers">Ouvrir</a>
                </div>

                <div class="priority-item">
                    <div>
                        <strong>À propos</strong>
                        <p>Retrouver les informations générales du projet.</p>
                    </div>
                    <a href="/about">Ouvrir</a>
                </div>
            </div>
        </div>
    </section>
@endsection
