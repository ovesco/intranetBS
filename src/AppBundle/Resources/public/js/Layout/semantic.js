/**
 * Cette fonction initialise tout les composant Semantic UI
 * nécaissaire au fonctionnement des pages.
 * On peut l'appeler à tout moment si la strucuture DOM
 * a changé (apèrs AJAX par exemple).
 * Elle est applée aussi lors du chargement de la page.
 */
$(function () {
    semantic_init();
});

/**
 * This function is used by the modal init
 */
function semantic_init()
{
    //Menu principal, s'ouvre lorsqu'on clique sur le bouton du menu
    $('#main-menu-button').click(function() {
        $('#main-menu').sidebar('toggle');
    });

    //init dropdown
    $('.ui.dropdown').dropdown();

    //Accordions
    $('.ui.accordion').accordion();

    /*
     * Enable popup with class name: popupable
     */
    $('.popupable').popup();
    $('.popupable.onclick').popup({on:'click'});
}