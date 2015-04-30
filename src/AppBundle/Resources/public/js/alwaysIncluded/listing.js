/**
 * affiche un menu permettant d'ajouter un membre à une liste.
 * Le bouton doit obligatoirement posséder l'attribut data-id qui contiendra l'id du membre à ajouter
 */
$('.single-listing-button').click(function() {

    var $btn  = $(this);
    var $menu = $btn.children('.menu');

    // On récupère l'ID
    var $id   = $btn.attr("data-id");

    $menu.empty();

    $.ajax({

        url: Routing.generate('listing_load_listes_as_json'),
        type: 'POST',
        success: function (data) {

            var strin = '<div class="header">Ajouter à une liste</div><div class="divider"></div>';

            // On insère ensuite les listes là dedans
            $.each(data, function(i, item) {

                strin += generateMenuItem($id, item);
            });

            $menu.html(strin);
        },

        error: function (data) {
            alert(JSON.stringify(data));
        }
    });
});

/**
 * Supprimme une liste
 */
$(document).on('click', '.listing-remove-liste-button', function() {

    var token = $(this).attr("data-token");

    $.ajax({
        url: Routing.generate('listing_remove_liste', {token: token}),
        type: 'GET',
        success: function () {

            var n = noty({text: "Liste supprimée", type: "information"});
            closeBar();
        },
        error: function (data) {
            alert("Erreur lors de la suppression de la liste");
        }
    });
});

/**
 * Génère un élément du menu de selection de liste
 * @param ids
 * @param item
 * @returns {string}
 */
function generateMenuItem(ids, item) {

    return '<a class="item listing-addToMenu-button" data-ids="' + ids + '" data-token="' + item.token + '">' + item.name + '<span class="description">' + item.size + '</span></a>';
}

/**
 * Quand un bouton du menu listing a été cliqué
 * On doit ensuite déterminer si le bouton doit récupérer le contenu d'une table ou un simple ID
 */
$(document).on('click', '.listing-addToMenu-button', function() {

    // On ajoute l'id passé à la liste
    var $btn = $(this);
    addElements($btn.attr("data-token"), $btn.attr("data-ids"));
});

//Appelé pour supprimer automatiquement la popup du menu des listes pour la réactualiser à chaque fois

/**
 * Ouvre la barre de listing
 */
$('#main-listing-button').click(function() {

    $.ajax({
        url: Routing.generate('listing_generate_top_bar'),
        type: 'POST',
        success: function (data) {

            var $bar = $('#listing-bar');
            $bar.html(data);
            $bar.sidebar('toggle');
        },

        error: function (data) {
            alert(JSON.stringify(data));
        }
    });
});

/**
 * Ouvre la modale pour créer un liste
 */
$(document).on('click', '#add-liste-modale-button', function() {

    showModal('#modal-listing-create-new');
});

/**
 * Ajoute une liste
 */
$(document).on('click', '#add-liste-button', function() {

    var $nom = $('#add-liste-input').val();

    if($nom != '' && $nom != null) {

        $.ajax({
            url: Routing.generate('listing_add', {name: $nom}),
            type: 'GET',
            success: function () {

                $('#modal-listing-create-new').modal('hide');
                closeBar();
                noty({text: "Liste créée", type: "success"})
            },

            error: function (data) {
                alert(JSON.stringify(data));
            }
        });
    }
});

/**
 * Affiche une liste
 */
$(document).on('click', '.show-liste-button', function() {

    var $token = $(this).attr("data-token");

    $.ajax({
        url: Routing.generate('listing_view_liste_by_token', {token: $token}),
        type: 'POST',
        success: function (data) {

            $('#liste-viewer-container').html(data);
        },

        error: function (data) {
            alert(JSON.stringify(data));
        }
    });
});


/**
 * Ferme la barre
 */
function closeBar() {

    $('#listing-bar').sidebar('hide');
}


/**
 * Ajoute des éléments à liste
 * @param token string le token de la liste
 * @param ids array les ids de membres à ajouter
 */
function addElements(token, ids) {

    $.ajax({
        url: Routing.generate('listing_add_members_by_id', {token: token, ids: ids}),
        type: 'GET',
        success: function () {

            var n = noty({text: "Membre(s) ajouté(s) avec succès", type: "success"});
        },
        error: function (data) {
            alert("Erreur lors de l'ajout des membres");
        }
    });
}


/**
 * listing.js
 * regroupe toutes les fonctions liées au listing, suppression, récupération, ajout...
 */
$(document).ready(function() {

    var listing = {

        /**
         * Supprimme une liste existante du listing
         * @param token le token de la liste à supprimer
         */
        remove: function (token) {


        },

        /**
         * Supprimme des éléments d'une liste
         * @param token string le token de la liste
         * @param ids array les ids de membres à enlever
         */
        removeElements: function (token, ids) {

            $.ajax({
                url: Routing.generate('listing_remove_members_by_id', {token: token, ids: ids}),
                type: 'GET',
                success: function () {
                    alerte.send('Element(s) supprimé(s) avec succès', 'info');
                },
                error: function (data) {
                    alert("Erreur lors de la suppression des membres");
                }
            });
        }
    }
});
