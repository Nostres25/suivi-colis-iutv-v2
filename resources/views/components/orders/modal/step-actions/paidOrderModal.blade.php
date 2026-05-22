@use(Database\Seeders\Status)
@use(App\Models\Role)
@use(App\Models\User)
@use(App\Http\Controllers\BaseController)


<div class="modal fade" id="orderPaidModal-{{$orderId}}" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="orderPaidModalLabel-{{$orderId}}" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderPaidModalLabel-{{$orderId}}">Marquer la commande comme payée</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if (session()->exists('orderPaidError-'.$orderId))
                    <div class="alert alert-danger">
                        {{session('orderPaidError-'.$orderId)}}
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
                <form id="orderPaidr-{{$orderId}}" class="ajax-form" method="POST" enctype="multipart/form-data" action="{{route('orders.step-actions.paid', $orderId)}}" autocomplete="off">
                    @csrf
                    <input type="hidden" name="modalId" value="orderPaidModal-{{$orderId}}">
                    <p>Marquer la commande N°{{$order->getOrderNumber()}}, désignée"{{$order->getTitle()}}" comme payée.</p>

                    <label class="form-label fs-6">Veuillez rectifier le montant payé s'il n'est pas correct :</label><br/>
                    <div class="input-group">
                        <input id="inputCost" name="cost" step="0.01" min="0" maxlength="12" max="2147483647" type="number" class="form-control" value="{{$order->getCost() ?? 0}}"
                               aria-label="Montant payé en euros">
                        <span class="input-group-text">€</span>
                    </div>
                    <hr/>
                    <x-orders.modal.modal-fields.auto-mail-field
                        :orderId="$orderId"
                        :defaultMailContent="BaseController::getDefaultMailContent('paid_order', $order, $user)"
                    ></x-orders.modal.modal-fields.auto-mail-field>
                </form>
            </div>
            <div class="modal-footer">
                <div class="me-auto" title="Passer la commande du statut {{ $order->getStatus()->getDisplayName() }} au statut {{ Status::LIVRE_ET_PAYE->getDisplayName() }}">
                    <input class="form-check-input me-2" name="nextStep" type="checkbox"
                           id="checkboxNextStep-{{$orderId}}" form="orderPaid-{{$orderId}}" checked>
                    <label class="form-check-label" for="checkboxNextStep-{{$orderId}}">
                        Passer la commande au statut suivant
                    </label>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="button" form="orderPaid-{{$orderId}}" class="btn btn-secondary m-1" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" form="orderPaid-{{$orderId}}" class="btn btn-success m-1">Commande payée au montant indiqué</button>
                </div>
            </div>
        </div>
    </div>
</div>
