
jQuery(document).ready(function() {

    /**
     * Gestion des évenement envoyé par les liste de créance et de facture.
     */
    document.addEventListener('data-liste-event', function (e) {

        var listeId = [];

        switch(e.detail.name){
            case 'event_voir_facture':
                openFactureShow(e.detail.data,'interface');
                break;
            case 'event_delete_facture':
                deleteFactureFromInterface(e.detail.data);
                break;
            case 'event_voir_creance':
                openCreanceShow(e.detail.data,'interface');
                break;
            case 'event_delete_creance':
                deleteCreanceFromInterface(e.detail.data);
                break;

            case 'event_send_facture':
                factureEnvoi(e.detail.data);
                break;

            case 'event_print_facture':
                printFacture(e.detail.data);
                break;

            case 'event_masse_facturation_creance':
                createFactureFromInterface(e.detail.data);
                break;

            case 'event_masse_ajout_rappel':
                setTemporaryStorage(e.detail.data);
                openRappelForm(null,'interfaceListe');
                break;




        }
    }, false);


    /**
     * Gestion des boutons de l'interface
     */
    $('#interface-add-creance').click(function(){
        var ownerId = $(this).data('id');
        var ownerClass = $(this).data('class');

        openCreanceForm(ownerId,ownerClass,'interface');

    });

});

function setTemporaryStorage(value){
    $('#temporary-storage').val(value);
}
function getTemporaryStorage(){
    return $('#temporary-storage').val().split(",");
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
            $interfaceContent = $(htmlResponse).find('#infoFinanceContent');

            //rempalce le nouveau contenu
            $('#infoFinanceContent').replaceWith($interfaceContent);

            /**
             * On réinitialise les listes.
             */
            initDataListe();

        }
    });
}



function deleteFactureFromInterface(id){

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


function deleteCreanceFromInterface(id){

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


function addCreanceFromInterface(idForm)
{
    if(addCreance(idForm))
    {
        modalDisplayClose();
        reloadContentInterface();
        alert('addcreance');
        alerte.send('Creance ajoutée','info',2000);
    }
    else
    {
        alerte.send('Erreur lors de la création de la créance','danger');
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

function createFactureFromInterface(listeCreance) {

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

function addRappelToListeFromInterface(idForm)
{
    var idArray = getTemporaryStorage();
    alert(idArray);
    if(addRappelToListeOfFacture(idArray,idForm))
    {
        alerte.send('Rappels Ajoutés','info',2000);
    }
    else
    {
        alerte.send('Erreur','danger');
    }

}