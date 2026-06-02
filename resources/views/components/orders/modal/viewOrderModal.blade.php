@use(Database\Seeders\PermissionValue)
@use(Database\Seeders\Status)

{{-- Modal de détails ou/et de modifications de commande --}}
<div class="modal fade" id="orderModal{{ $orderId }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            {{-- HEADER --}}
            <div class="modal-header">
                @if($edit)
                    <h5 class="modal-title fw-bold d-none d-md-block">Modifier la commande</h5>
                    {{-- Modification du TITRE --}}
                    <input type="text" name="title" form="editOrderForm-{{$orderId}}" class="mb-0 ms-2 form-control fw-bold" minlength="1" maxlength="255" value="{{ $order->getTitle() }}" required placeholder="Titre de la commande"/>
                @else
                    <h5 class="modal-title fw-bold">Commande : {{ $order->getTitle() }}</h5>
                @endif

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                {{-- MESSAGES FLASH --}}
                @if (session()->exists('orderError-'.$orderId))
                    <div class="alert alert-danger">
                        {{session()->get('orderError-'.$orderId)}}
                    </div>
                @endif
                @if (session()->exists('orderSuccess'))
                    <div class="alert alert-success">
                        {{session('orderSuccess')}}
                    </div>
                @endif

                {{-- BOUTON BASCULE VUE/EDITION --}}
                @if($user->hasPermission(PermissionValue::MODIFIER_TOUTES_COMMANDES) || ($user->hasPermission(PermissionValue::MODIFIER_COMMANDES_DEPARTEMENT) && $userDepartments->contains($order->getDepartment())))
                    <div class="d-flex justify-content-end mb-3">
                        @if($edit)
                            <button class="btn btn-outline-secondary btn-sm btn-load-modal" id="returnViewOrderButton-{{$orderId}}" title="Annuler et retourner à l'affichage" type="button" data-url="{{ route('orders.modal.view-details', ['id' => $orderId, 'edit' => false]) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
                                </svg> Retour
                            </button>
                        @else
                            <button class="btn btn-outline-primary btn-sm btn-load-modal" id="editOrderButton-{{$orderId}}" title="Modifier les informations" type="button" data-url="{{ route('orders.modal.view-details', ['id' => $orderId, 'edit' => true]) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                    <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                    <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                </svg> Modifier
                            </button>
                        @endif
                    </div>
                @endif

                {{-- =================================================================== --}}
                {{-- PARTIE VUE (LECTURE SEULE) --}}
                {{-- =================================================================== --}}
                @if(!$edit)
                    @php
                        $status = $order->getStatus();

                        $statusDisplayNames = [
                            Status::BROUILLON->value => 'Brouillon',
                            Status::DEVIS->value => 'Devis',
                            Status::DEVIS_REFUSE->value => 'Devis refusé',
                            Status::BON_DE_COMMANDE_NON_SIGNE->value => 'Bon de commande non signé',
                            Status::BON_DE_COMMANDE_REFUSE->value => 'Bon de commande refusé',
                            Status::BON_DE_COMMANDE_SIGNE->value => 'Bon de commande signé',
                            Status::COMMANDE->value => 'Commande envoyée',
                            Status::COMMANDE_REFUSEE->value => 'Commande refusée',
                            Status::COMMANDE_AVEC_REPONSE->value => 'Commande acceptée',
                            Status::PARTIELLEMENT_LIVRE->value => 'Partiellement livré',
                            Status::SERVICE_FAIT->value => 'Service fait',
                            Status::LIVRE_ET_PAYE->value => 'Livré et payé',
                            Status::ANNULE->value => 'Annulé',
                        ];

                        $refusedStatuses = [
                            Status::DEVIS_REFUSE,
                            Status::BON_DE_COMMANDE_REFUSE,
                            Status::COMMANDE_REFUSEE,
                        ];

                        $signedPurchaseOrderStatuses = [
                            Status::BON_DE_COMMANDE_SIGNE,
                            Status::COMMANDE,
                            Status::COMMANDE_REFUSEE,
                            Status::COMMANDE_AVEC_REPONSE,
                            Status::PARTIELLEMENT_LIVRE,
                            Status::SERVICE_FAIT,
                            Status::LIVRE_ET_PAYE,
                        ];

                        $hasQuote = $order->getAttribute('path_quote');
                        $hasPurchaseOrder = $order->getAttribute('path_purchase_order');
                        $hasDeliveryNote = $order->getAttribute('path_delivery_note');
                        $hasDocs = $hasQuote || $hasPurchaseOrder || $hasDeliveryNote;
                        $isPurchaseOrderSigned = in_array($status, $signedPurchaseOrderStatuses, true);
                    @endphp

                    <div id="viewPart">
                        <div class="border rounded-3 bg-light p-3 mb-4">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                                <div>
                                    <div class="text-muted small fw-bold text-uppercase mb-1">Auteur de la commande</div>
                                    <div class="fw-semibold fs-5">
                                        {{ $order->getAuthor()->getFullName() ?? 'Non renseigné' }}
                                    </div>
                                </div>

                                <div>
                                    <div class="text-muted small fw-bold text-uppercase mb-1">Fournisseur</div>
                                    <div class="fw-semibold fs-5">
                                        {{ $order->getSupplier()->getCompanyName() ?? 'Non renseigné' }}
                                    </div>
                                </div>

                                <div>
                                    <div class="text-muted small fw-bold text-uppercase mb-1">Statut</div>
                                    <span class="orders-status-badge" title="{{ $status->getDescription() }}">
                        {{ $statusDisplayNames[$status->value] ?? $status->value }}
                    </span>
                                </div>
                            </div>
                        </div>

                        @if(in_array($status, $refusedStatuses, true))
                            <div class="alert alert-danger mb-4">
                                <div class="fw-bold mb-1">Commande refusée</div>
                                <div>
                                    {{ $status->getDescription() }}
                                </div>
                            </div>
                        @endif

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 h-100">
                                    <div class="text-muted small fw-bold text-uppercase mb-1">N° Commande</div>
                                    <div class="fw-semibold fs-5">#{{ $order->getOrderNumber() }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 h-100">
                                    <div class="text-muted small fw-bold text-uppercase mb-1">Département</div>
                                    <div class="fs-5">{{ $order->getDepartment()->getName() }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 h-100">
                                    <div class="text-muted small fw-bold text-uppercase mb-1">Coût total</div>
                                    <div class="fs-5 text-primary fw-semibold">{{ $order->getCostFormatted() }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 h-100">
                                    <div class="text-muted small fw-bold text-uppercase mb-1">Référence devis</div>
                                    <div class="fs-5">{{ $order->getQuoteNumber() ?? 'Non renseigné' }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 h-100">
                                    <div class="text-muted small fw-bold text-uppercase mb-1">Date de création</div>
                                    <div>{{ $order->getCreationDate() }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 h-100">
                                    <div class="text-muted small fw-bold text-uppercase mb-1">Dernière modification</div>
                                    <div>{{ $order->getLastUpdateDate() }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="text-muted small fw-bold text-uppercase mb-2">Description</div>
                            <div class="p-3 bg-light rounded-3 border">
                                {{ $order->getDescription() ?? 'Aucune description renseignée.' }}
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="text-muted small fw-bold text-uppercase mb-2">Documents associés</div>

                            @if(!$hasDocs)
                                <div class="text-muted fst-italic border rounded-3 p-3 bg-light">
                                    Aucun document associé à cette commande.
                                </div>
                            @else
                                <div class="row g-2">
                                    @if($hasQuote)
                                        <div class="col-md-4">
                                            <a href="{{ route('orders.download', ['id' => $order->getId(), 'type' => 'quote']) }}"
                                               target="_blank"
                                               class="btn btn-outline-dark w-100 d-flex justify-content-center align-items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-text" viewBox="0 0 16 16">
                                                    <path d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5"/>
                                                    <path d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5zm0 1v2A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z"/>
                                                </svg>
                                                Devis
                                            </a>
                                        </div>
                                    @endif

                                    @if($hasPurchaseOrder)
                                        <div class="col-md-4">
                                            <a href="{{ route('orders.download', ['id' => $order->getId(), 'type' => 'purchase_order']) }}"
                                               target="_blank"
                                               class="btn btn-outline-dark w-100 d-flex justify-content-center align-items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-check" viewBox="0 0 16 16">
                                                    <path d="M10.854 7.854a.5.5 0 0 0-.708-.708L7.5 9.793 6.354 8.646a.5.5 0 1 0-.708.708l1.5 1.5a.5.5 0 0 0 .708 0z"/>
                                                    <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2M9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z"/>
                                                </svg>
                                                Bon de commande
                                            </a>

                                            @if($isPurchaseOrderSigned)
                                                <div class="text-success small fw-semibold mt-2 text-center">
                                                    Signé par le directeur
                                                </div>
                                            @else
                                                <div class="text-muted small mt-2 text-center">
                                                    Non marqué comme signé
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    @if($hasDeliveryNote)
                                        <div class="col-md-4">
                                            <a href="{{ route('orders.download', ['id' => $order->getId(), 'type' => 'delivery_note']) }}"
                                               target="_blank"
                                               class="btn btn-outline-dark w-100 d-flex justify-content-center align-items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-seam" viewBox="0 0 16 16">
                                                    <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5l2.404.961L10.404 2zm3.564 1.426L5.596 5 8 5.961 14.154 3.5zm3.25 1.7-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464z"/>
                                                </svg>
                                                Bon de livraison
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- =================================================================== --}}
                {{-- PARTIE EDITION (FORMULAIRE) --}}
                {{-- =================================================================== --}}
                @if($edit)
                    <div id="editPart">
                        <form id="editOrderForm-{{$orderId}}" class="ajax-form needs-validation" method="POST" enctype="multipart/form-data" action="{{route('orders.modal.view-details', ['id' => $orderId, 'edit' => true])}}" autocomplete="off">
                            @csrf

                            {{-- ... (Champs Titre, Numéro, Coût, Description restent inchangés) ... --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="text-muted small fw-bold text-uppercase ps-1">N° Commande</label>
                                    <input type="text" name="order_num" class="mb-0 form-control" maxlength="255" value="{{ $order->getOrderNumber() }}" required/>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small fw-bold text-uppercase ps-1">Coût Total (€)</label>
                                    <input type="number" step="0.01" name="cost" class="mb-0 form-control" value="{{ $order->getCost() }}"/>
                                </div>
                            </div>



                            <div class="mb-3">
                                <label class="text-muted small fw-bold text-uppercase ps-1">Description</label>
                                <textarea name="description" class="form-control" rows="4" required>{{ $order->getDescription() }}</textarea>
                            </div>

                            {{-- SELECTEUR DE STATUT MODIFIÉ --}}
                            <div class="mb-3">
                                <label class="text-muted small fw-bold text-uppercase ps-1">Statut de la commande</label>
                                {{-- Ajout de name="status" --}}
                                <select id="statusSelectOrder-{{$orderId}}" name="status" class="form-select status-selector">
                                    @foreach (Status::cases() as $status)
                                        {{-- Ajout de data-description pour le JS et value explicite --}}
                                        <option
                                            value="{{ $status->value }}"
                                            data-description="{{ $status->getDescription() }}"
                                            {{ $order->getStatus() == $status ? 'selected="selected"' : '' }}>
                                            {{$status}}
                                        </option>
                                    @endforeach
                                </select>
                                {{-- Ajout d'une classe pour ciblage JS facile --}}
                                <small id="statusDescription-{{$orderId}}" class="mt-2 d-block text-muted">
                                    {{ $order->getStatus()->getDescription()}}
                                </small>
                                {{-- Zone pour le message de suggestion automatique --}}
                                <small id="autoStatusMsg-{{$orderId}}" class="text-success fw-bold d-none"></small>
                            </div>


                            <hr/>

                            {{-- GESTION DES DOCUMENTS --}}
                            <div class="mb-3">
                                <h6 class="fw-bold mb-3">Gestion des documents</h6>
                                <p class="small text-muted mb-3">Sélectionnez un fichier pour l'ajouter ou le remplacer.</p>

                                <div class="mb-2">
                                    <label class="form-label small fw-bold">Devis @if($order->getAttribute('path_quote')) <span class="badge bg-success ms-2">Existant</span> @endif</label>
                                    <input type="file" name="quote" class="form-control" accept=".pdf,.doc,.docx">
                                    <div class="mt-1">
                                        <label class="form-label ps-1 small">Numéro de devis</label>
                                        <input type="text" name="quote_num" class="mb-0 form-control" maxlength="255" value="{{ $order->getQuoteNumber() }}"/>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label small fw-bold">
                                        Bon de commande
                                        @if($order->getAttribute('path_purchase_order'))
                                            <span class="badge bg-success ms-2">
                                                Existant
                                                @if(in_array($order->getStatus(), [
                                                    Status::BON_DE_COMMANDE_SIGNE,
                                                    Status::COMMANDE,
                                                    Status::COMMANDE_REFUSEE,
                                                    Status::COMMANDE_AVEC_REPONSE,
                                                    Status::PARTIELLEMENT_LIVRE,
                                                    Status::SERVICE_FAIT,
                                                    Status::LIVRE_ET_PAYE,
                                                ], true))
                                                        - signé
                                                    @endif
                                            </span>
                                        @endif
                                    </label>
                                    <input type="file" name="purchase_order" id="inputPurchaseOrder-{{$orderId}}" class="form-control" accept=".pdf,.doc,.docx">

                                    <div class="mt-2">
                                        <input class="form-check-input me-2" type="checkbox" name="signed"
                                               id="checkboxSigned-{{$orderId}}"
                                               form="editOrderForm-{{$orderId}}"
                                            @checked($user->hasPermission(PermissionValue::SIGNER_BONS_DE_COMMANDES))>
                                        <label class="form-check-label" for="checkboxSigned-{{$orderId}}">
                                            Marquer comme signé par le directeur de l'IUT
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label small fw-bold">Bon de livraison @if($order->getAttribute('path_delivery_note')) <span class="badge bg-success ms-2">Existant</span> @endif</label>
                                    {{-- Ajout d'un ID spécifique pour le ciblage JS --}}
                                    <input type="file" name="delivery_note" id="inputDeliveryNote-{{$orderId}}" class="form-control" accept=".pdf,.doc,.docx">
                                </div>
                            </div>

                        </form>
                    </div>
                @endif
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                @if($edit)
                    <button class="btn btn-primary" type="submit" form="editOrderForm-{{$orderId}}">Enregistrer</button>
                @endif
            </div>
        </div>
    </div>
</div>
