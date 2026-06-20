<div class="modal fade" id="deliveredPackagesModal-{{$orderId}}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Marquer des colis comme livrés</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <div class="modal-body">
                <p>
                    Commande N°{{$order->getOrderNumber()}} : "{{ $order->getTitle() }}".
                </p>

                <form id="deliveredPackages-{{$orderId}}" class="ajax-form" method="POST"
                      action="{{ route('orders.step-actions.packages-delivered', ['id' => $orderId]) }}">
                    @csrf

                    <label for="shippingDate-{{$orderId}}" class="form-label">
                        Date de livraison
                    </label>
                    <input type="date"
                           id="shippingDate-{{$orderId}}"
                           name="shipping_date"
                           class="form-control mb-3">

                    <small class="text-muted">
                        Si aucune date n'est renseignée, la date du jour sera utilisée.
                    </small>

                    <hr>

                    <p class="fw-bold mb-2">Colis à marquer comme livrés :</p>

                    @forelse($packages as $package)
                        <div class="form-check mb-2">
                            <input class="form-check-input"
                                   type="checkbox"
                                   name="packages[]"
                                   value="{{ $package->getId() }}"
                                   id="package-{{$orderId}}-{{$package->getId()}}"
                                {{ $checkAll || $package->getShippingDate() ? 'checked' : '' }}>

                            <label class="form-check-label" for="package-{{$orderId}}-{{$package->getId()}}">
                                Colis #{{ $package->getId() }}

                                @if($package->getShippingDate())
                                    <span class="badge bg-success ms-2">
                                        Déjà livré le {{ $package->getShippingDate() }}
                                    </span>
                                @endif
                            </label>
                        </div>
                    @empty
                        <div class="alert alert-warning">
                            Aucun colis n'est associé à cette commande.
                        </div>
                    @endforelse
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary m-1" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" form="deliveredPackages-{{$orderId}}" class="btn btn-success m-1">
                    Confirmer la livraison
                </button>
            </div>

        </div>
    </div>
</div>
