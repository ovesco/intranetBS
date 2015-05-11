// Datatables de membre
// On déclare un mega tableau qui contiendra les références de toutes les tables affichées sur la page
// pour pouvoir accéder à l'objet datatable
var datatables = [];

$.fn.dataTableExt.oStdClasses.sPageButton = "item";

$('.membre-datatable').each(function(i) {

    $(this).attr("data-table-id", i);

    datatables.push($(this).DataTable({

        "dom": '<"wrapper" <"ui two column grid" <"left aligned column"<"ui mini input"f>><"right aligned column"l> >t <"ui two column grid" <"left aligned column"i><"right aligned column"p > > >',
        "oLanguage": {
            "sSortAscending": " - clic pour trier de manière ascendante",
            "sSortDescending": " - clic pour trier de manière descendante",
            "sFirst": "premier",
            "sLast" : "dernier",
            "sNext": "Suivant",
            "sPrevious":"Précédent",
            "sEmptyTable": "Rien dans la table",
            "sInfo": "_TOTAL_ entrées totales (_START_ à _END_ affichées)",
            "sInfoEmpty": "Rien à afficher",
            "sInfoFiltered": " - filtré de _MAX_ entrées",
            "sInfoPostFix": "",
            "sLengthMenu": "Afficher _MENU_ entrées",
            "sLoadingRecords": "Patiente fils",
            "sProcessing": "Laisse moi bosser un peu",
            "sSearch": "Rechercher : ",
            "sZeroRecords": "Aucune entrée à afficher"
        }
    }));
});

$('.dataTables_paginate').addClass('ui small pagination menu');








/**
 * Le click sur la checkbox de la table selectionne/deselectionne toutes les
 * entrées de la table en question
 */
$('.table-checkbox').change(function() {

    toggleListe(datatables[$(this).closest(".membre-datatable").attr("data-table-id")], $(this).is(':checked'));
});

/**
 * Toggle un element d'une liste
 */
$('.membre-datatable').on( 'click', 'tr', function () {
    $(this).toggleClass('selected');
} );


/**
 * Toggle tous les éléments d'une liste
 */
function toggleListe($table, etat) {

    $table.rows().iterator( 'row', function ( context, index ) {

        if(etat)
            $( this.row( index ).node() ).addClass( 'selected' );
        else
            $( this.row( index ).node() ).removeClass( 'selected' );
    });
}