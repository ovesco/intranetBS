/**
 * Created by NicolasUffer on 18.11.15.
 */

$(document).ready(function(){

    //Menu principal, s'ouvre lorsqu'on clique sur le bouton du menu
    $('#main-menu-button').click(function() {
        $('#main-menu').sidebar('toggle');
    });

    //init dropdown
    $('.ui.dropdown').dropdown();

    //Accordions
    $('.ui.accordion').accordion();


    /*
     * Enable popup with two class name: popupable
     *
     */
    $('.popupable').popup();
    $('.popupable.onclick').popup({on:'click'});

});