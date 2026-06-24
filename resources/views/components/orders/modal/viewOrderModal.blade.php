@use(Database\Seeders\PermissionValue)
@use(Database\Seeders\Status)

{{-- Modal de détails ou/et de modifications de commande --}}
<div class="modal fade" id="orderModal{{ $orderId }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            @php
                $status = $order->getStatus();

                $refusedStatuses = [
                    Status::DEVIS_REFUSE,
                    Status::BON_DE_COMMANDE_REFUSE,
                    Status::COMMANDE_REFUSEE,
                ];

                $hasQuote = $order->hasQuote();
                $hasPurchaseOrder = $order->hasPurchaseOrder();
                $hasDeliveryNote = $order->hasDeliveryNote();
                $hasDocs = $hasQuote || $hasPurchaseOrder || $hasDeliveryNote;
                $isPurchaseOrderSigned = $order->hasSignedPurchaseOrder();

                $canEditOrder = $user->hasPermission(PermissionValue::MODIFIER_TOUTES_COMMANDES)
                    || ($user->hasPermission(PermissionValue::MODIFIER_COMMANDES_DEPARTEMENT) && $userDepartments->contains($order->getDepartment()));
            @endphp

            {{-- HEADER --}}
            <div class="modal-header align-items-start">
                @if($edit)
                    <div class="w-100 me-3">
                        <h5 class="modal-title fw-bold mb-2 d-none d-md-block">Modifier la commande</h5>
                        {{-- Modification du TITRE --}}
                        <input type="text" name="title" form="editOrderForm-{{$orderId}}" class="mb-0 form-control fw-bold" minlength="1" maxlength="255" value="{{ $order->getTitle() }}" required placeholder="Titre de la commande"/>
                    </div>
                @else
                    <div>
                        <div class="text-muted small fw-bold text-uppercase mb-1">Détails de la commande</div>
                        <h5 class="modal-title fw-bold mb-0">{{ $order->getTitle() }}</h5>
                    </div>
                @endif

                <button type="button" class="btn-close mt-1" data-bs-dismiss="modal" aria-label="Close"></button>
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
                @if (session()->exists('success'))
                    <div class="alert alert-success mb-0">
                        {{session('success')}}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger mb-0 pb-0">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif


                {{-- BOUTON BASCULE VUE/EDITION --}}
                @if($edit && $canEditOrder)
                    <div class="d-flex justify-content-end mb-3">
                        <button class="btn btn-outline-secondary btn-sm btn-load-modal" id="returnViewOrderButton-{{$orderId}}" title="Annuler et retourner à l'affichage" type="button" data-url="{{ route('orders.modal.view-details', ['id' => $orderId, 'edit' => false]) }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
                            </svg> Retour
                        </button>
                    </div>
                @endif

                {{-- =================================================================== --}}
                {{-- PARTIE VUE (LECTURE SEULE) --}}
                {{-- =================================================================== --}}
                @if(!$edit)
                    <div id="viewPart">
                        <div class="d-flex gap-2">
                            <a href="{{ route('orders.generate.quote', ['id' => $order->getId()]) }}" class="btn btn-outline-primary btn-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-pdf-fill" viewBox="0 0 16 16"><path d="M5.523 12.424q.21-.124.459-.238a8 8 0 0 1-.45.606c-.28.337-.498.516-.635.572l-.035.012a.3.3 0 0 1-.026-.044c-.056-.11-.054-.216.04-.36.106-.165.319-.354.647-.548m2.455-1.647q-.178.037-.356.078a21 21 0 0 0 .5-1.05 12 12 0 0 0 .51.858q-.326.048-.654.114m2.525.939a4 4 0 0 1-.435-.41q.344.007.612.054c.317.057.466.147.518.209a.1.1 0 0 1 .026.064.44.44 0 0 1-.06.2.3.3 0 0 1-.094.124.1.1 0 0 1-.069.015c-.09-.003-.258-.066-.498-.256M8.278 6.97c-.04.244-.108.524-.2.829a5 5 0 0 1-.089-.346c-.076-.353-.087-.63-.046-.822.038-.177.11-.248.196-.283a.5.5 0 0 1 .145-.04c.013.03.028.092.032.198q.008.183-.038.465z"/><path fill-rule="evenodd" d="M4 0h5.293A1 1 0 0 1 10 .293L13.707 4a1 1 0 0 1 .293.707V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2m5.5 1.5v2a1 1 0 0 0 1 1h2zM4.165 13.668c.09.18.23.343.438.419.207.075.412.04.58-.03.318-.13.635-.436.926-.786.333-.401.683-.927 1.021-1.51a11.7 11.7 0 0 1 1.997-.406c.3.383.61.713.91.95.28.22.603.403.934.417a.86.86 0 0 0 .51-.138c.155-.101.27-.247.354-.416.09-.181.145-.37.138-.563a.84.84 0 0 0-.2-.518c-.226-.27-.596-.4-.96-.465a5.8 5.8 0 0 0-1.335-.05 11 11 0 0 1-.98-1.686c.25-.66.437-1.284.52-1.794.036-.218.055-.426.048-.614a1.24 1.24 0 0 0-.127-.538.7.7 0 0 0-.477-.365c-.202-.043-.41 0-.601.077-.377.15-.576.47-.651.823-.073.34-.04.736.046 1.136.088.406.238.848.43 1.295a20 20 0 0 1-1.062 2.227 7.7 7.7 0 0 0-1.482.645c-.37.22-.699.48-.897.787-.21.326-.275.714-.08 1.103"/></svg>
                                Générer le devis
                            </a>
                            <a href="{{ route('orders.generate.purchase-order', ['id' => $order->getId()]) }}" class="btn btn-outline-primary btn-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-pdf-fill" viewBox="0 0 16 16"><path d="M5.523 12.424q.21-.124.459-.238a8 8 0 0 1-.45.606c-.28.337-.498.516-.635.572l-.035.012a.3.3 0 0 1-.026-.044c-.056-.11-.054-.216.04-.36.106-.165.319-.354.647-.548m2.455-1.647q-.178.037-.356.078a21 21 0 0 0 .5-1.05 12 12 0 0 0 .51.858q-.326.048-.654.114m2.525.939a4 4 0 0 1-.435-.41q.344.007.612.054c.317.057.466.147.518.209a.1.1 0 0 1 .026.064.44.44 0 0 1-.06.2.3.3 0 0 1-.094.124.1.1 0 0 1-.069.015c-.09-.003-.258-.066-.498-.256M8.278 6.97c-.04.244-.108.524-.2.829a5 5 0 0 1-.089-.346c-.076-.353-.087-.63-.046-.822.038-.177.11-.248.196-.283a.5.5 0 0 1 .145-.04c.013.03.028.092.032.198q.008.183-.038.465z"/><path fill-rule="evenodd" d="M4 0h5.293A1 1 0 0 1 10 .293L13.707 4a1 1 0 0 1 .293.707V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2m5.5 1.5v2a1 1 0 0 0 1 1h2zM4.165 13.668c.09.18.23.343.438.419.207.075.412.04.58-.03.318-.13.635-.436.926-.786.333-.401.683-.927 1.021-1.51a11.7 11.7 0 0 1 1.997-.406c.3.383.61.713.91.95.28.22.603.403.934.417a.86.86 0 0 0 .51-.138c.155-.101.27-.247.354-.416.09-.181.145-.37.138-.563a.84.84 0 0 0-.2-.518c-.226-.27-.596-.4-.96-.465a5.8 5.8 0 0 0-1.335-.05 11 11 0 0 1-.98-1.686c.25-.66.437-1.284.52-1.794.036-.218.055-.426.048-.614a1.24 1.24 0 0 0-.127-.538.7.7 0 0 0-.477-.365c-.202-.043-.41 0-.601.077-.377.15-.576.47-.651.823-.073.34-.04.736.046 1.136.088.406.238.848.43 1.295a20 20 0 0 1-1.062 2.227 7.7 7.7 0 0 0-1.482.645c-.37.22-.699.48-.897.787-.21.326-.275.714-.08 1.103"/></svg>
                                Générer le bon de commande
                            </a>
                        </div>
                        <div class="border rounded-3 bg-light p-3 mb-4">
                            <div class="row g-3 align-items-center">
                                <div class="col-md-4">
                                    <div class="text-muted small fw-bold text-uppercase mb-1">Auteur de la commande</div>
                                    <div class="fw-semibold fs-5">
                                        {{ $order->getAuthor()?->getFullName() ?? 'Non renseigné' }}
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="text-muted small fw-bold text-uppercase mb-1">Fournisseur</div>
                                    <div class="fw-semibold fs-5">
                                        {{ $order->getSupplier()?->getCompanyName() ?? 'Non renseigné' }}
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="text-muted small fw-bold text-uppercase mb-1">Statut</div>
                                    <span class="orders-status-badge" title="{{ $status->getDescription() }}">
                                        @php
                                            /* @var Status $status */
                                        @endphp
                                        {{ $status->getDisplayName() }}
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
                                    <div class="fs-5">{{ $order->getDepartment()?->getName() ?? 'Non renseigné' }}</div>
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
                @else

                    {{-- =================================================================== --}}
                    {{-- PARTIE EDITION (FORMULAIRE) --}}
                    {{-- =================================================================== --}}
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
                                    <input type="number" step="0.01" min="0" maxlength="12" max="2147483647" name="cost" class="mb-0 form-control" value="{{ $order->getCost() }}"/>
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
                                    @foreach (Status::cases() as $availableStatus)
                                        {{-- Ajout de data-description pour le JS et value explicite --}}
                                        <option
                                            value="{{ $availableStatus->value }}"
                                            data-description="{{ $availableStatus->getDescription() }}"
                                            {{ $order->getStatus() == $availableStatus ? 'selected="selected"' : '' }}>
                                            {{ $availableStatus->getDisplayName() }}
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

                                <div class="mb-3 border rounded-3 p-3">
                                    <label class="form-label small fw-bold d-flex justify-content-between align-items-center">
                                        <span>Devis</span>
                                        @if($hasQuote)
                                            <span class="badge bg-success">Existant</span>
                                        @endif
                                    </label>
                                    <input type="file" name="quote" class="form-control" accept=".pdf,.doc,.docx">
                                    <div class="mt-2">
                                        <label class="form-label ps-1 small">Numéro de devis</label>
                                        <input type="text" name="quote_num" class="mb-0 form-control" maxlength="255" value="{{ $order->getQuoteNumber() }}"/>
                                    </div>
                                </div>

                                <div class="mb-3 border rounded-3 p-3">
                                    <label class="form-label small fw-bold d-flex justify-content-between align-items-center">
                                        <span>Bon de commande</span>
                                        @if($hasPurchaseOrder)
                                            @if($isPurchaseOrderSigned)
                                                <span class="badge bg-success">Existant - signé</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Existant - non signé</span>
                                            @endif
                                        @endif
                                    </label>
                                    <input type="file" name="purchase_order" id="inputPurchaseOrder-{{$orderId}}" class="form-control" accept=".pdf,.doc,.docx">

                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="signed"
                                               id="checkboxSigned-{{$orderId}}"
                                               form="editOrderForm-{{$orderId}}"
                                            {{ $isPurchaseOrderSigned ? 'checked' : '' }}>
                                        <label class="form-check-label" for="checkboxSigned-{{$orderId}}">
                                            Marquer comme signé par le directeur de l'IUT
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-2 border rounded-3 p-3">
                                    <label class="form-label small fw-bold d-flex justify-content-between align-items-center">
                                        <span>Bon de livraison</span>
                                        @if($hasDeliveryNote)
                                            <span class="badge bg-success">Existant</span>
                                        @endif
                                    </label>
                                    {{-- Ajout d'un ID spécifique pour le ciblage JS --}}
                                    <input type="file" name="delivery_note" id="inputDeliveryNote-{{$orderId}}" class="form-control" accept=".pdf,.doc,.docx">
                                </div>
                            </div>

                        </form>
                    </div>
                @endif
            </div>

            <div class="modal-footer d-flex justify-content-between align-items-center">
                <div>
                    @if(!$edit)
                        <form method="POST"
                              action="{{ route('orders.delete', ['id' => $orderId]) }}"
                              onsubmit="return confirm('Voulez-vous vraiment supprimer cette commande ?');">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="btn btn-outline-danger d-inline-flex align-items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1 0-2H5a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1h2.5a1 1 0 0 1 1 1M4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                            </svg>
                            Supprimer
                            </button>
                        </form>
                    @endif
                </div>

                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>

                    @if($edit)
                        <button class="btn btn-primary d-inline-flex align-items-center gap-2" type="submit" form="editOrderForm-{{$orderId}}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2-circle" viewBox="0 0 16 16">
                                <path d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0"/>
                                <path d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0z"/>
                            </svg>
                            Enregistrer
                        </button>
                    @elseif($canEditOrder)
                        <button class="btn btn-primary btn-load-modal d-inline-flex align-items-center gap-2" id="editOrderButton-{{$orderId}}" title="Modifier les informations" type="button" data-url="{{ route('orders.modal.view-details', ['id' => $orderId, 'edit' => true]) }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                            </svg>
                            Modifier
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
