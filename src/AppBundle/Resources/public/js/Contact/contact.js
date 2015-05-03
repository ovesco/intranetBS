/**
 * Supprimme un email
 */
$('.button-remove-email').click(function() {

    removeContactElement('email', $(this).attr("data-id"));
});

/**
 * Supprimme un téléphone
 */
$('.button-remove-telephone').click(function() {

    removeContactElement('telephone', $(this).attr("data-id"));
});

function removeContactElement(type, id) {

    if(type != "telephone" && type != "email") alert("telephone ou email");
    noty({
        text: type + ' : Etes-vous sur de vouloir le supprimer ? cette action est définitive !',
        type: 'confirm',
        buttons: [
            {
                addClass: 'ui green button', text: 'Oui mon soss', onClick: function ($noty) {
                $noty.close();

                $.ajax({
                    url: Routing.generate('interne_contact_remove_' + type, {object: id}),
                    type: 'GET',
                    success: function() {
                        noty({text: '' + type + ' supprimé', type: 'information'});

                        $('.button-remove-' + type + '[data-id="' + id + '"]').parent().parent().remove();
                    }
                });
            }
            },
            {
                addClass: 'ui button', text: 'Annuler', onClick: function ($noty) {
                $noty.close();
            }
            }
        ]
    });
}

/**
 * Ajoute un telephone a un contact
 * Le bouton doit avoir data-contact-id défini
 */
$('.button-add-telephone').click(function() {

    var contactId = $(this).attr("data-contact-id");

    // On ouvre la modale
    $('#app_bundle_addtelephonetype_contact_id').val(contactId);
    showModal('#modal-contact-add-telephone');
});

/**
 * Ajoute un email a un contact
 * Le bouton doit avoir data-contact-id défini
 */
$('.button-add-email').click(function() {

    var contactId = $(this).attr("data-contact-id");

    // On ouvre la modale
    $('#app_bundle_addemailtype_contact_id').val(contactId);
    showModal('#modal-contact-add-email');
});