jQuery(document).ready(function() {

    //activation du menu
    $('#finances-infos-context .menu .item').tab({
        context: $('#finances-infos-context')
    });

});

function deleteFactureFromInterface(element){
    var id = $(element).data("id");

    if(deleteFacture(id))
    {
        //suppresion réussie
        reloadContentInterface();
        alerte.send('Facture supprimée','info',2000);
    }
    else
    {
        //erreur
        alerte.send('Erreur lors de la suppresion','danger');
    }

}


function deleteCreanceFromInterface(element){
    var id = $(element).data("id");

    if(deleteCreance(id))
    {
        //suppresion réussie
        reloadContentInterface();
        alerte.send('Créance supprimée','info',2000);
    }
    else
    {
        //erreur
        alerte.send('Erreur lors de la suppresion','danger');
    }

}

function reloadContentInterface()
{
    var id = $('#owner-entity-id').val();
    var type = $('#owner-entity-type').val();
    var data = { ownerId: id, ownerType: type};
    $.ajax({
        type: "POST",
        url: Routing.generate('interne_fiances_interface_reload_ajax'),
        data: data,
        error: function(jqXHR, textStatus, errorThrown) { alerte.send('Erreur lors du chargement','danger');  },
        success: function(htmlResponse) {
            //cherche le nouveau contenu
            $listeCreanceContent = $(htmlResponse).find('#listeCreanceContent');

            //rempalce le nouveau contenu
            $('#listeCreanceContent').replaceWith($listeCreanceContent);

            //cherche le nouveau contenu
            $listeFactureContent = $(htmlResponse).find('#listeFactureContent');

            //rempalce le nouveau contenu
            $('#listeFactureContent').replaceWith($listeFactureContent);

            //activation du menu
            $('#finances-infos-context .menu .item').tab({
                context: $('#finances-infos-context')
            });

        }
    });
}


function addCreanceFromInterface(idForm)
{
    if(addCreance(idForm))
    {
        modalDisplayClose();
        reloadContentInterface();
        alerte.send('Creance ajoutée','info',2000);
    }
    else
    {
        alerte.send('Erreur','danger');
    }
}

function addRappelFromInterface(idFacture,idForm)
{
    if(addRappel(idFacture,idForm))
    {
        modalDisplayClose();
        reloadContentInterface();
        alerte.send('Rappel ajouté','info',2000);
    }
    else
    {
        alerte.send('Erreur','danger');
    }
}





function validateAddCreanceForm()
{

    var titre = 'InterneFinancesBundle_creanceAddType[titre]';

    var settings = {
        inline : true,
        on     : 'blur',
        onSuccess : addCreance(),
        titre : {
            identifier : titre,
            rules : [{
                type : 'empty',
                prompt : 'Please enter a name'
            }]
        }

    };

    $('#addCreanceForm .ui .form').form(settings);
}


function createFactureFromInterface() {

    var listeCreance = [];

    //on récupère la liste des créances cochée
    $('.selectCreance:checked').each(function () {
        listeCreance.push($(this).val());
    });

    if(createFactureWithListeCreances(listeCreance))
    {
        reloadContentInterface();
        alerte.send('Facture crée','info',2000);
    }
    else
    {
        alerte.send('Erreur','danger');
    }
}