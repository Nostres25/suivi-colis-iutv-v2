<div class="mb-3">
    <label for="company-name" class="form-label">Nom de l'entreprise @if(@$suffix) fournisseur @endif <span title="champ requis"
                                                                           class="text-danger">*</span></label>
    <input type="text" class="form-control" id="company-name" @required(!@$notRequiered)>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="siret" class="form-label">SIRET @if(@$suffix) du fournisseur @endif<span title="champ requis"
                                                          class="text-danger">*</span></label>
        <input type="text" class="form-control" id="siret" maxlength="14" @required(!@$notRequiered)>
    </div>
    <div class="col-md-6 mb-3">
        <label for="email" class="form-label">Email @if(@$suffix) de contact du fournisseur @endif<span title="champ requis"
                                                          class="text-danger">*</span></label>
        <input type="email" class="form-control" id="email" @required(!@$notRequiered)>
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="phone" class="form-label">Téléphone @if(@$suffix) de contact du fournisseur @endif<span title="champ requis"
                                                              class="text-danger">*</span></label>
        <input type="tel" class="form-control" id="phone" @required(!@$notRequiered)>
    </div>
    <div class="col-md-6 mb-3">
        <label for="contact-name" class="form-label">Nom du contact @if(@$suffix) chez le fournisseur @endif<span title="champ requis"
                                                                          class="text-danger">*</span></label>
        <input type="text" class="form-control" id="contact-name" @required(!@$notRequiered)>
    </div>
</div>
