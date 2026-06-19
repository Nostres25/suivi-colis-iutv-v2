@use(App\Http\Controllers\BaseController)
<div class="modal fade" id="packageInfosModal-{{$orderId}}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form
                id="packageInfos-{{$orderId}}"
                class="ajax-form"
                method="POST"
                action="{{ route('orders.step-actions.package-infos', ['id' => $orderId]) }}"
            >
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">
                        Infos colis
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>

                <div class="modal-body">

                    <p>
                        Modifier les informations des colis de la commande N°{{$order->getOrderNumber()}}.
                    </p>

                    @foreach($order->getPackages() as $package)

                        <div class="border rounded p-3 mb-3">

                            <h6>{{ $package->getName() }}</h6>

                            <div class="mb-3">
                                <label class="form-label">
                                    Nom du colis
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    name="name_{{$package->getId()}}"
                                    value="{{ $package->getName() }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    Numéro de suivi (facultatif)
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    name="tracking_number_{{$package->getId()}}"
                                    value="{{ $package->getTrackingNumber() }}">
                            </div>


                            <div class="mb-3">
                                <label class="form-label">
                                    Coût (facultatif)
                                </label>

                                <input
                                    type="number"
                                    step="0.01"
                                    class="form-control"
                                    name="cost_{{$package->getId()}}"
                                    value="{{ $package->getCout() }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    Délai de livraison estimé (facultatif)
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    name="expected_delivery_time_{{$package->getId()}}"
                                    value="{{ $package->getExpectedDeliveryTime() }}">
                            </div>

                        </div>

                    @endforeach

                    <x-orders.modal.modal-fields.auto-mail-field
                        :orderId="$orderId"
                        :defaultMailContent="BaseController::getDefaultMailContent('package_infos_updated', $order, $user)"
                    ></x-orders.modal.modal-fields.auto-mail-field>

                </div>

                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Fermer
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary">
                        Enregistrer
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>