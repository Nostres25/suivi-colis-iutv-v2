@use(\Database\Seeders\PermissionValue)
<!-- Modal d'ajout de fournisseur -->
<div class="modal fade" id="addSupplierModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="addSupplierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSupplierModalLabel">Ajouter un fournisseur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addSupplierForm" class="needs-validation">
                @csrf
                   <x-suppliers.fields.supplierCreationFields :errors="$errors"></x-suppliers.fields.supplierCreationFields>
                    <div class="mb-3">
                        <label for="speciality" class="form-label">Spécialité</label>
                        <input type="text" class="form-control" id="speciality"
                               placeholder="Ex: Matériel informatique, Fournitures...">
                    </div>
                    <div class="mb-3">
                        <label for="note" class="form-label">Note / Remarque</label>
                        <textarea class="form-control" id="note" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                @if($user->hasPermission(PermissionValue::GERER_FOURNISSEURS))
                    <div class="d-flex justify-content-start"
                         title="Marquer qu'il est possible de passer commande avec ce fournisseur">
                        <input class="form-check-input me-2" type="checkbox"
                               id="checkboxValidate" form="addSupplierForm" checked>
                        <label class="form-check-label" for="checkboxValidate">
                            Valider le fournisseur
                        </label>
                    </div>
                @else
                    <div class="alert alert-info" role="alert">
                        Le fournisseur devra d'abord être validé par le service financier pour pouvoir passer une
                        commande avec.
                    </div>
                @endif
                <div class="d-inline">
                    <button type="reset" class="btn btn-secondary me-1" form="addSupplierForm"
                            data-bs-dismiss="modal">
                        Annuler
                    </button>
                    <button type="submit" form="addSupplierForm" class="btn btn-primary">Ajouter</button>
                </div>
            </div>
        </div>
    </div>
</div>
