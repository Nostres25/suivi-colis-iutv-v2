@use(App\Models\Supplier)
@use(Database\Seeders\PermissionValue)

@php
    $canManageSupplier = $user->hasPermission(PermissionValue::GERER_FOURNISSEURS);
@endphp

<div class="modal fade" id="supplierModal{{ $supplier->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                @if($canManageSupplier && $edit)
                    <h5 class="modal-title fw-bold">Modifier</h5>
                    <input type="text" name="companyName" form="editSupplier-{{$supplierId}}" class="mb-0 ms-2 form-control fw-bold" minlength="1" maxlength="255" value="{{ $supplier->getCompanyName() }}" required/>
                @else
                    <h5 class="modal-title fw-bold">Détails sur {{ $supplier->getCompanyName() }}</h5>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                @if (session()->has('supplierError-'.$supplierId))
                    <div class="alert alert-danger">
                        {{ session('supplierError-'.$supplierId) }}
                    </div>
                @endif
                @if (session()->has('supplierSuccess'))
                    <div class="alert alert-success">
                        {{ session('supplierSuccess') }}
                    </div>
                @endif

                @if($canManageSupplier)
                    <div class="mb-3">
                        @if($edit)
                            <button class="btn btn-sm btn-secondary btn-load-modal" id="returnViewSupplierButton" title="Retourner à l'affichage" type="button" data-url="{{ route('suppliers.modal.view-details', ['id' => $supplier->getId(), 'edit' => false]) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill me-1" viewBox="0 0 16 16">
                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                                </svg> Voir les détails
                            </button>
                        @else
                            <button class="btn btn-sm btn-primary btn-load-modal" id="editSupplierButton" title="Modifier le fournisseur" type="button" data-url="{{ route('suppliers.modal.view-details', ['id' => $supplier->getId(), 'edit' => true]) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square me-1" viewBox="0 0 16 16">
                                    <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                    <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                </svg> Modifier la fiche
                            </button>
                        @endif
                    </div>
                @endif

                <form id="editSupplier-{{$supplierId}}" class="ajax-form needs-validation" method="POST" enctype="multipart/form-data" action="{{ route('suppliers.modal.view-details', ['id' => $supplierId, 'edit' => $edit]) }}" autocomplete="off" novalidate>
                    @csrf
                    
                    {{-- MODE LECTURE SEULE --}}
                    <div id="viewPart" style="display: {{ $edit ? 'none' : 'block' }}">
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="text-muted small fw-bold text-uppercase">SIRET</label>
                                <p class="mb-0"><code>{{ $supplier->getSiret() }}</code></p>
                            </div>
                            <div class="col-6">
                                <label class="text-muted small fw-bold text-uppercase">Spécialité</label>
                                <p class="mb-0">{{ $supplier->getSpeciality() ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small fw-bold text-uppercase">Adresse</label>
                            <p class="mb-0">{{ $supplier->getAddress() ?? 'Aucune adresse spécifiée' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small fw-bold text-uppercase">Contact</label>
                            <p class="mb-0 fw-semibold">{{ $supplier->getContactName() ?? 'Aucun contact spécifié' }}</p>
                            @if($supplier->getEmail()) <p class="mb-0 small text-muted">{{ $supplier->getEmail() }}</p> @endif
                            @if($supplier->getPhoneNumber()) <p class="mb-0 small text-muted">{{ $supplier->getPhoneNumber() }}</p> @endif
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small fw-bold text-uppercase">Statut de validation</label>
                            <div>
                                @php
                                    $statusBadgeClass = match ($supplier->getValidityBadgeClass()) {
                                        'valid' => 'bg-success',
                                        'pending' => 'bg-warning text-dark',
                                        default => 'bg-danger',
                                    };
                                @endphp
                                <span class="badge {{ $statusBadgeClass }}">{{ $supplier->getValidityLabel() }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- MODE ÉDITION --}}
                    @if($canManageSupplier)
                        <div id="editPart" style="display: {{ $edit ? 'block' : 'none' }}">
                            <div class="mb-3">
                                <label for="editSiret-{{$supplierId}}" class="text-muted small fw-bold text-uppercase ps-1">SIRET <span class="text-danger">*</span></label>
                                <input type="text" id="editSiret-{{$supplierId}}" name="siret" class="form-control" pattern="[0-9]{14}" minlength="14" maxlength="14" value="{{ $supplier->getSiret() }}" @disabled(!$edit) required/>
                                <div class="invalid-feedback">Le numéro SIRET doit comporter exactement 14 chiffres.</div>
                            </div>
                            <div class="mb-3">
                                <label for="editAddress-{{$supplierId}}" class="text-muted small fw-bold text-uppercase ps-1">Adresse <span class="text-danger">*</span></label>
                                <input type="text" id="editAddress-{{$supplierId}}" name="address" class="form-control" maxlength="255" value="{{ $supplier->getAddress() }}" @disabled(!$edit) required/>
                            </div>
                            <div class="mb-3">
                                <label for="editSpeciality-{{$supplierId}}" class="text-muted small fw-bold text-uppercase">Spécialité</label>
                                <input type="text" id="editSpeciality-{{$supplierId}}" name="speciality" class="form-control" maxlength="255" value="{{ $supplier->getSpeciality() }}" @disabled(!$edit)/>
                            </div>
                            <div class="mb-3">
                                <label for="editContactName-{{$supplierId}}" class="text-muted small fw-bold text-uppercase ps-1">Nom du contact <span class="text-danger">*</span></label>
                                <input type="text" id="editContactName-{{$supplierId}}" name="contactName" class="form-control" maxlength="255" value="{{ $supplier->getContactName() }}" @disabled(!$edit) required/>
                            </div>
                            <div class="mb-3">
                                <label for="editEmail-{{$supplierId}}" class="text-muted small fw-bold text-uppercase">Adresse Email <span class="text-danger">*</span></label>
                                <input type="email" id="editEmail-{{$supplierId}}" name="email" class="form-control" maxlength="255" value="{{ $supplier->getEmail() }}" @disabled(!$edit) required/>
                            </div>
                            <div class="mb-3">
                                <label for="editPhone-{{$supplierId}}" class="text-muted small fw-bold text-uppercase">Numéro de téléphone <span class="text-danger">*</span></label>
                                <input type="text" id="editPhone-{{$supplierId}}" name="phoneNumber" class="form-control" maxlength="50" value="{{ $supplier->getPhoneNumber() }}" @disabled(!$edit) required/>
                            </div>
                            <div class="mb-3">
                                <label for="supplierStatus-{{$supplierId}}" class="text-muted small fw-bold text-uppercase">Statut</label>
                                <select class="form-select" name="isValid" id="supplierStatus-{{$supplierId}}" @disabled(!$edit)>
                                    @foreach (Supplier::validityOptions() as $value => $label)
                                        <option value="{{ $value }}" @selected($supplier->getValidityStatus() === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif

                    <div class="mb-3 mt-3">
                        <label for="inputNote-{{$supplier->getId()}}" class="text-muted small fw-bold text-uppercase">Notes & Commentaires Internes</label>
                        <textarea name="note" style="height: 120px" id="inputNote-{{$supplier->getId()}}" class="form-control text-muted small" placeholder="Ajouter une note descriptive..." @disabled(!$user->hasPermission(PermissionValue::NOTES_ET_COMMENTAIRES)) neighborhood>{{$supplier->getNote()}}</textarea>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                @if($user->hasPermission(PermissionValue::NOTES_ET_COMMENTAIRES) || ($canManageSupplier && $edit))
                    <button class="btn btn-primary" type="submit" form="editSupplier-{{$supplierId}}">Sauvegarder les modifications</button>
                @endif
            </div>
        </div>
    </div>
</div>