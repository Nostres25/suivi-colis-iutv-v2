<div class="modal-header">
    <h5 class="modal-title">
        Informations des colis
    </h5>
</div>

<div class="modal-body">

    @foreach($order->getPackages() as $package)

        <div class="border rounded p-3 mb-3">

            <div class="mb-3">
                <label class="form-label">
                    Nom du colis
                </label>

                <input
                    type="text"
                    class="form-control"
                    value="{{ $package->getName() }}">
            </div>

            <div class="mb-3">
                <label class="form-label">
                    Coût
                </label>

                <input
                    type="number"
                    class="form-control"
                    value="{{ $package->getCout() }}">
            </div>

            <div class="mb-3">
                <label class="form-label">
                    Délai de livraison estimé
                </label>

                <input
                    type="text"
                    class="form-control"
                    value="{{ $package->getExpectedDeliveryTime() }}">
            </div>

        </div>

    @endforeach

</div>

<div class="modal-footer">
    <button
        type="button"
        class="btn btn-secondary"
        data-bs-dismiss="modal">
        Fermer
    </button>
</div>