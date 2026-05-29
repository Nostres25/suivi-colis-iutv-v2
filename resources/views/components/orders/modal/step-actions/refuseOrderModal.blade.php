@use(App\Http\Controllers\BaseController)
@use(Database\Seeders\Status)
<div class="modal fade" id="refuseOrderModal-{{$orderId}}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Refuser le devis</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <div class="modal-body">
                <p>
                    Vous allez refuser le devis de la commande
                    N°{{$order->getOrderNumber()}} : "{{ $order->getTitle() }}".
                </p>

                <form id="refuseOrder-{{$orderId}}" class="ajax-form" method="POST" action="{{ route('orders.step-actions.refuse', ['id' => $orderId]) }}">
                    @csrf

                    <label for="reason-{{$orderId}}" class="form-label">Raison du refus</label>
                    <textarea
                        id="reason-{{$orderId}}"
                        name="reason"
                        class="form-control"
                        rows="4"
                        maxlength="1000"
                        required
                    ></textarea>
                    <hr/>
                    <x-orders.modal.modal-fields.auto-mail-field
                        :orderId="$orderId"
                        :defaultMailContent="BaseController::getDefaultMailContent('refuse', $order, $user)"
                    ></x-orders.modal.modal-fields.auto-mail-field>
                </form>
            </div>

            <div class="modal-footer">
                <div class="me-auto" title="Passer la commande du statut {{ $order->getStatus()->getDisplayName() }} au statut {{ Status::DEVIS_REFUSE->getDisplayName() }}">
                    <input class="form-check-input me-2" name="nextStep" type="checkbox"
                           id="checkboxNextStep-{{$orderId}}" form="refuseOrder-{{$orderId}}" checked>
                    <label class="form-check-label" for="checkboxNextStep-{{$orderId}}">
                        Passer la commande au statut refusé
                    </label>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-secondary m-1" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" form="refuseOrder-{{$orderId}}" class="btn btn-danger m-1">
                        Refuser le devis
                    </button>
                </div>

            </div>

        </div>
    </div>
</div>
