$(document).ready(function () {

    /**
     * Gestion des évenement envoyé par les listes pour les factures.
     */
    document.addEventListener('data-liste-event', function (e) {


        switch(e.detail.name){
            case 'event_voir_creance':
                showCreance(e.detail.data,'interface');
                break;
            case 'event_delete_creance':
                deleteCreance(e.detail.data);
                break;
            case 'event_masse_delete_creance':
                deleteListeCreance(e.detail.data);
                break;

        }
    }, false);

});




function deleteCreance(id,reload){

    //default value
    reload = typeof reload !== 'undefined' ? reload : true;


    $.ajax({
        type: "POST",
        url: Routing.generate('interne_finances_creance_delete',{creance: id}),
        error: function(jqXHR, textStatus, errorThrown) {
            alerte.send('Erreur lors de la suppresion','error');
        },
        success: function(response) {
            if(response == 'success')
            {
                if(reload)
                {
                    reloadPage();
                }
            }
            else
            {
                alerte.send('Erreur lors de la suppresion','error');
            }
        }
    });
}

function deleteListeCreance(idArray)
{
    idArray.forEach(function(id){ deleteCreance(id,false); });
    reloadPage();
}

/**
 *
 * @param idForm
 */
function addCreance(idForm){

    //on récupère les valeur du formulaire
    var form = $('#'+idForm).serialize();
    $.ajax({
        type: "POST",
        url: Routing.generate('interne_finances_creance_add_ajax'),
        data: form,
        error: function(jqXHR, textStatus, errorThrown) {
            reloadPage();
        },
        success: function(response) {
            alerte.send('Erreur lors de la création de la créance','error');

        }
    });
}

/**
 *
 * @param id
 */
function showCreance(id)
{
    var url = Routing.generate('interne_finances_creance_show',{creance: id});
    getModal(null,url);
}

/**
 *
 * @param ownerId
 * @param ownerType
 */
function openCreanceForm(ownerId,ownerType)
{
    var data = { ownerId: ownerId, ownerType:ownerType};
    var url = Routing.generate('interne_finances_creance_get_form_ajax');

    getModal(data,url);

}




