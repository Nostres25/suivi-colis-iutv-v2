@use(Database\Seeders\Status)

<!-- Modal de création de commande -->
{{-- TODO ajouter un message d'avertissement de validation de formulaire --}}
{{--TODO: Il n'est pas possible de définir une langue par défaut bootstrap mais on peut changer la valeur des attribtuts (notamment pour le message qui apparaît quand on valide sans remplir les champs requis : https://stackoverflow.com/questions/23731862/how-can-i-set-bootstrap-language-manually--}}
{{--TODO pour traduire le message "champ requis", la solution de l'attribut "oninvalid="this.setCustomValidity('Veuillez remplir un titre de commande avant de valider !')" sur l'input ne fonctionne pas. Peut-être trouver une solution (pas prioritaire)--}}

<div class="modal fade" id="createOrderModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="createOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createOrderModalLabel">Création d'une commande<span id="devis-in-title"> (Devis)</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createOrderForm" method="POST" action="{{ route('orders.create') }}" class="needs-validation ajax-form" autocomplete="off" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label for="order-label" class="col-form-label fs-5">Titre de la commande <span
                                title="champ requis" class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="order-label" name="title" placeholder="Ex: Câblage réseau"
                               maxlength="255"
                               required/>
                        <div class="invalid-feedback">
                            Le titre est obligatoire. Veuillez renseigner un titre descriptif concis.
                        </div>
                    </div>
                    <div class="mb-4">
                        {{--TODO Tester le js et gérer le backend--}}
                        <label for="supplierInput" class="col-form-label fs-5">Fournisseur <span title="champ requis"
                                                                                                 class="text-danger">*</span></label><br/>
                        <div class="btn-group mb-3" id="new-or-existant-supplier" role="group" aria-label="Sélectionner un fournisseur ou en ajouter un nouveau">
                            <input type="radio" class="btn-check" name="newOrExistantSupplierRadio" id="selectSupplierRadio" autocomplete="off" checked>
                            <label class="btn btn-outline-primary" for="selectSupplierRadio">Sélectionner un fournisseur existant</label>

                            <input type="radio" class="btn-check" name="newOrExistantSupplierRadio" id="newSupplierRadio" autocomplete="off">
                            <label class="btn btn-outline-primary" for="newSupplierRadio">Demander à valider un nouveau fournisseur</label>
                        </div>
                        <div id="selectSupplierDiv" style="display:block">
                            <input type="text" id="supplierInput" name="supplier_name" class="form-select" list="supplierList"
                                   placeholder="Veuillez écrire ou sélectionner un fournisseur" required/>

                            <datalist id="supplierList">
                                @foreach ($suppliers as $supplier)
                                    <option>{{$supplier->getCompanyName()}}</option>
                                @endforeach
                            </datalist>
                            <div class="mt-1" id="askToAddSupplierDiv" style="display: none;">
                                <div class="alert alert-warning" role="alert">
                                    {{-- TODO lorsqu'il y aura les statuts des fournisseurs, afficher la raison du refus --}}
                                    <p class="fs-5">Fournisseur invalide !</p>
                                    <p>Vous ne pouvez pas commander auprès de ce fournisseur car il n'a pas été validé par le service financier. Si vous choisissez de valider la commande, elle sera en attente de validation du fournisseur. Sinon vous pouvez choisir de changer de fournisseur pour un fournisseur valide ou en ajouter un nouveau qui devra également être validé.
                                        C'est seulement lorsque le fournisseur sera validé que vous pourrez passer la commande à l'état de devis.</p>
                                </div>
                            </div>
                            <div class="alert alert-warning" id="unknown-supplier-msg" style="display: none;">
                                <p class="fs-5">Fournisseur non trouvé</p>
                                <p>Veuillez sélectionner ou écrire le nom exact d'un fournisseur valide. Sinon cliquez sur "Demander à valider un nouveau fournisseur" et remplissez les champs requis. Vous ne pouvez pas commander auprès d'un fournisseur qui n'a pas été validé au préalable par le service financier.</p>
                            </div>
                        </div>
                        <div id="newSupplierDiv" style="display:none">
                            <x-suppliers.fields.supplierCreationFields :suffix="true" :notRequiered="true"></x-suppliers.fields.supplierCreationFields>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="order-num" class="col-form-label fs-5">Numéro de la commande <span
                                title="champ requis" class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="order-num" name="order_num" placeholder="Ex: 4500161828"
                               maxlength="255" required>
                        <div class="invalid-feedback">
                            Le numéro de la commande est obligatoire. Veuillez renseigner le numéro de la commande
                            associé au devis ou au bon de commande (numéro en provenance de chorus).
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="quote-num" class="col-form-label fs-5">Numéro du devis <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="quote-num" name="quote_num"
                               placeholder="Ex: d2509129" maxlength="255" required>
                        <div class="invalid-feedback">
                            Le numéro du devis est obligatoire.
                        </div>
                    </div>
                    {{--                    TODO ne pas oublier de vérifier qu'une option est bien choisie avant de valider--}}
                    {{--                    TODO ajouter une permission "CREER_COMMANDES_POUR_TOUS" qui permet de créer une commande pour d'autres départements--}}
                    @if($userDepartments->count() > 1)
                        <div class="mb-4">
                            <label for="departmentSelect" class="col-form-label fs-5"
                                   title="{{Status::getDescriptions()}}">Département <span title="champ requis"
                                                                                           class="text-danger">*</span></label>
                            <p>
                                Vous êtes membre de plusieurs départements, veuillez choisir pour quel département vous
                                créez cette commande<br/>
                            </p>
                            <select id="departmentSelect" name="department_name" class="form-select" required>
                                <option>
                                    Veuillez sélectionner le département de la commande...
                                </option>
                                @foreach ($userDepartments as $department)
                                    <option>{{$department->getName()}}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="mb-4">
                        <label for="order-description" class="col-form-label fs-5">Description:</label>
                        <dl class="fw-light">Ajoutez des détails sur la commande et son contenu (facultatif).</dl>
                        <textarea class="form-control" id="order-description" name="description"></textarea>
                    </div>
                    {{--                        TODO permettre d'ajouter des colis--}}
                    {{--                    <div class="mb-4">- -}}
                    {{--                        <label class="col-form-label fs-5">Colis <span title="Au moins un colis requis"--}}
                    {{--                                                                       class="text-danger">*</span></label>--}}
                    {{--                        --}}{{-- TODO probablement de trop, autant modifier la commande après l'avoir crée si la commande à une étape avancée. À la première étape de la commande, cette option ne devrait pas exister--}}
                    {{--                        <div class="mb-3">--}}
                    {{--                          <input class="form-check-input" type="checkbox" id="checkboxBonDeCommandeSigne">--}}
                    {{--                          <label class="form-check-label" for="checkboxBonDeCommandeSigne">--}}
                    {{--                            Marquer les colis comme livrés--}}
                    {{--                          </label>--}}
                    {{--                        </div>--}}
                    {{--                        --}}{{-- TODO ajouter les colis progressivement quand on clique sur le bouton avec possibilité de définir : titre, cout, date_prevu_livraison et date_reception --}}
                    {{--                        <div class="newPackages"><p>à suivre</p></div>--}}
                    {{--                        <div class="input-group mb-3">--}}
                    {{--                            <button type="button" class="btn btn-outline-primary">+ Ajoute un colis</button>--}}
                    {{--                        </div>--}}

                    {{--                        --}}{{-- TODO ne devrait apparaîte que si on défini les colis comme livrés --}}
                    {{--                        <div class="input-group mb-3">--}}
                    {{--                            <label class="input-group-text" for="inputFichierBonDeLivraison"><strong>Bon de livraison</strong></label>--}}
                    {{--                            <input type="file" class="form-control" id="inputFichierBonDeLivraison">--}}
                    {{--                        </div>--}}
                    {{--                    </div>--}}
                    <div class="mb-3">
                        <label for="order-input-devis" class="col-form-label fs-5">Devis:</label>
                        <dl class="fw-light">
                            Ajoutez un devis <strong>au format pdf</strong>
                            {{--                            TODO pouvoir remplir automatiquement les champs en fonctions des infos du fichier--}}
                            {{--                            Le contenu présent dans le fichier peut permettre de remplir certains champs vides--}}
                            {{--                            (experimental)--}}
                        </dl>
                        <div id="order-input-devis">
                            <input type="file" class="form-control mb-3" id="inputFichierDevis" name="quote" accept=".pdf,.doc,.docx">
                        </div>
                    </div>
                    <hr/>
                    <div class="mb-4">
                        <label for="advancedInputs" class="col-form-label"><a class="" data-bs-toggle="collapse"
                                                                              href="#advancedInputs" role="button"
                                                                              aria-expanded="false"
                                                                              aria-controls="advancedInputs">Avancé
                                ></a></label>
                        <div class="collapse" id="advancedInputs">
                            <p>Les options avancées servent lorsque vous souhaitez créer une commande qui est déjà à une
                                étape avancée. Après la rédaction d'un bon de commande par exemple.</p>
                            <div class="mb-4">
                                <label for="statusSelect" class="col-form-label fs-5"
                                       title="{{Status::getDescriptions()}}">Statut de la commande</label>
                                <div id="alertLockedStatusBySupplierValue" class="alert alert-warning pb-0" role="alert"
                                     style="display: none">
                                    <p>
                                        Vous ne pouvez pas passer commande auprès d'un fournisseur non validé au
                                        préalable par le service financier.<br/>
                                        Ainsi, la commande restera à l'état ci-dessous tant
                                        qu'elle ne sera pas associée à un fournisseur valide.
                                    </p>
                                </div>
                                <select id="statusSelect" name="status" class="form-select">
                                    @foreach (Status::cases() as $status)
                                        <option
                                            {{ Status::getDefault() == $status ? 'selected="selected"' : '' }} title="{{$status->getDescription()}}" value="{{$status}}">{{$status->getDisplayName()}}</option>
                                    @endforeach
                                </select>
                                <small id="statusDescription"
                                       class="mt-2">{{ Status::getDefault()->getDescription()}}</small>
                            </div>
                            <label for="inputFichierBonDeCommande" class="col-form-label fs-5">Bon de commande</label>
                            <div id="inputsBonDeCommande">
                                <input type="file" class="form-control mb-3" id="inputFichierBonDeCommande" accept=".pdf,.doc,.docx" name="purchase_order">
                                <div class="mb-3 d-flex justify-content-start">
                                    <input class="form-check-input me-2" type="checkbox"
                                           id="checkboxBonDeCommandeSigne">
                                    <label class="form-check-label" for="checkboxBonDeCommandeSigne">
                                        Bon de commande signé par le directeur de l'IUT
                                    </label>
                                </div>
                                <label for="inputCost" class="col-form-label fs-5">Coût total</label>
                                <dl class="fw-light">
                                    Coût total de la commande en euros (€)
                                </dl>
                                <div class="input-group">
                                    <input id="inputCost" name="cost" step="0.01" min="0" maxlength="12" max="2147483647" type="number" class="form-control"
                                           aria-label="Quantité en euros">
                                    <span class="input-group-text">€</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <div class="alert alert-warning" id="invalidSupplierMsg" style="display: none;">
                    Vous ne pouvez pas passer commande car le fournisseur défini n'a pas été validé par le service financier. Si vous choisissez de valider la commande, elle passera à l'état <span title="{{Status::EN_ATTENTE_VALIDATION_FOURNISSEUR->getDescription()}}">"{{Status::EN_ATTENTE_VALIDATION_FOURNISSEUR->getDisplayName()}}" (?)</span>
                </div>
                <div class="d-flex justify-content-start" title="{{Status::BROUILLON->getDescription()}}">
                    <input class="form-check-input me-2" type="checkbox"
                           id="checkboxDraft" form="createOrderForm">
                    <label class="form-check-label" for="checkboxDraft">
                        Enregistrer comme brouillon
                    </label>
                </div>
                <div class="d-inline">
                    <button type="reset" class="btn btn-secondary me-1" form="createOrderForm" data-bs-dismiss="modal">
                        Annuler
                    </button>
                    <button type="submit" form="createOrderForm" class="btn btn-primary ajax-form">Valider</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    const suppliers = {!! json_encode($suppliers) !!};

    const supplierInput = document.getElementById("supplierInput");
    const unknownSupplierMessage = document.getElementById('unknown-supplier-msg');
    const statusSelect = document.getElementById('statusSelect');
    const alertLockedStatusBySupplierValue = document.getElementById('alertLockedStatusBySupplierValue');
    const draftCheckBox = document.getElementById('checkboxDraft');
    const invalidSupplierMsg = document.getElementById('invalidSupplierMsg')
    const askToAddSupplier = document.getElementById("askToAddSupplierDiv");


    const newOrExistantSupplierRadio = document.getElementById('newOrExistantSupplierRadio');
    const selectSupplierRadio = document.getElementById('selectSupplierRadio');
    const newSupplierRadio = document.getElementById('newSupplierRadio');
    const selectSupplierDiv = document.getElementById('selectSupplierDiv');
    const newSupplierDiv = document.getElementById('newSupplierDiv');


    document.getElementById('new-or-existant-supplier').addEventListener('click', (event) => {
        if (event.target.id === 'selectSupplierRadio') {
            if (event.target.checked) {
                selectSupplierDiv.style.display = 'block';
                newSupplierDiv.style.display = 'none';
                supplierInput.required = true;
                document.getElementById('company-name').required = false;
                document.getElementById('email').required = false;
                document.getElementById('phone').required = false;
                document.getElementById('contact-name').required = false;
                changeSupplierStatus(!checkSupplierIsInvalid());

            }
        } else if (event.target.id === 'newSupplierRadio') {
            if (event.target.checked) {
                newSupplierDiv.style.display = 'block';
                selectSupplierDiv.style.display = 'none';
                supplierInput.required = false;
                document.getElementById('company-name').required = true;
                document.getElementById('email').required = true;
                document.getElementById('phone').required = true;
                document.getElementById('contact-name').required = true;
                changeSupplierStatus(false);
            }
        }
    });

    supplierInput.addEventListener("blur", (event) => {
        const supplierName = event.target.value;
        const supplierFound = suppliers.find((supplier) => supplier.company_name === supplierName || supplier.siret === supplierName);
        const supplierInputIsEmpty = supplierName === "";

        if (supplierInputIsEmpty || supplierFound) {
            unknownSupplierMessage.style.display = 'none';
            if (supplierInputIsEmpty || supplierFound?.is_valid) {
                changeSupplierStatus(true);
                askToAddSupplier.style.display = "none";
            } else if (!supplierInputIsEmpty) {
                changeSupplierStatus(false);
                askToAddSupplier.style.display = "block";
            }
        } else {
            changeSupplierStatus(false);
            unknownSupplierMessage.style.display = 'block';
            askToAddSupplier.style.display = "none";
        }
    });


    const statusDescriptions = {!! json_encode(Status::getDescriptionsDict()) !!};
    const statusDescriptionP = document.getElementById('statusDescription');

    statusSelect.addEventListener('change', (event) => {
        statusDescriptionP.textContent = statusDescriptions[event.target.value];
        draftCheckBox.checked = event.target.value === "{{Status::BROUILLON}}" && !draftCheckBox.checked;

        document.getElementById('devis-in-title').textContent = statusSelect.value !== "{{Status::DEVIS}}" && statusSelect.value !== "{{Status::BROUILLON}}" && statusSelect.value !== "{{Status::EN_ATTENTE_VALIDATION_FOURNISSEUR}}" ? '': ' (Devis)';

    });

    draftCheckBox.addEventListener('click', (event) => {
        statusSelect.value = event.target.checked
            ? "{{Status::BROUILLON}}"
            : checkSupplierIsInvalid()
            ? "{{Status::EN_ATTENTE_VALIDATION_FOURNISSEUR}}"
            : "{{Status::getDefault()}}";

        statusDescriptionP.textContent = statusDescriptions[statusSelect.value];
    });

    function changeSupplierStatus(isValid) {
        if (isValid) {
            statusSelect.disabled = false;
            if (statusSelect.value !== "{{Status::BROUILLON->value}}") statusSelect.value = "{{ Status::DEVIS }}";
            alertLockedStatusBySupplierValue.style.display = "none";
            invalidSupplierMsg.style.display='none';
        } else {
            statusSelect.disabled = true;
            if (statusSelect.value !== "{{Status::BROUILLON->value}}") statusSelect.value = "{{ Status::EN_ATTENTE_VALIDATION_FOURNISSEUR }}";
            alertLockedStatusBySupplierValue.style.display = "block";
            invalidSupplierMsg.style.display='block';
        }
    }

    function checkSupplierIsInvalid() {
        const supplierName = supplierInput.value;
        const supplierFound = suppliers.find((supplier) => supplier.company_name === supplierName || supplier.siret === supplierName);
        const supplierInputIsEmpty = supplierName === "";

        return newSupplierRadio.checked || (!supplierInputIsEmpty && !supplierFound?.is_valid);
    }


</script>
