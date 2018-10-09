Le dossier /intro contient une application symfony v4.1
Après téléchargement ou clonage de ce repository:
- ouvrir une console et se placer dans le dossier intro
- utiliser composer pour installer les dépendances:
composer Install
- modifier le paramètre DATABASE_URL du fichier .env

Pour installer composer: https://getcomposer.org/download/
Pour créer un projet symfony: https://symfony.com/download

Si nécessaire:
 - créer la base de données:
 php bin/console doctrine:database:create
 - exécuter les migrations:
 php bin/console doctrine:migrations:migrate
