# Installation d'Elastic

Pour faire fonctionner le bundle FOSElasticaBundle, il est nécessaire d'installer "elasticsearch" comme service sur son serveur.

## Elastic sur MacOSX
```bash
brew update
brew install elasticsearch
brew info elasticsearch //liste des commandes pour démarré le service
```
Commencer par la commande qui crée le lien suivant:
```bash
sudo ln -sfv /usr/local/opt/elasticsearch/*.plist ~/Library/LaunchAgents
```
Ceci met Elastic dans les services à lancer au démarrage pour cette session

Il faut s'assurer des droits corrects pour le fichier *.plist

```bash
sudo chmod 600 *.plist // modification des droits
sudo chown root *.plist // changement du propriétaire pour "root"
```
Ensuite si l'erreur suivante survient: 
```
[Elastica\Exception\Connection\HttpException]  
Couldn't connect to host, Elasticsearch down? 
```
c'est que la configuration ne peut pas résoudre le "localhost" donc dans parameters.yml (pour des raisons de compatibilité IPv4/IPv6

mettre:
```yml
elastic_host: 127.0.0.1
elastic_port: 9200
```
en lieu et place de:
```yml
elastic_host: lcoalhost
elastic_port: 9200
```

## Elasticsearch sous Debian :
Installer java-jdk version 8 (possible d'installer le 7 normalement)
```bash
echo "deb http://ppa.launchpad.net/webupd8team/java/ubuntu trusty main" | sudo tee /etc/apt/sources.list.d/webupd8team-java.list
echo "deb-src http://ppa.launchpad.net/webupd8team/java/ubuntu trusty main" | sudo tee -a /etc/apt/sources.list.d/webupd8team-java.list
sudo apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv-keys EEA14886
sudo apt-get update
sudo apt-get install oracle-java8-installer
```
Installer elasticsearch :
```bash
wget -qO - https://packages.elastic.co/GPG-KEY-elasticsearch | sudo apt-key add -
echo "deb http://packages.elastic.co/elasticsearch/1.5/debian stable main" | sudo tee -a /etc/apt/sources.list
sudo apt-get update && sudo apt-get install elasticsearch
sudo mkdir /usr/share/elasticsearch/data
sudo chown elasticsearch:elasticsearch /usr/share/elasticsearch/data -R
(optional) start @boot
sudo update-rc.d elasticsearch defaults 95 10
```
Sinon
```bash
sudo service elasticsearch start
```

## Initialisation de FOSElasticaBundle

```bash
php app/console fos:elastica:reset --force
php app/console fos:elastica:populate
```