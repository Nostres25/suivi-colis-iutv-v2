<div class="modal fade" id="sentToSupplierModal-{{$orderId}}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    Bon envoyé au fournisseur
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <div class="modal-body">

                <p>
                    Confirmez que le bon de commande de la commande N°{{$order->getOrderNumber()}}
                    a bien été envoyé au fournisseur.
                </p>

                <form id="sentToSupplier-{{$orderId}}" class="ajax-form" method="POST" 
                    action="{{ route('orders.step-actions.sent-to-supplier', ['id' => $orderId]) }}">
                    @csrf

                    <label for="deliveryDelay-{{$orderId}}" class="form-label">
                        Délai de livraison estimé (facultatif)
                    </label>

                    <input
                        type="text"
                        id="deliveryDelay-{{$orderId}}"
                        name="delivery_delay"
                        class="form-control"
                        placeholder="Ex : 15 jours, 2 semaines..."
                    >

                    <div class="mt-2">
                        <input
                            class="form-check-input me-2"
                            name="nextStep"
                            type="checkbox"
                            id="checkboxNextStep-{{$orderId}}"
                            checked
                        >

                        <label class="form-check-label" for="checkboxNextStep-{{$orderId}}">
                            Passer la commande au statut suivant
                        </label>
                    </div>
                </form>

            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn-secondary m-1" data-bs-dismiss="modal">
                    Annuler
                </button>

                <button type="submit" form="sentToSupplier-{{$orderId}}" class="btn btn-primary m-1">
                    Valider
                </button>

            </div>

        </div>
    </div>
</div>