Bienvenue sur le repository de l'intranet de la Brigade de Sauvabelin.
Si vous êtes intéressé par le projet, n'hésitez pas à [me contacter](mailto:it@sauvabelin.ch).

[![Build Status](https://travis-ci.org/sysmoh/intranetBS.svg?branch=master)](https://travis-ci.org/sysmoh/intranetBS)

# Résumé
Le but du NetBS est d'offir une interface gratuite de gestion de groupe scoute. Cette plate-forme est actuellement adaptée à de grandes structures (>100 membres) avec une importante structure hiérarchique. L'objectif est de réduire la charge administrative en automatisant les tâches et en proposant un outils collaboratif pour que les modifications s'effectuent depuis tous les niveaux du groupe.

## Fonctionnalités
### Gestion des membres
* Informations du membre (adresse, téléphone, ...)
* Attributions (rôles dans le groupe)
* Distinctions (cours de formation effectués, ...)
* Gestion des familles et des frateries

### Gestion du groupe
* Création de sous-groupe et structure hiérarchique du groupe
* Rôle des membres dans le groupe en fonction de leur attributions

### Gestion des factures
* Ajouter des créances (dettes) aux membres et familles
* Facturer des créances
* Gérer l'état de paiement des factures

### Accès en fonction des attributions
* Les attributions des membres leurs donnent accès à certanes parties (en lecture ou modification)
* Supervision centralisée avec surveillance des modifications


# Installation
## Acquérir les sources
* Cloner le dépôt Git
* Installer composer sur le serveur
* Installer les dépendances à l'aide de composer
```
composer update
```


## Mise en route
* Initialiser la base de données
```
php app/console doctrine:schema:create
```
* Peupler la base de donnée
```
php app/console doctrine:schema:update --force
```

## Données
### Générer des données de test
```
php app/console app:populate create
php app/console app:populate fill 200
```
### Générer les droits
Les droits sont sotckés dans la base de données. Ils euvent être extraits d'un fichier yml respectant la structure avec la commande suivante :
```
php app/console security:roles:build roles.yml
```
### Générer un user de login
Ajouter manuellement avec phpmyadmin dans security_users et roles_users



## Indexation

Les membres et les factures sont indexés pour la recherche avec [Elastic](https://www.elastic.co/) (anciennement Elasticsearch)

### Installation d'Elastic

Pour faire fonctionner le bundle FOSElasticaBundle, il est nécessaire d'installer "elasticsearch" comme service sur son serveur.

#### Elastic sur MacOSX
```
brew update
brew install elasticsearch
brew info elasticsearch //liste des commandes pour démarré le service
```
Commencer par la commande qui crée le lien suivant:
```
sudo ln -sfv /usr/local/opt/elasticsearch/*.plist ~/Library/LaunchAgents
```
Ceci met Elastic dans les services à lancer au démarrage pour cette session

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
### Initialisation de FOSElasticaBundle

```
php app/console fos:elastica:reset --force
php app/console fos:elastica:populate
```
### Elasticsearch sous Debian :
Installer java-jdk version 8 (possible d'installer le 7 normalement)
```
echo "deb http://ppa.launchpad.net/webupd8team/java/ubuntu trusty main" | tee /etc/apt/sources.list.d/webupd8team-java.list
echo "deb-src http://ppa.launchpad.net/webupd8team/java/ubuntu trusty main" | tee -a /etc/apt/sources.list.d/webupd8team-java.list
apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv-keys EEA14886
apt-get update
apt-get install oracle-java8-installer
```
Installer elasticsearch :
```
wget -qO - https://packages.elastic.co/GPG-KEY-elasticsearch | sudo apt-key add -
echo "deb http://packages.elastic.co/elasticsearch/1.5/debian stable main" | sudo tee -a /etc/apt/sources.list
sudo apt-get update && sudo apt-get install elasticsearch
sudo mkdir /usr/share/elasticsearch/data
sudo chown elasticsearch:elasticsearch usr/share/elasticsearch/data -R

```
(optional) start @boot
```
sudo update-rc.d elasticsearch defaults 95 10
```
Sinon
```
sudo service elasticsearch start
```

# Tests

## Lancer les tests
```
phpunit -c app/
```
