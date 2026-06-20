<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Order;
use App\Models\Package;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\PermissionValue;
use Database\Seeders\Status;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends BaseController
{
    // TODO Annotation pour utiliser la fonction auth() de AuthController pour chaque page
    // Routes GET
    public function viewOrders(): View|Response|RedirectResponse|Redirector
    {
        $request = request();
        $search = $request->input('search');
        $page = $request->input('page');

        $options = [
            'search' => $request->input('search'),
            'page' => $request->input('page'),
            'recentOnly' => $request->input('recentOnly'),
        ];

        /* @var User $user */
        $user = Auth::user();
        $userRoles = $user->getRoles(); // Récupération des rôles en base de données
        $userPermissions = $user->getPermissions(); // Récupération d'un dictionnaire des permissions pour simplifier la vérification de permissions
        $userDepartments = $userRoles->filter(fn (Role $role) => $role->isDepartment());

        $orders = $this->fetchOrders($user, $options)->withQueryString();

        $options['page'] = $orders->currentPage();

        $suppliers = Supplier::all(['id', 'company_name', 'is_valid', 'siret']); // Récupération uniquement des informations utiles à propos des fournisseurs

        // TODO flash messages: redirect('urls.create')->with('success', 'URL has been added');
        return view('orders', [
            'user' => $user,
            'orders' => $orders,
            'suppliers' => $suppliers,
            'userDepartments' => $userDepartments,
            'options' => $options, // Variable pour la vue
        ]);
    }

    public function fetchOrdersTable()
    {
        // Récupération des informations sur la requête
        $user = Auth::user();
        $request = request();
        $options = [
            'search' => $request->input('search'),
            'page' => $request->input('page'),
            'recentOnly' => $request->input('recentOnly'),
        ];

        // Récupération des commandes
        $orders = $this->fetchOrders($user, $options);

        // Redéfinition de l'URL des boutons de navigation afin de pointer vers la page des commandes et non vers la route pour actualiser la table
        $orders->withPath('/orders')->withQueryString();

        // Récupération de valeurs supplémentaires
        $userDepartments = $user->getRoles()->filter(fn (Role $role) => $role->isDepartment());

        $suppliers = Supplier::all(['id', 'company_name', 'is_valid', 'siret']); // Récupération uniquement des informations utiles à propos des fournisseurs

        // TODO flash messages, ex: redirect('urls.create')->with('success', 'URL has been added');
        return view('components.orders.orders-table', [
            'user' => $user,
            'orders' => $orders,
            'userDepartments' => $userDepartments,
        ]);

    }

    // Routes GET modal

    public function modalViewDetails(string $id)
    {
        $user = Auth::user();
        $request = request();

        /* @var Order $order */
        $order = Order::where('id', $id)->first();
        $orderId = $order->getId();
        $edit = $request['edit'];

        return view('components.orders.modal.viewOrderModal', [
            'user' => $user,
            'order' => $order,
            'orderId' => $orderId,
            'edit' => $edit,
            'userDepartments' => $user->getDepartments(),
        ]);
    }

    // Routes GET modal pour les actions d'état (actions rapides)

    public function modalUploadPurchaseOrder($id)
    {

        /* @var Order $order */
        $sign = request()['sign'];
        $order = Order::where('id', $id)->first();

        $user = Auth::user();

        // On retourne une vue partielle (sans header, footer, etc.)
        // render() est important si vous voulez manipuler le string,
        // mais return view() suffit souvent car Laravel le convertit en string.
        return view('components.orders.modal.step-actions.addPurchaseOrderModal', [
            'user' => Auth::user(),
            'order' => $order,
            'orderId' => $order->getId(),
            'canSign' => $sign || $user->hasPermission(PermissionValue::SIGNER_BONS_DE_COMMANDES),
        ]);
    }

    public function modalRefuseToSign($id) {}

    public function modalRefuse($id)
    {

        $order = Order::findOrFail($id);
        $about = request()->input('about');

        return view('components.orders.modal.step-actions.refuseOrderModal', [
            'order' => $order,
            'orderId' => $order->getId(),
            'user' => Auth::user(),
            'about' => $about,
        ]);
    }

    public function modalPaid($id)
    {
        /* @var Order $order */
        $sign = request()['sign'];
        $order = Order::where('id', $id)->first();

        $user = Auth::user();

        // On retourne une vue partielle (sans header, footer, etc.)
        // render() est important si vous voulez manipuler le string,
        // mais return view() suffit souvent car Laravel le convertit en string.
        return view('components.orders.modal.step-actions.paidOrderModal', [
            'user' => Auth::user(),
            'order' => $order,
            'orderId' => $order->getId(),
        ]);
    }

    public function modalUploadDeliveryNote($id)
    {
        $order = Order::findOrFail($id);

        return view(
            'components.orders.modal.step-actions.addDeliveryNoteModal',
            [
                'order' => $order,
                'orderId' => $order->getId(),
                'user' => Auth::user(),
            ]
        );
    }

    public function modalSupplierReponseInfosPackages(string $id)
    {
        $order = Order::findOrFail($id);

        return view(
            'components.orders.modal.step-actions.supplierResponsePackagesInfos',
            [
                'order' => $order,
                'orderId' => $order->getId(),
                'user' => Auth::user(),
            ]
        );
    }

    public function modalSentToSupplier($id)
    {
        $order = Order::findOrFail($id);

        return view('components.orders.modal.step-actions.sentToSupplier', [
            'order' => $order,
            'orderId' => $order->getId(),
            'user' => Auth::user(),
        ]);
    }

    public function modalDeliveredPackages($id)
    {
        $order = Order::findOrFail($id);

        return view('components.orders.modal.step-actions.deliveredPackagesModal', [
            'order' => $order,
            'orderId' => $order->getId(),
            'user' => Auth::user(),
            'packages' => $order->getPackages(true),
            'checkAll' => false,
        ]);
    }

    public function modalDeliveredAll(string $id)
    {
        $order = Order::findOrFail($id);

        return view('components.orders.modal.step-actions.deliveredPackagesModal', [
            'order' => $order,
            'orderId' => $order->getId(),
            'user' => Auth::user(),
            'packages' => $order->getPackages(true),
            'checkAll' => true,
        ]);
    }

    // Routes POST

    public function submitNewOrder(): RedirectResponse|Response|Redirector|View
    {

        /* @var Request|mixed|mixed[]|object $request */
        $request = request();
        $user = Auth::user();
        $userDepartments = $user->getDepartments();
        $title = $request['title'];
        $orderNum = $request['order_num'];
        $status = $request['status'];
        $department = $request['department'];
        $description = $request['description'];
        $quote_num = $request['quote_num'];
        $cost = $request['cost'];
        $isSigned = $request['isSigned'] == 'on';
        $department = count($userDepartments) == 1
            ? $userDepartments->first()
            : $userDepartments->first(function ($dept) use ($department) {
                return $dept->getId() == $department;
            });

        $order = null;
        $supplier = null;

        $componentVars = array_merge([
            'users' => $user,
            'userDepartments' => $userDepartments,
            'suppliers' => Supplier::all('id', 'company_name', 'is_valid', 'siret'),
            'retry' => true,
        ], $request->all());

        // ça ne fonctionne pas
        //        $request_files = $request->allFiles();
        //        $fileInputNames = array_keys($request_files);
        //        foreach ($fileInputNames as $fileInputName) {
        //            $componentVars[$fileInputName] = $request_files[$fileInputName]->getClientOriginalPath();
        //        }

        try {
            // 1) VALIDATION

            $hasExistantSupplier = $request['newOrExistantSupplierRadio'] == 'existant';
            $createNewSupplier = ! $hasExistantSupplier;
            $rulesValidator = [
                'title' => 'required|string|max:255',
                'order_num' => 'required|string|max:255|unique:orders,order_num',
                'quote_num' => 'required|string|max:255|',
                'department' => 'nullable|exists:roles,id',
                'description' => 'nullable|string|max:16777215',
                'status' => [Rule::enum(Status::class), 'required'],
                'cost' => 'nullable|decimal:0,2|max:2147483647',
                'quote' => 'nullable|mimes:pdf,doc,docx|max:10240',
                'purchase_order' => 'nullable|mimes:pdf,doc,docx|max:10240',
            ];

            // if ($hasExistantSupplier) {
            // Cette règle de validator a été remplacée par une vérification manuelle
            //                $rulesForNewSupplier = [
            //                    'supplier_name' => 'required|exists:suppliers,company_name',
            //                ];
            //                $rulesValidator = array_merge($rulesValidator, $rulesForNewSupplier);

            if ($createNewSupplier) {

                $rulesForExistantSupplier = [
                    'companyName' => 'required|string|max:255|unique:suppliers,company_name',
                    'siret' => 'required|number|max:14|min:14|unique:suppliers,siret',
                    'phoneNumber' => 'required|string|max:255',
                    'email' => 'required|string|max:255',
                    'contactName' => 'required|string|max:255',
                    'address' => 'required|string|max:255',
                ];
                $rulesValidator = array_merge($rulesValidator, $rulesForExistantSupplier);
            }

            $validator = Validator::make($request->all(), $rulesValidator);

            if ($validator->fails()) {
                return view('components.orders.modal.orderCreationModal', $componentVars)->withErrors($validator);
            }

            if (! $department) {
                return view('components.orders.modal.orderCreationModal', $componentVars)->withErrors('Le département indiqué est invalide. Vous devez faire partie de ce département.');
            }

            if ($hasExistantSupplier) {
                /* @var Supplier $supplier */
                $supplier = Supplier::where(function ($query) use ($request) {
                    $query->where('company_name', '=', $request['supplier_name'])
                        ->orWhere('siret', '=', $request['supplier_name']);
                })->first();

                if ($supplier && ! $supplier->isValid() && $status != Status::BROUILLON->value) {
                    $status = Status::EN_ATTENTE_VALIDATION_FOURNISSEUR;
                    // TODO avertir le service financier ?
                }

            } else {
                // TODO is_valid ne va plus être un boolean mais un string où il faut mettre la bonne valeur
                $supplier = new Supplier([
                    'company_name' => $request['companyName'],
                    'siret' => $request['siret'],
                    'email' => $request['email'],
                    'phone_number' => $request['phoneNumber'],
                    'contact_name' => $request['contactName'],
                    'address' => $request['address'],
                    'is_valid' => false,
                ]);

                $isSavedSupplier = $supplier->save();
                if (! $isSavedSupplier) {
                    return view('components.orders.modal.orderCreationModal', $componentVars)->withErrors('Une erreur inattendue est survenue lors de la sauvegarde du fournisseur de la commande N°'.$orderNum.'.');
                }

            }

            if (! $supplier) {
                return view('components.orders.modal.orderCreationModal', $componentVars)->withErrors(['supplier_name' => "Le fournisseur \"{$request['supplier_name']}\" n'a pas été trouvé pour la création de la commande N°$orderNum."]);
            }

            // 2 CRÉATION DE LA COMMANDE
            $order = new Order([
                'title' => $title,
                'order_num' => $orderNum,
                'status' => $status,
                'author_id' => $user->getId(),
                'department_id' => $department->getId(),
                'supplier_id' => $supplier->getId(),
            ]);

            // 3 ATTRIBUTION DES CHAMPS
            if (isset($quote_num)) {
                $order->setQuoteNumber($quote_num, false);
            }

            if (isset($description)) {
                $order->setDescription($description, false);
            }

            $order->setStatus($status, false);

            if (isset($cost)) {
                $order->setCost($cost, false);
            }

            // 4 UPLOAD DU DEVIS
            if ($request->hasFile('quote')) {
                $order->uploadQuote($request, false, false);
            }

            // 5 UPLOAD DU DEVIS
            if ($request->hasFile('purchase_order')) {
                $order->uploadPurchaseOrder($request, $isSigned, false, false);
            }

            // 6 SAUVEGARDE
            $isSaved = $order->save();
            if (! $isSaved) {
                return view('components.orders.modal.orderCreationModal', $componentVars)->withErrors("Une erreur est survenue lors de la sauvegarde de la commande N°$orderNum.");
            }

            $logData = $order->sendLog("La commande $orderNum a été créée !".($createNewSupplier ? " Avec l'ajout d'un nouveau fournisseur : {$supplier->getCompanyName()}." : ''), $user);
            /* @var Log $log */
            $log = $logData['model'];
            session()->flash('success', $log->getContent());

            return $this->viewOrders()->withErrors($logData['success'] ? null : "Le journal d'activité n'a pas pas été envoyé à cause d'un erreur");

        } catch (\Throwable $t) {
            if (config('app.debug')) {
                error_log($t->getMessage());
                error_log($t->getTraceAsString());
            }

            return view('components.orders.modal.orderCreationModal', $componentVars)->withErrors('Une erreur inattendue est survenue à la création de la commande N°'.$orderNum.'.');
        }
    }


    // Routes POST modal
    public function editOrder(string $id)
    {
        $user = Auth::user();
        $request = request();

        /* @var Order $order */
        $order = Order::where('id', $id)->first();
        $orderId = $order->getId();
        $edit = $request['edit'];

        if ($request->method() === 'POST') {
            // TODO corriger le fait que le message erreur ou succès il apparaît seulement au bout de 2 actualisations, pas une.

            if ($edit && (($user->hasPermission(PermissionValue::MODIFIER_COMMANDES_DEPARTEMENT) && $user->hasRole($order->getDepartment())) || $user->hasPermission(PermissionValue::MODIFIER_TOUTES_COMMANDES))) {
                $title = $request['title'];
                $orderNum = $request['order_num'];
                $quoteNum = $request['quote_num'];
                $description = $request['description'];
                $cost = $request['cost'];
                $status = $request['status'];
                $quote = $request['quote'];
                $purchaseOrder = $request['purchase_order'];
                $deliveryNote = $request['delivery_note'];

                if (isset($title)) {
                    $order->setTitle($title, false);
                }
                if (isset($orderNum)) {
                    $order->setOrderNumber($orderNum, false);
                }
                if (isset($quoteNum)) {
                    $order->setQuoteNumber($quoteNum, false);
                }

                if (isset($description)) {
                    $order->setDescription($description, false);
                }

                if (isset($cost)) {
                    $order->setCost($cost, false);
                }

                if ($request->hasFile('quote')) {
                    $order->uploadQuote($request, false);
                }

                if ($request->hasFile('purchase_order')) {
                    $order->uploadPurchaseOrder($request, false);
                }

                if ($request->hasFile('delivery_note')) {
                    $order->uploadDeliveryNote($request, false);
                }

                $order->setStatus($status, false);

                $order->save();

                session()->flash('orderSuccess', 'La commande a été mise à jour !');
            } else {
                session()->flash('orderError-'.$orderId, "Vous n'avez pas la permission de modifier cette commande");
                $edit = false;
            }
        }

        return view('components.orders.modal.viewOrderModal', [
            'user' => $user,
            'order' => $order,
            'orderId' => $orderId,
            'edit' => $edit,
            'userDepartments' => $user->getDepartments(),
        ]);
    }

    // Routes POST des actions d'états (actions rapides)

    public function actionUploadPurchaseOrder()
    {
        /* @var Order $order */
        /* @var User $user */
        $request = request();
        $id = $request['id'];

        try {
            $order = Order::findOrFail($id);
            $user = Auth::user();

            if (! ($user->hasPermission(PermissionValue::GERER_BONS_DE_COMMANDES) || $user->hasPermission(PermissionValue::SIGNER_BONS_DE_COMMANDES) || $user->hasRole($order->getDepartment()))) {
                return $this->modalUploadPurchaseOrder($id)->withErrors("Vous n'avez pas la permission d'ajouter un bon de commande !");
            }

            $nextStep = $request['nextStep'];
            $isSigned = $request['signed'];

            // Stockage du fichier dans storage/app/public/quotes
            $output = $order->uploadPurchaseOrder($request, $isSigned, false);
            $validator = $output['validator'];
            $validatorFails = $validator->fails();

            if ($validatorFails || count($output) > 1) {
                return $this->modalUploadPurchaseOrder($id)->withErrors($validatorFails ? $validator : $output['otherError']);
            }

            $oldStatus = $order->getStatus();
            if ($nextStep && (
                $oldStatus == Status::DEVIS ||
                $oldStatus == Status::BON_DE_COMMANDE_NON_SIGNE ||
                $oldStatus == Status::DEVIS_REFUSE ||
                $oldStatus == Status::BROUILLON ||
                $oldStatus == Status::BON_DE_COMMANDE_REFUSE)
            ) {
                $order->setStatus($isSigned ? Status::BON_DE_COMMANDE_SIGNE : Status::BON_DE_COMMANDE_NON_SIGNE, false);
            } else {
                $oldStatus = null;
            }

            $successfulSave = $order->save();
            if (! $successfulSave) {
                return $this->modalUploadPurchaseOrder($id)->withErrors('Une erreur est survenue à la sauvegarde de la commande !');
            }

            $logData = $order->sendLog('Le bon de commande'.($isSigned ? ' signé' : '').' a été publié.', $user, $oldStatus);
            /* @var Log $log */
            $log = $logData['model'];
            session()->flash('success', $log->getContent());

            return $this->modalViewDetails($id)->withErrors($logData['success'] ? null : "Le journal d'activité n'a pas pas été envoyé à cause d'un erreur");
        } catch (\Throwable $th) {
            return $this->modalUploadPurchaseOrder($id)->withErrors('Une erreur inconnue est survenue à la publication du bon de commande.');
        }
    }

    public function actionOrderPaid(string $id)
    {
        /* @var Order $order */
        /* @var User $user */
        $request = request();
        $id = $request['id'];

        try {
            $order = Order::findOrFail($id);
            $user = Auth::user();

            if (! $user->hasPermission(PermissionValue::GERER_PAIEMENT_FOURNISSEURS)) {
                return $this->modalPaid($id)->withErrors("Vous n'avez pas la permission de marquer la commande comme payée !");
            }

            $validator = Validator::make($request->all(), [
                'cost' => 'decimal:0,2|max:2147483647',
            ]);

            $nextStep = $request['nextStep'];
            $cost = $request['cost'];
            $sendMail = $request['sendMail'];
            $isCostChanged = $order->getCost() != $cost;
            $logMsg = 'La commande a été marquée comme payée à une somme de '.Order::getFormattedCost($cost).'.'.($isCostChanged ? ' Qui est différente de la somme précédemment définie à '.$order->getCostFormatted().'.' : '');

            error_log($sendMail);
            // Mise à jour du coût de la commande
            if ($isCostChanged) {
                $order->setCost($cost, false);
            }

            if ($validator->fails()) {
                return $this->modalPaid($id)->withErrors($validator);
            }

            $oldStatus = $order->getStatus();
            if ($nextStep && $oldStatus == Status::SERVICE_FAIT) {
                $order->setStatus(Status::LIVRE_ET_PAYE, false);
            } else {
                $oldStatus = null;
            }

            $successfulSave = $order->save();
            if (! $successfulSave) {
                return $this->modalPaid($id)->withErrors('Une erreur est survenue à la sauvegarde des modifications de la commande !');
            }

            $logData = $order->sendLog($logMsg, $user, $oldStatus);
            /* @var Log $log */
            $log = $logData['model'];
            session()->flash('success', $log->getContent());

            if ($sendMail) {
                $mailContent = str_replace('{coûtEnEuros}', Order::getFormattedCost($cost), $request['mailContent']);
                error_log($mailContent);
            }

            return $this->modalViewDetails($id)->withErrors($logData['success'] ? null : "Le journal d'activité n'a pas pas été envoyé à cause d'un erreur");
        } catch (\Throwable $th) {
            return $this->modalPaid($id)->withErrors("Une erreur inconnue est survenue à lors de l'opération.");
        }
    }

    public function actionDeliveredPackages(string $id)
    {
        $request = request();
        $order = Order::findOrFail($id);
        $user = Auth::user();

        if (! $user->hasPermission(PermissionValue::GERER_COLIS_LIVRES)) {
            return $this->modalDeliveredPackages($id)->withErrors("Vous n'avez pas la permission de marquer des colis comme livrés.");
        }

        $packageIds = $request->input('packages', []);

        if (empty($packageIds)) {
            return $this->modalDeliveredPackages($id)->withErrors('Veuillez sélectionner au moins un colis.');
        }

        $deliveryDate = $request->input('shipping_date') ?: now()->toDateString();

        $packages = $order->getPackages(true);

        foreach ($packages as $package) {
            if (in_array($package->getId(), $packageIds)) {
                $package->setShippingDate($deliveryDate);
            }
        }

        $allPackagesDelivered = true;

        foreach ($packages as $package) {
            if (empty($package->getShippingDate())) {
                $allPackagesDelivered = false;
                break;
            }
        }

        $oldStatus = $order->getStatus();

        if ($allPackagesDelivered) {
            $order->setStatus(Status::SERVICE_FAIT, false);
            $message = 'Tous les colis ont été marqués comme livrés.';
        } else {
            $order->setStatus(Status::PARTIELLEMENT_LIVRE, false);
            $message = 'Des colis ont été marqués comme livrés.';
        }

        $order->save();

        $logData = $order->sendLog($message.' Date de livraison : '.$deliveryDate.'.', $user, $oldStatus);
        session()->flash('success', $logData['model']->getContent());

        return $this->modalViewDetails($id);
    }

    public function actionRefuse($id)
    {
        $request = request();
        $order = Order::findOrFail($id);
        $user = Auth::user();
        $about = $request->input('about');

        // Validation simple : la raison est obligatoire
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        // Vérification des permissions selon ce qu'on refuse
        if ($about === 'purchaseOrderSignature') {
            // Pour refuser la signature, il faut avoir le droit de signer ou de gérer les bons de commande
            if (! $user->hasPermission(PermissionValue::SIGNER_BONS_DE_COMMANDES) && ! $user->hasPermission(PermissionValue::GERER_BONS_DE_COMMANDES)) {
                return $this->modalRefuse($id)->withErrors("Vous n'avez pas la permission de refuser la signature.");
            }
        } else {
            // Par défaut (refus de devis), il faut avoir le droit de gérer les bons de commande
            if (! $user->hasPermission(PermissionValue::GERER_BONS_DE_COMMANDES)) {
                return $this->modalRefuse($id)->withErrors("Vous n'avez pas la permission de refuser.");
            }
        }

        $reason = $request->input('reason');
        $nextStep = $request->input('nextStep');
        $sendMail = $request->input('sendMail');
        $oldStatus = null;

        // --- CAS 1 : Refus de la signature du bon de commande ---
        if ($about === 'purchaseOrderSignature') {
            if ($order->getStatus() != Status::BON_DE_COMMANDE_NON_SIGNE) {
                return $this->modalRefuse($id)->withErrors("Cette commande n'est pas en attente de signature.");
            }

            if ($nextStep) {
                $oldStatus = $order->getStatus();
                $order->setStatus(Status::BON_DE_COMMANDE_REFUSE, false);
            }
            $message = 'La signature du bon de commande a été refusée. Raison : '.$reason;
        }
        // --- CAS 2 : Refus du devis (par défaut) ---
        else {
            if ($order->getStatus() != Status::DEVIS) {
                return $this->modalRefuse($id)->withErrors("Cette commande n'est pas à l'état devis.");
            }

            if ($nextStep) {
                $oldStatus = $order->getStatus();
                $order->setStatus(Status::DEVIS_REFUSE, false);
            }
            $message = 'Le devis a été refusé. Raison : '.$reason;
        }

      // Sauvegarde des changements
        $order->save();

        $message = 'Le devis a été refusé pour la raison suivante : '.$reason;


        // Enregistrement du log avec la raison du refus
        $logData = $order->sendLog($message, $user, $oldStatus);
        session()->flash('success', $message);

        // Simulation d'envoi de mail si demandé (comme dans le reste du projet)
        if ($sendMail) {
            $mailContent = str_replace('{raison}', $reason ?? 'Raison non définie', $request->input('mailContent'));
            error_log('Mail de refus : '.$mailContent);
        }

        // Retourne vers la vue détaillée de la commande
        return $this->modalViewDetails($id);
    }

    // Notifications
    public function sendAutoMail(Request $request)
    {
        // TODO code pour envoyer un mail automatique
    }

    // Routes pour le téléchargement des documents

    public function downloadDocument(string $id, string $type)
    {
        /* @var Order $order */
        $order = Order::findOrFail($id);
        $user = Auth::user();

        // 1. VÉRIFICATION DES PERMISSIONS (Sécurité)
        // Adaptez selon vos permissions existantes.
        // Ici, je vérifie juste si l'user peut consulter la commande.

        // Exemple basique basé sur votre logique actuelle :
        $canView = $user->hasPermission(PermissionValue::CONSULTER_TOUTES_COMMANDES);

        if (! $canView) {
            // Vérification si membre du département
            $userDepartments = $user->getRoles()->filter(fn (Role $role) => $role->isDepartment());
            if ($userDepartments->contains($order->getDepartment())) {
                $canView = $user->hasPermission(PermissionValue::CONSULTER_COMMANDES_DEPARTMENT);
            }
        }

        if (! $canView) {
            abort(403, "Vous n'avez pas accès à ce document.");
        }

        // 2. RÉCUPÉRATION DU CHEMIN DU FICHIER
        $path = match ($type) {
            'quote' => $order->getAttribute('path_quote'), // On accède à l'attribut brut en BDD
            'purchase_order' => $order->getAttribute('path_purchase_order'),
            'delivery_note' => $order->getAttribute('path_delivery_note'),
            default => null,
        };

        if (! $path || ! Storage::disk('public')->exists($path)) {
            abort(404, "Le fichier n'existe pas ou a été déplacé.");
        }

        // 3. TÉLÉCHARGEMENT
        // Storage::download(chemin_disque, nom_fichier_pour_l_utilisateur)
        // Note: Comme vos fichiers sont dans storage/app/public, on utilise le disk 'public'
        return Storage::disk('public')->download($path);

        // Si vous préférez afficher le PDF dans le navigateur au lieu de forcer le téléchargement :
        // return Storage::disk('public')->response($path);
    }

    // Autres fonctions

   public function fetchOrders(User $user, ?array $options): AbstractPaginator {
    // 1. Récupération sécurisée et typée des options
    $recentOnly = (bool)($options['recentOnly'] ?? false);
    $page = isset($options['page']) ? (int)$options['page'] : 1;

    // Sécurisation de la recherche (
    $search = $options['search'] ?? null;
    if (!is_string($search) && !is_array($search)) {
        $search = null;
    }

    $userRoles = $user->getRoles();
    $userPermissions = $user->getPermissions();
    $userDepartments = $userRoles->filter(fn (Role $role) => $role->isDepartment());

    // Initialisation sql
    $query = Order::query()->select('orders.*');

    // Ordre d'affichage global des Bons de commande
    $query->orderByRaw("CASE
        WHEN orders.status = 'BON_DE_COMMANDE_NON_SIGNE' THEN 1
        WHEN orders.status = 'BON_DE_COMMANDE_SIGNE' THEN 2
        WHEN orders.status = 'BON_DE_COMMANDE_REFUSE' THEN 3
        ELSE 4
    END");

    // Filtrage par droits / départements
    if (!$user->hasPermission(PermissionValue::CONSULTER_TOUTES_COMMANDES)) {
        $query->where(function (Builder $q) use ($userDepartments, $userPermissions) {
            $userDepartments->each(function (Role $department) use ($q, $userPermissions) {
                $permissionKey = PermissionValue::CONSULTER_COMMANDES_DEPARTMENT->value;
                if (!empty($userPermissions[$permissionKey])) {
                    $q->orWhere('orders.department_id', $department->getId());
                }
            });
        });
    }

    // --- 2. TRI SELON LE RÔLE ---
    if (!$recentOnly) {
        $isDirecteur = $user->hasPermission(PermissionValue::SIGNER_BONS_DE_COMMANDES);
        $isFinancier = $user->hasPermission(PermissionValue::GERER_PAIEMENT_FOURNISSEURS);
        $isResponsableColis = $user->hasPermission(PermissionValue::GERER_COLIS_LIVRES);
        $isDepartment = $userDepartments->isNotEmpty();

        if ($isDirecteur) {
            $query->orderByRaw("CASE
                WHEN orders.status = ? THEN 1
                WHEN orders.status = ? THEN 2
                ELSE 3
            END", [Status::BON_DE_COMMANDE_NON_SIGNE->value, Status::DEVIS->value]);

        } elseif ($isFinancier) {
            $query->orderByRaw("CASE
                WHEN orders.status = ? THEN 1
                WHEN orders.status = ? THEN 2
                WHEN orders.status = ? THEN 3
                WHEN orders.status = ? THEN 4
                WHEN orders.status = ? THEN 5
                ELSE 6
            END", [
                Status::SERVICE_FAIT->value,
                Status::DEVIS->value,
                Status::BON_DE_COMMANDE_NON_SIGNE->value,
                Status::BON_DE_COMMANDE_REFUSE->value,
                Status::LIVRE_ET_PAYE->value
            ]);

        } elseif ($isResponsableColis) {
            $query->orderByRaw("CASE
                WHEN orders.status IN (?, ?) THEN 1
                WHEN orders.status = ? THEN 2
                ELSE 3
            END", [
                Status::COMMANDE_AVEC_REPONSE->value,
                Status::PARTIELLEMENT_LIVRE->value,
                Status::COMMANDE->value
            ]);

        } elseif ($isDepartment) {
            $sqlSort = "CASE
                WHEN orders.status = ? AND orders.author_id = ? THEN 1
                WHEN orders.status IN (?, ?, ?) THEN 2
                WHEN orders.status IN (?, ?, ?, ?) THEN 3
                ELSE 4
            END";

            $query->orderByRaw($sqlSort, [
                Status::BROUILLON->value, $user->getId(),
                Status::DEVIS_REFUSE->value, Status::BON_DE_COMMANDE_REFUSE->value, Status::COMMANDE_REFUSEE->value,
                Status::BON_DE_COMMANDE_SIGNE->value, Status::COMMANDE->value, Status::COMMANDE_AVEC_REPONSE->value, Status::PARTIELLEMENT_LIVRE->value
            ]);
        }
    }

    // --- 2.1 FILTRES DES BOUTONS DE RECHERCHE SECURISÉS ---
    if ($search) {
        if (is_string($search) && $search === 'BON_DE_COMMANDE') {
            $search = ['BON_DE_COMMANDE_NON_SIGNE', 'BON_DE_COMMANDE_SIGNE', 'BON_DE_COMMANDE_REFUSE'];
        }

        $query->where(function (Builder $q) use ($search) {
            if (is_array($search)) {
                $cleanSearch = array_filter($search, 'is_string');
                if (!empty($cleanSearch)) {
                    $q->whereIn('orders.status', $cleanSearch);
                }
            } else {
                $s = "%{$search}%";
                $q->where('orders.order_num', 'LIKE', $s)
                  ->orWhere('orders.title', 'LIKE', $s)
                  ->orWhere('orders.status', 'LIKE', $s)
                  ->orWhere('orders.quote_num', 'LIKE', $s);
            }
        });
    }

    // --- 2.2 GESTION DU FILTRE CUMULÉ  ---
    if ($recentOnly) {
        $query->where(function (Builder $innerQuery) use ($search, $user) {
            $innerQuery->whereIn('orders.id', function ($subQuery) use ($user) {
                $subQuery->select('logs.order_id')
                    ->from('logs')
                    ->whereDate('logs.created_at', '>', \Carbon\Carbon::today()->subDays(5))
                    ->where('logs.author_id', $user->getId());
            });

            if ($search && is_array($search)) {
                $cleanSearch = array_filter($search, 'is_string');
                if (!empty($cleanSearch)) {
                    $innerQuery->orWhereIn('orders.status', $cleanSearch);
                }
            }
        });
    }

    // --- 3. TRI FINAL DE SÉCURITÉ ---
    $direction = $recentOnly ? 'desc' : 'asc';
    $query->orderBy('orders.updated_at', $direction);

    return $query->paginate(20, ['orders.*'], 'page', $page);
}

    public function actionSentToSupplier($id) {
        $request = request();
        $order = Order::findOrFail($id);
        $user = Auth::user();

        $deliveryDelays = $request->input('delivery_delay', []);
        $nextStep = $request->input('nextStep');
        $withResponse = $request->input('withResponse');

        $oldStatus = null;

        if ($nextStep && $order->getStatus() == Status::BON_DE_COMMANDE_SIGNE) {
            $oldStatus = $order->getStatus();
            $order->setStatus($withResponse ? Status::COMMANDE_AVEC_REPONSE : Status::COMMANDE, false);
        }

        $order->save();

        $message = 'Le bon de commande a été envoyé au fournisseur.';

        foreach ($deliveryDelays as $packageId => $deliveryDelay) {
            if (!empty($deliveryDelay)) {
                $package = Package::find($packageId);
                $package->setExpectedDeliveryTime($deliveryDelay);
                $message .= " Délai de livraison annoncé pour le colis \"".$package->getName()."\" : ".$deliveryDelay.".";
            }
        }

        $logData = $order->sendLog($message, $user, $oldStatus);

        session()->flash('success', $logData['model']->getContent());

        return $this->modalViewDetails($id);
    }

    public function actionUpdatePackageInfos($id)
    {
        /* @var Order $order */
        $order = Order::findOrFail($id);
        $user = Auth::user();
        $orderDepartment = $order->getDepartment();

        if ($user->hasPermission(PermissionValue::MODIFIER_TOUTES_COMMANDES) || ! $user->hasPermission(PermissionValue::MODIFIER_COMMANDES_DEPARTEMENT) && ! $user->hasRole($orderDepartment)) {
            return $this->viewOrders()->withErrors("Vous n'avez pas la permission de modifier les informations des colis de cette commande");
        }

        $failedPackages = [];
        /* @var Package $package */
        foreach ($order->getPackages() as $package) {

            /* @var \Illuminate\Validation\Validator $validator */

            $validator = Validator::make(request()->all(), [
                'name_'.$package->getId() => 'required|string|max:255',
                'tracking_number_'.$package->getId() => 'nullable|string|max:255',
                'cost_'.$package->getId() => 'nullable|decimal:0,2|max:2147483647',
                'expected_delivery_time_'.$package->getId() => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return $this->modalSupplierReponseInfosPackages($id)->withErrors($validator);
            }

            $logs = [];
            $isPackageEdited = false;

            $name = request()->input('name_'.$package->getId());
            $tracking_number = request()->input('tracking_number_'.$package->getId());
            $cost = request()->input('cost_'.$package->getId());
            $expectedDeliveryTime = request()->input('expected_delivery_time_'.$package->getId());

            if ($tracking_number && $tracking_number != $package->getTrackingNumber()) {
                $logs[] = $order->sendLog("Le numéro de suivi du colis \"{$package->getName()}\" a été changé de \"{$package->getTrackingNumber()}\" à \"$tracking_number\".", $user, null, false)['model'];

                $package->setTrackingNumber(
                    $tracking_number,
                    false
                );
                $isPackageEdited = true;
            }

            if ($name && $name != $package->getName()) {
                $logs[] = $order->sendLog("Le nom du colis \"{$package->getName()}\" (N°{$package->getTrackingNumber()}) a été changé en \"$name\".", $user, null, false)['model'];

                $package->setName(
                    $name,
                    false
                );
                $isPackageEdited = true;
            }

            if ($cost != $package->getCost()) {
                $logs[] = $order->sendLog("Le coût du colis \"{$package->getName()}\" (N°{$package->getTrackingNumber()}) a été changé de {$package->getCostFormatted()} à ".Order::getFormattedCost($cost).'.', $user, null, false)['model'];

                $package->setCost(
                    $cost,
                    false
                );
                $isPackageEdited = true;
            }

            if ($expectedDeliveryTime != $package->getExpectedDeliveryTime()) {
                $logs[] = $order->sendLog("Le délai prévu de livraison du colis \"{$package->getName()}\" (N°{$package->getTrackingNumber()}) a été changé de \"{$package->getExpectedDeliveryTime()}\" à \"$expectedDeliveryTime\".", $user, null, false)['model'];

                $package->setExpectedDeliveryTime(
                    $expectedDeliveryTime,
                    false
                );
                $isPackageEdited = true;
            }

            if ($isPackageEdited) {
                $isSaved = $package->save();
                if ($isSaved) {
                    /* @var Log $log */
                    foreach ($logs as $log) {
                        $log->save();
                    }
                } else {
                    $failedPackages[] = ["package-{$package->getId()}" => "La sauvegarde des informations du colis  \"{$package->getName()}\" (N°{$package->getTrackingNumber()}) a échouée."];
                }
            }
        }

        if (count($failedPackages) > 0) {
            return $this->modalSupplierReponseInfosPackages($id)->withErrors($failedPackages);
        }

        $oldStatus = $order->getStatus();
        $nextStep = request()->input('nextStep');

        if ($nextStep && $oldStatus == Status::COMMANDE) {
            $order->setStatus(Status::COMMANDE_AVEC_REPONSE);
            if ($order->save()) {
                $order->sendLog('Les informations sur les colis ont été mises à jour suite à la réponse du fournisseur.', $user, $oldStatus);
            } else {
                return $this->modalSupplierReponseInfosPackages($id)->withErrors("Une erreur est survenue lors de la sauvegarde de la commande N°{$order->getId()} pour le changement de statut.");

            }
        }

        $sendMail = request()->input('sendMail');
        if ($sendMail) {
            $mailContent = request()->input('mailContent');
            if (! $nextStep) {
                $mailContent = str_replace(' suite à une réponse du fournisseur', '', $mailContent);
            }
            error_log($mailContent);

            // TODO mettre code pour mail auto
        }

        session()->flash(
            'success',
            "Les informations des colis de la commande N°{$order->getOrderNumber()} ont été mises à jour.",

        );

        return $this->modalViewDetails($id);
    }


}
