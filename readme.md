# Marketplace

Ce projet est une marketplace de vente en ligne construite avec Symfony et Docker-compose. Cette application permet de mettre en relation des vendeurs et des utilisateurs, qui peuvent noter les produits achetés. Pour assurer la sécurité et la confidentialité des paiements, Stripe est utilisé comme plateforme de paiement en ligne. Le suivi des colis est assuré par l'API de La Poste.
Les conteneurs Docker utilisés dans ce projet sont Nginx, PHP et une base de données MySQL.

## Prérequis

- Docker
- Docker-compose
- Stripe API keys
- API key pour l'API de La Poste

## Installation

1. Clonez le projet à partir du dépôt Git :

git clone https://github.com/moncefSaiki/marketplace.git


2. Créez le fichier `.env`.

3. Remplissez les champs `STRIPE_API_KEY` et `LAPOSTE_API_KEY` avec vos propres clés API.

4. Construisez les images Docker :

make build


5. Installez les dépendances PHP :

docker-compose exec php composer install

5.  Créez la base de données :

docker-compose exec php bin/console doctrine:database:create
docker-compose exec php bin/console doctrine:migrations:migrate


## Utilisation

- Accédez à l'application en visitant l'URL suivante dans votre navigateur : `http://localhost:8000`
- Pour arrêter les conteneurs Docker, utilisez la commande suivante :


## Structure du projet

- `app/` : le répertoire de l'application Symfony
- `nginx/` : le fichier de configuration du serveur web Nginx
- `php/` : le fichier de configuration PHP
- `.gitignore` : la liste des fichiers à ignorer pour Git
- `Makefile` : un fichier Make pour automatiser les tâches courantes
- `docker-compose.yml` : le fichier de configuration Docker-compose

## Contribuer

Si vous souhaitez contribuer à ce projet, n'hésitez pas à soumettre une pull request ou à ouvrir une issue.

## Auteur

Ce projet a été créé par @YacineSoussi.


