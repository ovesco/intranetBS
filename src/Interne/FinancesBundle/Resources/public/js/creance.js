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








