@use(Database\Seeders\Status)
@use(Database\Seeders\PermissionValue)
@use(App\Models\Role)
@use(App\Models\Order)

<table class="table table-striped mb-0">
    {{ $orders->links()}}
    <caption>
        @if ($user->hasPermission(PermissionValue::CONSULTER_TOUTES_COMMANDES))
            Liste des commandes
        @else
            Liste des commandes {{($userDepartments->count() > 1 ? "de : " : "du  ").implode(', ', $userDepartments->map(fn (Role $department) => $department->getName())->toArray()).' '}}
        @endif
        à l'IUT de Villetaneuse
    </caption>
    <thead>
    <tr>
        {{-- TODO Pouvoir trier les différentes colonnes --}}
        {{-- TODO mettre les différentes explications sur les colonnes (quand on survole  avec la souris par exemple) --}}
        <th scope="col">N°</th>
        <th scope="col" class="d-none d-sm-table-cell">Département&nbsp<span title="Explications, différents départements" class="d-none d-md-inline">(?)</span></th>
        <th scope="col" class="ps-0 pe-0">Désignation&nbsp<span title="Explications" class="d-none d-md-inline">(?)</span></th>
        <th scope="col">Statut&nbsp<span title="{{Status::getDescriptions()}}" class="d-none d-md-inline">(?)</span></th>
        <th scope="col" class="d-none d-sm-table-cell">Actions&nbsp<span title="Les actions peuvent dépendre de votre rôle" class="d-none d-md-inline">(?)</span></th>
        <th scope="col" class="d-none d-md-table-cell">Date création&nbsp<span title="Explications" class="d-none d-md-inline">(?)</span></th>
    </tr>
    </thead>
    <tbody>
    @foreach ($orders as $order)
        {{-- TODO Pouvoir cliquer sur les commandes pour les détails --}}
        {{-- TODO Pouvoir faire un clique droit sur un élément pour plus d'options --}}
        <tr class="btn-load-modal" data-url="{{ route('orders.modal.view-details', ['id' => $order->getId(), 'edit' => false]) }}">
            <th scope="row" class="text-break">
                #{{ $order['order_num'] }}<br/>
            </th>
            <td class="d-none d-sm-table-cell"><strong>{{ $order->getDepartment()->getName() }}</strong><br></td>
            <td class="ps-0 pe-0">{{ $order->getTitle() }} <span class="d-table-cell d-sm-none">({{$order->getDepartment()->getName()}})</span></td>
            <td>
                <span class="orders-status-badge" title="{{$order->getStatus()->getDescription()}}">{{ $order->getStatus() }}</span><br>
            </td>
            {{-- Mettre des petties icones --}}
            <td class="d-none d-sm-table-cell">
                <div>
                    {{--                            TODO faire fonctionner tous les boutons d'actions--}}
                    {{-- TODO optimiser tout ça notamment avec un switch par status et après seulement vérifier les rôles + cache pour éviter que la vérification de permissions envoie pleins de requêtes--}}
                    {{-- TODO ajouter le bouton bon de commande signé du DIRECTEUR IUT pour le statut devis aussi --}}

                    @if($order->getStatus() == Status::BON_DE_COMMANDE_NON_SIGNE && ($user->hasPermission(PermissionValue::SIGNER_BONS_DE_COMMANDES) || $user->hasPermission(PermissionValue::GERER_BONS_DE_COMMANDES)))
                        <button class="btn btn-success btn-action mb-2 btn-load-modal" title="Déposer un bon de commande signé" type="button" data-url="{{ route('orders.step-actions.upload-purchase-order', ['id' => $order->getId(), 'sign' => true]) }}">
                            {{--                                    data-bs-toggle="modal" data-bs-target="#addPurchaseOrderModal-{{$order->getId()}}" id="addPurchaseOrderButton-{{$order->getId()}}"--}}
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-upload" viewBox="0 0 16 16">
                                <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
                                <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z"/>
                            </svg> Déposer un bon signé
                        </button>
                        <button class="btn btn-danger btn-action mb-2 btn-load-modal" title="Marquer comme signature refusée" type="button" data-url="{{ route('orders.step-actions.refuse', ['id' => $order->getId(), 'about' => 'purchaseOrderSignature']) }}">
                            Signature refusée
                        </button>
                    @endif

                    @if($user->hasPermission(PermissionValue::GERER_BONS_DE_COMMANDES) && $order->getStatus() == Status::DEVIS)
                        <button class="btn btn-success btn-action mb-2 btn-load-modal" title="Déposer un bon de commande" type="button" data-url="{{ route('orders.step-actions.upload-purchase-order', ['id' => $order->getId(), 'sign' => false]) }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-upload" viewBox="0 0 16 16">
                                <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
                                <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z"/>
                            </svg> Déposer un bon
                        </button>
                        <button class="btn btn-danger btn-action mb-2 btn-load-modal" title="Refuser la demande de bon de commande" type="button" data-url="{{ route('orders.step-actions.refuse', ['id' => $order->getId(), 'about' => 'purchaseOrder']) }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                            </svg> Refuser
                        </button>
                    @endif

                    @if($userDepartments->contains($order->getDepartment()))
                        @if($order->getStatus() == Status::COMMANDE)
                            <button class="btn btn-primary btn-action mb-2 btn-load-modal" title="Informations sur les colis" type="button" data-url="{{ route('orders.step-actions.upload-delivery-note', ['id' => $order->getId()]) }}">

                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-pencil-square" viewBox="0 0 16 16">
                                    <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                    <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                </svg>
                                Infos colis / Réponse fournisseur
                            </button>
                            <button class="btn btn-danger btn-action mb-2 btn-load-modal" title="Marquer la commande comme refusée par le fournisseur" type="button" data-url="{{ route('orders.step-actions.refuse', ['id' => $order->getId(), 'about' => 'supplier']) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                                </svg> Refus du fournisseur
                            </button>
                        @endif

                        @if($order->getStatus() == Status::BON_DE_COMMANDE_SIGNE)
                            <button class="btn btn-success btn-action mb-2 btn-load-modal" title="Marquer le bon de commande comme envoyé au fournisseur" type="button" data-url="{{ route('orders.step-actions.sent-to-supplier', ['id' => $order->getId()]) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-lg" viewBox="0 0 16 16">
                                    <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425z"/>
                                </svg> Bon envoyé au fournisseur
                            </button>
                        @elseif($order->getStatus() == Status::COMMANDE || $order->getStatus() == Status::COMMANDE_AVEC_REPONSE || $order->getStatus() == Status::PARTIELLEMENT_LIVRE)
                            <button class="btn btn-success btn-action mb-2 btn-load-modal" title="Marquer un colis comme livré" type="button" data-url="{{ route('orders.step-actions.packages-delivered', ['id' => $order->getId()]) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-lg" viewBox="0 0 16 16">
                                    <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425z"/>
                                </svg> Colis&nbsplivré(s)
                            </button>
                            <button class="btn btn-success mb-2 btn-action btn-load-modal" title="Déposer un bon de livraison" type="button" data-url="{{ route('orders.step-actions.all-delivered', ['id' => $order->getId()]) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-upload" viewBox="0 0 16 16">
                                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
                                    <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z"/>
                                </svg> Service fait
                            </button>
                        @elseif($order->getStatus() == Status::DEVIS_REFUSE || $order->getStatus() == Status::BON_DE_COMMANDE_REFUSE || $order->getStatus() == Status::COMMANDE_AVEC_REPONSE || $order->getStatus() == Status::COMMANDE_REFUSEE)
                            <button class="btn btn-primary btn-action mb-2 btn-load-modal" title="Voir raison du refus" type="button" data-url="{{ route('orders.modal.view-details', ['id' => $order->getId()]) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                                </svg> Raison du refus
                            </button>
                            <button class="btn btn-secondary btn-action mb-2 btn-load-modal" title="Rectifier la commande" type="button" data-url="{{ route('orders.modal.view-details', ['id' => $order->getId(), 'edit' => true]) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                    <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                    <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                </svg>Rectifier la commande
                            </button>
                        @else
                            <button class="btn btn-secondary btn-action mb-2 btn-load-modal" title="Modifier la commande" type="button" data-url="{{ route('orders.modal.view-details', ['id' => $order->getId(), 'edit' => true]) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                    <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                    <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                </svg>
                            </button>
                        @endif
                    @endif

                    @if($user->hasPermission(PermissionValue::GERER_PAIEMENT_FOURNISSEURS) && $order->getStatus() == Status::SERVICE_FAIT)
                        <button class="btn btn-success btn-action mb-2 btn-load-modal" title="Marquer la commande comme payée" type="button" data-url="{{ route('orders.step-actions.paid', ['id' => $order->getId()]) }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-currency-euro" viewBox="0 0 16 16">
                                <path d="M4 9.42h1.063C5.4 12.323 7.317 14 10.34 14c.622 0 1.167-.068 1.659-.185v-1.3c-.484.119-1.045.17-1.659.17-2.1 0-3.455-1.198-3.775-3.264h4.017v-.928H6.497v-.936q-.002-.165.008-.329h4.078v-.927H6.618c.388-1.898 1.719-2.985 3.723-2.985.614 0 1.175.05 1.659.177V2.194A6.6 6.6 0 0 0 10.341 2c-2.928 0-4.82 1.569-5.244 4.3H4v.928h1.01v1.265H4v.928z"/>
                            </svg> Payé
                        </button>
                    @endif

                    {{--                                TODO De trop. Cliquer sur la commande où les petits points suffisent. Quand on voit les détails on aura un bouton pour modifier--}}
                    {{--                                <button class="btn btn-secondary mb-0" title="Voir les détails">--}}
                    {{--                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">--}}
                    {{--                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>--}}
                    {{--                                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>--}}
                    {{--                                    </svg>--}}
                    {{--                                </button>--}}
                    {{--                                <button class="btn btn-outline-primary orders-btn-edit" title="Modifier">--}}
                    {{--                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">--}}
                    {{--                                        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>--}}
                    {{--                                        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>--}}
                    {{--                                    </svg> Modifier--}}
                    {{--                                </button>--}}
                    {{--                                <button class="btn btn-light orders-btn-more" title="Plus d'options">--}}
                    {{--                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-three-dots" viewBox="0 0 16 16">--}}
                    {{--                                        <path d="M3 9.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3m5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3m5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3"/>--}}
                    {{--                                    </svg>--}}
                    {{--                                </button>--}}
                </div>
                <small class="d-none d-lg-inline">Dernière modification: {{ $order->getLastUpdateDate() }}</small>
            </td>
            <td class="d-none d-md-table-cell">{{ $order->getCreationDate() }}</td>
            <td class="ps-0 pe-0">
                <button class="btn btn-light btn-more-options">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-three-dots-vertical" viewBox="0 0 16 16">
                        <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0"/>
                    </svg>
                </button>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
{{ $orders->links()}}
