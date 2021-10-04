# Skeleton pour API sur Slim Framework 4

[![Coverage Status](https://coveralls.io/repos/github/slimphp/Slim-Skeleton/badge.svg?branch=master)](https://coveralls.io/github/slimphp/Slim-Skeleton?branch=master)

Use this skeleton application to quickly setup and start working on a new Slim Framework 4 application. This application uses the latest Slim 4 with Slim PSR-7 implementation and PHP-DI container implementation. It also uses the Monolog logger.

This skeleton application was built for Composer. This makes setting up a new Slim Framework application quick and easy.

## Installer et configurer l'Api

Déplacer simplement tous les fichiers à la racine d'un serveur Apache2 

Installer les dépendances avec 
```
composer install
```

Executer dans un navigateur l'api afin de vérifier si il y a une réponse, 
si il y a une erreur c'est normal car la base de donnees n'est pas renseigné

Ouvrir le fichier .env qui a été généré par l'Api
```
SHOW_ERRORS=0

JWT_KEY='Nzg5OWM0NzkzZGEyNzM4MzYzYjhkMDc1NjI0NjBmNGFmNTkyMzBiMDAzOWU0NzU2YTgwOTE3YmY5MWY5MGI2Yw=='
JWT_EXPIRE=3600

DB_HOSTNAME='localhost'
DB_USERNAME='user'
DB_PASSWORD='password'
DB_DATABASE='base'
```

## Important
Veillez à ce que le .htaccess à la racine du projet et dans le dossier /public soit bien lu par le serveur Apache2, autrement les informations confidentielles risquent d'etre visibles