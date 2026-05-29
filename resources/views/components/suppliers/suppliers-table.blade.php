<table class="table table-striped mb-0">
    {{ $suppliers->links() }}
    <caption>
        Liste des fournisseurs de l'IUT de Villetaneuse
    </caption>
    <thead>
    <tr>
        <th scope="col">Entreprise</th>
        <th scope="col" class="d-none d-md-table-cell">Contact</th>
        <th scope="col" class="d-none d-lg-table-cell">Spécialité</th>
        <th scope="col" class="ps-0">SIRET</th>
        <th scope="col">Statut</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($suppliers as $supplier)
        <tr class="btn-load-modal" data-url="{{ route('suppliers.modal.view-details', ['id' => $supplier->getId(), 'edit' => false]) }}">
            <td class="text-break">
                <strong>{{ $supplier['company_name'] }}</strong><br>
                <small>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope-fill" viewBox="0 0 16 16">
                        <path d="M.05 3.555A2 2 0 0 1 2 2h12a2 2 0 0 1 1.95 1.555L8 8.414zM0 4.697v7.104l5.803-3.558zM6.761 8.83l-6.57 4.027A2 2 0 0 0 2 14h12a2 2 0 0 0 1.808-1.144l-6.57-4.027L8 9.586zm3.436-.586L16 11.801V4.697z"/>
                    </svg> {{ $supplier['email'] }}<br>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-telephone-fill" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z"/>
                    </svg> {{ $supplier['phone_number'] }}
                </small>
            </td>
            <td class="d-none d-md-table-cell">
                <strong>{{ $supplier['contact_name'] }}</strong>
            </td>
            <td class="d-none d-lg-table-cell">{{ $supplier['speciality'] }}</td>
            <td class="text-break ps-0"><code>{{ $supplier['siret'] }}</code></td>
            <td>
                                @if($supplier['is_valid'])
                                        <span class="fournisseurs-badge-valid">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-lg" viewBox="0 0 16 16">
                                                                    <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425z"/>
                                                                </svg> Validé
                                                        </span>
                                @else
                                        <span class="fournisseurs-badge-invalid">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                                    <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                                                </svg> Non validé
                                                        </span>
                                @endif
                <br>
                <small>{{ Str::limit($supplier['note'], 100) }}</small>
            </td>
            <td class="ps-0 pe-0">
                <button class="btn btn-light btn-more-options">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-three-dots-vertical" viewBox="0 0 16 16">
                        <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0"/>
                    </svg>
                </button>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
{{ $suppliers->links() }}
