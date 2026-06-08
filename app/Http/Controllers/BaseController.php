<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends Controller
{
    public static function getSuccessModal(string $message)
    {
        return view('components.base.modal.successModal', [
            'message' => $message,
        ]);
    }

    /**
     * Procède à l'authentification de l'utilisateur.
     * S'il est déjà connecté, rien ne se passe
     * S'il n'est pas connecté, cela va définir l'utilisateur comme connecté avec `Auth::login($user)`.
     * L'utilisateur est ensuite récupérable dans les autres controllers avec `Auth::user()`
     * Dans la valeur retournée :
     * - "success" signifie que l'authentification a réussie si sa valeur est en true (clé toujours présente)
     * - "response" est la réponse à retourner en cas d'erreur (clé présente uniquement quand "success" est à false)
     *
     * @param  Request  $request  Requête
     * @return array // dictionnaire au format ['success' => bool, 'response' => Response|ResponseFactory|null].
     */
    public function auth(Request $request): array
    {
        // Connexion de l'utilisateur
        if (Auth::check()) {
            if (Auth::user() !== null) {
                // Si l'utilisateur est déjà connecté, rediriger l'utilisateur vers la page qu'il souhaitait accéder à l'origine
                return ['success' => true];
            } else {
                dd("Erreur temporaire de test dans l'AuthController, c'est vraiment pas normal, l'authentification ne devrait pas être considérée comme valide alors que l'utilisateur n'existe pas !");
            }
        }

        /* @var User $user */
        $user = null;

        if (! app()->isLocal()) {
            // En Production, l'utilisateur s'est authentifié par le CAS
            if (! isset($_SERVER['REMOTE_USER'])) {
                $response_content =
                    "
                <h1> Erreur 401 : Unauthorized</h1><br/>
                Vous n'avez pas été détecté comme connecté par le biais du CAS prévu à cet effet.<br/>
                Cette erreur n'est pas normale. Veuillez contacter la personne responsable de ce site ! <br/>

                <h2>Causes possibles</h2>
                La raison de cette erreur peut varier :<br/><br/>
                - En accédant à ce site, vous avez été redirigé vers le CAS/la page de connexion de l'université Sorbonne Parid Nord à l'adresse \"https://cas.univ-paris13.fr/\". Dans ce cas, il se peut que le serveur web ne redirige plus correctement vers le CAS à cause d'un changement dans la configuration.<br/><br/>
                - Sinon, il peut s'agir d'un changement par rapport au CAS de l'université qui n'envoie plus les informations de connexion sous le même nom de clé. Dans ce cas, la solution pour le responsable du site est de corriger le nom de clé utilisé dans le code.<br/><br/>
                - d'une erreur au niveau du CAS de l'université qui provoque le non envoie des informations de connexions sous les noms de clés habituelles. Dans cette situation, c'est aux responsables du CAS de l'université de corriger le problème<br/><br/>
                ";

                // TODO rédiger cette exception ?
                report('Les informations normalement envoyées par le CAS ');

                return ['success' => false, 'response' => response($response_content, 403)];
            }

            $login = $_SERVER['REMOTE_USER'];

            // Création ou récupération de l'utilisateur
            $user = User::first(
                ['login' => $login],
            );
        } else {
            // En local, on choisit un utilisateur de test
            $user =
                User::all()->first(
                    function (User $user) {
                        $roles = $user->getRoles();

                        // Rôle que l'utilisateur de test doit avoir (mettre null pour pas de rôle en particulier)
                        // Choix du rôle de l'utilisateur : Service financier, Directeur IUT, Département Info, Département SD, Département RT, Administrateur BD
                        $roleToHave = 'Service financier';

                        // Nombre de rôles que l'utilisateur de test doit avoir
                        $roleNumber = 1;

                        return (is_null($roleToHave) || $roles->first((fn (Role $role) => $role->getName() == $roleToHave))) && $roles->count() == $roleNumber;

                    }
                );
        }

        if (is_null($user)) {
            $response_content =
                "
                <h1> Erreur 403 : Forbidden</h1><br/>
                Votre compte utilisateur n'a pas été trouvé, vous n'avez donc pas la permission d'accéder au site.<br/>
                Vous n'avez pas encore été ajouté au système. Autrement, il s'agit très probablement d'une erreur, veuillez contacter le responsable de ce site de suivi des colis à l'IUT de Villetaneuse et de sa base de données pour résoudre le problème.
                ";

            if (app()->isLocal()) {
                $response_content .=
                    "
                    <br/><br/><strong>Si vous êtes en local</strong>, pour tester le site par exemple, la raison est qu'aucun des utilisateurs en base de données ne respecte les conditions de l'utilisateur de test.<br/>
                    Pour régler cela :<br/>
                    - mettez des conditions qui correspondent à au moins un utilisateur dans la base de données<br/><br/>
                    - générez de nouvelles données de test additionnelles avec la commande <code>php artisan db:seed</code> une ou plusieurs fois afin d'espérer générer l'utilisateur qui correspond à ces conditions (Il se peut que les tables de la base de données n'aient pas été créées ou qu'elles aient été modifiées, ce qui provoquerait une erreur. Ainsi, exécutez <code>php artisan migrate --seed</code> pour créer tables de la base de données et les remplir de données de test)<br/><br/>
                    - créez vous-même l'utilisateur en base de données, en lui attribuant un rôle via la table pivot (ou table association) <code>role_user</code> (accédez à la base de données à l'aide de la commande <code>php artisan db</code>. Si le site est correctement installé, cela fonctionnera)<br/><br/>
                    - ajoutez dans le fichier <code>database/seeders/LocalTestSeeder.php</code> une condition pour toujours garantir d'avoir un utilisateur respectant les conditions souhaitées dans les données de test <br/><br/>

                    Si vous venez d'installer le projet, assurez-vous de bien suivre toutes les instructions de l'installation au préalable !
                    ";
            }

            return ['success' => false, 'response' => response($response_content, 403)];
        }

        // Charger les permissions de l'utilisateur
        // TODO en changeant de page, les variables du modèle se déchargent.
        //  Malgré le stockage de l'utilisateur avec l'authentification (et peut-être même malgré la session)
        //  Pour optimiser plus de sorte à ce que le chargement de permissions ne se fait pas à chaque chargement de page
        //  il faudrait faire un cache pour stocker les utilisateurs qui se sont connectés
        $user->getPermissions(true);

        Auth::login($user);

        $rolesToStrring = implode(', ', $user->getRoles()->map(fn (Role $role) => $role->getName())->toArray());
        session()->flash(
            'success',
            "Connecté en tant que {$user->getFullName()} avec le(s) rôle(s) {$rolesToStrring}"
        );

        return ['success' => true];
    }

    /**
     * Fonction de callback permettant d'exécuter des actions avant chaque fonction de chaque controllers.
     *
     * @param  mixed  $method  Fonction du controller à exécuter à la fin des actions
     * @param  mixed  $parameters  liste des paramètres de la fonction du controller. Le premier paramètre sera la requête généralement
     * @return Response // dictionnaire au format ['success' => bool, 'response' => Response|ResponseFactory]
     */
    public function callAction($method, $parameters)
    {
        // Charger l'utilisateur connecté pour être recupérable avec `Auth::user()`
        // S'il y a une erreur dans le processus d'authentification, retourner pour afficher la vue d'erreur

        $result = $this->auth(request());
        if (! $result['success']) {
            return $result['response'];
        }

        return parent::callAction($method, $parameters); // TODO: Change the autogenerated stub
    }

    public static function getDefaultMailContent(string $type, ?Order $order = null, ?User $user = null)
    {
        $signature_roles = implode(', ', $user->getRoles()->map(fn (Role $role) => $role->getName())->toArray());

        if ($type === 'update_purchase_order') {
            return "Madame, monsieur,\n".
                "Un bon de commande a été ajouté à la commande désignée \"{$order->getTitle()}\" de numéro {$order->getOrderNumber()}.\n\n".
                "{$user->getFullName()}\n".
                "{$signature_roles},\n".
                'IUT de Villetaneuse, Sorbonne Paris Nord';
        }

        if ($type === 'paid_order') {
            return "Madame, monsieur,\n".
                "La commande désignée \"{$order->getTitle()}\" et de numéro {$order->getOrderNumber()}, a été payée par le service financier de l'IUT pour la somme de {coûtEnEuros}.\n\n".
                "{$user->getFullName()}\n".
                "{$signature_roles},\n".
                'IUT de Villetaneuse, Sorbonne Paris Nord';
        }

        if ($type === 'refuse') {
            return "Madame, monsieur,\n".
                "Le devis de la commande désignée \"{$order->getTitle()}\" et de numéro {$order->getOrderNumber()}, a été refusé pour la raison suivante :\n".
                "{raison}\n".
                "Par conséquent, aucun bon de commande ne sera rédigé en l'état.\n\n".
                "{$user->getFullName()}\n".
                "{$signature_roles},\n".
                'IUT de Villetaneuse, Sorbonne Paris Nord';
        }

        if ($type === 'refuse_signature') {
            return "Madame, monsieur,\n".
                "La signature du bon de commande désigné \"{$order->getTitle()}\" et de numéro {$order->getOrderNumber()}, a été refusée pour la raison suivante :\n".
                "{raison}\n\n".
                "{$user->getFullName()}\n".
                "{$signature_roles},\n".
                'IUT de Villetaneuse, Sorbonne Paris Nord';
        }

        return null;
    }
}
