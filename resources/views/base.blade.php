@use(Database\Seeders\Status)

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Suivi IUT Villetaneuse') }}</title>
    <link href="{{asset('bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
    <script type="text/javascript" src="{{asset('bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <link href="{{asset('style.css')}}" rel="stylesheet" type="text/css">
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
</head>
<body>
<header class="mb-5">
    <x-base.nav></x-base.nav>
    <x-base.alert></x-base.alert>

    {{--Bannière bleue--}}
<div class="page-header">
    <div class="container d-flex flex-row-reverse align-items-center justify-content-between">
        <img src="{{ asset('217.png') }}" alt="Logo Sorbonne" style="height: 70px; width: auto; margin-left: 20px;">
        <div>
            @yield('header')
        </div>
    </div>
</div>
</header>
<main>
    @yield('content')
    <div id="modal-container"></div>
</main>
<footer>
    @yield('footer')
    <div class="bg-gray-50 px-8 py-5 text-center border-t">
        <p class="text-sm font-semibold text-gray-700">BUT2 Informatique - IUT de Villetaneuse</p>
        <p class="text-xs text-gray-500 mt-1">Projet SAE - Suivi de Colis • 2024-2025</p>
    </div>
{{--    <div class="min-h-screen bg-gray-50 py-8">--}}
{{--        <div class="mx-auto max-w-5xl">--}}

{{--            <div class="bg-white shadow-lg rounded-xl overflow-hidden">--}}

{{--                <Ancien contrenu footer>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
</footer>
</body>

<script>
    {{--let modalToOpenId = '{{$modalToOpen}}';--}}
    {{--if (modalToOpenId) {--}}
    {{--    console.debug(modalToOpenId);--}}
    {{--    let modalToOpen = new bootstrap.Modal(document.getElementById(modalToOpenId));--}}
    {{--    modalToOpen.show();--}}
    {{--}--}}

    document.addEventListener('DOMContentLoaded', function () {

        const modalContainer = document.getElementById('modal-container');

        // ============================================================
        // 1. GESTION DE L'OUVERTURE (GET) - Via Délégation Globale
        // ============================================================
        // On écoute les clics sur TOUT le document (body)
        document.body.addEventListener('click', function (e) {

            // On cherche si l'élément cliqué (ou un de ses parents) est un bouton .btn-load-modal
            // .closest() est magique : il remonte l'arbre DOM jusqu'à trouver la classe
            const button = e.target.closest('.btn-load-modal');

            // Si on a trouvé un bouton et qu'il a un data-url
            if (button && button.getAttribute('data-url')) {
                e.preventDefault(); // Empêche le comportement par défaut (lien ou submit)

                const url = button.getAttribute('data-url');

                // --- NETTOYAGE (Important si on vient d'un autre modal) ---
                // Si un modal est déjà ouvert, on le détruit proprement avant de charger le suivant
                // Cela évite d'avoir des conflits de fond gris (backdrop)
                const existingModalEl = modalContainer.querySelector('.modal');
                if (existingModalEl) {
                    const existingInstance = bootstrap.Modal.getInstance(existingModalEl);
                    if (existingInstance) {
                        existingInstance.dispose();
                    }
                }
                // -----------------------------------------------------------

                fetch(url)
                    .then(response => response.text())
                    .then(html => {
                        modalContainer.innerHTML = html;

                        const modalElement = modalContainer.querySelector('.modal');
                        // Initialisation du nouveau modal
                        const myModal = new bootstrap.Modal(modalElement);
                        myModal.show();
                    })
                    .catch(error => console.error('Erreur chargement modal:', error));
            }
        });

        // GESTION DU POST (Formulaire)
        modalContainer.addEventListener('submit', function (e) {
            if (e.target && e.target.classList.contains('ajax-form')) {
                e.preventDefault();

                const form = e.target;
                const url = form.action;
                const formData = new FormData(form);

                const existingModalEl = modalContainer.querySelector('.modal');
                let existingInstance = null;
                if (existingModalEl) {
                    existingInstance = bootstrap.Modal.getInstance(existingModalEl);
                }

                fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json, text/html' // On accepte les deux
                    }
                })
                    .then(response => {
                        // On regarde l'entête pour savoir si c'est du JSON ou du HTML
                        const contentType = response.headers.get("content-type");

                        if (contentType && contentType.indexOf("application/json") !== -1) {
                            // C'est du JSON (Succès)
                            return response.json().then(data => {
                                return { type: 'json', data: data };
                            });
                        } else {
                            // C'est du HTML (Erreur / Vue partielle)
                            return response.text().then(html => {
                                return { type: 'html', html: html };
                            });
                        }
                    })
                    .then(result => {
                        // CAS 1 : SUCCÈS (JSON) -> ON RECHARGE LA PAGE
                        if (result.type === 'json' && result.data.status === 'success') {
                            // Comme Laravel a mis un message en session()->flash(),
                            // il s'affichera au rechargement.
                            window.location.reload();
                            return;
                        }

                        // CAS 2 : ERREUR (HTML) -> ON RÉAFFICHE LE MODAL
                        if (result.type === 'html') {
                            const html = result.html;

                            // Sécurité anti page complète
                            if (html.includes('<html') || html.includes('<!DOCTYPE')) {
                                window.location.reload(); // Au cas où
                                return;
                            }

                            // Nettoyage de l'ancien modal
                            if (existingInstance) existingInstance.dispose();
                            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                            document.body.classList.remove('modal-open');

                            // Injection
                            modalContainer.innerHTML = html;

                            // Réouverture
                            const newModalEl = modalContainer.querySelector('.modal');
                            if (newModalEl) {
                                const newModal = new bootstrap.Modal(newModalEl);
                                newModal.show();
                            }
                        }
                    })
                    .catch(error => console.error('Erreur AJAX:', error));

                // --- GESTION DYNAMIQUE DE LA CHECKBOX MAIL ---
                // On écoute l'événement 'change' sur tout le conteneur du modal
                modalContainer.addEventListener('change', function (e) {

                    // 1. On vérifie si l'élément modifié est bien la checkbox "sendMail"
                    if (e.target && e.target.name === 'sendMail') {
                        const checkbox = e.target;

                        // 2. On récupère l'ID de la commande à partir de l'ID de la checkbox
                        // Format HTML : id="checkboxMail-123" -> On coupe au tiret pour avoir "123"
                        const parts = checkbox.id.split('-');
                        const orderId = parts[parts.length - 1]; // Prend le dernier morceau (l'ID numérique)

                        // 3. On cible la div correspondante
                        const targetDiv = document.getElementById('mailOptionsDiv-' + orderId);

                        // 4. On applique la logique d'affichage
                        if (targetDiv) {
                            // Utilisation de .style.display (plus propre que .style = "...")
                            targetDiv.style.display = checkbox.checked ? "block" : "none";
                        }
                    }
                });
            }
        });

        // -------------------------------------------------------------------------
        // CONFIGURATION : Mapping des Enums PHP vers JS
        // -------------------------------------------------------------------------
        const STATUS = {
            BROUILLON:                  "{{ Status::BROUILLON->value }}",
            DEVIS:                      "{{ Status::DEVIS->value }}",
            DEVIS_REFUSE:               "{{ Status::DEVIS_REFUSE->value }}",
            BON_DE_COMMANDE_NON_SIGNE:  "{{ Status::BON_DE_COMMANDE_NON_SIGNE->value }}",
            BON_DE_COMMANDE_REFUSE:     "{{ Status::BON_DE_COMMANDE_REFUSE->value }}",
            BON_DE_COMMANDE_SIGNE:      "{{ Status::BON_DE_COMMANDE_SIGNE->value }}",
            COMMANDE:                   "{{ Status::COMMANDE->value }}",
            COMMANDE_REFUSEE:           "{{ Status::COMMANDE_REFUSEE->value }}",
            COMMANDE_AVEC_REPONSE:      "{{ Status::COMMANDE_AVEC_REPONSE->value }}",
            PARTIELLEMENT_LIVRE:        "{{ Status::PARTIELLEMENT_LIVRE->value }}",
            SERVICE_FAIT:               "{{ Status::SERVICE_FAIT->value }}",
            LIVRE_ET_PAYE:              "{{ Status::LIVRE_ET_PAYE->value }}",
            ANNULE:                     "{{ Status::ANNULE->value }}",
        };

        // -------------------------------------------------------------------------
        // LOGIQUE D'AUTOMATISATION
        // -------------------------------------------------------------------------
        modalContainer.addEventListener('change', function (e) {

            // On ne s'intéresse qu'aux changements dans un formulaire d'édition
            if (!e.target || !e.target.closest('form')) return;

            const target = e.target;

            // Extraction de l'ID de la commande (format "nom-123")
            if (!target.id.includes('-')) return; // Sécurité
            const parts = target.id.split('-');
            const orderId = parts[parts.length - 1];

            // Récupération des éléments du DOM liés à cette commande
            const statusSelect = document.getElementById(`statusSelectOrder-${orderId}`);
            const statusDesc = document.getElementById(`statusDescription-${orderId}`);
            const autoMsg = document.getElementById(`autoStatusMsg-${orderId}`);
            const checkboxSigned = document.getElementById(`checkboxSigned-${orderId}`);
            const fileInputPO = document.getElementById(`inputPurchaseOrder-${orderId}`);

            // Si le selecteur de statut n'existe pas, on n'est pas sur le bon modal
            if (!statusSelect) return;

            const currentStatus = statusSelect.value;
            let newStatus = null;
            let reason = "";

            // =====================================================================
            // CAS 1 & 2 : BON DE COMMANDE (Non signé ou Signé)
            // =====================================================================
            if (target.name === 'purchase_order' || target.name === 'signed') {

                const isSigned = checkboxSigned ? checkboxSigned.checked : false;
                const hasFile = fileInputPO && fileInputPO.files.length > 0;

                // --- CAS 2 : Bon de commande SIGNE (Checkbox cochée) ---
                if (isSigned) {
                    // Statuts éligibles pour passer en BC_SIGNE
                    const allowedStatuses = [
                        STATUS.BROUILLON,
                        STATUS.DEVIS,
                        STATUS.DEVIS_REFUSE,
                        STATUS.BON_DE_COMMANDE_NON_SIGNE,
                        STATUS.BON_DE_COMMANDE_REFUSE
                    ];

                    if (allowedStatuses.includes(currentStatus)) {
                        newStatus = STATUS.BON_DE_COMMANDE_SIGNE;
                        reason = "Statut suggéré suite à la validation du bon de commande signé.";
                    }
                }
                // --- CAS 1 : Bon de commande NON SIGNE (Fichier ajouté mais pas coché) ---
                else if (hasFile) {
                    // Statuts éligibles pour passer en BC_NON_SIGNE
                    const allowedStatuses = [
                        STATUS.BROUILLON,
                        STATUS.DEVIS,
                        STATUS.DEVIS_REFUSE
                    ];

                    if (allowedStatuses.includes(currentStatus)) {
                        newStatus = STATUS.BON_DE_COMMANDE_NON_SIGNE;
                        reason = "Statut suggéré suite à l'ajout du bon de commande.";
                    }
                }
            }

            // =====================================================================
            // CAS 3 : BON DE LIVRAISON
            // =====================================================================
            if (target.name === 'delivery_note' && target.files.length > 0) {

                // On exclut les statuts finaux ou annulés
                const excludedStatuses = [
                    STATUS.LIVRE_ET_PAYE,
                    STATUS.ANNULE,
                    STATUS.SERVICE_FAIT
                ];

                // Si le statut actuel n'est PAS dans les exclus, on propose SERVICE_FAIT
                if (!excludedStatuses.includes(currentStatus)) {
                    newStatus = STATUS.SERVICE_FAIT;
                    reason = "Statut suggéré suite à l'ajout du bon de livraison.";
                }
            }

            // =====================================================================
            // APPLICATION DES CHANGEMENTS
            // =====================================================================
            if (newStatus && newStatus !== currentStatus) {
                // 1. Mise à jour du Select
                statusSelect.value = newStatus;

                // 2. Mise à jour de la description textuelle
                // On cherche l'option correspondante pour récupérer son data-description
                const selectedOption = statusSelect.querySelector(`option[value="${newStatus}"]`);
                if (selectedOption && statusDesc) {
                    statusDesc.innerText = selectedOption.getAttribute('data-description');
                }

                // 3. Feedback visuel utilisateur
                if (autoMsg) {
                    autoMsg.innerText = "✨ " + reason;
                    autoMsg.classList.remove('d-none');

                    // Effet de surbrillance sur le select
                    statusSelect.classList.add('border-success', 'text-success', 'fw-bold');

                    // On retire l'effet visuel après quelques secondes
                    setTimeout(() => {
                        statusSelect.classList.remove('border-success', 'text-success', 'fw-bold');
                    }, 3000);
                }
            }
        });
    });


    // TODO à voir pour ne pas pouvoir sortir des modals sans faire exprès en rechargeant la page par exemple
    // TODO ce serait bien aussi d'ajouter l'avertissement quand on clique sur la croix ou "annuler" MAIS PAS POUR "VALIDER" COMME CE CODE LE FAIT ACTUELLEMENT
    // const buttonsWithErasureAlert = document.querySelectorAll('.erasure-alert');
    // console.debug(buttonsWithErasureAlert)
    // Pour tous les boutons avec la classe d'alerte d'effacement, activer l'alerte avant de décharger la page lorsque cliqué
    // buttonsWithErasureAlert.forEach((button) => {
    //     console.debug(button);
    //     button.addEventListener('click', () => {
    //         window.onbeforeunload = function(){
    //             return 'Êtes-vous sûr de partir ? Si vous déchargez la page, vos modifications seront perdues !';
    //         };
    //     })
    // });
    // for (const button in buttonsWithErasureAlert) {
    //     console.debug(button);
    //     button.addEventListener('click', () => {
    //         window.onbeforeunload = function(){
    //             return 'Êtes-vous sûr de partir ? Si vous déchargez la page, vos modifications seront perdues !';
    //         };
    //     })
    // }
    //
    // // Désactiver le message d'avertissement du déchargement de la page à la fermeture des modals (event bootstrap)
    document.addEventListener('hidden.bs.modal', () => {
        window.location.href = window.location.href;
    });

    document.addEventListener('DOMContentLoaded', () => {
        const loginAlert = document.getElementById('login-alert');
        if (loginAlert) {
            setTimeout(() => {
            loginAlert.classList.add('d-none');
            }, 6000); // 6 sec
        }
        });


</script>
</html>
