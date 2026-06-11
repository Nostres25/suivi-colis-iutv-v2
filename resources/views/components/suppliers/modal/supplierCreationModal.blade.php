@use(App\Models\Supplier)
@use(\Database\Seeders\PermissionValue)

@php
    // Recalcul propre et sécurisé de la permission directement sur l'objet $user
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
                <form id="addSupplierForm" class="needs-validation" novalidate>
                    @csrf
                    
                    {{-- Inclusion de vos champs d'adresse et coordonnées --}}
                    <x-suppliers.fields.supplierCreationFields :suffix="false" :notRequiered="false" />
                    
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
                        @if($canManageSupplier)
                            {{-- S'affichera correctement pour l'ID 3 (Finance) et ID 1 (Admin) --}}
                            <select class="form-select" id="supplier-status" name="isValid" required>
                                @foreach (Supplier::validityOptions() as $value => $label)
                                    <option value="{{ $value }}" @selected($value === Supplier::VALIDITY_STATUS_VALIDATED)>{{ $label }}</option>
                                @endforeach
                            </select>
                        @else
                            {{-- S'affiche actuellement pour vous (Département Info - ID 4) --}}
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
            e.preventDefault();
            
            if (!this.checkValidity()) {
                e.stopPropagation();
                this.classList.add('was-validated');
                return;
            }

            const formData = new FormData(this);

            fetch('/suppliers', { 
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(async response => {
                const isJson = response.headers.get('content-type')?.includes('application/json');
                const data = isJson ? await response.json() : null;

                if (!response.ok) {
                    if (response.status === 422 && data && data.errors) {
                        let errorMessage = "Erreurs de validation :\n";
                        Object.keys(data.errors).forEach(field => {
                            errorMessage += `- ${data.errors[field].join(', ')}\n`;
                        });
                        alert(errorMessage);
                    } else {
                        alert("Erreur : " + (data?.message || "Une erreur serveur est survenue."));
                    }
                    throw new Error('Validation or Server Failure');
                }
                
                return data;
            })
            .then(data => {
                if (data && data.success) {
                    const modalElement = document.getElementById('addSupplierModal');
                    const modalInstance = bootstrap.Modal.getInstance(modalElement);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                    
                    this.reset();
                    this.classList.remove('was-validated');

                    if (typeof fetchSuppliersTable === "function") {
                        fetchSuppliersTable();
                    } else {
                        window.location.reload();
                    }
                }
            })
            .catch(error => {
                console.error('Submission Processing Failure:', error);
            });
        });
    }
});
</script>
@endif