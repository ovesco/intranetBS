function deleteCreance(id){

    var data = { idCreance: id};
    var success;
    $.ajax({
        type: "POST",
        url: Routing.generate('interne_fiances_creance_delete_ajax'),
        data: data,
        async: false, //option utiliée pour retourner la valeur de success en dehors de la requete ajax
        error: function(jqXHR, textStatus, errorThrown) { success = false; },
        success: function(htmlResponse) { success = true; }
    });
    return success;
}


/*
 * Ajoute une créance à un Membre ou une Famille
 *
 *
 */
function addCreance(idForm){

    //on récupère les valeur du formulaire
    var form = $('#'+idForm).serialize();
    var success;
    $.ajax({
        type: "POST",
        url: Routing.generate('interne_fiances_creance_add_ajax'),
        data: form,
        async: false, //option utiliée pour retourner la valeur de success en dehors de la requete ajax
        error: function(jqXHR, textStatus, errorThrown) { success = false;  },
        success: function(htmlResponse) { success = true; }
    });
    return success;
}


/*
 * Selection/deséléction de toutes les créances d'une table
 */

function selectAllCreances(box)
{
    var $table = $(box).closest('.table');

    if(box.checked)
    {
        $table.find('input.selectCreance').each(function() {
            this.checked = true;
        });
    }
    else
    {
        $table.find('input.selectCreance').each(function() {
            this.checked = false;

        });
    }
}






