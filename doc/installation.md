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
Ajouter manuellement avec phpmyadmin dans security_users et roles_users


##Indexation

Les membres et les factures sont indexés pour la recherche avec [Elastic](https://www.elastic.co/) (anciennement Elasticsearch).

[Installation d'Elastic](/doc/install_elastic.md)

### Initialisation de FOSElasticaBundle

```bash
php app/console fos:elastica:reset --force
php app/console fos:elastica:populate
```