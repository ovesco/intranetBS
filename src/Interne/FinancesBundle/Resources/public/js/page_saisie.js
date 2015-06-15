$(document).ready(function() {


    /**
     * Cette fonction s'occupe d'ajouter un formulaire à la collection
     * pour la saisie manuel de payement
     */
    $(document).on('change','.payement_add_form',function(){

        var collectionHolder = $('#payements_add_form');
        var firstChildren = collectionHolder.children().first();

        var id = firstChildren.find('.idFacutre').val();
        var montant = firstChildren.find('.montantRecu').val();

        if((montant != '') && (id != ''))
        {

            var prototype = collectionHolder.data('prototype');
            var newForm = prototype.replace(/__name__/g, collectionHolder.children().length);
            firstChildren.before(newForm);

            //set cursor in new position
            collectionHolder.children().first().find('.idFacture').focus();
        }


    });

});