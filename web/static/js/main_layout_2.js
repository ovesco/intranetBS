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