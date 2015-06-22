$(document).ready(function() {

    /**
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


    $(document).on('change','.payement_add_form',function(){

        var collectionHolder = $('#payements_add_form');
        var collection = collectionHolder.find('.payement_add_form');

        var addForm = true;

        collection.each(function(){

            var id = $(this).find('.idFacutre').val();
            var montant = $(this).find('.montantRecu').val();

            if(isNumeric(id) && isNumeric(montant)){

            }
            else{
                addForm = false;
            }

        });

        if(addForm)
        {

            var prototype = collectionHolder.data('prototype');
            var newForm = prototype.replace(/__name__/g, collectionHolder.children().length);
            firstChildren.before(newForm);

            //set cursor in new position
            collectionHolder.children().first().find('.idFacture').focus();
        }


    });

});

function isNumeric(n) {
    return !isNaN(parseFloat(n)) && isFinite(n);
}