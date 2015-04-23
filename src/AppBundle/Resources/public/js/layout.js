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
$('.popupable.onclick').popup({
    on    : 'click'
});


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

// x-editable
$.fn.editable.defaults.mode = 'inline';

// x-editable templates
$.fn.editableform.template = '<form class="form-inline editableform"> <div class="control-group"> <div><div class="editable-input ui mini input"></div><div class="editable-buttons"></div></div> <div class="editable-error-block"></div> </div> </form>';
$.fn.editableform.buttons  = '<button type="submit" class="editable-submit ui tiny circular green icon button"><i class="checkmark icon"></i></button> <button type="button" class="editable-cancel ui tiny circular red icon button"><i class="remove icon"></i></button>';