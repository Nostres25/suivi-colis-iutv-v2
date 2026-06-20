@use(App\Http\Controllers\BaseController)
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

                    <div class="mt-2">
                        <input
                            class="form-check-input me-2"
                            name="withResponse"
                            type="checkbox"
                            id="checkboxWithResponse-{{$orderId}}"
                            onchange="document.getElementById('deliveryDelayBlock-{{$orderId}}').style.display = this.checked ? 'block' : 'none'"
                        >

                        <label class="form-check-label" for="checkboxWithResponse-{{$orderId}}">
                            Avec réponse du fournisseur
                        </label>
                    </div>

                    <div id="deliveryDelayBlock-{{$orderId}}" class="mt-2" style="display:none">
                        <label class="form-label">
                            Délai de livraison estimé par colis (facultatif)
                        </label>

                        @foreach($order->getPackages() as $package)
                            <div class="mb-2">
                                <label for="deliveryDelay-{{$orderId}}-{{$package->getId()}}" class="form-label">
                                    {{ $package->getName() }}
                                </label>

                                <input
                                    type="text"
                                    id="deliveryDelay-{{$orderId}}-{{$package->getId()}}"
                                    name="delivery_delay[{{$package->getId()}}]"
                                    class="form-control"
                                    placeholder="Ex : 15 jours, 2 semaines..."
                                    value="{{ $package->getExpectedDeliveryTime() }}"
                                >
                            </div>
                        @endforeach
                    </div>

                    <x-orders.modal.modal-fields.auto-mail-field
                        :orderId="$orderId"
                        :defaultMailContent="BaseController::getDefaultMailContent('sent_to_supplier', $order, $user)"
                    ></x-orders.modal.modal-fields.auto-mail-field>
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