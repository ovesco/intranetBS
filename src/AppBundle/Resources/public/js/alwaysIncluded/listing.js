/**
 * listing.js
 * regroupe toutes les fonctions liées au listing, suppression, récupération, ajout...
 */

var listing =  {

    /**
     * Ajoute une nouvelle liste au listing
     * @param name le nom de la liste à créer
     */
    create : function(name) {

        $.ajax({
            url:Routing.generate('listing_creer_liste', {name:name}),
            type:'GET',
            success:function(){return true;},
            error:function(data){alert("Erreur lors de la création de la liste");}
        });
    },

    /**
     * Supprimme une liste existante du listing
     * @param token le token de la liste à supprimer
     */
    remove : function(token) {

        $.ajax({
            url:Routing.generate('listing_supprimer_liste', {token:token}),
            type:'GET',
            success:function(){return true;},
            error:function(data){alert("Erreur lors de la suppression de la liste");}
        });
    },

    /**
     * Ajoute des éléments à liste
     * @param token string le token de la liste
     * @param ids array les ids de membres à ajouter
     */
    addElements : function(token, ids) {

        $.ajax({
            url:Routing.generate('listing_ajouter_membres_par_id', {token:token, ids:ids}),
            type:'GET',
            success:function(){alerte.balance('Element(s) ajouté(s) avec succès', 'info');},
            error:function(data){alert("Erreur lors de l'ajout des membres");}
        });
    },

    /**
     * Supprimme des éléments d'une liste
     * @param token string le token de la liste
     * @param ids array les ids de membres à enlever
     */
    removeElements : function(token, ids) {

        $.ajax({
            url:Routing.generate('listing_supprimer_membres_par_id', {token:token, ids:ids}),
            type:'GET',
            success:function(){alerte.balance('Element(s) supprimé(s) avec succès', 'info');},
            error:function(data){alert("Erreur lors de la suppression des membres");}
        });
    }
};