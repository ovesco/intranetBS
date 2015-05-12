intranetBS
========================

Le netBS est une interface permettant de gérer un groupe scout. Actuellement en développement.

# Installation
## Acquérir les sources
### Cloner le dépôt Git

### Installer composer

### Installer les dépendances à l'aide de composer
```
composer update
```
## Mise en route
### Initialiser la base de données
```
php app/console doctrine:schema:create
```
### Peupler la base de donnée
```
php app/console doctrine:schema:update --force
```
## Données
### Générer des données de test
```
php app/console app:populate create
php app/console app:populate fill 500
```
### Générer les droits
```
php app/console security:roles:build roles.yml
```
### Générer un user de login
Ajouter manuellement avec phpmyadmin dans security_users et roles_users

### Indexer le contenu à rechercher

#### Installation de elasticsearch

Pour faire fonctionner le bundle FOSElasticaBundle, il est nécessaire d'avoir installer le programme "elasticsearch" comme service sur son server.

##### Elasticsearch sur MacOSX
```
brew update
brew install elasticsearch
brew info elasticsearch //liste des commandes pour démarré le service
```
Commencer par la commande qui crée le lien suivant:
```
sudo ln -sfv /usr/local/opt/elasticsearch/*.plist ~/Library/LaunchAgents
```
Ceci met elasticsearch dans les services à lancer au démarrage pour cette session

Il faut s'assurer des droits corrects pour le fichier *.plist

```
sudo chmod 600 *.plist // modification des droits
sudo chown root *.plist // changement du propriétaire pour "root"
```
Ensuite si l'erreur suivante survient: 
```
[Elastica\Exception\Connection\HttpException]  
Couldn't connect to host, Elasticsearch down? 
```
c'est que la configuration ne peut pas résoudre le "localhost" donc dans parameters.yml

mettre:
```
elastic_host: 127.0.0.1
elastic_port: 9200
```
en lieu et place de:
```
elastic_host: lcoalhost
elastic_port: 9200
```
#### Initialisation de FOSelasticaBundle

```
php app/console fos:elastica:reset --force
php app/console fos:elastica:populate
```
