/**
 * Ce script est excusivement destiné à une utilisation dans la page
 * de saisie des payements.
 */
$(document).ready(function() {

    /**
     * Liée au bouton d'ajout de payement manuel
     * Cette fonction s'occupe d'ajouter un formulaire à la collection
     * pour la saisie manuelle de payement
     */
    $('#AddPayement').click(function(){

        var collectionHolder = $('#payements_add_form');
        var firstChildren = collectionHolder.children().first();
        var prototype = collectionHolder.data('prototype');
        var newForm = prototype.replace(/__name__/g, collectionHolder.children().length);
        firstChildren.before(newForm);
        //set cursor in new position
        collectionHolder.children().first().find('.idFacture').focus();

    });

    /**
     * Cette fonction perment de supprimer des formulaires de la collection.
     */
    $('#deletePayement').click(function(){
        var collectionHolder = $('#payements_add_form');
        if(collectionHolder.children().length > 1)
        {
            collectionHolder.children().first().remove();
        }

    });




});

