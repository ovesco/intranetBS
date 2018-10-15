# Github best practice

Voici un petit tutoriel à suivre si l'on souhaite participer à ce projet.
Le but étant de formaliser un peu le processus de développement du projet
en peremettant un suivit des modifications ainsi que la mise en place d'une
relecture attentive de chaque modifications par les autres développeurs.

[Un peu de doc sur les branches](https://github.com/Kunena/Kunena-Forum/wiki/Create-a-new-branch-with-git-and-manage-branches)

## Je souhaite faire une modification, que dois-je faire?

Le principe de base de ce tutoriel est de créer un branche spécifique à chaque 
modifications souhaitée. Par exemple, je veux mettre en place une nouvelle possibité
d'export de liste en fichier CSV. Dans ce cas, je vais mettre tout les modifications
relative à cette fonctionnalité dans une branche dédiée (ex. nuffer_add_new_list_export_csv).
Une fois que tout marche 
et que la branche a été vérifiée par les autres développeur alors on fusionera cette 
branche avec la branche master. De cette façon, nous avons un controle très précis
des modifications apportées à la branche master. 

Alors comment faire tout ceci?

### Commencer par être à jour

La commande 
```bash
git status
```
permet de connaitre l'état du répértoir actuel.
```bash
On branch master
Your branch is up-to-date with 'origin/master'.
```
Si le résultat n'est pas celui-ci alors il vous faudras commiter le travail actuel et executer la commande suivante:
```bash
git checkout master //switch to branch master
```
Ensuite un petit pull pour avoir la derniere version de la branche master:
```bash
git pull
```

### Nouvelle branche
Nous créeons maintenant la nouvelle branche qui contiendra tout les modifications reliée à votre nouvelle fonctionalité.
La branche devrais contenir en début votre pseudo et ensuite un titre clair lié à votre développement.

```bash
git checkout -b nuffer_add_new_list_export_csv //create new branch and swith to it
git push origin nuffer_add_new_list_export_csv //push the branche on github
git branch //permet de voir les branches et de verifier qu'on se trouve dans la nouvelle
```

### On taf un peu
Ce qui à pour effet généralement de modifier/créer/supprimer des lignes/fichiers.

### Ajout des modifications à la branch
Une fois le travail fait il faut mettre les modifications effectuée sur la branche.

```bash
git add //mode bourrin: à ne pas faire
git add -p //mode safe: avec un prompteur pour vérifier chaque modification à ajouter
```   
Ensuite, on commit les changement sur la branche local:

```bash
git commit //avec editeur de message qui s'ouvre 
git commit -m "Message"
``` 
Voici un peu de lecture:   
[Lecture importante sur les messages de commit 1](http://chris.beams.io/posts/git-commit/)   
[Lecture importante sur les messages de commit 2](http://tbaggery.com/2008/04/19/a-note-about-git-commit-messages.html)

Attention, oubliez pas de configuer auteur et email dans git si c'est pas encore fait:
```bash
git config --global user.name "John Doe"
git config --global user.email johndoe@example.com
``` 

### Push des mofifications

Ensuite, on envoie les modifications sur le server de github:
```bash
git push //Si la branche existe déjà sur github
git push --set-upstream origin nuffer_add_new_list_export_csv //si la branche n'existe pas encore sur github
``` 

### Vous avez pas encore finit le taf

Vous avez réalisé qu'une partie du travail à faire. Bien, alors continuer à bosser dessus plus tard sur la branche local associée
et envoyez vos modifications par commit sur la meme branche sur github.

### Vous avez finit le taf

Super, il va falloir maintenant merger la branche qui contient vos modifications avec la branche master. 

Pour ce faire, il faut d'abord crée un "pull request" depuis la branche sur le site de github en ajoutant les autres développeur 
comme reviewers (!!! important !!!)

Une fois les reviewers ayant accepté vos modifications, la branche pourra etre mergée. Si des modifications sont demandée, il suffit de faire un 
les modificaiton dans la branche et commiter les modifications demandée. Le reviewers ferrons à nouveaux leur travail.

A noté que la branche doit aussi passsé les tests travis et être a jours avec la branche master...c'est bien expliqué sur la page de "pull request"

### Suppression des branches
On supprime les branches une fois que le merge à eu lieu.

Suppression local:
```bash
git branch -d your_local_branch
``` 
Suppresion remote (sur github): Se fait via la page github des branches
