# Installation
## Acquérir les sources
* Cloner le dépôt Git
* Installer composer sur le serveur
* Installer les dépendances à l'aide de composer
```bash
composer update
```


## Mise en route
* Initialiser la base de données
```bash
php app/console doctrine:schema:create
```
* Peupler la base de donnée
```bash
php app/console doctrine:schema:update --force
```

## Données
### Générer des données de test
```bash
php app/console app:populate create
php app/console app:populate fill 200
```
### Générer les droits
Les droits sont sotckés dans la base de données. Ils euvent être extraits d'un fichier yml respectant la structure avec la commande suivante :
```bash
php app/console security:roles:build roles.yml
```
### Générer un user de login
En ligne de commande:
```bash
php app/console app:populate create_admin
```
ou ajouter manuellement avec phpmyadmin dans security_users et roles_users

###Exemple de script pour générer des données de test

Ce script requière que la base de donnée soit vide avant son execution.

```bash
php app/console cache:clear
php app/console doctrine:schema:update --force
php app/console security:roles:build roles.yml
php app/console app:populate create
php app/console app:populate fill 200
php app/console app:populate create_admin
php app/console fos:elastica:populate
```

Après ceci, une connexion avec le user "admin" (psw: "admin") et le tour et joué...

##Indexation

Les membres et les factures sont indexés pour la recherche avec [Elastic](https://www.elastic.co/) (anciennement Elasticsearch).

[Installation d'Elastic](/doc/install_elastic.md)

### Initialisation de FOSElasticaBundle

```bash
php app/console fos:elastica:reset --force
php app/console fos:elastica:populate
```