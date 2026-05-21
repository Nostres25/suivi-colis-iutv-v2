@use(Database\Seeders\Status)
@use(App\Models\Role)
@use(App\Models\User)
@use(App\Http\Controllers\BaseController)


<div class="modal fade" id="addPurchaseOrderModal-{{$orderId}}" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="addPurchaseOrderModalLabel-{{$orderId}}" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPurchaseOrderModalLabel-{{$orderId}}">Dépôt d'un bon de commande</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if (session()->exists('purchaseOrderError-'.$orderId))
                    <div class="alert alert-danger">
                        {{session('purchaseOrderError-'.$orderId)}}
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
                <form id="addPurchaseOrder-{{$orderId}}" class="ajax-form" method="POST" enctype="multipart/form-data" action="{{route('orders.step-actions.upload-purchase-order', $orderId)}}" autocomplete="off">
                    @csrf
                    <input type="hidden" name="modalId" value="addPurchaseOrderModal-{{$orderId}}">

                    <p>Déposer un bon de commande pour la commande N°{{$order->getOrderNumber()}} : "{{$order->getTitle()}}" </p>
                    <label class="form-label fs-5">Sélectionnez un bon de commande :</label><br/>
                    <small>Fichiers acceptés : pdf, doc, docx jusqu'à 10MB</small>
                    <input type="file" name="purchase_order" id="purchase_order" class="form-control mb-3 @error('purchase_order') is-invalid @enderror" accept="*,.pdf,.docx,.doc" value="{{ old('purchase_order') }}" required>
{{--                    @error('purchase_order')--}}
{{--                    <div class="alert alert-danger">{{ $message }}</div>--}}
{{--                    @enderror--}}

                    <div class="d-flex justify-content-start" title="À cocher si le directeur de l'IUT a signé le bon de commande">
                        <input class="form-check-input me-2" type="checkbox" name="signed"
                               id="checkboxSigned-{{$orderId}}" form="addPurchaseOrder-{{$orderId}}" @checked($canSign)>
                        <label class="form-check-label" for="checkboxSigned-{{$orderId}}">
                            Marquer comme signé par le directeur de l'IUT
                        </label>
                    </div>
                    <hr/>
                    <x-orders.modal.modal-fields.auto-mail-field
                        :orderId="$orderId"
                        :defaultMailContent="BaseController::getDefaultMailContent('update_purchase_order', $order, $user)"
                    ></x-orders.modal.modal-fields.auto-mail-field>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <div class="d-flex justify-content-start" title="Passer la commande du statut {{ $order->getStatus() }} au statut de {{ Status::BON_DE_COMMANDE_NON_SIGNE }} ou de {{ Status::BON_DE_COMMANDE_SIGNE }} si le bon de commande est marqué comme signé.">
                    <input class="form-check-input me-2" name="nextStep" type="checkbox"
                           id="checkboxNextStep-{{$orderId}}" form="addPurchaseOrder-{{$orderId}}" checked>
                    <label class="form-check-label" for="checkboxNextStep-{{$orderId}}">
                        Passer la commande au statut suivant
                    </label>
                </div>
                <div class="d-inline">
                    <button type="submit" form="addPurchaseOrder-{{$orderId}}" class="btn btn-primary">Sauvegarder</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    // TODO ajouter les modals avec le php
    const mailCheckBox = document.getElementById('checkboxMail-{{$orderId}}');
    const mailOptionsDiv = document.getElementById('mailOptionsDiv-{{$orderId}}');

    mailCheckBox.addEventListener('click', (event) => {
        console.debug(mailOptionsDiv.style);
        mailOptionsDiv.style = event.target.checked ? "display:block;" : "display:none;"
    });
</script>
