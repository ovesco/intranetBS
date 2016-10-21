/**
 * layout.js
 * ce fichier gère le fonctionnement global de l'application
 */

$(function () {
    init();
});

function init() {

    //datepicker
    $('.datepicker').click(function () {
        $(this).datepicker({dateFormat: 'dd.mm.yy'}).datepicker("show");
    });

    //datatable
    $('.datatable').dataTable();

    $('.tabular .item').tab();

    /**
     * scan la page à la recherche de pattern à respecter pour tous les inputs type="text"
     */
    $("input[data-formatter=true]").each(function () {

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
    $('#layout-search').search({
        apiSettings: {
            url: Routing.generate('interne_main_layout_search')+'?pattern={query}'
        },
        type: 'category'
    });


    $('#menu_search').search({source: $('#menu_search').data("entries")});
}