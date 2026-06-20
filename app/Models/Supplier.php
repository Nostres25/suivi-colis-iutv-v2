<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Supplier extends Model
{
    use HasFactory;

    public const VALIDITY_STATUS_VALIDATED = 'validated';
    public const VALIDITY_STATUS_PENDING = 'pending';
    public const VALIDITY_STATUS_REFUSED = 'refused';

   
    protected $guarded = [

    ];

    /**
     * Retourne l'identifiant du fournisseur
     *
     * @return string // identifiant du fournisseur
     */
    public function getId(): string
    {
        return $this->attributes['id'];
    }

    /**
     * Retourne le nom de l'entreprise du fournisseur.
     *
     * @return string // Nom d'entreprise du fournisseur
     */
    public function getCompanyName(): string
    {
        return $this->attributes['company_name'];
    }

    /**
     * Retourne le SIRET de l'entreprise du fournisseur.
     *
     * @return string // SIRET d'entreprise du fournisseur
     */
    public function getSiret(): string
    {
        return $this->attributes['siret'];
    }

    /**
     * Retourne l'email de contact du fournisseur.
     *
     * @return ?string // email de contact du fournisseur
     */
    public function getEmail(): ?string
    {
        return $this->attributes['email'];
    }

    /**
     * Retourne le numéro de téléphone de contact du fournisseur.
     *
     * @return ?string // numéro de téléphone de contact du fournisseur
     */
    public function getPhoneNumber(): ?string
    {
        return $this->attributes['phone_number'];
    }

    /**
     * Retourne le nom du contact dans l'entreprise fournisseur.
     *
     * @return ?string // nom du contact dans l'entreprise fournisseur
     */
    public function getContactName(): ?string
    {
        return $this->attributes['contact_name'];
    }

    public function getAddress(): ?string
    {
        return $this->attributes['address'];
    }

    /**
     * Retourne la description des spécialités de l'entreprise fournisseur.
     *
     * @return ?string // description des spécialités de l'entreprise fournisseur
     */
    public function getSpeciality(): ?string
    {
        return $this->attributes['speciality'];
    }

    /**
     * Retourne les notes sur le fournisseur.
     *
     * @return ?string // notes sur le fournisseur.
     */
    public function getNote(): ?string
    {
        return $this->attributes['note'];
    }

    /**
     * Retourne le statut de validité du fournisseur.
     *
     * @return string // Statut du fournisseur
     */
    public function getValidityStatus(): string
    {
        return $this->attributes['is_valid'] ?? self::VALIDITY_STATUS_PENDING;
    }

    /**
     * Retourne le libellé lisible du statut du fournisseur.
     *
     * @return string // Statut affichable du fournisseur
     */
    public function getValidityLabel(): string
    {
        return match ($this->getValidityStatus()) {
            self::VALIDITY_STATUS_VALIDATED => 'Validé',
            self::VALIDITY_STATUS_REFUSED => 'Refusé',
            default => 'En attente',
        };
    }

    /**
     * Retourne la variante visuelle du statut du fournisseur.
     *
     * @return string // Classe visuelle du statut
     */
    public function getValidityBadgeClass(): string
    {
        return match ($this->getValidityStatus()) {
            self::VALIDITY_STATUS_VALIDATED => 'valid',
            self::VALIDITY_STATUS_REFUSED => 'invalid',
            default => 'pending',
        };
    }

    /**
     * Retourne les statuts disponibles pour un fournisseur.
     *
     * @return array<string, string>
     */
    public static function validityOptions(): array
    {
        return [
            self::VALIDITY_STATUS_VALIDATED => 'Validé',
            self::VALIDITY_STATUS_PENDING => 'En attente',
            self::VALIDITY_STATUS_REFUSED => 'Refusé',
        ];
    }

    /**
     * Retourne true si le fournisseur est considéré comme valide.
     * Un fournisseur valide est un fournisseur auprès duquel il est possible de commander.
     *
     * @return bool // Si le fournisseur est valide
     */
    public function isValid(): bool
    {
        return $this->getValidityStatus() === self::VALIDITY_STATUS_VALIDATED;
    }

    /**
     * Définit le nom de l'entreprise du fournisseur
     *
     * @param  string  $companyName  nom de l'entreprise fournisseur
     * @param  bool  $save  si la donnée doit directement être sauvegardée en base de données
     */
    public function setCompanyName(string $companyName, bool $save = true): void
    {
        if ($save) {
            $this->setAttribute('company_name', $companyName);
        } else {
            $this->attributes['company_name'] = $companyName;
        }
    }

    /**
     * Définit le SIRET de l'entreprise du fournisseur
     *
     * @param  string  $siret  SIRET de l'entreprise fournisseur
     * @param  bool  $save  si la donnée doit directement être sauvegardée en base de données
     */
    public function setSiret(string $siret, bool $save = true): void
    {
        if ($save) {
            $this->setAttribute('siret', $siret);
        } else {
            $this->attributes['siret'] = $siret;
        }
    }

    /**
     * Définit l'email de contact du fournisseur.
     *
     * @param  string  $email  email de contact du fournisseur
     * @param  bool  $save  si la donnée doit directement être sauvegardée en base de données
     */
    public function setEmail(string $email, bool $save = true): void
    {
        if ($save) {
            $this->setAttribute('email', $email);
        } else {
            $this->attributes['email'] = $email;
        }
    }

    /**
     * Définit le numéro de téléphone de contact du fournisseur.
     *
     * @param  string  $phone_number  numéro de téléphone de contact du fournisseur
     * @param  bool  $save  si la donnée doit directement être sauvegardée en base de données
     */
    public function setPhoneNumber(string $phone_number, bool $save = true): void
    {
        if ($save) {
            $this->setAttribute('phone_number', $phone_number);
        } else {
            $this->attributes['phone_number'] = $phone_number;
        }
    }

    /**
     * Définit le nom du contact dans l'entreprise fournisseur.
     *
     * @param  string  $contact_name  nom du contact dans l'entreprise fournisseur
     * @param  bool  $save  si la donnée doit directement être sauvegardée en base de données
     */
    public function setContactName(string $contact_name, bool $save = true): void
    {
        if ($save) {
            $this->setAttribute('contact_name', $contact_name);
        } else {
            $this->attributes['contact_name'] = $contact_name;
        }
    }

    public function setAddress(string $address, bool $save = true): void
    {
        if ($save) {
            $this->setAttribute('address', $address);
        } else {
            $this->attributes['address'] = $address;
        }
    }

    /**
     * Définit la description des spécialités de l'entreprise fournisseur.
     *
     * @param  string  $speciality  description des spécialités de l'entreprise fournisseur.
     * @param  bool  $save  si la donnée doit directement être sauvegardée en base de données
     */
    public function setSpeciality(string $speciality, bool $save = true): void
    {
        if ($save) {
            $this->setAttribute('speciality', $speciality);
        } else {
            $this->attributes['speciality'] = $speciality;
        }
    }

    /**
     * Définit le texte des notes sur le fournisseur.
     *
     * @param  string  $note  texte des notes sur le fournisseur.
     * @param  bool  $save  si la donnée doit directement être sauvegardée en base de données
     */
    public function setNote(string $note, bool $save = true): void
    {
        if ($save) {
            $this->setAttribute('note', $note);
        } else {
            $this->attributes['note'] = $note;
        }
    }


    /**
     * Définit le statut de validité du fournisseur.
     *
     * @param  string  $is_valid  statut du fournisseur
     * @param  bool  $save  si la donnée doit directement être sauvegardée en base de données
     */
    public function setValidity(string $is_valid, bool $save = true): void
    {
        if ($save) {
            $this->setAttribute('is_valid', $is_valid);
        } else {
            $this->attributes['is_valid'] = $is_valid;
        }
    }

    /**
     * Retourne la date de la dernière modification du fournisseur
     *
     * @return ?string // date
     */
    public function getLastUpdateDate(): ?string
    {
        return $this->attributes[$this->getUpdatedAtColumn()];
    }

    /**
     * Retourne la date de création du fournisseur
     *
     * @return string // date
     */
    public function getCreationDate(): string
    {
        return $this->attributes[$this->getCreatedAtColumn()];
    }

    /**
     * Retourne la liste des commandes du fournisseur
     *
     * @return Collection // Collection (liste) des commandes du fournisseur
     */
    public function getOrders(): Collection
    {
        return $this->getAttribute('orders');
    }

    /**
     * Retourne la liste des commandes du fournisseur
     *
     * @return HasMany // Liste des commandes du fournisseur
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
