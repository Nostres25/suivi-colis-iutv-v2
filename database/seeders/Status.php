<?php

namespace Database\Seeders;

// /---------------------------------------------------------------------------------------------------------------\
// | ATTENTION ! si vous souhaitez ajouter ou modifiez des status :                                                 |
// | Dû à la colonne "status" de type ENUM de la table "orders", ajouter un nouveau status nécessite de changer le  |
// | type de la colonne "status" par les migrations.                                                                |
// | Pour cela il vous faudra créer une nouvelle migration de la table orders visant à renommer la colonne "status" |
// | de la table "orders" SANS PERTE DE DONNÉES ! (SE RENSEIGNER sur les migrations laravel)                        |
// | Ensuite vous pourrez exécuter "php artisan migrate --seed"                                                     |
// | OU si vous êtes en local et que les pertes de données engendrées par la recréation totale de la base de données|
// | ne sont d'aucun souci, alors vous pouvez totalement la base de données à partir des migrations et exécuter à   |
// | avec la commande "php artisan migrate:fresh --seed"                                                            |
// \---------------------------------------------------------------------------------------------------------------/
enum Status: string
{ // états possibles de la commande (triés dans l'ordre) :

    case BROUILLON = 'BROUILLON';
    case EN_ATTENTE_VALIDATION_FOURNISSEUR = 'EN_ATTENTE_VALIDATION_FOURNISSEUR';
    case DEVIS = 'DEVIS'; // (première étape)
    case DEVIS_REFUSE = 'DEVIS_REFUSE';
    case BON_DE_COMMANDE_NON_SIGNE = 'BON_DE_COMMANDE_NON_SIGNE';
    case BON_DE_COMMANDE_REFUSE = 'BON_DE_COMMANDE_REFUSE';
    case BON_DE_COMMANDE_SIGNE = 'BON_DE_COMMANDE_SIGNE';
    case COMMANDE = 'COMMANDE';
    case COMMANDE_REFUSEE = 'COMMANDE_REFUSEE';
    case COMMANDE_AVEC_REPONSE = 'COMMANDE_AVEC_REPONSE';
    case PARTIELLEMENT_LIVRE = 'PARTIELLEMENT_LIVRE';
    case SERVICE_FAIT = 'SERVICE_FAIT';
    case LIVRE_ET_PAYE = 'LIVRE_ET_PAYE'; // (dernière étape)
    case ANNULE = 'ANNULE';

    public static function getDefault(): Status
    {
        return Status::DEVIS;
    }

    public static function getDescriptions(): string
    {
        return "
        - EN_ATTENTE_VALIDATION_FOURNISSEUR : Commande en attente de validation de son fournisseur par le service financier. Si le fournisseur est validé, la commande passe à l'état de devis. Sinon si le fournisseur est refusé, la commande passe à l'état de devis refusé.
        - BROUILLON : Commande enregistrée à l'état de brouillon. Mis en avant seulement pour l'auteur de la commande.
        - DEVIS : Commande à l'état de devis. En attente d'un bon de commande (première étape).
        - DEVIS_REFUSE : À l'état de devis. Le service financier a refusé de faire un bon de commande.
        - BON_DE_COMMANDE_NON_SIGNE : À l'état de bon de commande non signé. Le bon de commande réalisé par le service financier doit être signé par le directeur.
        - BON_DE_COMMANDE_REFUSE : À l'état de bon de commande non signé. Le directeur de l'IUT a refusé de signer de le bon de commande.
        - BON_DE_COMMANDE_SIGNE : À l'état de bon de commande signé. Le directeur a signé le bon de commande et la commande peut être passée auprès du fournisseur.
        - COMMANDE : À l'état de bon de commande signé. Le bon de commande a été envoyé au fournisseur. Sans répone pour le moment.
        - COMMANDE_REFUSEE : À l'état de bon de commande signé. Le fournisseur a refusé le bon de commande.
        - COMMANDE_AVEC_REPONSE : À l'état de commande officiellement en cours. Le fournisseur a répondu favorablement à la commande avec ou sans communication d'un délai de livraison. En attente de livraison.
        - PARTIELLEMENT_LIVRE : À l'état de commande officiellement en cours. Seulement certains colis ont été marqués comme livrés par des membre du département de la commande. Les autres colis sont en attente de livraison.
        - SERVICE_FAIT : À l'état de bon de livraison. Commande marquée comme totalement livrée par des membres du département qui ont aussi transmis le bon de livraison au service financier. En attente du paiement par le service financier.
        - LIVRE_ET_PAYE : À l'état de bon de livraison. Commande totalement livrée et payée par le service financier.
        ";
    }

    public static function getDescriptionsDict(): array
    {
        return [
            Status::EN_ATTENTE_VALIDATION_FOURNISSEUR->value => "Commande en attente de validation de son fournisseur par le service financier. Si le fournisseur est validé, la commande passe à l'état de devis. Sinon si le fournisseur est refusé, la commande passe à l'état de devis refusé.",
            Status::BROUILLON->value => "Commande enregistrée à l'état de brouillon. Mis en avant seulement pour l'auteur de la commande.",
            Status::DEVIS->value => "Commande à l'état de devis. En attente d'un bon de commande (première étape).",
            Status::DEVIS_REFUSE->value => "À l'état de devis. Le service financier a refusé de faire un bon de commande.",
            Status::BON_DE_COMMANDE_NON_SIGNE->value => "À l'état de bon de commande non signé. Le bon de commande réalisé par le service financier doit être signé par le directeur.",
            Status::BON_DE_COMMANDE_REFUSE->value => "À l'état de bon de commande non signé. Le directeur de l'IUT a refusé de signer de le bon de commande.",
            Status::BON_DE_COMMANDE_SIGNE->value => "À l'état de bon de commande signé. Le directeur a signé le bon de commande et la commande peut être passée auprès du fournisseur.",
            Status::COMMANDE->value => "À l'état de bon de commande signé. Le bon de commande a été envoyé au fournisseur. Sans répone pour le moment.",
            Status::COMMANDE_REFUSEE->value => "À l'état de bon de commande signé. Le fournisseur a refusé le bon de commande.",
            Status::COMMANDE_AVEC_REPONSE->value => "À l'état de commande officiellement en cours. Le fournisseur a répondu favorablement à la commande avec ou sans communication d'un délai de livraison. En attente de livraison",
            Status::PARTIELLEMENT_LIVRE->value => "À l'état de commande officiellement en cours. Seulement certains colis ont été marqués comme livrés par des membre du département de la commande. Les autres colis sont en attente de livraison",
            Status::SERVICE_FAIT->value => "À l'état de bon de livraison. Commande marquée comme totalement livrée par des membres du département qui ont aussi transmis le bon de livraison au service financier. En attente du paiement par le service financier",
            Status::LIVRE_ET_PAYE->value => "À l'état de bon de livraison. Commande totalement livrée et payée par le service financier",
            Status::ANNULE->value => 'Commande annulée',
        ];
    }

    public function getDescription(): string
    {
        return Status::getDescriptionsDict()[$this->value];
    }

    public static function getDisplayNamesDict(): array
    {
        return [
            Status::EN_ATTENTE_VALIDATION_FOURNISSEUR->value => 'En attente de validation du fournisseur',
            Status::BROUILLON->value => 'Brouillon',
            Status::DEVIS->value => 'Devis',
            Status::DEVIS_REFUSE->value => 'Devis refusé',
            Status::BON_DE_COMMANDE_NON_SIGNE->value => 'Bon de commande non signé',
            Status::BON_DE_COMMANDE_REFUSE->value => 'Bon de commande refusé',
            Status::BON_DE_COMMANDE_SIGNE->value => 'Bon de commande signé',
            Status::COMMANDE->value => 'Commandé',
            Status::COMMANDE_REFUSEE->value => 'Commande refusée',
            Status::COMMANDE_AVEC_REPONSE->value => 'Commande en cours',
            Status::PARTIELLEMENT_LIVRE->value => 'Commande partiellement livrée',
            Status::SERVICE_FAIT->value => 'Service fait (livré)',
            Status::LIVRE_ET_PAYE->value => 'Livré et payé',
            Status::ANNULE->value => 'Commande annulée',
        ];
    }

    public function getDisplayName(): string
    {
        return Status::getDisplayNamesDict()[$this->value];
    }
}
