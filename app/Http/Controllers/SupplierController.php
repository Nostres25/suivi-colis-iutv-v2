<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\User;
use Database\Seeders\PermissionValue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class SupplierController extends BaseController
{
    // =========================================================================
    // GET ROUTES (Listing & Views)
    // =========================================================================

    /**
     * Renders the main suppliers list view page.
     */
    public function viewSuppliers(): View|Response|RedirectResponse|Redirector
    {
        $request = request();

        /** @var User $user */
        $user = Auth::user();

        $search = $request->input('search');
        $page = $request->input('page');

        $suppliers = $this->fetchSuppliers($user, $page, $search)->withQueryString();

        return view('suppliers', [
            'user' => $user,
            'suppliers' => $suppliers,
            'search' => $search,
        ]);
    }

    /**
     * Refreshes and returns just the HTML table slice component via AJAX.
     */
    public function fetchSuppliersTable()
    {
        $user = Auth::user();
        $request = request();
        $search = $request->input('search');

        $suppliers = $this->fetchSuppliers($user, $request->input('page'), $search);

        // Redirect URL inside navigation items to point back to standard routing index
        $suppliers->withPath('/suppliers')->withQueryString();

        return view('components.suppliers.suppliers-table', [
            'suppliers' => $suppliers,
        ]);
    }

    /**
     * Renders detailed profile data within a modal element frame.
     */
    public function modalViewDetails(string $id)
    {
        $user = Auth::user();
        $request = request();

        /** @var Supplier $supplier */
        $supplier = Supplier::where('id', $id)->firstOrFail();
        $edit = $request['edit'];

        return view('components.suppliers.modal.viewSupplierModal', [
            'user' => $user,
            'supplier' => $supplier,
            'supplierId' => $supplier->getId(),
            'edit' => $edit,
        ]);
    }

    /**
     * Updates an existing supplier record profile entry.
     */
    public function editSupplier(string $id)
    {
        $user = Auth::user();
        $request = request();

        /** @var Supplier $supplier */
        $supplier = Supplier::where('id', $id)->firstOrFail();
        $edit = $request['edit'];

        if ($request->method() === 'POST') {
            if ($user->hasPermission(PermissionValue::NOTES_ET_COMMENTAIRES)) {
                $note = $request['note'];
                $supplier->setNote($note, false);
                session()->flash('supplierSuccess', 'Note du fournisseur mise à jour !');
            }

            if ($edit && $user->hasPermission(PermissionValue::GERER_FOURNISSEURS)) {
                
                // Validation rapide des données entrantes pour éviter les crashs SQL
                $validated = $request->validate([
                    'siret' => 'required|string|size:14',
                    'address' => 'required|string|max:255',
                    'contactName' => 'required|string|max:255',
                    'email' => 'required|email|max:255',
                    'phoneNumber' => 'required|string|max:50',
                    'speciality' => 'nullable|string|max:255',
                    'isValid' => 'required|string',
                ]);

                $supplier->setCompanyName($request->input('companyName', $supplier->getCompanyName()), false);
                $supplier->setEmail($validated['email'], false);
                $supplier->setPhoneNumber($validated['phoneNumber'], false);
                $supplier->setContactName($validated['contactName'], false);
                $supplier->setSpeciality($validated['speciality'], false);
                $supplier->setAddress($validated['address'], false);
                $supplier->setSiret($validated['siret'], false);
                $supplier->setValidity($validated['isValid'], false);

                session()->flash('supplierSuccess', 'Fournisseur mis à jour !');
            } else {
                $edit = false;
            }

            if ($user->hasPermission(PermissionValue::GERER_FOURNISSEURS) || $user->hasPermission(PermissionValue::NOTES_ET_COMMENTAIRES)) {
                $supplier->save();
            } else {
                session()->flash('supplierError-'.$supplier->getId(), "Vous n'avez pas la permission de modifier la moindre information concernant les fournisseurs.");
            }
        }

        return view('components.suppliers.modal.viewSupplierModal', [
            'user' => $user,
            'supplier' => $supplier,
            'supplierId' => $supplier->getId(),
            'edit' => $edit,
        ]);
    }

    /**
     * Route POST - Crée un nouveau fournisseur.
     * Accessible par le Service financier/Admin BD (via GERER_FOURNISSEURS) 
     * ou d'autres rôles disposant de la permission de demande d'ajout.
     *
     * @return JsonResponse
     */
    public function create(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Validation stricte via Permissions uniquement
        $canManage = $user->hasPermission(PermissionValue::GERER_FOURNISSEURS);
        $canRequest = $user->hasPermission(PermissionValue::DEMANDER_AJOUT_FOURNISSEUR);

        if (!$canManage && !$canRequest) {
            return response()->json([
                'success' => false,
                'message' => "Accès refusé. Vous n'avez pas l'autorisation d'ajouter un fournisseur."
            ], 403);
        }

        $request = request();

        $validated = $request->validate([
            'companyName' => 'required|string|max:255|unique:suppliers,company_name',
            'siret'       => 'required|string|size:14|unique:suppliers,siret',
            'email'       => 'required|email|max:255',
            'phoneNumber' => 'required|string|max:50',
            'contactName' => 'required|string|max:255',
            'address'     => 'required|string|max:255', // Prise en compte du nouveau composant
            'speciality'  => 'nullable|string|max:255',
            'note'        => 'nullable|string',
        ]);

        try {
            $supplier = new Supplier();
            $supplier->setCompanyName($validated['companyName'], false);
            $supplier->setSiret($validated['siret'], false);
            $supplier->setEmail($validated['email'], false);
            $supplier->setPhoneNumber($validated['phoneNumber'], false);
            $supplier->setContactName($validated['contactName'], false);
            $supplier->setAddress($validated['address'], false);

            if (isset($validated['speciality'])) {
                $supplier->setSpeciality($validated['speciality'], false);
            }
            if (isset($validated['note'])) {
                $supplier->setNote($validated['note'], false);
            }

            // Sécurité Backend absolue : Gestion du statut selon les droits réels
            if ($canManage && $request->filled('isValid')) {
                $isValid = $request->input('isValid');
                $supplier->setValidity($isValid, false);
            } else {
                // Règle d'or : Passage forcé en "En attente" pour les utilisateurs de département / demandeurs
                $supplier->setValidity(Supplier::VALIDITY_STATUS_PENDING, false);
            }

            $supplier->save();

            return response()->json([
                'success' => true,
                'data' => ['id' => $supplier->getId()],
                'message' => 'Le fournisseur a été créé avec succès.'
            ], 201);
        } catch (QueryException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de créer le fournisseur. Le nom de l\'entreprise ou le SIRET existe déjà.'
            ], 409);
        }
    }

    // =========================================================================
    // INTERNAL UTILITY FUNCTIONS
    // =========================================================================

    /**
     * Query orchestrator containing complex sorting and pagination logic for suppliers lists.
     */
    public function fetchSuppliers(User $user, int|string|null $page, ?string $search): LengthAwarePaginator
    {
        $query = Supplier::query();

        // ---------------------------------------------------------
        // STEP 1: SORT BY VALIDITY STATUS PREFERENCE RULES
        // ---------------------------------------------------------
        $isFinancier = $user->hasPermission(PermissionValue::GERER_FOURNISSEURS);

        if ($isFinancier) {
            // Service Financier: Pending requests process first
            $query->orderByRaw("CASE is_valid WHEN 'pending' THEN 0 WHEN 'refused' THEN 1 WHEN 'validated' THEN 2 ELSE 3 END", []);
        } else {
            // Basic User Roles: Validated accounts surface first
            $query->orderByRaw("CASE is_valid WHEN 'validated' THEN 0 WHEN 'pending' THEN 1 WHEN 'refused' THEN 2 ELSE 3 END", []);
        }

        // ---------------------------------------------------------
        // STEP 2: ORDER BY SYSTEM ACTIVITY PATTERNS
        // ---------------------------------------------------------
        $sqlActivitySort = '
            GREATEST(
                suppliers.updated_at,
                COALESCE(
                    (SELECT updated_at FROM orders WHERE supplier_id = suppliers.id ORDER BY updated_at DESC LIMIT 1),
                    suppliers.updated_at
                )
            ) DESC
        ';

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('company_name', 'LIKE', "%{$search}%")
                  ->orWhere('contact_name', 'LIKE', "%{$search}%")
                  ->orWhere('siret', 'LIKE', "%{$search}%");
            });
        }
        $query->orderByRaw($sqlActivitySort, []);

        if (is_string($page)) {
            $page = intval($page);
        }

        return $query->paginate(10, ['*'], 'page', $page);
    }
}