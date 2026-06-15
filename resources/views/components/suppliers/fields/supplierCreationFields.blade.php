<div class="mb-3">
    <label for="company-name" class="form-label">
        Nom de l'entreprise @if(@$suffix) fournisseur @endif <span title="champ requis" class="text-danger">*</span>
    </label>
    <input
        type="text"
        class="form-control @error('companyName') is-invalid @enderror"
        id="company-name"
        name="companyName"
        @required(!@$notRequiered) @isset($companyName) value="{{$companyName}}" @endisset
    />
    <div class="invalid-feedback">{{@$errors->get('companyName')[0]}}</div>

</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="siret" class="form-label">SIRET @if(@$suffix) du fournisseur @endif<span title="champ requis"
                                                          class="text-danger">*</span></label>
        <input
            type="text"
            class="form-control @error('siret') is-invalid @enderror"
            id="siret"
            name="siret"
            maxlength="14"
            minlength="14"
            @required(!@$notRequiered) @isset($siret) value="{{$siret}}" @endisset
        />
        <div class="invalid-feedback">{{@$errors->get('siret')[0]}}</div>
    </div>
    <div class="col-md-6 mb-3">
        <label for="email" class="form-label">Email @if(@$suffix) de contact du fournisseur @endif<span title="champ requis"
                                                          class="text-danger">*</span></label>
        <input
            type="email"
            class="form-control @error('email') is-invalid @enderror"
            id="email"
            name="email"
            @required(!@$notRequiered) @isset($email) value="{{$email}}" @endisset
        />
        <div class="invalid-feedback">{{@$errors->get('email')[0]}}</div>
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="phone" class="form-label">Téléphone @if(@$suffix) de contact du fournisseur @endif<span title="champ requis"
                                                              class="text-danger">*</span></label>
        <input
            type="tel"
            class="form-control @error('phoneNumber') is-invalid @enderror"
            id="phone"
            name="phoneNumber"
            @required(!@$notRequiered) @isset($phoneNumber) value="{{$phoneNumber}}" @endisset
        />
        <div class="invalid-feedback">{{@$errors->get('phoneNumber')[0]}}</div>
    </div>
    <div class="col-md-6 mb-3">
        <label for="contact-name" class="form-label">Nom du contact @if(@$suffix) chez le fournisseur @endif<span title="champ requis"
                                                                          class="text-danger">*</span></label>
        <input
            type="text"
            class="form-control @error('contactName') is-invalid @enderror"
            id="contact-name"
            name="contactName"
            @required(!@$notRequiered) @isset($contactName) value="{{$contactName}}" @endisset
        />
        <div class="invalid-feedback">{{@$errors->get('contactName')[0]}}</div>
    </div>
</div>

<div class="mb-3">
    <label for="address" class="form-label">Adresse @if(@$suffix) du fournisseur @endif <span title="champ requis"
                                                                                                            class="text-danger">*</span></label>
    <input
        type="text"
        class="form-control @error('address') is-invalid @enderror"
        id="address"
        name="address"
        @required(!@$notRequiered) @isset($contactName) value="{{$contactName}}" @endisset
    />
    <div class="invalid-feedback">{{@$errors->get('address')[0]}}</div>
</div>
