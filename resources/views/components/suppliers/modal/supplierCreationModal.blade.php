@use(App\Models\Supplier)
@use(\Database\Seeders\PermissionValue)

@php
    // Remplacement de la logique stricte de rôle par les permissions associées
    $canManageSupplier = $user->hasPermission(PermissionValue::GERER_FOURNISSEURS);
    $canCreateSupplier = $canManageSupplier || $user->hasPermission(PermissionValue::DEMANDER_AJOUT_FOURNISSEUR);
@endphp

@if($canCreateSupplier)
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
                   <x-suppliers.fields.supplierCreationFields></x-suppliers.fields.supplierCreationFields>
                    <div class="mb-3">
                        <label for="speciality" class="form-label">Spécialité</label>
                        <input type="text" class="form-control" id="speciality" name="speciality" placeholder="Ex: Matériel informatique, Fournitures...">
                    </div>
                    <div class="mb-3">
                        <label for="note" class="form-label">Note / Remarque</label>
                        <textarea class="form-control" id="note" name="note" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="supplier-status" class="form-label">Statut de validation</label>
                        {{-- Seuls les utilisateurs avec la permission GERER_FOURNISSEURS ont accès au menu déroulant --}}
                        @if($canManageSupplier)
                            <select class="form-select" id="supplier-status" name="isValid" required>
                                @foreach (Supplier::validityOptions() as $value => $label)
                                    <option value="{{ $value }}" @selected($value === Supplier::VALIDITY_STATUS_VALIDATED)>{{ $label }}</option>
                                @endforeach
                            </select>
                        @else
                            {{-- Assignation automatique en tâche de fond au statut En attente pour les autres --}}
                            <input type="hidden" name="isValid" value="{{ Supplier::VALIDITY_STATUS_PENDING }}" />
                            <input type="text" class="form-control text-muted bg-light" readonly value="{{ Supplier::validityOptions()[Supplier::VALIDITY_STATUS_PENDING] }} (Automatique)">
                        @endif
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <div class="d-inline">
                    <button type="reset" class="btn btn-secondary me-1" form="addSupplierForm" data-bs-dismiss="modal">
                        Annuler
                    </button>
                    <button type="submit" form="addSupplierForm" class="btn btn-primary">Ajouter</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const addSupplierForm = document.getElementById('addSupplierForm');
    
    if (addSupplierForm) {
        addSupplierForm.addEventListener('submit', function (e) {
            e.preventDefault(); // Terminate default browser routing behaviors
            
            // Check basic required attributes criteria
            if (!this.checkValidity()) {
                e.stopPropagation();
                this.classList.add('was-validated');
                return;
            }

            const formData = new FormData(this);

            // Execute processing request context
            fetch('/suppliers', { 
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw response;
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Safe execution to clean up active modal components
                    const modalElement = document.getElementById('addSupplierModal');
                    const modalInstance = bootstrap.Modal.getInstance(modalElement);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                    
                    // Clear out cached contextual inputs
                    this.reset();
                    this.classList.remove('was-validated');

                    // Refresh table without layout breaks if helper exists, otherwise refresh the page
                    if (typeof fetchSuppliersTable === "function") {
                        fetchSuppliersTable();
                    } else {
                        window.location.reload();
                    }
                }
            })
            .catch(async (error) => {
                if (error instanceof Response) {
                    const errorData = await error.json();
                    alert("Erreur : " + (errorData.message || "Une erreur est survenue lors de l'enregistrement."));
                } else {
                    console.error('Submission Processing Failure:', error);
                    alert("Une défaillance réseau ou système s'est produite.");
                }
            });
        });
    }
});
</script>
@endif