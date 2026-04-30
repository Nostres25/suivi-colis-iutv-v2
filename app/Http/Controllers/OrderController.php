<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
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
use Illuminate\View\View;

class OrderController extends BaseController
{

    // TODO Annotation pour utiliser la fonction auth() de AuthController pour chaque page
    // Routes GET
    public function viewOrders(?string $alertMsg = null): View|Response|RedirectResponse|Redirector
    {
        $request = request();
        $search = $request->input('search');

        /* @var User $user */
        $user = Auth::user();
        $userRoles = $user->getRoles(); // Récupération des rôles en base de données
        $userPermissions = $user->getPermissions(); // Récupération d'un dictionnaire des permissions pour simplifier la vérification de permissions
        $userDepartments = $userRoles->filter(fn (Role $role) => $role->isDepartment());

        $orders = $this->fetchOrders($user, $search);

        $suppliers = Supplier::all(['id', 'company_name', 'is_valid']); // Récupération uniquement des informations utiles à propos des fournisseurs

        // TODO flash messages: redirect('urls.create')->with('success', 'URL has been added');
        return view('orders', [
            'user' => $user,
            'orders' => $orders,
            'validSupplierNames' => $suppliers->where('is_valid', true)->map(fn (Supplier $supplier) => $supplier->getCompanyName())->values()->toArray(),
            'userDepartments' => $userDepartments,
            'search' => $search, // Variable pour la vue
        ]);
    }

    public function fetchOrdersTable()
    {
        $user = Auth::user();
        $request = request();
        $orders = $this->fetchOrders($user, $request->input('search'));
        $userDepartments = $user->getRoles()->filter(fn (Role $role) => $role->isDepartment());

        $suppliers = Supplier::all(['id', 'company_name', 'is_valid']); // Récupération uniquement des informations utiles à propos des fournisseurs

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
        $request = request();
        $about = $request['about'];

    }

    public function modalPaid($id) {}

    public function modalUploadDeliveryNote($id) {}

    public function modalSentToSupplier($id) {}

    public function modalDeliveredPackage($id) {}

    public function modalDeliveredAll(string $id) {}

    // Routes POST

    public function submitNewOrder(): RedirectResponse|Redirector
    {

        $request = request();
        $user = Auth::user();
        $orderNum = $request['order_num'];

        try {
            // 1) VALIDATION

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'supplier_name' => 'required|exists:suppliers,company_name',
                'order_num' => 'required|string|max:255',
                'quote_num' => 'required|string|max:255',
                'department_name' => 'nullable|exists:roles,name',
                'description' => 'nullable|string',
                'status' => 'required|string',
                'cost' => 'nullable|numeric',
                'quote' => 'nullable|file|mimes:pdf|max:20480',
                'purchase_order' => 'nullable|file|mimes:pdf|max:20480',
            ]);

            $userDepartment = $user->getDepartments()->first();
            $departmentName = $request['department_name'];

            $description = $request['description'];
            $quote_num = $request['quote_num'];
            $status = $request['status'];
            $cost = $request['cost'];
            $isSigned = $request['signed'];

            $department = Role::where('name', $departmentName ? $departmentName : $userDepartment->getName())->firstOrFail();
            $supplier = Supplier::where('company_name', $request['supplier_name'])->firstOrFail();

            // 2 CRÉATION DE LA COMMANDE
            $order = new Order([
                'title' => $validated['title'],
                'order_num' => $validated['order_num'],
                'status' => $validated['status'],
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
                $order->uploadQuote($request, false);
            }

            // 5 UPLOAD DU DEVIS
            if ($request->hasFile('purchase_order')) {
                $order->uploadPurchaseOrder($request, $isSigned, false);
            }

            // 6 SAUVEGARDE
            $order->save();
            session()->flash('success', 'La commande N°'.$order->getOrderNumber().' a été créée avec succès.');

        } catch (\Throwable $t) {
            session()->flash('error', 'Une erreur est survenue lors de la création de la commande commande N°'.$orderNum.'.');
        }

        return redirect('orders');
    }

    // Routes POST des actions d'états (actions rapides)

    public function actionUploadPurchaseOrder($page = 1)
    {
        $request = request();
        $id = $request['id'];
        /* @var Order $order */
        $order = Order::findOrFail($id);

        // Vérification de permissions
        $user = Auth::user();
        if (! ($user->hasPermission(PermissionValue::GERER_BONS_DE_COMMANDES) || $user->hasPermission(PermissionValue::SIGNER_BONS_DE_COMMANDES) || $user->hasRole($order->getDepartment()))) {
            session()->flash('purchaseOrderError-'.$id, "Vous n'avez pas la permission d'ajouter un bon de commande");

            return $this->modalUploadPurchaseOrder($id);
        }
        $nextStep = $request['nextStep'];
        $isSigned = $request['signed'];

        // Stockage du fichier dans storage/app/public/quotes
        // TODO ce serait bien que upload purchase order retourne le validator pour avoir l'erreur personnalisée
        $success = $order->uploadPurchaseOrder($request, $isSigned, false);
        if (! $success) {
            session()->flash('purchaseOrderError-'.$id, "Une erreur est survenue à l'enregistrement du bon de commande");

            return $this->modalUploadPurchaseOrder($id);
        }

        if ($nextStep) {
            $order->setStatus($isSigned ? Status::BON_DE_COMMANDE_SIGNE : Status::BON_DE_COMMANDE_NON_SIGNE, false);
        }

        $successToSave = $order->save();
        if (! $successToSave) {
            session()->flash('purchaseOrderError-'.$id, 'Une erreur est survenue à la sauvegarde de la commande !');

            return $this->modalUploadPurchaseOrder($id);
        }

        // Fallback pour fonctionnement sans JS (si besoin)
        return BaseController::getSuccessModal('Le bon de commande a été ajouté avec succès à la commande N°'.$order->getOrderNumber().'.');
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

    public function fetchOrders(User $user, ?string $search): AbstractPaginator
    {

        // TODO réduire le nombre de requêtes et voir à propos du cache (je pense qu'on ne fera pas de cache mais on opti les requêtes)
        // TODO factoriser avec un déctorateur le code pour l'utilisateur et si possible factoriser l'envoi des variables courantes (ex: $suerPermissions)
        $userRoles = $user->getRoles(); // Récupération des rôles en base de données
        $userPermissions = $user->getPermissions(); // Récupération d'un dictionnaire des permissions pour simplifier la vérification de permissions
        $userDepartments = $userRoles->filter(fn (Role $role) => $role->isDepartment());

        // Initialisation de la requête
        $query = Order::query();

        $userId = $user->getId();

        // Récupération uniquement des commandes dont l'utilisateur a accès
        if (! $user->hasPermission(PermissionValue::CONSULTER_TOUTES_COMMANDES)) {
            $query->where(function (Builder $q) use ($userDepartments, $userPermissions) {
                $userDepartments->each(function (Role $department) use ($q, $userPermissions) {
                    if ($userPermissions[PermissionValue::CONSULTER_COMMANDES_DEPARTMENT->value]) {
                        $q->orWhere('department_id', $department->getId());
                    }
                });
            });
        }

        // --- 2. TRI INTELLIGENT AVEC ENUMS ---

        // Définition des rôles
        $isDirecteur = $user->hasPermission(PermissionValue::SIGNER_BONS_DE_COMMANDES);
        $isFinancier = $user->hasPermission(PermissionValue::GERER_PAIEMENT_FOURNISSEURS);
        $isResponsableColis = $user->hasPermission(PermissionValue::GERER_COLIS_LIVRES);
        $isDepartment = $userDepartments->isNotEmpty();

        if ($isDirecteur) {
            // TRI DIRECTEUR
            $bcNonSigne = Status::BON_DE_COMMANDE_NON_SIGNE->value;
            $devis = Status::DEVIS->value;

            $query->orderByRaw("CASE
            WHEN status = '$bcNonSigne' THEN 1
            WHEN status = '$devis' THEN 2
            ELSE 3
        END");

        } elseif ($isFinancier) {
            // TRI FINANCIER
            $p1 = Status::SERVICE_FAIT->value;
            $p2 = Status::DEVIS->value;
            $p3 = Status::BON_DE_COMMANDE_NON_SIGNE->value;
            $p4 = Status::BON_DE_COMMANDE_REFUSE->value;
            $p5 = Status::LIVRE_ET_PAYE->value;

            $query->orderByRaw("CASE
            WHEN status = '$p1' THEN 1
            WHEN status = '$p2' THEN 2
            WHEN status = '$p3' THEN 3
            WHEN status = '$p4' THEN 4
            WHEN status = '$p5' THEN 5
            ELSE 6
        END");

        } elseif ($isResponsableColis) {
            // TRI RESPONSABLE COLIS
            // 1. En attente de livraison (Réponse reçue ou Partiel)
            // 2. Commande envoyée (Potentiellement en attente)
            // 3. Le reste

            $p1_Colis = implode("','", [
                Status::COMMANDE_AVEC_REPONSE->value,
                Status::PARTIELLEMENT_LIVRE->value,
            ]);

            $p2_Colis = Status::COMMANDE->value;

            $query->orderByRaw("CASE
            WHEN status IN ('$p1_Colis') THEN 1
            WHEN status = '$p2_Colis' THEN 2
            ELSE 3
        END");

        } elseif ($isDepartment) {
            // TRI DEPARTEMENTS
            $brouillon = Status::BROUILLON->value;

            $refusals = implode("','", [
                Status::DEVIS_REFUSE->value,
                Status::BON_DE_COMMANDE_REFUSE->value,
                Status::COMMANDE_REFUSEE->value,
            ]);

            $actionsRequises = implode("','", [
                Status::BON_DE_COMMANDE_SIGNE->value,
                Status::COMMANDE->value,
                Status::COMMANDE_AVEC_REPONSE->value,
                Status::PARTIELLEMENT_LIVRE->value,
            ]);

            $sqlSort = "CASE
            WHEN status = '$brouillon' AND author_id = ? THEN 1
            WHEN status IN ('$refusals') THEN 2
            WHEN status IN ('$actionsRequises') THEN 3
            ELSE 4
        END";

            $query->orderByRaw($sqlSort, [$user->getId()]);
            $query->orderByRaw($sqlSort, [$user->getId()]);
        }

        // 2.1 Filtre de recherche (si rempli)
        if ($search) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('order_num', 'LIKE', "%{$search}%")
                    ->orWhere('title', 'LIKE', "%{$search}%")
                    ->orWhere('status', 'LIKE', "%{$search}%")
                    ->orWhere('quote_num', 'LIKE', "%{$search}%");
            });
        }

        // --- 3. TRI SECONDAIRE (Date) ---
        // Les plus anciennes (date lointaine) en premier
        $query->orderBy('updated_at', 'asc');

        // Commandes retournées
        return $query->paginate(20)->withQueryString();
    }
}
