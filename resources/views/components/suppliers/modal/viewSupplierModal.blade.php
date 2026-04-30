@use(Database\Seeders\PermissionValue)

{{-- Modal de détails ou/et de modifications --}}
<div class="modal fade" id="supplierModal{{ $supplier->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                @if($user->hasPermission(PermissionValue::GERER_FOURNISSEURS) && $edit)
                    <h5 class="modal-title fw-bold">Modifier</h5><input type="text" name="companyName" form="editSupplier-{{$supplierId}}" class="mb-0 ms-2 form-control fw-bold" minlength="1" maxlength="255" value="{{ $supplier->getCompanyName() }}" required/>
                @else
                    <h5 class="modal-title fw-bold">Détails sur {{ $supplier->getCompanyName() }}</h5>
                @endif

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if (session()->exists('supplierError-'.$supplierId))
                    <div class="alert alert-danger">
                        {{session('supplierError-'.$supplierId)}}
                    </div>
                @endif
                @if (session()->exists('supplierSuccess'))
                    <div class="alert alert-success">
                        {{session('supplierSuccess')}}
                    </div>
                @endif

                @if($user->hasPermission(PermissionValue::GERER_FOURNISSEURS))
                    @if($edit)
                        <button  class="btn btn-secondary mb-2 btn-load-modal" id="returnViewSupplierButton" title="Retourner à l'affichage du fournisseur" type="button" data-url="{{ route('suppliers.modal.viewDetails', ['id' => $supplier->getId(), 'edit' => false]) }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                                <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                            </svg>
                        </button>
                    @else
                        <button  class="btn btn-secondary mb-2 btn-load-modal" id="editSupplierButton" title="Modifier le fournisseur" type="button" data-url="{{ route('suppliers.modal.viewDetails', ['id' => $supplier->getId(), 'edit' => true]) }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                            </svg> Modifier
                        </button>
                    @endif

                @endif

                <form id="editSupplier-{{$supplierId}}" class="ajax-form" method="POST" class="needs-validation" enctype="multipart/form-data" action="{{route('suppliers.modal.viewDetails', ['id' => $supplierId, 'edit' => $edit])}}" autocomplete="off">
                    @csrf
                    <div id="viewPart" style="display: {{$edit ? 'none' : 'block'}}">
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="text-muted small fw-bold text-uppercase">SIRET</label>
                                <p class="mb-0"><code>{{ $supplier->siret }}</code></p>
                            </div>
                            <div class="col-6">
                                <label class="text-muted small fw-bold text-uppercase">Spécialité</label>
                                <p class="mb-0">{{ $supplier->speciality ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small fw-bold text-uppercase">Contact</label>
                            <p class="mb-0">{{ $supplier->contact_name }}</p>
                            <p class="mb-0 small text-muted">{{ $supplier->email }}</p>
                            <p class="mb-0 small text-muted">{{ $supplier->phone_number }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small fw-bold text-uppercase">Statut</label>
                            <div>
                                @if($supplier->is_valid)
                                    <span class="badge bg-success">Validé</span>
                                @else
                                    <span class="badge bg-danger">Non validé</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if($user->hasPermission(PermissionValue::GERER_FOURNISSEURS))
                        <div id="editPart" style="display: {{$edit ? 'block' : 'none'}}">
                            <div class="mb-3">
                                <label class="text-muted small fw-bold text-uppercase ps-1">SIRET</label>
                                <input type="number" name="siret" class="mb-0 form-control" minlength="14" maxlength="14" value="{{ $supplier->getSiret() }}" @disabled(!$edit) required/>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small fw-bold text-uppercase">Spécialité</label>
                                <input type="text" name="speciality" class="mb-0 form-control" minlength="1" maxlength="255" value="{{ $supplier->getSpeciality() }}" @disabled(!$edit) required/>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small fw-bold text-uppercase ps-1">Nom du contact</label>
                                <input type="text" name="contactName" class="mb-0 form-control" minlength="1" maxlength="255" value="{{ $supplier->getContactName() }}" @disabled(!$edit) required/>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small fw-bold text-uppercase">Adresse Email</label>
                                <input type="text" name="email" class="mb-0 form-control" minlength="1" maxlength="255" value="{{ $supplier->getEmail() }}" @disabled(!$edit) required/>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small fw-bold text-uppercase">Numéro téléphone</label>
                                <input type="text" name="phoneNumber" class="mb-0 form-control" minlength="1" maxlength="255" value="{{ $supplier->getPhoneNumber() }}" @disabled(!$edit) required/>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small fw-bold text-uppercase">Statut</label>
                                <div>
                                    @if($supplier->isValid())
                                        <span class="badge bg-success">Validé</span>
                                    @else
                                        <span class="badge bg-danger">Non validé</span>
                                    @endif

                                    <div class="d-flex justify-content-start mt-2" title="Cocher pour valider le fournisseur">
                                        <input class="form-check-input me-2" type="checkbox" name="isValid"
                                               id="checkboxIsValid-{{$supplierId}}" @checked($supplier->isValid())>
                                        <label class="form-check-label" for="checkboxIsValid-{{$supplierId}}">
                                            Valider le fournisseur
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <textarea name="note" style="height: 200px" id="inputNote-{{$supplier->getId()}}" class="form-control text-muted small" @disabled(!$user->hasPermission(PermissionValue::NOTES_ET_COMMENTAIRES))>{{$supplier->getNote()}}</textarea>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                @if($user->hasPermission(PermissionValue::NOTES_ET_COMMENTAIRES) || $user->hasPermission(PermissionValue::GERER_FOURNISSEURS))
                    <button class="btn btn-primary end-0" type="submit" form="editSupplier-{{$supplierId}}">Sauvegarder</button>
                @endif
            </div>
        </div>
    </div>
</div>
