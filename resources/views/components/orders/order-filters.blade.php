@php
    $currentFilters = $options['filters'] ?? [];
    $currentSearch = $options['search'] ?? '';
    $currentSort = $options['sort'] ?? 'priority';
@endphp

<div class="offcanvas offcanvas-end" tabindex="-1" id="ordersFiltersOffcanvas" aria-labelledby="ordersFiltersOffcanvasLabel">
    <div class="offcanvas-header">
        <div>
            <h5 class="offcanvas-title mb-1" id="ordersFiltersOffcanvasLabel">Filtres avancés</h5>
            <p class="text-muted mb-0">Filtrer par champ précis sans alourdir la recherche générale.</p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Fermer"></button>
    </div>
    <div class="offcanvas-body">
        <form method="GET" action="{{ url('/orders') }}" class="d-grid gap-3">
            <input type="hidden" name="search" value="{{ $currentSearch }}">

            @if($currentSort)
                <input type="hidden" name="sort" value="{{ $currentSort }}">
            @endif

            @foreach(($currentFilters['status'] ?? []) as $status)
                <input type="hidden" name="filters[status][]" value="{{ $status }}">
            @endforeach
            @foreach(($currentFilters['department_id'] ?? []) as $departmentId)
                <input type="hidden" name="filters[department_id][]" value="{{ $departmentId }}">
            @endforeach
            @foreach(($currentFilters['author_id'] ?? []) as $authorId)
                <input type="hidden" name="filters[author_id][]" value="{{ $authorId }}">
            @endforeach

            <div>
                <label class="form-label" for="filter-order-num">N° commande</label>
                <input type="text" class="form-control" id="filter-order-num" name="filters[order_num]" value="{{ $currentFilters['order_num'] ?? '' }}" placeholder="Ex: 2026-015">
            </div>

            <div>
                <label class="form-label" for="filter-title">Titre</label>
                <input type="text" class="form-control" id="filter-title" name="filters[title]" value="{{ $currentFilters['title'] ?? '' }}" placeholder="Ex: achat de...">
            </div>

            <div>
                <label class="form-label" for="filter-quote-num">N° devis</label>
                <input type="text" class="form-control" id="filter-quote-num" name="filters[quote_num]" value="{{ $currentFilters['quote_num'] ?? '' }}" placeholder="Ex: Q-2026-01">
            </div>

            <div>
                <label class="form-label" for="filter-status">Statuts</label>
                <select class="form-select" id="filter-status" name="filters[status][]" multiple size="6">
                    @foreach($filterData['statuses'] as $statusValue => $statusLabel)
                        <option value="{{ $statusValue }}" @selected(in_array($statusValue, $currentFilters['status'] ?? [], true))>{{ $statusLabel }}</option>
                    @endforeach
                </select>
                <div class="form-text">Maintiens `Ctrl` ou `Cmd` pour sélectionner plusieurs statuts.</div>
            </div>

            <div>
                <label class="form-label" for="filter-department">Départements</label>
                <select class="form-select" id="filter-department" name="filters[department_id][]" multiple size="5">
                    @foreach($filterData['departments'] as $department)
                        <option value="{{ $department->getId() }}" @selected(in_array((string) $department->getId(), array_map('strval', $currentFilters['department_id'] ?? []), true))>{{ $department->getName() }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label" for="filter-author">Créateurs</label>
                <select class="form-select" id="filter-author" name="filters[author_id][]" multiple size="6">
                    @foreach($filterData['authors'] as $author)
                        <option value="{{ $author->getId() }}" @selected(in_array((string) $author->getId(), array_map('strval', $currentFilters['author_id'] ?? []), true))>{{ $author->getFullName() }}</option>
                    @endforeach
                </select>
            </div>

            <div class="row g-2">
                <div class="col-6">
                    <label class="form-label" for="filter-created-from">Créée à partir du</label>
                    <input type="date" class="form-control" id="filter-created-from" name="filters[created_from]" value="{{ $currentFilters['created_from'] ?? '' }}">
                </div>
                <div class="col-6">
                    <label class="form-label" for="filter-created-to">Créée jusqu'au</label>
                    <input type="date" class="form-control" id="filter-created-to" name="filters[created_to]" value="{{ $currentFilters['created_to'] ?? '' }}">
                </div>
            </div>

            <div>
                <label class="form-label" for="filter-inactive-days">Inactivité minimale</label>
                <div class="input-group">
                    <input type="number" min="1" class="form-control" id="filter-inactive-days" name="filters[inactive_days]" value="{{ $currentFilters['inactive_days'] ?? '' }}" placeholder="30">
                    <span class="input-group-text">jours</span>
                </div>
            </div>

            <div>
                <label class="form-label" for="filter-sort">Tri</label>
                <select class="form-select" id="filter-sort" name="sort">
                    @foreach($filterData['sortOptions'] as $sortValue => $sortLabel)
                        <option value="{{ $sortValue }}" @selected($sortValue === $currentSort)>{{ $sortLabel }}</option>
                    @endforeach
                </select>
            </div>

            <div class="d-flex gap-2 pt-2">
                <button type="submit" class="btn btn-primary">Appliquer</button>
                <a href="{{ url('/orders') }}" class="btn btn-outline-secondary">Réinitialiser</a>
            </div>
        </form>
    </div>
</div>
