/**
 * layout.js
 * ce fichier gère le fonctionnement global de l'application
 */

//Menu principal, s'ouvre lorsqu'on clique sur le bouton du menu
$('#main-menu-button').click(function() {

    $('#main-menu')
        .sidebar('toggle')
    ;
});

//Dropdowns
$('.ui.dropdown')
    .dropdown()
;

//Accordions
$('.ui.accordion')
    .accordion()
;

//Checkbox
$('.ui.checkbox')
    .checkbox()
;

//datepicker
$('.datepicker').datepicker({
    format: 'dd.mm.yyyy'
});

//popup
$('.popupable').popup();

//modal
function showModal(id) {

    $(id).modal('show');
}

//datatable
$('.datatable').dataTable();

//select2
$('.select2').select2();

/**
 * scan la page à la recherche de pattern à respecter pour tous les inputs type="text"
 */

$("input[data-formatter=true]").each(function() {

    var pattern = $(this).attr("data-pattern");

    $(this).formatter({
        persistent: true,
        pattern: pattern
    });
});

/**
 * On met en place le petit script tout simple de recherche
 *
 * La recherche se fait en méthode GET donc on passe la variable dans l'URL.
 *
 */
$('#layout-search')
    .search({
        apiSettings: {
            url: Routing.generate('interne_main_layout_search')+'?pattern={query}'
        },
        type: 'category'
    })
;
