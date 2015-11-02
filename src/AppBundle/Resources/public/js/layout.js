/**
 * layout.js
 * ce fichier gère le fonctionnement global de l'application
 */

//Noty defaults
$.noty.defaults = {
    layout: 'topRight',
    theme: 'defaultTheme', // or 'relax'
    type: 'alert',
    text: '', // can be html or string
    dismissQueue: true, // If you want to use queue feature set this true
    template: '<div class="noty_message"><span class="noty_text"></span><div class="noty_close"></div></div>',
    animation: {
        open: {height: 'toggle'}, // or Animate.css class names like: 'animated bounceInLeft'
        close: {height: 'toggle'}, // or Animate.css class names like: 'animated bounceOutLeft'
        easing: 'swing',
        speed: 500 // opening & closing animation speed
    },
    timeout: false, // delay for closing event. Set false for sticky notifications
    force: false, // adds notification to the beginning of queue when set to true
    modal: false,
    maxVisible: 5, // you can set max visible notification for dismissQueue true option,
    killer: false, // for close all notifications before show
    closeWith: ['click'], // ['click', 'button', 'hover', 'backdrop'] // backdrop click will close all notifications
    callback: {
        onShow: function() {},
        afterShow: function() {},
        onClose: function() {},
        afterClose: function() {},
        onCloseClick: function() {},
    },
    buttons: false // an array of buttons
};

//Menu principal, s'ouvre lorsqu'on clique sur le bouton du menu
$('#main-menu-button').click(function() {

    $('#main-menu')
        .sidebar('toggle')
    ;
});

//Listing, affichage de la barre en dessous

//Dropdowns
$(document).on('click', '.ui.dropdown', function() {

    $(this).dropdown('show');

});


//Accordions
$('.ui.accordion')
    .accordion()
;



//datepicker
$(document).on('click', '.datepicker', function () {

    $(this).datepicker({dateFormat: 'dd.mm.yy'}).datepicker( "show" );
});



//popup
$('.popupable').popup();
$('.popupable.onclick').popup({
    on    : 'click'
});


//modal
function showModal(id) {
    $(id).modal('show');
}

//datatable
$('.datatable').dataTable();


$('.tabular .item').tab();

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

// x-editable
$.fn.editable.defaults.mode = 'inline';
$.fn.editable.defaults.onblur = 'submit';
$.fn.editable.defaults.emptytext = '—';

// x-editable templates
$.fn.editableform.template = '<form class="form-inline editableform"> <div class="control-group"> <div><div class="editable-input ui mini input"></div><div class="editable-buttons"></div></div> <div class="editable-error-block"></div> </div> </form>';
$.fn.editableform.buttons  = '<button type="submit" class="editable-submit ui tiny circular green icon button"><i class="checkmark icon"></i></button> <button type="button" class="editable-cancel ui tiny circular red icon button"><i class="remove icon"></i></button>';

$(document).ready(function() {
    $('.xeditable').editable({

        success: function(response, newValue) {
            if(!response.success) return response.msg;
        }
    });
});