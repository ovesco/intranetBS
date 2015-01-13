var tables = [];

/**
 * Au début, on va récupérer l'ensemble des tables et les stocker dans un array pour garder les références.
 * Pour cela, on va viser le .listing-container, qui a un attribut data-token contenant le token de la liste.
 */
$('.listing-table').each(function(i, obj) {

    var token = $(obj).parent().attr("data-token");
    tables[token] = $(obj).DataTable({
        "sDom": '<f><"flex"<l><p>>',
        "oLanguage": {
            "sEmptyTable": "Aucun résultats",
            "sSearch": "<span>Rechercher dans la liste :</span> _INPUT_",
            "oPaginate": { "sFirst": "Suivant", "sLast": "Précédent", "sNext": ">", "sPrevious": "<" }
        }
    });
});


/**
 * Gestion de la selection d'éléments dans une table. Comme l'ensemble des tables est stocké dans un array
 * on peut facilement accéder à la référence sur la table, pour ainsi obtenir le nombre de lignes cliquées etc...
 */
$('.listing-table tbody').on( 'click', 'tr', function () {

    $(this).toggleClass('selected');

    //On met à jour les endroits où on affiche le nombre de ligne cliquées
    var container = $(this).closest('.listing-container'),
        token     = $(container).attr("data-token"),
        table     = tables[token],
        number    = table.rows('.selected').data().length;

    $('.listing-update-' + token).text(number);
} );


/**
 * cette fonction est appelée lorsqu'on veut supprimer un ou plusieurs éléments de la liste
 * @param btn le bouton qui a cliqué. On peut ainsi facilement récupérer les IDs
 */
function removeElementsBySelector(btn) {

    var token = $(btn).parent().parent().attr("data-token"),
        table = tables[token],
        rows  = table.rows('.selected').nodes(),
        ids   = [];

    $(rows).each(function(i, obj) {

        ids.push($(obj).attr("data-id"));
    });

    if(ids != ''){

        listing.removeElements(token, ids);
        table.rows('.selected').remove().draw( false );
    }
}

/**
 * permet de copier des éléments d'une liste dans une autre. On va simplement récupérer les IDs à copier, puis
 * les ajouter dans l'autre liste
 */
function copyElementsBySelector(btn) {

}

/**
 * permet de créer une nouvelle liste dynamique. La fonction peut être appelée de deux manière différentes :
 * - sans trigger elle ouvrira la modale
 * - avec trigger elle ajoutera la liste
 * @param trigger boolean
 */
function addListe(trigger) {

    if(trigger == false) showModal('#add-liste-modal');
    else {

        var name = $('#new-liste-name').val();
        if(name != '') listing.create(name);

        setTimeout(function(){ alert("Liste ajoutée, la page va s'actualiser"); location.reload();}, 1000);
    }
}