<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
use Database\Seeders\PermissionValue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SupplierController extends BaseController
{
    // Routes GET
    public function viewSuppliers(): View|Response|RedirectResponse|Redirector
    {
        $request = request();

        /* @var User $user */
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

    public function fetchSuppliersTable()
    {
        // Récupérer les informations de la requête
        $user = Auth::user();
        $request = request();
        $search = $request->input('search');

        // Récupérer les fournisseurs
        $suppliers = $this->fetchSuppliers($user, $request->input('page'), $search);

        // Redéfinition de l'URL des boutons de navigation afin de pointer vers la page des fournisseurs et non vers la route pour actualiser la table
        $suppliers->withPath('/suppliers')->withQueryString();

        return view('components.suppliers.suppliers-table', [
            'suppliers' => $suppliers,
        ]);
    }

    // Routes GET modal
    public function modalViewDetails(string $id)
    {
        $user = Auth::user();
        $request = request();

        /* @var Supplier $supplier */
        $supplier = Supplier::where('id', $id)->first();
        $edit = $request['edit'];

        return view('components.suppliers.modal.viewSupplierModal', [
            'user' => $user,
            'supplier' => $supplier,
            'supplierId' => $supplier->getId(),
            'edit' => $edit,
        ]);
    }

    // Routes POST modal
    public function editSupplier(string $id)
    {
        $user = Auth::user();
        $request = request();

        /* @var Supplier $supplier */
        $supplier = Supplier::where('id', $id)->first();
        $edit = $request['edit'];

        if ($request->method() === 'POST') {
            // TODO corriger le fait que le message erreur ou succès il disparait seulement au bout de 2 actualisations, pas une.
            if ($user->hasPermission(PermissionValue::NOTES_ET_COMMENTAIRES)) {
                $note = $request['note'];
                $supplier->setNote($note, false);
                session()->flash('supplierSuccess', 'Note du fournisseur mise à jour !');
            }

            if ($edit && $user->hasPermission(PermissionValue::GERER_FOURNISSEURS)) {
                $companyName = $request['companyName'];
                $email = $request['email'];
                $phoneNumber = $request['phoneNumber'];
                $siret = $request['siret'];
                $isValid = $request['isValid'];
                $speciality = $request['speciality'];
                $contactName = $request['contactName'] ?? null; // Ajout d'une sécurité si contactName n'est pas dans la requête

                if (isset($companyName)) {
                    $supplier->setCompanyName($companyName, false);
                }
                if (isset($email)) {
                    $supplier->setEmail($email, false);
                }
                if (isset($phoneNumber)) {
                    $supplier->setPhoneNumber($phoneNumber, false);
                }

                if (isset($contactName)) {
                    $supplier->setContactName($contactName, false); // Correction: c'était setCompanyName avant
                }

                if (isset($speciality)) {
                    $supplier->setSpeciality($speciality, false);
                }

                if (isset($siret)) {
                    $siretLength = strlen($siret);
                    if ($siretLength > 14 || $siretLength < 14) {
                        session()->flash('supplierError-'.$supplier->getId(), 'Le siret doit faire exactement 14 chiffres et non '.$siretLength.' chiffres');
                    } else {
                        $supplier->setSiret($siret, false);
                    }
                }
                $supplier->setValidity((bool) $isValid, false);

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

    // Route POST - create supplier
    public function create()
    {
        $user = Auth::user();
        
        // 1. VÉRIFICATION DES RÔLES
        $roleNames = $user->getRoles()->pluck('name')->toArray();
        $isAuthorized = in_array('Service financier', $roleNames) || in_array('Administrateur BD', $roleNames);

        if (!$isAuthorized) {
            return response()->json([
                'success' => false, 
                'message' => "Accès refusé. Seul le Service financier ou l'Administrateur BD peut créer un fournisseur."
            ], 403);
        }

        $request = request();

        $validated = $request->validate([
            'companyName' => 'required|string|max:255',
            'siret' => 'required|string|size:14',
            'email' => 'required|email|max:255',
            'phoneNumber' => 'required|string|max:50',
            'contactName' => 'required|string|max:255',
            'speciality' => 'nullable|string|max:255',
            'note' => 'nullable|string',
            'isValid' => 'nullable|boolean',
        ]);

        $supplier = new Supplier();
        $supplier->setCompanyName($validated['companyName'], false);
        $supplier->setSiret($validated['siret'], false);
        $supplier->setEmail($validated['email'], false);
        $supplier->setPhoneNumber($validated['phoneNumber'], false);
        $supplier->setContactName($validated['contactName'], false);

        if (isset($validated['speciality'])) {
            $supplier->setSpeciality($validated['speciality'], false);
        }
        if (isset($validated['note'])) {
            $supplier->setNote($validated['note'], false);
        }

        // Seuls les rôles autorisés arrivent ici, on peut donc valider directement si la checkbox est cochée
        $isValid = false;
        if (isset($validated['isValid'])) {
            $isValid = (bool)$validated['isValid'];
        }
        $supplier->setValidity($isValid, false);

        $supplier->save();

        return response()->json(['success' => true, 'data' => ['id' => $supplier->getId()], 'message' => 'Fournisseur créé']);
    }

    // Autres fonctions
    public function fetchSuppliers(User $user, int|string|null $page, ?string $search): LengthAwarePaginator
    {
        $userRoles = $user->getRoles();
        $userPermissions = Role::getPermissionsAsDict($userRoles);

        // 1. Initialisation de la Query
        $query = Supplier::query();

        // ---------------------------------------------------------
        // ETAPE 1 : TRI PAR VALIDITÉ
        // ---------------------------------------------------------
        $isFinancier = $user->hasPermission(PermissionValue::GERER_FOURNISSEURS);

        if ($isFinancier) {
            // Service Financier : Non validés en premier
            $query->orderBy('is_valid', 'asc');
        } else {
            // Autres : Validés en premier
            $query->orderBy('is_valid', 'desc');
        }

        // ---------------------------------------------------------
        // ETAPE 2 : LE TRI "MÉLANGÉ" (Activité Globale)
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
            $query->where('company_name', 'LIKE', "%{$search}%")
                ->orWhere('contact_name', 'LIKE', "%{$search}%")
                ->orWhere('siret', 'LIKE', "%{$search}%");
        }
        $query->orderByRaw($sqlActivitySort);

        // return suppliers pagination
        if (is_string($page)) {
            $page = intval($page);
        }

        return $query->paginate(10, ['*'], 'page', $page);
    }
}