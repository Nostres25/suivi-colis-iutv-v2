Bienvenue sur le projet de création de logiciel de suivi de colis pour l'IUT de Villetaneuse, Sorbonne Paris-Nord !

Le demandeur du projet est l'enseignant chercheur et responsable du département CRIT à l'IUTV, **Franck Butelle**.
# Équipe A
Il s'agit de l'équipe ayant commencé le projet entre septembre 2025 et janvier 2026 
-   **@weame959** Weame EL MOUTTAQUI
-   **@D4CJ** - Dimitar DIMITROV
-   **@ysmn-a** - Yasmine AIT SALAH
-   **@Myriam** - Myriam ABDELLAOUI
-   **@MeganeMaz** - Mégane Mazekem
-   **@Nostres25** - Soan MOREAU (**auteur de ce document**)

# Équipe B
Il s'agit de l'équipe ayant repris le projet en avril 2026 jusqu'en juin 2026
-   **@SanjaiV2** Sanjai RAMASAMY
-   **@krishna171826** - Gopi SURESH
-   **@sarah-dev05** - Sarah HELLAL
-   **At9ph** - Lissam LOUTFI
-   **@Nostres25** - Soan MOREAU (**auteur de ce document**)

# Développement

## Git

Tout d'abord le repository/dépôt GitHub est là pour héberger le code en ligne afin d'éviter les pertes de progression et de faciliter le travail en équipe.  
GitHub s'utilise avec **le logiciel [Git](https://git-scm.com/) qu'il vous faut installer** pour travailler sur le développement. C'est ce logiciel qui vous permettra de récupérer le code du projet, mais aussi de publier vos modifications.  
La plupart des IDE (ou éditeurs de code) comme Visual Studio Code embarquent des menus pour interagir avec git via l'interface, afin d'appuyer sur des boutons plutôt que de rédiger des commandes git. Mais ce document s'appuie sur les commandes git.

###### [GitHub Desktop](https://desktop.github.com/download/) existe pour interagir avec git avec une interface, mais c'est aussi plutôt limité et inutile si l'IDE comprend des menus git.

## Environnement

-   Langage: **PHP** (Actuellement 8.2),
-   Framework: [**Laravel 12**](https://laravel.com) [(documentation)](https://laravel.com/docs/12.x)
-   Gestionnaire de paquets PHP: [**Composer**](https://getcomposer.org/)
-   Base de données (SGBDR) : **MariaDB**
-   Serveur de développement (Intégré à Laravel) : `php artisan serv`
-   Serveur de production (probable): **Apache2**
-   Système de production : **Ubuntu 24.04** (linux)
-   IDE: **Visual Studio Code** (avec des extensions)

### Autres outils :

Blade

(NodeJs ne pourra pas être utilisé pour ne pas faire tourner de JavaScript sur serveur. Ce qui signifie que les outils comme "Vite", "tailwind" et d'autres outils css ne sont pas disponibles)

Un site web PHP implique un serveur web supportant le PHP. Car le PHP n'est pas exécuté par le navigateur comme le HTML/CSS/Javascript, il s'exécute sur le serveur.

## Mise en place de l'environnement

Cette partie concerne la mise en place de l'environnement **pour le développement** du projet, mais sert aussi pour connaître les paquets à installer pour la production.  
Autrement dit, si vous commencez le développement sur ce projet, suivez les instructions suivantes. Si vous souhaitez installer l'application en production, vous pouvez suivre les étapes en adaptant certaines parties.

### I. Installation des paquets (sur Ubuntu)

> [!NOTE]
> Les commandes sont à entrer dans un terminal linux.

#### 1. Mise à jour du système :

###### à faire avant toute installation de paquets

```bash
sudo apt update && sudo apt upgrade
```

#### 2. Installer l'IDE :

Vous pouvez installer l'IDE de votre choix sur votre système. Il est tout de même fortement PHPStorm pour ce projet et pour tous les projets PHP. Pour avoir testé Visual Studio Code, le support du PHP n'est que très partiel et insuffisant pour un développement confortable même avec des extensions.
> [!NOTE]
> Si vous utilisez WSL, vous pourrez vous contenter d'installer votre IDE sur Windows et non sur le WSL. Une fois cela fait, vous pourrez sauter cette étape.

#### 3. Installer git, curl, mariadb, php et ses extensions

curl sert à l'installation de certains paquets, mariadb c'est la base de données et les extensions php servent au bon fonctionnement de notre application, notamment avec la base de données.

```bash
sudo apt-get install git && sudo apt-get install curl && sudo apt-get install mariadb-server && sudo apt-get install php && sudo apt-get install php-curl && sudo apt-get install php-mbstring && sudo apt-get install php-xml && sudo apt-get install php-mysql && sudo apt-get install php-zip
```

#### 4. Installer Composer :

Composer est le gestionnaire de modules qui nous permet d'installer et d'utiliser des modules tiers.
Vous pouvez le faire en exécutant ceci :

```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === 'c8b085408188070d5f52bcfe4ecfbee5f727afa458b2573b8eaaf77b3419b0bf2768dc67c86944da1544f06fa544fd47') { echo 'Installer verified'.PHP_EOL; } else { echo 'Installer corrupt'.PHP_EOL; unlink('composer-setup.php'); exit(1); }"
php composer-setup.php
php -r "unlink('composer-setup.php');"
```

puis ceci:

```bash
sudo mv composer.phar /usr/local/bin/composer
```

> ✅ Vous disposez désormais de tous les paquets système nécéssaires pour le développement du projet. Dorénavant, lisez la suite du document pour les configurations.

### II. Éditeur de code / IDE:

Si vous choisissez Visual Studio Code, sachez qu'il n'est pas directement adapté à php et au développement avec Laravel. C'est pour cela que nous installons des extensions pour bénéficier de certaines fonctionnalités pratiques qui nous feront gagner du temps dans le développement.

#### **Visual Studio Code**

Avec les extensions suivantes :

-   **Laravel** (Autocomplétion, couleurs et autre)
-   **Laravel Pint** (Autoformatting Laravel/PHP pour que les indentations soient rectifiées automatiquement afin d'avoir un code bien lisible)
-   **Live Share** (pour fluidifier le travail en collaboration)
-   **Laravel Extra Intellisense** (Pour une meilleure autocomplétion comprenant les routes, les vues, les valeurs de config etc...)
-   **Laravel Goto View** (Pour aller rapidement sur des vues ou des controllers grâce à un CTRL + Clique gauche sur le nom de la vue ou du controller dans le code)
-   **PHP Intelephense** (Autocomplétion pour du pure PHP)
-   **PHP Debug** (Outil permettant de debug le code php ligne par ligne)
-   **Prettier** (Autoformatting pour du Javascript, JSON et CSS)
-   **GitLens** (Outil Git très complet permettant notamment de voir rapidement dans quel commit a été modifié une ligne de code)
-   **Conventional commits** (pour des commits à la norme)

> [!NOTE]
> Certaines extensions vscode affichent une erreur en bas à droite de l'écran. Ne faites pas attention, cliquez sur "Ne plus afficher", l'extension fonctionne quand même pour certaines fonctionnalités.  
> Toutefois, à cause de l'efficacité minime des extensions vscode pour du développement laravel. Je vous invite à utiliser PHPStorm qui est certes payant, mais **[gratuit](https://www.jetbrains.com/fr-fr/phpstorm/buy/?section=commercial&billing=yearly&special-offers=students) pour les étudiants, les enseignants** et les projets opens-source 

> [!WARNING]
> Concernant les extensions Vscode surtout, il se peut que **si votre ordinateur est lent**, ce soit encore pire avec les extensions. Je vous conseille donc de tester les extensions, mais si ça ralentit votre ordinateur, vous pouvez désinstaller les extensions suivantes progressivement, dans l'ordre ci-dessous :
>
> 1.  **Conventional commits** : Vous pouvez écrire des commits conventionnels sans cette extension, c'est juste pour vous guider
> 2.  **GitLens** : C'est très pratique sur certains points, mais pas nécéssaire pour le code. Surtout que vu à quel point c'est complet, ça doit plus ralentir que les autres
> 3.  **PHP Debug** : On peut se débrouiller à debug sans les outils que ça propose
> 4.  **Prettier** : Ce n'est pas obligatoire pour un code fonctionnel, c'est juste pour la lisibilité, surtout que c'est seulement pour du Javascript et CSS, ce qu'on ne va sans doute pas beaucoup utiliser. De plus, il suffit de, soit bien formatter à la main, soit que quelqu'un passe derrière vous avec l'extension pour appliquer le formatage automatique.
> 5.  **Laravel Pint** : La même chose que Prettier même si là c'est plus utile car ça concerne le PHP
> 6.  **Live Share** : ça risque d'être très utile, notamment pour demander de l'aide à quelqu'un mais au pire, vous l'installez uniquement lorsque vous en avez besoin et lorsque vous ne l'utilisez plus, vous le désinstallez.
>
> Et pour le reste, elles sont toutes très pratique pour coder dans de bonnes conditions, sans ralentissement, sans être perdu, etc. Si votre pc est encore lent, vous pouvez en désinstaller encore quelques-unes, du moins utile au plus utile (Laravel Goto View en premier et Laravel ainsi que PHP Intelephense en dernier). Mais sans ces autres extensions le développement risque d'être compliqué.

#### **PHPStorm**
Cet IDE de JetBrains IntelliJ est beaucoup plus adapté que VSCode pour du développement PHP. Aucune extension supplémentaire n'est requise pour un fonctionnement. Toutefois, le plugin "Laravel" peut être utile pour quelques fonctionnalités comme une autocomplétion un peu plus poussée 
> [!NOTE]
> Il s'agit d'un logiciel payant, mais il est possible de se procurer facilement une [license gratuite](https://www.jetbrains.com/fr-fr/phpstorm/buy/?section=commercial&billing=yearly&special-offers=students) :
> - Pour les étudiants ;
> - Pour les enseignants ;
> - Pour les projets opensource.

> ✅ Maintenant que l'IDE est configuré, lisez la prochaine partie pour importer le code du projet depuis github.

### III Importation du projet

Avant tout, le dossier du projet n'a pas été crée. Il vous faut d'abord importer le projet, ce qui créera le dossier.

#### Rappel: Utilisation de Git

Tout d'abord, assurez-vous de bien avoir le logiciel Git d'installé [(lien d'installation)](https://git-scm.com/downloads).
Pour commencer, on parlera de **git** quand on veut parler de l'outil qui permet de gérer un **dépot local**[^1]. Alors que **github** est l'outil en ligne qui nous permet d'héberger le code en ligne, c'est-à-dire sur un **dépôt distant**[^2].
Pour en savoir plus sur le fonctionnement de git et de github, et notamment comprendre la notion de dépôts, [cliquez ici](https://comprendre-git.com/fr/glossaire/git-depot-distant-et-local/).

##### Utiliser git sur Windows:

> [!NOTE]
> Si vous utilisez WSL, vous pouvez utiliser cette méthode ou utiliser git depuis le WSL, selon une utilisation standard sur linux donc.
> Cette méthode est peut-être meilleure pour ne pas avoir de problème avec la création d'un token d'accès personnel pour se connecter avec git.

Je vous conseille d'utiliser l'invite de commandes git, disponible avec un clique droit sur un dossier, en appuyant sur "Plus d'options" si vous êtes sur windows 11 et en cliquant sur "**Git Bash Here**". Cet invite de commandes permet d'utiliser [la commande `git`](https://git-scm.com/docs/git) pour interagir avec git et github. Mais il apporte aussi d'autres commandes comme `cd` pour changer de dossier et [`nano`](https://nano-editor.org/dist/v2.2/nano.html) pour modifier un fichier directement dans le terminal et autre (un peu comme sur linux).

Pour exécuter des commandes git, vous devrez tout le temps passer par cet invite de commande git (Git bash) **et dans le bon dossier**.
Mais l'invite de commande windows fonctionne également pour utiliser git en principe.

##### Utiliser git sur Linux

Vous pouvez utiliser la commande `git` dans le terminal classique

##### Utiliser git sur Mac

Aucune idée. Bon courage ! :) _ça doit être proche de linux je suppose ?_

#### Clonage

1. Pour cela, placez-vous dans le dossier dans lequel vous souhaitez placer le projet, et ouvrez l'invite de commandes. (l'invite de commandes ou "Git bash" sur windows)

> [!NOTE]
> Vous pouvez utiliser la commande `cd` dans l'invite de commande pour vous déplacer de dossier

2. Ensuite, clonez le code du projet à l'aide de la commande :

```
git clone https://github.com/Nostres25/suivi-colis-iutv.git
```

> [!NOTE]
> la première fois, il vous sera demandé de vous connecter. Si vous n'êtes pas redirigé vers une interface pour entrer vos identifiants github, vous devrez [créer un token d'accès personnel](https://docs.github.com/fr/authentication/keeping-your-account-and-data-secure/managing-your-personal-access-tokens) pour l'entrer à la place du mot de passe.

> ✅ Maintenant, vous pouvez ouvrir **le dossier du projet**, crée sous le nom de "suivi-colis-iutv", dans votre IDE favori ! Accédez à la suite pour la suite de la mise en place de l'environnement.

> [!IMPORTANT]
> Toutefois, attention à ne rien modifier dans le code à cette étape. Car vous êtes sur la branche `main` du projet et que si vous modifiez quoi que ce soit, vous pouvez créer des conflits sur cette branche. **Prenez connaissance de la suite de cette documentation, dont [les règles d'organisation du développement](#r%C3%A9sum%C3%A9-des-r%C3%A8gles-du-d%C3%A9veloppement-du-projet) avant de faire quoi que ce soit**.
> _Spécifiquement pour la suite de la mise en place de l'environnement, il n'est pas nécéssaire de changer de branche._

### IV. Installation des modules du projet

D'abord, assurez-vous de bien avoir installé php, composer, mariadb, vscode avec ses extensions et autres [comme indiqué plus haut](#i-installation-des-paquets-sur-ubuntu)

Pour installer les modules composer du projet, il vous faut d'abord vous rendre dans le répertoire du projet (le dossier `suivi-colis-iutv` créé suite à `git clone...`)
Ensuite exécutez la commande suivante :

```bash
composer install
```

> [!NOTE]
>
> -   Cette commande est à exécuter de nouveau pour installer les nouveaux modules utilisés et les mises à jour des modules reférencés dans le fichier `composer.json`.
> -   Cela peut prendre du temps. Sachez que pour les prochaines exécutions, cela sera plus rapide, car seuls les paquets avec du changement seront installés

> [!NOTE]
> À ne pas confondre avec `composer update` qui va mettre à jour si possible ou installer les modules si besoin selon le contenu de `composer.json`.
> `composer install` ne met pas à jour les paquets.

> ✅ Si tout s'est bien passé, les modules sont désormais installés. Dès à présent, il reste à la suite la configuration des variables d'environnement, la mise en place de la base de donnée et la création de la clé de chiffrement à faire pour que l'application fonctionne correctement. Et pour accessoirement commencer le développement.

### V. Définition des variables d'environnement

Ouvrez le dossier du projet (suivi-colis-iutv) avec votre IDE (VSCode ou autre) et vous verrez un fichier `.env.example`.

1. **Copiez** le fichier `.env.example` **et appelez sa copie `.env`** (ce fichier est ignoré par git et ne sera jamais commit sur le dépôt distant grâce à sa définition dans le fichier `.gitignore`)
2. Dans `.env`, changez la valeur de `DB_PASSWORD` si vous le souhaitez pour y mettre votre propre mot de passe.
   Il s'agit du mot de passe pour l'utilisateur de base de données présente sur votre pc.
   (Si vous avez déjà une base de données et un utilisateur MariaDB, vous pouvez modifier les valeurs du `.env` selon votre système).

> ✅ Les valeurs dans les variables d'environnement du`.env` seront utilisées par l'application. Notamment pour la connexion à la base de données dont le guide de mise en place est à la suite.

### VI. Mise en place de la base de données

1. Accédez à MariaDB:

```bash
sudo mariadb -u root -p
```

> [!NOTE]
> Si vous n'avez jamais configuré de mot de passe pour root, il n'y a pas de mot de passe par défaut donc vous pouvez ne rien écrire et appuyer sur "Entrer".

2. Une fois connecté en tant que root, créez la base de donnée `suivi_colis_iutv`, créez l'utilisateur `app_colis` et donnez les permissions nécéssaires en entrant les instructions suivantes :

```sql
CREATE DATABASE IF NOT EXISTS suivi_colis_iutv;
CREATE USER 'app_colis'@'localhost' IDENTIFIED BY "password"; -- REMPLACEZ password PAR LE MOT DE PASSE QUE VOUS AVEZ MIS DANS LE .env
GRANT ALL PRIVILEGES ON suivi_colis_iutv.* TO 'app_colis'@'localhost';
FLUSH PRIVILEGES;
```

Si tout a fonctionné, vous pouvez sortir de la base de données en entrant `EXIT` ou `\q`

3. Pour tester si votre base de données fonctionne correctement, vous pouvez exécuter `php artisan db` dans le dossier du projet, ce qui devrait vous connecter à la base de données selon les informations présentes dans le `.env`
4. Si vous réussissez à créer une table (ex: `CREATE TABLE TABLE_NAME (a INT);`), c'est que les permissions ont bien été configurées
   (pensez à sortir avec `EXIT` ou `\q` pour continuer)
5. Enfin, il ne reste plus qu'à préparer la base de données `suivi_colis_iutv` grâce aux migrations et aux seeders, de sorte que l'application puisse se lancer. Pour cela, rendez-vous dans le dossier du projet et exécutez :

```bash
php artisan migrate --seed
```

> [!NOTE]
> Les migrations dans Laravel sont des fichiers de classe qui contiennent des instructions pour créer, modifier ou supprimer des tables, des colonnes, des index et d'autres éléments de la base de données. Chaque migration correspond à une étape spécifique dans l'évolution de la structure de la base de données. La commande `php artisan migrate` va créer, modifier ou supprimer les tables en fonctions des migrations présentes.
> Les seeders permettent le remplissage automatique de données par défaut comme des données de test, mais pas seulement (ce projet a des données par défaut également en production pour les rôles et les permissions)
> 
> -   À chaque changement au niveau des migrations ou des seeder, il faut exécuter la commande suivante :
>
> ```bash
> php artisan migrate:fresh --seed
> ```
>
> -   Si vous supprimez une migration ou que vous modifiez une migration existante, veuillez exécuter :
>
> ```bash
> composer dump-autoload
> ```
>
> -   Une fois l'application en production, ne modifiez jamais une migration ! Préférez en créer une nouvelle migration avec la nouvelle structure des tables concernées. Ceci permet de garder une rétrocompatibilité des données suite aux changements structuraux.

> ✅ La base de données est maintenant prête ! Seulement, Laravel requiert encore une dernière chose pour faire fonctionner l'application. Vous trouverez la dernière étape à la suite.

### VII Création de la clé de chiffrement

Laravel impose la création d'une clé de chiffrement. Sûrement pour des raisons de sécurité avec certaines fonctionnalités. Je ne sais pas si c'est utile pour nous, mais c'est obligatoire pour faire fonctionner l'application :

```bash
php artisan key:generate
```

> ✅ Dorénavant, l'application est prête ! La suite concerne le lancement du serveur de développement pour pouvoir la faire fonctionner en local et la tester.

### VIII Lancement du serveur local de développement

Tout est en principe, correctement configuré. Utilisez la commande suivante pour lancer l'application sur votre machine :

```bash
php artisan serv
```

Pour arrêter le serveur local de développement, appuyez sur CTRL + C

> ✅ Vous pouvez maintenant commencer le développement. Attention toutefois, travailler à plusieurs sur un même problème amène des problématiques qui peuvent faire perdre du temps de travail. Pour éviter tout problème, nous utilisons git avec github mais il faut également respecter une certaine organisation afin de garantir un développement fluide. Les détails de cette organisation sont ci-dessous.

## Page administrateur

### Filament

Le panel d'administration utilise [Filament](https://filamentphp.com/), accessible sur `/admin`.

Il a été mis en place avec les commandes suivantes :

```bash
# Installation du panel
composer require filament/filament
php artisan filament:install --panels

# Génération d'une resource pour une table (à faire pour chaque modèle)
php artisan make:filament-resource NomDuModele --generate
```

Le modèle `User` (`app/Models/User.php`) implémente `FilamentUser` et `HasName` pour que Filament fonctionne.

> [!WARNING]
> Dans `app/Models/User.php` ligne 26, `canAccessPanel()` retourne `true` (tout le monde peut accéder au panel).
> **En production**, remplacer `return true;` par la ligne commentée en dessous qui vérifie la permission administrateur.

### Adminer

[Adminer](https://www.adminer.org/) est une console SQL accessible sur `/adminer`, installée via :

```bash
composer require vrana/adminer
```

C'est un équivalent de phpMyAdmin en un seul fichier. Il demande les identifiants de la base de données à chaque connexion.

## Déploiement de l'application

### Environnement de tests

Les tests utilisent SQLite en mémoire, ce qui veut dire qu'il n'y a pas besoin d'une vraie base de données pour les faire tourner. Tout est configuré dans `phpunit.xml`.

Pour lancer les tests en local :

```bash
cp .env.example .env
php artisan key:generate
php artisan test
```

C'est tout. Pas besoin de configurer MariaDB ou quoi que ce soit d'autre.

### CI/CD - Intégration et déploiement continus

Le projet utilise GitHub Actions pour automatiser les tests et le déploiement. Il y a deux fichiers dans `.github/workflows/` :

- `ci.yml` : s'occupe des tests et de la construction de l'image Docker
- `cd.yml` : s'occupe du déploiement sur le VPS (appelé par `ci.yml`)

#### Ce qui se passe à chaque push ou pull request sur `main`

La CI lance trois choses en parallèle :

- les tests Pest (avec SQLite en mémoire)
- les migrations sur une vraie base MySQL (pour s'assurer qu'elles ne cassent rien)
- la construction de l'image Docker suivie d'un scan de sécurité avec Trivy (qui fait échouer la CI si une faille CVE critique ou haute est trouvée)

#### Ce qui se passe uniquement lors d'un merge sur `main`

Si les trois étapes ci-dessus passent et que c'est bien un merge sur `main` (pas juste une PR), alors le déploiement se déclenche automatiquement :

1. L'image Docker est construite et poussée sur le registre GitHub (`ghcr.io/at9ph/suivi-colis-iutv:latest`)
2. GitHub Actions se connecte au VPS en SSH
3. Il exécute `make prod-suivi-colis-iutv` depuis le dossier `DeployConfig/` sur le VPS

En résumé : **dès qu'une PR est mergée sur `main`, l'application se met à jour en production automatiquement**, à condition que tous les tests passent.

> [!IMPORTANT]
> Le nom d'utilisateur `at9ph` est celui du compte GitHub qui héberge l'image Docker sur le registre (`ghcr.io`). Si le projet change de mains, il faut remplacer `at9ph` par le pseudo GitHub du nouveau responsable dans les fichiers suivants :
> - `.github/workflows/cd.yml` (les lignes `docker tag` et `docker push`)
> - `.github/workflows/ci.yml` (la ligne `docker login-action` et le `username`)
> - `Makefile` (la ligne `docker pull ghcr.io/at9ph/...`)
> - `colis.yaml` (la ligne `image: ghcr.io/at9ph/...`)
>
> Le nouveau responsable devra aussi s'assurer que son compte GitHub a les droits pour publier des packages sur le dépôt (Settings > Collaborators and teams).

Pour que ça fonctionne, les secrets suivants doivent être configurés dans les paramètres GitHub du dépôt (Settings > Secrets and variables > Actions) :

- `GITHUBTOKEN` : token d'accès GitHub avec les droits `packages:write`
- `VPS_HOST` : adresse IP ou domaine du VPS
- `VPS_PORT` : port SSH du VPS
- `VPS_USER` : utilisateur SSH
- `VPS_SSH_KEY` : clé privée SSH
- `VPS_SSH_PASSPHRASE` : passphrase de la clé SSH si elle en a une
- `VPS_SUDO_PASSWORD` : mot de passe sudo de l'utilisateur sur le VPS

### Mise en production sur le VPS

#### Prérequis

Le VPS doit avoir :

- Docker avec le mode Swarm activé (`docker swarm init`)
- Un reverse proxy Traefik déjà en place, connecté au réseau externe `reverse_proxy2`
- Accès au registre GitHub Container Registry (se connecter avec `docker login ghcr.io`)

#### Structure des fichiers sur le VPS

Le déploiement s'appuie sur un dossier `DeployConfig/` à placer sur le VPS. Les fichiers `Makefile` et `colis.yaml` sont disponibles à la racine du dépôt.

```
DeployConfig/
├── Makefile
└── sae/
    ├── colis.yaml
    ├── .env
    └── cacert.pem
```

- `colis.yaml` : fichier Docker Swarm qui décrit le service (image, ressources, réseau, secrets, volumes)
- `.env` : variables d'environnement de l'application Laravel (APP_KEY, DB_*, etc.)
- `cacert.pem` : certificat CA nécessaire pour les connexions HTTPS sortantes (CAS de l'université par exemple)
- `Makefile` : contient la commande `prod-suivi-colis-iutv` qui pull l'image et redéploie le stack

Pour déployer manuellement (sans passer par la CI) :

```bash
cd DeployConfig/
sudo make prod-suivi-colis-iutv
```

#### Reverse proxy et Traefik

Le fichier `colis.yaml` contient des labels Traefik qui indiquent au reverse proxy comment router le trafic vers le conteneur :

```yaml
labels:
  - "traefik.enable=true"
  - "traefik.http.routers.sae-jupiter.rule=Host(`sae.nom.domaine`)"
  - "traefik.http.routers.sae-jupiter.tls=true"
  - "traefik.http.routers.sae-jupiter.tls.certresolver=prodresolver"
  - "traefik.http.routers.sae-jupiter.entrypoints=websecure"
  - "traefik.http.services.sae-jupiter.loadbalancer.server.port=80"
```

Il faut changer `sae.nom.domaine` par le vrai nom de domaine utilisé.

Le `certresolver=prodresolver` correspond au nom du résolveur Let's Encrypt configuré dans Traefik. Si le nom est différent sur votre installation, modifiez-le.

Si vous utilisez un autre reverse proxy que Traefik (nginx, Caddy, etc.), supprimez ces labels et configurez votre reverse proxy pour qu'il pointe vers le conteneur sur le port 80. Dans ce cas, pensez aussi à retirer le réseau `reverse_proxy2` dans `colis.yaml` et à le remplacer par celui de votre propre configuration.

> [!IMPORTANT]
> Comme le reverse proxy termine le SSL et forward les requêtes en HTTP interne, Laravel doit être configuré pour faire confiance au proxy. C'est déjà fait dans `bootstrap/app.php` avec `$middleware->trustProxies(at: '*')`. Si vous changez de reverse proxy, gardez cette ligne telle quelle, elle fonctionne avec n'importe quel proxy.

### Page de connexion - Fonctionnement du CAS
Cette application utilise la page de connexion de l'université Sorbonne Paris Nord utilisée par plusieurs services au sein de l'organisation.  
Pour la manière dont l'application utilise cette page de connexion, aussi appellée CAS pour **C**entral **A**uthentication **S**ervice :
- La redirection vers le CAS est à la charge du serveur web Apache2 de l'application grâce au module Apache `mod_auth_cas`
- Les utilisateurs redirigés sur le site sont identifiés par la valeur du login à la clé `REMOTE_USER` des informations retournées par le CAS 
- L'authentification par le CAS de l'université Sorbonne Paris Nord ne fonctionnera que si l'application est hébergé sur un serveur de l'université.
- Le CAS doit retourner vers la racine du site ("/")

## Travailler avec git

Étant donné que nous sommes plusieurs à travailler sur ce projet et qu'il n'y a pas de synchronisation automatique entre le dépôt local[^1] et le dépôt distant[^2], l'un d'entre nous pourrait avoir des modifications en cours pendant que vous travaillez sur le projet. Et ces modifications peuvent porter sur le même fichier voir le même bout de code. Ce qui peut causer des conflits, car vous avanceriez sur un projet non à jour et lorsque vous souhaiterez publier vos modifications, git ne saura pas choisir quelle modification est bonne à garder car les deux modifications sont incompatibles.
Pour éviter ce genre de complications, nous devons respecter une organisation stricte. Voici un résumé des règles ci-dessous.

### Résumé des règles du développement du projet

Si vous n'êtes pas familier avec les termes employés dans les consignes ci-dessous, [Accédez à la suite](#etat-du-d%C3%A9p%C3%B4t-local) et lisez le règlement une fois que vous aurez compris comment git fonctionne. Le non-respect de ces règles nous risque à de la perte de travail et de la perte de temps.

#### 1. Ne jamais modifier la branche[^4] `main`

> Pour développer une nouvelle fonctionnalité, corriger un bug ou autre, vous devez [créer une nouvelle branche](#cr%C3%A9ation-dune-branche) au nom de la fonctionnalité, du bug ou autre. (exemple: "creation_dao" ou "fix_echappement"). La branche main doit être composée uniquement de commits/modifications vérifiées (via pull requests).

#### 2. Ne jamais avancer sur une branche non à jour (dont on n'a pas les dernières modifications, publiées ou non)

> Avant de commencer à travailler sur une branche, assurez-vous que toute personne travaillant sur cette branche ait publié toutes ses modifications pour ensuite exécuter [`git pull`](#pull) **depuis la branche en question** afin de mettre à jour votre dépôt local et travailler dessus.

#### 3. Ne jamais modifier la branche de quelqu'un d'autre sans prévenir

> Cette règle rejoint la règle du dessus. Utilisez le serveur Discord du projet pour prévenir publiquement, dans le salon relatif au code que vous comptez apporter une modification sur une branche déjà prise en charge. Il vous faut d'abord vous assurer que toutes les modifications de cette branche aient été publiées, et exécuter un [`git pull`](#pull) depuis la branche pour importer tous les commits. La personne qui prenait en charge la branche auparavant ne doit par conséquent plus apporter de modification sur cette branche avant que vous ayez fini.

#### 4. Ne jamais apporter de modification sur une fonctionnalité ou sur un fichier pour lequel des modifications sont en cours par quelqu'un d'autre

> Même si ce fichier ou cette fonctionnalité est modifiée depuis une autre branche, cela n'a pas de sens de la modifier ailleurs. C'est donc à la personne qui a pris en charge la modification de cette fonctionnalité qui doit apporter les modifications que vous vous apprêtiez à faire. Sauf si vous prenez en charge la branche en question, dans ce cas, c'est la règle ci-dessus qui s'applique. \
> Dans de rares cas, il peut être nécéssaire de modifier un fichier ou une fonctionnalité dont une modification est en cours dans une autre branche. Mais dans tous les cas, il faudra en discuter avec la personne qui travaille sur le fichier ou la fonctionnalité. Et le conflit potentiel devra être géré lors du merge.

#### 5. Toujours pull[^5] avant de commencer une modification sur une branche

> Pour éviter tout problème (conflits), assurez-vous de faire un [`git pull`](#pull) avant d'entamer la moindre modification sur une branche. `git status` vous permet de voir si votre branche est à jour de manière fiable uniquement si un `git fetch` à été effectué au préalable.

#### 6. Commit[^3] à chaque modification. C'est-à-dire à chaque version stable du code

> Évitez de commit des modifications avec lesquelles il y a des erreurs. Le code doit fonctionner parfaitement à chaque commit. En général : 1 correction/1 ajout/1 modification = 1 commit

#### 7. À la fin de votre session de travail, vous devez push[^6] tous vos commits

> Ne gardez pas des commits non publiés sur votre ordinateur. Sinon, les modifications apportées peuvent être perdues si vous avez un problème avec votre ordinateur (corruption ou autre incident), mais aussi nous ne pourrons pas suivre votre avancement pour savoir quelles modifications vous avez déjà apportées. Ce qui ralentirait le développement. C'est pour cela que vous devez exécuter [`git push`](#push) pour publier tous vos commits à chaque fin de session de travail ou à chaque fin de journée.

#### 8. Ne jamais merge[^7] directement, mais créer un pull request[^8] à la place

> Pour s'assurer que les merges sont corrects et donc éviter des pertes de travail ou l'introduction de bugs, ne faites pas de merge directement si vous n'êtes pas sûr de ce que vous faites. Il faudra [créer un pull request](#pull-requests-et-merges) à la place.

#### 9. Vous devez créer un pull request une fois que vous avez terminé votre travail sur une branche.

> Vous n'avez pas la permission de merge pour éviter tout problème. À la place, vous devez [créer un pull request](#pull-requests-et-merges) via l'interface GitHub (onglet Pull Request). Ce qui est une "demande de pull" ou plutôt une "demande de merge", et @Nostres25 s'occupera de vérifier les modifications et confirmer le merge si tout est correct. Sans cela, votre travail ne sera jamais intégré au reste du projet.

#### 10. Toujours créer un pull request pour merge la branche à partir de laquelle on a créé notre branche

> Il est possible de créer une branche à partir de n'importe quelle branche. Mais si vous créez une branche "fix_actions_rapides" à partir de "actions_rapides" mais que vous souhaitez merge "fix_actions_rapides" sur "main", votre modification et les anciennes modifications de "actions_rapides" seront publiées sur la branche principale alors que les modifications dans "actions_rapides" ne sont probablement pas terminées. Il faut donc procéder étape par étape et créer un pull request pour merge "fix_actions_rapides" sur "actions_rapides" puis plus tard "actions_rapides" sur "main". Évidemment les pulls requests sont à créer une fois les modifications sur la branche, terminées.

### Etat du dépôt local

Pour éviter de faire des erreurs, il faut déjà savoir où nous sommes, et qu'avons-nous fait à présent ? Si nous ne pouvons pas répondre à ces interrogations, alors il est risqué d'aller plus loin.
Pour remédier à cela, il existe la commande :

```
git status
```

Cette commande affiche l'état du dépôt local, c'est-à-dire :

-   la branche actuelle
-   les fichiers modifiés, non ajoutés pour le prochain commit
-   les fichiers modifiés, ajoutés au prochain commit
-   s'il y a des commits non publiés
-   si la branche actuelle est à jour (il vaut mieux faire `git fetch` avant pour s'assurer que l'information est bien actualisée)
-   les fichiers avec des conflits s'il y en a

> [!TIP]
> je vous invite à souvent utiliser `git status` pour vous repérer. C'est-à-dire pour savoir dans quelle branche vous êtes, si votre branche est à jour, si vous avez des modifications non commit et si vous avez des commits non publiés. Il est très important de savoir tout cela pour ne pas modifier la mauvaise branche, ou créer des conflits.

### Au sujet des branches[^4]

Si vous avez suivi [le tuto pour importer le projet](#importation-du-projet) sur votre ordinateur, vous avez cloné la branche principale (main) du repository.

> [!CAUTION]
>
> -   **Evitez un maximum de travailler sur la branche principale ! Et si vous avez des difficultés ou que vous pensez avoir fait une erreur, ne faites rien sans certitude de ce que vous faites. Appelez @Nostres25 avant de continuer**.
> -   Les fichiers actuellement dans le dossier du projet sur votre ordinateur correspondent aux fichiers du dépôt local **de la branche active**. Cela signifie que ces fichiers sont potentiellement pas à jour (d'où les précautions données précédemment). Ainsi que changer de branche correspond en réalité à tout supprimer du dossier du projet (sauf .git/) pour remettre tous les fichiers conformément à la nouvelle branche sélectionnée. Alors, les modifications non commit seront perdues au changement de branche, mais les commits non publiés, comme publiés, de l'ancienne branche seront bien conservés grâce au répertoire `.git`.

#### Création d'une branche

Si vous voulez travailler sur une fonctionnalité, un ajout ou une correction en particulier (base de données, dao, historique des commandes...), vous devez créer une nouvelle branche.
Pour ce faire, rendez-vous dans le dossier du projet depuis l'invite de commandes git.
Puis, créez une nouvelle branche avec la commande :

```
git branch <nom_de_la_branche> <branche_de_départ>
```

> [!NOTE]
>
> -   La nouvelle branche créée reprendra le code de la "branche_de_départ". **Donc si vous n'avez encore rien commencé et que vous allez commencer une nouvelle fonctionnalité, mettez le nom de la foncitonnalité en nom de branche (le plus concis possible) et "main" en branche de départ (la plus part du temps)**
> -   Si vous vérifiez avec `git status`, vous verrez que vous n'avez pas automatiquement basculé sur la nouvelle branche. Pour cela il existe [une autre commande](#changer-de-branche).

> [!TIP]
> Sinon, vous pouvez utiliser la commande suivante pour créer une nouvelle branche **et basculer dessus automatiquement** :
>
> ```
> git checkout -b <nouvelle_branche> <branche_de_depart>
> ```

#### Changer de branche

Pour afficher et/ou modifier le contenu d'une branche, vous devez accéder/charger le contenu de cette branche. Car le contenu du dossier du projet correspond uniquement à l'état du projet tel qu'il l'est **dans une seule branche**.

Alors pour changer de branche, exécutez :

```
git checkout <nom_de_la_branche>
```

### Pull

> [!NOTE]
> Avant d'avancer sur une branche déjà existante, il se peut que des modifications aient été faites sur cette branche et que vous n'avez pas la dernière version du code.
> Alors, avant de commencer à travailler sur une branche, pensez à pull[^5] avec :

```
git pull
```

Pour mettre à jour le code de la branche actuelle sur votre pc.

### Ajouter des fichiers pour le prochain commit

Une fois avoir fait une modification précise (correction d'un certain bug, ajout ou amélioration d'une certaine fonctionnalité), pensez à exécuter :

```
git add <fichier/dossier>
```

Pour ajouter les fichiers modifiés au commit.

> [!NOTE]
>
> -   Vous pouvez faire `git add *` pour ajouter tous les fichiers modifiés au commit d'un coup
> -   Vous devrez refaire `git add` si vous re-modifiez un fichier, même avant de l'avoir commit.

### Commit

Pour commit[^3] tous les fichiers "ajoutés" avec `git add`, exécutez :

```
git commit -m "<message>"
```

> [!NOTE]
>
> -   Un commit correspond à une modification dans le code. Vous devez vous assurer de commit à chaque version stable de votre code, c'est-à-dire sans erreurs.
> -   Vous pouvez créer plusieurs commits que vous pourrez [push](#push) en même temps
> -   Et à la place de `<message>`, vous devez décrire le commit (le changement) de la manière la plus conçise possible tout en restant précis

### Push

Enfin, quand vous voulez publier/push[^6] vos commits (c'est-à-dire vos modifications) effectuées sur la branche actuelle, sur github, faites :

```
git push
```

> [!WARNING]
> Si vous ne pouvez pas [push](#push) vos commits, car votre branche n’est pas à jour, vous devez [pull](#pull) d’abord. Ensuite, il est possible que cela engendre des conflits. Vous devrez les régler vous même ou faire appel à @Nostres25 si vous ne savez pas comment faire.

### Pull Requests et merges

> [!NOTE]
>
> -   Un merge[^7] est une fusion du code de deux branches. Par exemple: Soan a terminé le système d'actions rapides, il veut le fusionner à la branche principale (main). Il va falloir merge le code de la branche "actions_rapides" au code de la branche "main".
> -   Alors qu'un pull request[^8], c'est une demande de merge par github. Qui devra être vérifiée et validée avant d’effectuer le merge

> [!NOTE]
> Un merge mal fait peut engendrer des pertes de progression et/ou rendre le code non fonctionnel à causes des conflits qui peuvent survenir durant le merge. En effet, la fusion de deux code provoque la suppression, modification ou l'écrasement de lignes de code (ou fichier) en masse sur une branche. Alors, lors d'un merge avec des conflits, il faudra manuellement décider de ce qu'il faut garder, supprimer ou écraser.
>
> La rédaction de cette documentation et l'organisation associée pour le développement du projet permet justement d'éviter un maximum les conflits.

Alors, lorsque vous voulez merge votre branche, c'est-à-dire fusionner le code de votre branche avec sa branche de départ:

1. Rendez-vous sur le [projet GitHub en ligne à la page Pull Request](https://github.com/Nostres25/HeartOfStellars/pulls)
2. Faites "New pull request"
3. Sélectionnez la branche dans laquelle vous voulez fusionner votre code à gauche
4. Puis sélectionnez la branche que vous voulez fusionner à droite
5. Ensuite, vous pourrez appuyer sur "Create Pull Request"
6. Et attendre que @Nostres25 s'occupe du merge.

### Risques de travailler à plusieurs sur une même branche

Il peut être possible de travailler à plusieurs sur une même branche, mais il faut respecter certaines règles pour éviter des conflits :

-   Si vous voulez avancer sur une branche alors que quelqu'un y travaille déjà, et donc a potentiellement du code/des commits non publié, créez une autre branche à partir de celle que vous voulez modifier. Lorsque vous aurez terminé avec cette nouvelle branche, vous pourrez faire un [Pull Request](https://github.com/Nostres25/HeartOfStellars/pulls) pour fusionner avec la branche de départ (à ne pas confondre avec la branche principale). **Et communiquez pour ne pas apporter les mêmes modifications ou des modifications contradictoires**
-   Cependant si la personne qui s'occupe de cette branche a terminé, n'a plus de modification/de commit non publié et ne va pas continuer sur cette branche avant la fin de vos modifications, alors vous pouvez continuer le travail sur cette branche sans en créer une nouvelle. (⚠️ en vous assurant bien d'être sur la bonne branche et de [`git pull`](#pull) avant)

[--> [Revenir aux règles de l'organisation du développement du projet]](#r%C3%A9sum%C3%A9-des-r%C3%A8gles-du-d%C3%A9veloppement-du-projet)

### Autre

-   De même, vous le voyez quand vous tappez juste la commande `git` dans votre invite de commande, mais il y a beaucoup de commandes git et de possibilités avec celles-ci. Cette documentation vous apprend les bases mais vous pourrez toujours avoir besoin de faire des recherches internet, de demander à un membre de l'équipe de développement ou de vérifier la documentation git pour effectuer dans actions spécifiques dans certains cas (comme annuler une action)

-   Ensuite, une autre commande très utile permet de voir les modifications effectuées dans le détail jusqu'aux lignes de codes. La commande est :

    ```
    git diff
    ```

    Pour plus d'informations sur la commande rendez-vous sur la [documentation git](https://git-scm.com/docs/git-diff).

-   Ce n'est pas obligatoire, mais de manière conventionnelle, les messages de commits et le [nom des branches](https://conventional-branch.github.io/) doivent respecter une certaine syntaxe. Pour un commit ça peut ressembler à `fix: 🐛 fight system bug fixed`. Et oui, **en anglais**. C'est plus pratique d'écrire comme cela, car plus facilement lisible lorsqu'on visionne la progression du projet. [(Plus d'infos sur les conventionnal commits)](https://www.conventionalcommits.org/fr/v1.0.0/)

    Pout ma part, je suis habitué à utiliser cette syntaxe de commits conventionnels en anglais. Ce serait mieux que tout le monde fasse de même pour un ensemble cohérent.

## Avec Github Desktop

Malheureusement cette partie n'a pas encore été rédigée. Passer par la commande, surtout en suivant cette documentation vous permettra de beaucoup mieux comprendre le fonctionnement de git et de GitHub.  
Mais si vous avez compris le fonctionnement de git avec les commandes, Github desktop sera facile à comprendre car chaque action correspond en réalité à une commande git. Tout comme les "menu git" dans les IDE.  
Il est vrai que Github desktop offre un meilleur confort avec une interface. Notamment pour l'affichage des différences (équivalent à `git diff`). Mais certains IDE, dont Visual Studio Code intègre un menu dédié à git.

## Avec Docker

On pourra utiliser docker pour tester le déploiement sur un système Ubuntu avec Apache2. Actuellement, la question du déploiement n'a pas été travaillée, mais c'est à faire en parallèle du développement. (pas trop à la fin, sinon on risque d'avoir beaucoup de problèmes à corriger)  
Pour un maximum de stabilité, on considère que l'application devrait être testée sur une machine Ubuntu (qui est dérivé de débian) dans un environnement le plus semblable possible à l'environnement de production. Maintenant, l'image Docker est une image Debian par souci de praticité, mais cela pourrait changer pour des Ubuntu à terme. Aussi, il est probablement préférable que l'application tourne sur un repertoire personnalisé comme `/var/www/suivi-colis-iutv`. Mais j'ai passé une après-midi à essayer de faire cela en vain, apache2 fait donc tourner l'application web sur `/var/www/html` pour le moment.

###### Triste histoire

Toute fois ce qui est intéressant, c'est que grâce au Dockerfile, vous savez ce qu'il faut faire pour faire fonctionner l'environnement en production.
_Toutefois, il y a beaucoup de mauvaises choses qui ça pourraient ne pas ressembler à ça à l'avenir_

Commandes docker:

-   build & run : `docker compose up --build`
-   build : `docker compose -f ./docker-compose.yml build`
-   run : `docker compose up`

# Programmation

### Habitudes de programmation & conseils

#### Documentations

Aidez-vous de [la documentation PHP](<[https://manual.gamemaker.io/monthly/fr/#t=Content.htm](https://www.php.net/docs.php)>) et de tutos. Cependant, il est fortement déconseillé de copier du code sans comprendre son fonctionnement. Même s'il fonctionne.

###### Évidemment, aidez-vous aussi de la documentation de votre framework si vous en utilisez-un

#### Programmation orientée object

Il vous faut maîtriser la programmation orientée objets avec le principe d'héritage qui est très important.

#### Style de programmation et lisibilité du code

> [!NOTE]
> Avec un formateur, le style de programmation sera formalisé automatiquement.

-   Adaptez votre [style de programmation](https://fr.wikipedia.org/wiki/Style_de_programmation) au projet. En effet, il y a différentes façons de formatter son code, notamment pour la position des accolades, des parenthèses etc... Avoir un style de programmation commun au sein du projet garantira une meilleure lisibilité et une meilleure compréhension du code.
-   À propos du formatage du code, veillez à bien espacer (pas trop) les différents éléments de code de sorte à créer des blocs de lignes associées. Il faut qu'on puisse dissocier rapidement les lignes qui n'ont pas de lien direct entre elles. Pour cela, vous pouvez vous inspirer de ce qui est déjà fait dans le projet.

#### Optimisation

-   Pensez à utiliser des [fonctions](https://www.php.net/manual/fr/language.functions.php) pour des bouts de codes que vous souhaitez utiliser plusieurs fois, de sorte à ne jamais répéter des blocs de code.
-   Une des règles avec les fonctions est : une fonction pour un usage. Si votre fonction possède plusieurs étapes (exemple : le chargement de plusieurs types de données différentes), il est probablement nécéssaire de faire une fonction pour chaque étape (exemple: une fonction pour le chargement des sauvegardes, une fonction pour le chargement des paramètres etc...).
-   Pensez optimisation de la mémoire et des opérations. En effet, votre code doit en priorité comporter le moins d'opérations possibles, en éliminant les répétitions et en stockant des données en mémoire à l'aide de variables. Mais il faut aussi faire attention à ne pas utiliser de mémoire inutilement.
-   Pour les projets Laravel trouvez [quelques conseils pour optimiser le code](https://medium.com/@dev.muhammadazeem/14-advanced-tips-to-optimize-laravel-database-queries-0e738108a548).

#### Commentaires & noms de symboles

-   Pensez clarté. Si un bout de code n'est pas suffisant à lui tout seul pour comprendre son fonctionnement et/ou son utilité (avec la connaissance du langage), alors il faut ajouter des commentaires. Pour expliquer un fonctionnement peu intuitif par exemple. Faites attention à ne pas mettre trop de commentaires, pour par exemple expliquer chaque ligne de code. La plupart du temps le code doit être suffisamment clair pour ne pas avoir besoin de commentaires.
-   Langage anglais : les commentaires, les variables, les fonctions, les noms des fichiers, et tout ce qui touche au code doivent être écrits en anglais. Même les commits et les noms des branches de préférence.
-   Nom de variables : Le nom des [variables](https://www.php.net/manual/fr/language.variables.php) et des [fonctions](https://www.php.net/manual/fr/language.functions.php) doit décrire clairement la fonction de la variable ou de la méthode. Et, pour suivre la convention, ils s'écrivent en minuscule. Si le nom comporte plusieurs mots, la première lettre des mots suivants est en majuscule (ex: `lastIndex`). Une autre solution pour les variables est par exemple : `last_index`. Ensuite, les variables constantes sont en majuscule (ex: `VERSION`) et les classes ainsi que les [énumérations](<[https://manual.gamemaker.io/monthly/en/GameMaker_Language/GML_Overview/Variables/Constants.htm](https://www.php.net/manual/fr/language.types.enumerations.php)>) commencent par une majuscule (ex: `Player` et `Color`). Toutefois, les valeur des énumérations sont en majuscule également (`Color.RED`)
-   Pratique de renommage : Lorsque vous souhaitez renommer une variable, une énumération, une fonction ou une classe si vous le faites de manière traditionnelle cela va probablement causer des erreurs dans le code, car il faudra changer le nom du symbole partout où il est utilisé/appelé, et cela peut prendre beaucoup de temps. C'est pour cela que je vous conseille d'utiliser l'outil de renommage de masse de votre IDE et de vous assurer qu'il fonctionne. Cet outil s'utilise généralement en sélectionnant le nom de la fonction, variable, classe ou énumération que vous voulez renommer puis faisant un raccourci clavier (sur VSCode c'est F2, sur PHPStorm c'est MAJ + F6. Cherchez sur votre IDE, il y en a probablement un)

[^1]: Le **dépôt local** correspond à l'ensemble du projet tel qu'il est sauvegardé localement. C'est-à-dire sur votre appareil. Le dépôt local comprend l'ensemble des branches avec l'ensemble des commits qui ont été mis à jour depuis le dépôt distant. Le dépôt local ne se met pas à jour automatiquement et il est représenté par un repertoire `.git` dans le dossier du projet.
[^2]: Le **dépôt distant** correspond à l'ensemble du projet tel qu'il est en ligne, sur github.com. Il comprend l'ensemble des branches et des commits qui ont été publiés via [`git push`](#push). Et c'est à partir de lui que se fait la mise à jour du dépôt local via [`git pull`](#pull).
[^3]: Un **commit** est une modification dans le code accompagnée d'un court message de description et d'un identifiant généré automatiquement. Il correspond à UN ajout, à UNE amélioration ou à UNE correction. Dans l'idéal, il faut commit à chaque version stable du code (un commit ne doit pas comporter d'erreur). La commande [`git commit`](#commit) va enregistrer en local les modifications faites dans les fichiers qui ont été ajoutés via `git add` au préalable, pour la branche actuelle.
[^4]: Une **branche** en git, cela correspond à une section dans laquelle le code et l'ensemble des commits qui le définissent sont représentés. La notion de branche vient du fait qu'il peut y avoir plusieurs de ses sections. Ce qui se représente par des branches (d'un arbre) dans la liste des commits. Car la création d'une nouvelle branche se fait obligatoirement à partir d'un commit d'une branche existante (généralement le plus récent). Ainsi, les nouveaux commits sur la nouvelle branche seront différés des commits sur la branche originelle et inversement. Par conséquent, le code aussi. Le noeux est donc le commit de départ de la nouvelle branche sur l'ancienne branche et les branches correspondent à l'ensemble des commits dans ces différentes branches. Pour vérifier dans quelle branche vous êtes, vous pouvez utiliser `git status`.
[^5]: L'action de **pull**, traduite par "tirer" en français correspond à la mise à jour de la branche actuelle à partir de la même branche du dépôt distant. Tous les commits publiés sur le dépôt distant via [`git push`](#push) par autrui seront importés sur le dépôt local
[^6]: L'action de **push**, traduite par "pousser" en français correspond par la publication de tous les commits non publiés de la branche actuelle du dépôt local, vers la même branche du dépôt distant
[^7]: L'action de **merge**, traduite par "fusionner" en français correspond à la mise en commun des commits des deux branches. Autrement dit, une fusion. Cette fusion se fait d'une branche à une autre, comme l'import des nouveaux commits d'une première branche vers une deuxième branche. Et les commits contradictoires entre les branches, c'est-à-dire qui touchent aux mêmes lignes d'un même fichier, feront l'objet d'un conflit. Qui nécessitera une intervention humaine pour choisir quel commit accepter et quel commit rejeter. La fusion des commits se traduit d'ailleurs par une fusion du code des deux branches.
[^8]: L'action de **pull request**, traduite en français par "demande de tirer", n'a contre toute attente, pas de lien direct avec l'action de pull. Mais plutôt avec l'action de merge. En effet, au lieu de merge directement, créer un pull request permet de publier une demande de merge d'une branche à une autre sur le répertoire github. Cette demande est accompagnée d'un nom, d'une description et d'un fil de commentaires. Cela permet à un autre développeur (@Nostres25 dans le cas actuel), de vérifier le merge demandé et les différents commits dont il est question et de procéder au merge lui-même et gérant les conflits s'il y en a. Pour garder un contrôle sur les modifications apportées au projet et pour notamment éviter aux autres développeurs d'avoir à gérer les conflits.
