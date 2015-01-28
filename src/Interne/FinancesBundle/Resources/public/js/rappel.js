function addRappel(idFacture,idForm){

    var form = $('#'+idForm).serialize()+'&idFacture='+idFacture;
    var data = form;
    var success;
    $.ajax({
        type: "POST",
        url: Routing.generate('interne_finance_rappel_add_ajax'),
        data: data,
        async: false, //option utiliée pour retourner la valeur de success en dehors de la requete ajax
        error: function(jqXHR, textStatus, errorThrown) { success = false; },
        success: function(response) { success = (response == 'success');}
    });
    return success;
}

/**
 *
 * @param idArray
 * @param idForm
 * @returns {boolean}
 */
function addRappelToListeOfFacture(idArray,idForm)
{

    var success = true;

    for(i = 0; i < idArray.length; i++)
    {
        success = success && addRappel(idArray[i],idForm);
    }

    return success;

}