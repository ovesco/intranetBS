
# Passer en mode "Prod"

##Erreur courrante avec les CSS et Assetic.

Il faut que l'appel aux resources se fasse par le chemin correct dans le répértoire /web et non pas avec "@MonBundle...".
C'est nécessaire pour que le filtre "cssrewrite" marche correctement. Ci-dessous un exemple correct:

```html+jinja
{% stylesheets 'bundles/acme_foo/css/*' filter='cssrewrite' output='exemple.css'%}
    <link rel="stylesheet" href="{{ asset_url }}" />
{% endstylesheets %}
```

##Script pour mode "Prod"

Suggestion d'un petit script .sh pour executer les commandes nécaissaire au passage en mode "prod":

```bash
rm -rf ./web/js/*
rm -rf ./web/css/*
rm -rf ./web/images/*
rm -rf ./web/bundles/*
php app/console assets:install --symlink
php app/console cache:clear --env=dev --no-debug
php app/console cache:clear --env=prod --no-debug
php app/console assetic:dump --env=prod --no-debug
```