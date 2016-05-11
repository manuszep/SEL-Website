#Bouts de fiSEL

Un site web pour gérer le SEL Athois.

## Installation

- Check-out à partir de la [source](https://github.com/manuszep/SEL-Website.git)
- Installer [Composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx)
- Lancer la commande
    ```
    composer install
    ```
- Configurer le serveur pour qu'il pointe vers le dossier /web de l'application
    - N'oubliez pas d'activer mod-rewrite et le support des fichiers .htaccess
- Créer une nouvelle base de données
- Copier et renommer le fichier parameters.yml.dist
    ```
    cp app/config/parameters.yml.dist app/config/parameters.yml
    ```
- Mettre ce fichier à jour avec les données de connexion de la DB
- Créer le schéma de la DB
    ```
    php bin/console doctrine:schema:create
    ```
- Charger les données de démo
    ```
    php bin/console doctrine:fixtures:load
    ```
- [Installer NPM](https://docs.npmjs.com/cli/install)
- Installer les dépendances NPM
    ```
    npm install
    ```
- [Installer Grunt](http://gruntjs.com/installing-grunt)
- Générer et surveiller les CSS / JS
    - Les CSS sont écrits en SASS et doivent être compilés; Les JS sont inclus à l'aide de Browserify et nécéssitent un traitement.
    - Tout ça se fait automatiquement à l'aide de Grunt.
    ```
    grunt dev
    ```
    - Une fois cette commande lancée, Grunt surveille les fichiers et recompile lorsqu'un fichier change.
    - Ne pas fermer la fenêtre de terminal lorsque Grunt tourne. Pour arrêter Grunt, utiliser CTRL + C
