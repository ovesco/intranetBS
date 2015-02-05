$('.effectuerEnvoi').click(function(){
    var idGroupe = $(this).data('id');

    displayFormEnvoiDocument(idGroupe,null);

});


/**
 * on initialise la datatable des effectifs dans le cas où on doit l'initialiser. On met également en place les
 * différentes méthodes concernant le listing
 */
var table = $('#effectifs-table').DataTable({
    "sDom": '<f><"flex"<l><p>>',
    "oLanguage": {
        "sEmptyTable": "Aucun résultats",
        "sSearch": "<span>Rechercher dans la liste :</span> _INPUT_",
        "oPaginate": { "sFirst": "Suivant", "sLast": "Précédent", "sNext": ">", "sPrevious": "<" }
    }
});

/**
 * Si on sélectionne une ligne de la table
 */
$('#effectifs-table').on( 'click', 'tr', function () {

    $(this).toggleClass('selected');

    //On met à jour les endroits où on affiche le nombre de ligne cliquées

    var number = table.rows('.selected').data().length;
    $('.listing-update').text(number);
} );


/**
 * Permet d'ajouter les éléments sélectionnés au listing.
 * @param token string le token de la liste choisie
 */
function addToListing(token) {

    var rows  = table.rows('.selected').nodes(),
        ids   = [];

    $(rows).each(function(i, obj) {

        ids.push($(obj).attr("data-id"));
    });

    listing.addElements(token, ids);
}

/**
 * permet de sélectionner tous les membres contenus dans la table datatable initialisée
 */
function selectAll() {

    $('#effectifs-table tr').addClass('selected');
    var number  = table.rows('.selected').data().length;
    $('.listing-update').text(number);
}

/**
 * permet de désélectionner tous les membres contenus dans la table datatable initialisée
 */
function deselectAll() {

    $('#effectifs-table tr').removeClass('selected');
    $('.listing-update').text(0);
}