#!/bin/sh
# Arrête le script immédiatement si une commande échoue
set -e

# Crée le lien symbolique public/storage -> storage/app/public pour accéder aux fichiers uploadés via URL
php artisan storage:link --force

# Met en cache la configuration Laravel (plus rapide qu'de lire les fichiers config/ à chaque requête)
php artisan config:cache

# Lance les migrations manquantes au démarrage du conteneur (--force car APP_ENV=production)
php artisan migrate --force || true

# Démarre Apache en avant-plan (obligatoire en Docker, sinon le conteneur s'arrête immédiatement)
exec apache2-foreground
