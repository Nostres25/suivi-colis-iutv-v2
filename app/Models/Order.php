<?php

namespace App\Models;

use Database\Seeders\Status;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'cost',
        'quote_num',
        'order_num',
        'path_quote',
        'path_purchase_order',
        'path_delivery_note',
        'status',
        'department_id',
        'supplier_id',
        'author_id',
        'content',
    ];

    /**
     * Retourne l'identifiant de la commande
     *
     * @return string // identifiant de la commande
     */
    public function getId(): string
    {
        return $this->attributes['id'];
    }

    /**
     * Retourne le titre/la désignation de la commande
     *
     * @return string // titre de la commande
     */
    public function getTitle(): string
    {
        return $this->attributes['title'];
    }

    /**
     * Retourne le numéro de la commande
     *
     * @return string // numéro de la commande
     */
    public function getOrderNumber(): string
    {
        return $this->attributes['order_num'];
    }

    /**
     * Retourne la description de la commande
     *
     * @return string|null // description de la commande
     */
    public function getDescription(): ?string
    {
        return $this->attributes['description'];
    }

    /**
     * Retourne le statut de la commande
     *
     * @param  bool  $noEnum  Si le résultat ne doit pas retourner une énumération mais un string
     * @return Status|string // Statut de la commande
     */
    public function getStatus(bool $noEnum = false): Status|string
    {
        $status = $this->attributes['status'];

        return $noEnum ? $status : Status::from($status);
    }

    /**
     * Retourne le coût en euros total de la commande
     *
     * @return float|null // coût en euros de la commande
     */
    public function getCost(): ?float
    {
        return $this->attributes['cost'];
    }

    /**
     * Retourne le coût formaté avec la devise en euro en chaîne de caractères.
     *
     * @return string le coût formaté en euro ou 'Non communiqué' si le coût n'est pas encore indiqué.
     */
    public function getCostFormatted(): string
    {
        return Order::getFormattedCost($this->getCost());
    }

    /**
     * Retourne le numéro du devis associé à la commande
     *
     * @return string|null // Numero du devis de la commande
     */
    public function getQuoteNumber(): ?string
    {
        return $this->attributes['quote_num'];
    }

    /**
     * Retourne l'url du devis.html/css avec bootstrap)
     *
     * @return string|null l'url du devis ou null si le devis n'est pas encore enregistré.
     */
    public function getUrlQuote(): ?string
    {
        $path_quote = $this->getAttributeValue('path_quote');
        if (is_null($path_quote)) {
            return null;
        }

        return Storage::url($path_quote);
    }

    /**
     * Retourne l'url du bon de commande.
     *
     * @return string|null l'url du bon de commande ou null si le bon de commande n'est pas encore enregistré.
     */
    public function getUrlPurchaseOrder(): ?string
    {
        $path_purchase_order = $this->getAttributeValue('path_purchase_order');
        if (is_null($path_purchase_order)) {
            return null;
        }

        return Storage::url($path_purchase_order);
    }

    /**
     * Retourne l'url du bon de livraison.
     *
     * @return string|null l'url du bon de livraison ou null si le bon de livraison n'est pas encore enregistré.
     */
    public function getUrlDeliveryNote(): ?string
    {
        $path_delivery_note = $this->getAttributeValue('path_delivery_note');
        if (is_null($path_delivery_note)) {
            return null;
        }

        return Storage::url($path_delivery_note);
    }

    /**
     * Retourne la date de la dernière modification de la commande
     *
     * @return ?string // date
     */
    public function getLastUpdateDate(): ?string
    {
        return $this->attributes[$this->getUpdatedAtColumn()];
    }

    /**
     * Retourne la date de création de la commande
     *
     * @return string // date
     */
    public function getCreationDate(): string
    {
        return $this->attributes[$this->getCreatedAtColumn()];
    }

    /**
     * Retourne les colis de la commande
     *
     * @param  bool  $foreRefresh  garantir de récupérer les informations depuis la base de données en toutes circonstances
     * @return Collection // Colis
     */
    public function getPackages(bool $foreRefresh = false): Collection
    {
        return $foreRefresh ? $this->packages()->getResults() : $this->getAttribute('packages');
    }

    /**
     * Retourne le fournisseur de la commande
     *
     * @return Supplier // Fournisseur de la commande
     */
    public function getSupplier(): Supplier
    {
        return $this->getAttribute('supplier');
    }

    /**
     * Retourne le rôle correspondant au département de la commande
     *
     * @return Role // Département (rôle) de la commande
     */
    public function getDepartment(): Role
    {
        return $this->getAttribute('department');
    }

    /**
     * Retourne la collection (liste) de logs associés à la commande (type Log)
     *
     * @return Collection // Collection (liste) de logs de la commande
     */
    public function getLogs(): Collection
    {
        return $this->logs()->getResults();
    }

    /**
     * Retourne le premier log associé à la création de la commande
     *
     * @return Log // Le premier log associé à la création de la commande
     */
    public function getFirstLog(): Log
    {
        // TODO Peut-être faire un cache ?
        /* @var Log $log */
        $log = $this->getLogs()->first();

        return $log;
    }

    /**
     * Retourne l'auteur de la commande (mentionné dans le premier log)
     *
     * @return User // L'utilisateur auteur de la commande
     */
    public function getAuthor(): User
    {
        // TODO Peut-être faire un cache ?
        /* @var User $author */
        $author = $this->author()->getResults();

        return $author;
    }

    /**
     * Définir le titre d'une commande. Cela va automatiquement passer la première lettre en majuscule
     *
     * @param  string  $title  Titre à définir qui doit décrire la commande de manière assez concise (taille max de 255)
     * @param  bool  $save  : si la fonction sauvegarde en base de données
     */
    public function setTitle(string $title, bool $save = true): void
    {
        $title = ucfirst($title);
        if ($save) {
            $this->setAttribute('title', $title);
        } else {
            $this->attributes['title'] = $title;
        }
    }

    /**
     * Définir le numéro d'une commande.
     *
     * @param  string  $order_num  Numéro de commande à définir
     * @param  bool  $save  : si la fonction sauvegarde en base de données
     */
    public function setOrderNumber(string $order_num, bool $save = true): void
    {
        if ($save) {
            $this->setAttribute('order_num', $order_num);
        } else {
            $this->attributes['order_num'] = $order_num;
        }
    }

    /**
     * Définir la déscription longue de la commande.
     *
     * @param  string  $description  Description de commande à définir
     * @param  bool  $save  : si la fonction sauvegarde en base de données
     */
    public function setDescription(string $description, bool $save = true): void
    {
        if ($save) {
            $this->setAttribute('description', $description);
        } else {
            $this->attributes['description'] = $description;
        }
    }

    /**
     * Définir le status de la commande.
     *
     * @param  Status|string  $status  Status de commande à définir
     * @param  bool  $save  : si la fonction sauvegarde en base de données (true par défaut)
     */
    public function setStatus(Status|string $status, bool $save = true): void
    {
        $status = is_string($status) ? $status : $status->value;
        if ($save) {
            $this->setAttribute('status', $status);
        } else {
            $this->attributes['status'] = $status;
        }

    }

    /**
     * Définir le coût en euros total de la commande.
     *
     * @param  float  $cost  Coût de la commande à définir
     * @param  bool  $save  : si la fonction sauvegarde en base de données
     * */
    public function setCost(float $cost, bool $save = true): void
    {

        if ($save) {
            $this->setAttribute('cost', $cost);
        } else {
            $this->attributes['cost'] = $cost;
        }
    }

    /**
     * Définir le numéro du devis la commande.
     *
     * @param  string  $quote_num  Coût de la commande à définir.
     * @param  bool  $save  : si la fonction sauvegarde en base de données
     */
    public function setQuoteNumber(string $quote_num, bool $save = true): void
    {
        if ($save) {
            $this->setAttribute('quote_num', $quote_num);
        } else {
            $this->attributes['quote_num'] = $quote_num;
        }

    }

    // TODO : Autre moyen de récupérer l'url d'un fichier (à tester)
    public function getUrlQuoteAlt(): ?string
    {
        if (is_null($this->path_quote)) {
            return null;
        }

        return asset('storage/'.$this->path_quote);
    }

    public function getUrlPurchaseOrderAlt(): ?string
    {
        if (is_null($this->path_purchase_order)) {
            return null;
        }

        return asset('storage/'.$this->path_purchase_order);
    }

    public function getUrlDeliveryNoteAlt(): ?string
    {
        if (is_null($this->path_delivery_note)) {
            return null;
        }

        return asset('storage/'.$this->path_delivery_note);
    }

    // TODO peut-être un peut factoriser l'upload des fichiers mais... plus tard

    /**
     * Enregistrer le fichier du devis
     *
     * @param  Request  $request  : la requête HTTP issue du controlleur contenant le fichier à uploader
     * @param  bool  $save  : si la fonction sauvegarde en base de données (true par défaut)
     * @return array true si l'enregistrement du fichier a fonctionné, false sinon
     */
    public function uploadQuote(Request $request, bool $save = true, bool $checkValidator = true): array
    {
        /* @var UploadedFile $file */
        $file = $request->file('quote');
        $validator = null;

        if ($checkValidator) {
            $validator = $this->checkQuote($request);
        }

        if ((! $validator || ! $validator->fails()) && $file) {
            try {

                $fileName = $file->getClientOriginalName();

                if (! stripos($fileName, 'devis')) {
                    $fileName = 'Devis'.$fileName;
                }

                $path_quote = $file->storeAs('uploads/orders/'.$this->getOrderNumber(), $fileName, 'public'); // public -> le dossier

                if ($path_quote) {
                    if ($save) {
                        $this->setAttribute('path_quote', $path_quote);
                    } else {
                        $this->attributes['path_quote'] = $path_quote;
                    }
                } else {
                    return ['validator' => @$validator, 'otherError' => 'Une erreur est survenue lors de la sauvegarde du fichier de bon de commande'];
                }

            } catch (\Throwable $th) {
                error_log("Une erreur est survenue lors de l'enregistrement d'un devis : \n".$th->getMessage());
                report($th);

                return ['validator' => @$validator, 'otherError' => "Une erreur est survenue lors de l'enregistrement d'un bon de commande"];

            }
        }

        return ['validator' => @$validator];
    }

    /**
     * Enregistrer le fichier du bon de commande
     *
     * @param  Request  $request  : la requête HTTP issue du controlleur contenant le fichier à uploader
     * @param  bool  $is_signed  : indique si le devis est signé ou non
     * @param  bool  $save  : si la fonction sauvegarde en base de données (true par défaut)
     * @return array Dictionnaire contenant un validator et potentiellement une autre erreur
     */
    public function uploadPurchaseOrder(Request $request, ?bool $is_signed = false, bool $save = true, bool $checkValidator = true): array
    {
        /* @var UploadedFile $file */
        $file = $request->file('purchase_order');
        $validator = null;

        if ($checkValidator) {
            $validator = $this->checkPurchaseOrder($request);
        }

        if ((! $validator || ! $validator->fails()) && $file) {
            try {
                $fileName = $file->getClientOriginalName();

                if (! stripos($fileName, 'BonDeCommande')) {
                    $fileName = 'BonDeCommande'.$fileName;
                }

                if ($is_signed) {
                    $ext = $file->getExtension();
                    $fileName = str_replace('.'.$ext, '(signe).'.$ext, $fileName);
                }

                $purchase_order = $file->storeAs('uploads/orders/'.$this->getOrderNumber(), $fileName, 'public'); // public -> le dossier

                if ($purchase_order) {
                    if ($save) {
                        $this->setAttribute('path_purchase_order', $purchase_order);
                    } else {
                        $this->attributes['path_purchase_order'] = $purchase_order;
                    }
                } else {
                    return ['validator' => @$validator, 'otherError' => 'Une erreur est survenue lors de la sauvegarde du fichier de bon de commande'];
                }

            } catch (\Throwable $th) {
                error_log("Une erreur est survenue lors de l'enregistrement d'un bon de commande : \n".$th->getMessage());
                report($th);

                return ['validator' => @$validator, 'otherError' => "Une erreur est survenue lors de l'enregistrement d'un bon de commande"];
            }
        }

        return ['validator' => @$validator];
    }

    /**
     * Enregistrer le fichier du bon de livraison
     *
     * @param  Request  $request  : la requête HTTP issue du controlleur contenant le fichier à uploader
     * @param  bool  $save  : si la fonction sauvegarde en base de données (true par défaut)
     * @return bool true si l'enregistrement du fichier a fonctionné, false sinon
     */
    public function uploadDeliveryNote(Request $request, bool $save = true): bool
    {
        $request->validate([
            'delivery_note' => 'required|mimes:pdf,doc,docx|max:10240', // Max 10MB
        ]);

        /* @var UploadedFile $file */
        $file = $request->file('delivery_note');
        if ($file) {

            try {

                $fileName = $file->getClientOriginalName();

                if (! stripos($fileName, 'BonDeLivraison')) {
                    $fileName = 'BonDeLivraison'.$fileName;
                }

                $path_delivery_note = $file->storeAs('uploads/orders/'.$this->getOrderNumber(), $fileName, 'public'); // public -> le dossier

                if ($path_delivery_note) {
                    if ($save) {
                        $this->setAttribute('path_delivery_note', $path_delivery_note);
                    } else {
                        $this->attributes['path_delivery_note'] = $path_delivery_note;
                    }

                    return true;
                }

            } catch (\Throwable $th) {
                error_log("Une erreur est survenue lors de l'enregistrement d'un bon de livraison : \n".$th->getMessage());
                report($th);

                return false;
            }

        }

        return false;
    }

    public function checkPurchaseOrder(Request $request): \Illuminate\Validation\Validator
    {
        return Validator::make($request->all(), [
            'purchase_order' => 'required|mimes:pdf,doc,docx|max:10240', // Max 10MB
        ]);
    }

    public function checkQuote(Request $request): \Illuminate\Validation\Validator
    {
        return Validator::make($request->all(), [
            'quote' => 'required|mimes:pdf,doc,docx|max:10240', // Max 10MB
        ]);
    }

    /**
     * Permet d'envoyer un journal (log)/ une ligne de modification pour garder une trace sur les actions de la commande
     *
     * @param  string  $content  : Contenu descriptif du log, description de l'action
     * @param  User  $author  : Auteur de l'action ou de la modification
     * @param  ?Status  $oldStatus  : Ancien statut à indiquer s'il y a un changement de statut afin d'indiquer automatiquement le changement de statut
     * @return array dictionnaire indiquant si la sauvegarde du log a été réussie ou non avec la valeur booléenne à la clé `success` et transmettant le model du log à la clé `model`
     */
    public function sendLog(string $content, User $author, ?Status $oldStatus = null, ?bool $save = true): array
    {
        /* @var Log $log */

        $log = $this->logs()->make([
            'content' => $content.($oldStatus ? " De plus, le statut de la commande passe de \"{$oldStatus->getDisplayName()}\" à \"{$this->getStatus()->getDisplayName()}\"." : ''),
        ]);

        $log->author()->associate($author);

        return ['success' => $save ? $log->save() : isset($log), 'model' => $log];
    }

    /**
     * Retourne la liste des colis de la commande
     *
     * @return HasMany // Liste des colis de la commande
     */
    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }

    /**
     * Retourne la liste le fournisseur de la commande
     *
     * @return BelongsTo // Fournisseur de la commande
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Retourne la liste des commentaires de la commande
     *
     * @return HasMany // Liste des commentaires de la commande
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Retourne la liste des actions associées à la commande
     *
     * @return HasMany // Liste des actions de la commande
     */
    public function logs(): HasMany
    {
        return $this->hasMany(Log::class);
    }

    /**
     * Retourne le rôle du département associé à la commande
     *
     * @return BelongsTo // Rôle du département
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'department_id');
    }

    /**
     * Retourne l'utilisateur, auteur de la commande
     *
     * @return BelongsTo // Utilisateur, auteur de la commande
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // Pas prioritaire - TODO
    // j'ai mis pleins d'options de recherche mais pas obliger de toutes les coder si on manque de temps
    //
    // /**
    //  * Récupérer le log d'une commande à partir d'un indice
    //  *
    //  * @param  int  $indexToSearch  Indice dans la liste des logs de la commande
    //  * @return array // ligne de log
    //  */
    // public function getLog(int $indexToSearch): string {}
    //
    // /**
    //  * Récupérer les logs d'une commande
    //  *
    //  * @return array // tableau de lignes de logs
    //  */
    // public function getLogs(): array {}
    //
    // // /**
    // //  * Récupérer les logs d'une commande contenant un certain texte
    // //  *
    // //  * @param  string  $valueToSearch  récupérer tous les logs contenant une chaîne de caractère en particulier
    // //  * @return array // tableau de lignes de logs
    // //  */
    // // public function getLogsWithText(string $valueToSearch) {}
    //
    // /**
    //  * Ajouter un log
    //  *
    //  * @param  User  $author  Auteur de l'action à l'origine du log
    //  * @param  string  $text  Contenu du log
    //  * @return void
    //  */
    // public function addLog(User $author, string $text) {}
    //
    // /**
    //  * Retirer un log
    //  *
    //  * @param  int  $index  Indice du log
    //  * @return void
    //  */
    // public function removeLog(int $index) {}

    public static function getFormattedCost(?float $cost): string
    {
        if (is_null($cost)) {
            return 'Non précisé';
        }

        return number_format($cost, 2, ',', ' ').' €';
    }
}
