function deleteCreance(id,reload){

    //default value
    reload = typeof reload !== 'undefined' ? reload : true;

    var data = { idCreance: id};
    $.ajax({
        type: "POST",
        url: Routing.generate('interne_fiances_creance_delete_ajax'),
        data: data,
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
        url: Routing.generate('interne_fiances_creance_add_ajax'),
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
function openCreanceShow(id)
{
    var data = { idCreance: id};
    var url = Routing.generate('interne_fiances_creance_show_ajax');

    getModal(data,url);
}

/**
 *
 * @param ownerId
 * @param ownerType
 */
function openCreanceForm(ownerId,ownerType)
{
    var data = { ownerId: ownerId, ownerType:ownerType};
    var url = Routing.generate('interne_fiances_creance_get_form_ajax');

    getModal(data,url);

}




