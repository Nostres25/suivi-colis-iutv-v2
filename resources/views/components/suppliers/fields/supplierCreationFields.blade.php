<div class="mb-3">
    <label for="company-name" class="form-label">
        Nom de l'entreprise {{ (isset($suffix) && $suffix) ? 'fournisseur' : '' }} 
        <span title="champ requis" class="text-danger">*</span>
    </label>
    <input type="text" class="form-control" id="company-name" name="companyName" {{ (isset($notRequiered) && $notRequiered) ? '' : 'required' }}>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="siret" class="form-label">
            SIRET {{ (isset($suffix) && $suffix) ? 'du fournisseur' : '' }}
            <span title="champ requis" class="text-danger">*</span>
        </label>
        <input type="text" class="form-control" id="siret" name="siret" maxlength="14" {{ (isset($notRequiered) && $notRequiered) ? '' : 'required' }}>
    </div>
    <div class="col-md-6 mb-3">
        <label for="email" class="form-label">
            Email {{ (isset($suffix) && $suffix) ? 'de contact du fournisseur' : '' }}
            <span title="champ requis" class="text-danger">*</span>
        </label>
        <input type="email" class="form-control" id="email" name="email" {{ (isset($notRequiered) && $notRequiered) ? '' : 'required' }}>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="phone" class="form-label">
            Téléphone {{ (isset($suffix) && $suffix) ? 'de contact du fournisseur' : '' }}
            <span title="champ requis" class="text-danger">*</span>
        </label>
        <input type="tel" class="form-control" id="phone" name="phoneNumber" {{ (isset($notRequiered) && $notRequiered) ? '' : 'required' }}>
    </div>
    <div class="col-md-6 mb-3">
        <label for="contact-name" class="form-label">
            Nom du contact {{ (isset($suffix) && $suffix) ? 'chez le fournisseur' : '' }}
            <span title="champ requis" class="text-danger">*</span>
        </label>
        <input type="text" class="form-control" id="contact-name" name="contactName" {{ (isset($notRequiered) && $notRequiered) ? '' : 'required' }}>
    </div>
</div>

<div class="mb-3">
    <label for="address" class="form-label">
        Adresse {{ (isset($suffix) && $suffix) ? 'du fournisseur' : '' }} 
        <span title="champ requis" class="text-danger">*</span>
    </label>
    <input type="text" class="form-control" id="address" name="address" {{ (isset($notRequiered) && $notRequiered) ? '' : 'required' }}>
</div>