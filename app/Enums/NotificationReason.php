<?php

namespace App\Enums;

enum NotificationReason: string
{
    case COMMANDE_CREEE = 'COMMANDE_CREEE';
    case COMMANDE_MODIFIEE = 'COMMANDE_MODIFIEE';
    case BON_DE_COMMANDE_PUBLIE = 'BON_DE_COMMANDE_PUBLIE';
    case BON_DE_COMMANDE_SIGNE = 'BON_DE_COMMANDE_SIGNE';
    case COMMANDE_PAYEE = 'COMMANDE_PAYEE';
    case DEVIS_REFUSE = 'DEVIS_REFUSE';
    case SIGNATURE_REFUSEE = 'SIGNATURE_REFUSEE';
    case ENVOYEE_FOURNISSEUR = 'ENVOYEE_FOURNISSEUR';
    case COLIS_MIS_A_JOUR = 'COLIS_MIS_A_JOUR';

    public function getSubject(string $orderTitle): string
    {
        return match ($this) {
            self::COMMANDE_CREEE => "Nouvelle commande : $orderTitle",
            self::COMMANDE_MODIFIEE => "Commande modifiée : $orderTitle",
            self::BON_DE_COMMANDE_PUBLIE => "Bon de commande publié : $orderTitle",
            self::BON_DE_COMMANDE_SIGNE => "Bon de commande signé : $orderTitle",
            self::COMMANDE_PAYEE => "Commande payée : $orderTitle",
            self::DEVIS_REFUSE => "Devis refusé : $orderTitle",
            self::SIGNATURE_REFUSEE => "Signature refusée : $orderTitle",
            self::ENVOYEE_FOURNISSEUR => "Commande envoyée au fournisseur : $orderTitle",
            self::COLIS_MIS_A_JOUR => "Informations colis mises à jour : $orderTitle",
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::COMMANDE_CREEE => 'Une nouvelle commande a été créée dans votre département.',
            self::COMMANDE_MODIFIEE => 'Une commande a été modifiée.',
            self::BON_DE_COMMANDE_PUBLIE => 'Un bon de commande a été publié et nécessite votre attention.',
            self::BON_DE_COMMANDE_SIGNE => 'Un bon de commande a été signé.',
            self::COMMANDE_PAYEE => 'Une commande a été marquée comme payée.',
            self::DEVIS_REFUSE => 'Un devis a été refusé.',
            self::SIGNATURE_REFUSEE => 'La signature d\'un bon de commande a été refusée.',
            self::ENVOYEE_FOURNISSEUR => 'Une commande a été envoyée au fournisseur.',
            self::COLIS_MIS_A_JOUR => 'Les informations des colis ont été mises à jour.',
        };
    }
}
