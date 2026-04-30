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
                <form id="addSupplierForm" class="needs-validation" novalidate>
                @csrf
                    <div class="mb-3">
                        <label for="company-name" class="form-label">Nom de l'entreprise <span title="champ requis"
                                                                                               class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="company-name" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="siret" class="form-label">SIRET <span title="champ requis"
                                                                              class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="siret" maxlength="14" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email <span title="champ requis"
                                                                              class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Téléphone <span title="champ requis"
                                                                                  class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="phone" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="contact-name" class="form-label">Nom du contact <span title="champ requis"
                                                                                              class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="contact-name" required>
                        </div>
                    </div>
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
