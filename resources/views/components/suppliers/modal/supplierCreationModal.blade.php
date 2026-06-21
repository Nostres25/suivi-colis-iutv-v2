@use(App\Models\Supplier)
@use(\Database\Seeders\PermissionValue)

@php
    // Recalcul propre et sécurisé de la permission directement sur l'objet $user
    $canManageSupplier = $user->hasPermission(PermissionValue::GERER_FOURNISSEURS);
@endphp

<div class="modal fade" id="addSupplierModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="addSupplierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSupplierModalLabel">Ajouter un fournisseur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addSupplierForm" class="needs-validation ajax-form" action="{{ route('suppliers.create') }}">
                @csrf
                    <x-base.alert :errors="$errors"></x-base.alert>
                    <x-suppliers.fields.supplierCreationFields
                        :suffix="false"
                        :notRequiered="false"
                        :companyName="@$companyName"
                        :siret="@$siret"
                        :email="@$email"
                        :phoneNumber="@$phoneNumber"
                        :contactName="@$contactName"
                        :address="@$address"
                        :errors="$errors"
                    ></x-suppliers.fields.supplierCreationFields>                    <div class="mb-3">
                        <label for="note" class="form-label">Note / Remarque</label>
                        <textarea class="form-control @error('note') is-invalid @enderror" id="note" name="note" rows="3">@isset($note){{$note}}@endisset</textarea>
                        <div class="invalid-feedback">{{@$errors->get('note')[0]}}</div>
                    </div>
                    <div class="mb-3">
                        <label for="supplier-status" class="form-label">Statut de validation</label>
                        @if($canManageSupplier)
                            <select class="form-select" id="supplier-status" name="isValid" required>
                                @foreach (Supplier::validityOptions() as $value => $label)
                                    <option value="{{ $value }}" @selected(isset($isValid) ? @$isValid == $value : $value === Supplier::VALIDITY_STATUS_VALIDATED)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">{{@$errors->get('isValid')[0]}}</div>

                        @else
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
