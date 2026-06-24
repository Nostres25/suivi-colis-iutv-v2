@use(App\Http\Controllers\BaseController)
@use(App\Models\Package)
@use(Database\Seeders\Status)

<div class="modal fade" id="packageInfosModal-{{$orderId}}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    Informations sur les colis
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <div class="modal-body">
                <form
                    id="packageInfos-{{$orderId}}"
                    class="ajax-form"
                    method="POST"
                    action="{{ route('orders.step-actions.package-infos', ['id' => $orderId]) }}"
                >
                    @csrf
                    <x-base.alert :errors="$errors"></x-base.alert>

                    <p>
                        Modifier les informations des colis de la commande N°{{$order->getOrderNumber()}}. intitulée
                        : {{$order->getTitle()}}
                    </p>

                    @foreach($order->getPackages() as $package)
                        @php
                            /* @var Package $package*/
                        @endphp

                        <div class="border rounded p-3 mb-3">

                            <h5>Colis "{{ $package->getName() }}"</h5>

                            <div class="mb-3">
                                <label class="form-label">
                                    Nom du colis
                                </label>

                                <input
                                    type="text"
                                    class="form-control @error("name_{$package->getId()}") is-invalid @enderror"
                                    name="name_{{$package->getId()}}"
                                    value="{{ $package->getName() }}"
                                />
                                <div class="invalid-feedback">{{@$errors->get("name_{$package->getId()}")[0]}}</div>

                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    Numéro de suivi (facultatif)
                                </label>

                                <input
                                    type="text"
                                    class="form-control @error("tracking_number_{$package->getId()}") is-invalid @enderror"
                                    name="tracking_number_{{$package->getId()}}"
                                    value="{{ $package->getTrackingNumber() }}"
                                />
                                <div
                                    class="invalid-feedback">{{@$errors->get("tracking_number_{$package->getId()}")[0]}}</div>

                            </div>


                            <div class="mb-3">
                                <label class="form-label">
                                    Coût (facultatif)
                                </label>

                                <input
                                    type="number"
                                    step="0.01" min="0" maxlength="12" max="2147483647"
                                    class="form-control @error("cost_{$package->getId()}") is-invalid @enderror"
                                    name="cost_{{$package->getId()}}"
                                    value="{{ $package->getCost() }}"
                                />
                                <div class="invalid-feedback">{{@$errors->get("cost_{$package->getId()}")[0]}}</div>

                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    Délai de livraison estimé (facultatif)
                                </label>

                                <input
                                    type="text"
                                    class="form-control @error("expected_delivery_time_{$package->getId()}") is-invalid @enderror"
                                    name="expected_delivery_time_{{$package->getId()}}"
                                    value="{{ $package->getExpectedDeliveryTime() }}"
                                />
                                <div
                                    class="invalid-feedback">{{@$errors->get("expected_delivery_time_{$package->getId()}")[0]}}</div>
                            </div>

                        </div>

                    @endforeach

                    <x-orders.modal.modal-fields.auto-mail-field
                        :orderId="$orderId"
                        :defaultMailContent="BaseController::getDefaultMailContent('package_infos_updated', $order, $user)"
                    ></x-orders.modal.modal-fields.auto-mail-field>
                </form>

            </div>

            <div class="modal-footer">
                <div class="me-auto"
                     title="Passer la commande du statut {{ $order->getStatus()->getDisplayName() }} au statut de {{ Status::COMMANDE_AVEC_REPONSE->getDisplayName() }}.">
                    <input class="form-check-input me-2" name="nextStep" type="checkbox"
                           id="checkboxNextStep-{{$orderId}}" form="packageInfos-{{$orderId}}" checked>
                    <label class="form-check-label" for="checkboxNextStep-{{$orderId}}">
                        Marquer une réponse du fournisseur
                    </label>
                </div>

                <div class="d-flex justify-content-end">
                    <button
                        type="button"
                        class="btn btn-secondary m-1"
                        form="packageInfos-{{$orderId}}"
                        data-bs-dismiss="modal">
                        Fermer
                    </button>
                    <button
                        type="submit"
                        form="packageInfos-{{$orderId}}"
                        class="btn btn-primary m-1">
                        Enregistrer
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
