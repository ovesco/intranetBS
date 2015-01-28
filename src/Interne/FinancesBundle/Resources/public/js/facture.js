function deleteFacture(id){

    var data = { idFacture: id};
    return $.ajax({
        type: "POST",
        url: Routing.generate('interne_fiances_facture_delete_ajax'),
        data: data,
        error: function(jqXHR, textStatus, errorThrown) { return false; },
        success: function(response) { return (response == 'success');}
    });
}




/*
 * envoie en Ajax de la liste des créances à facturer
 */
function createFactureWithListeCreances(listeCreance){

    var success;
    var data = {listeCreance:listeCreance};

    $.ajax({
        type: "POST",
        url: Routing.generate('interne_fiances_facture_create_ajax'),
        data: data,
        async: false, //option utiliée pour retourner la valeur de success en dehors de la requete ajax
        error: function(jqXHR, textStatus, errorThrown) { success = false; },
        success: function(response) { success = (response == 'success'); }
    });

    return success;
}


/*
 * Ajoute la facture au service d'envoi
 */
function factureEnvoi(idFacture){

    var success;
    var data = {idFacture:idFacture};

    return $.ajax({
        type: "POST",
        url: Routing.generate('interne_fiances_facture_envoi_ajax'),
        data: data,
        //async: false, //option utiliée pour retourner la valeur de success en dehors de la requete ajax
        error: function(jqXHR, textStatus, errorThrown) { return false; },
        success: function(response) { return (response == 'success');}
    });

}

/*
 * imprimer la facture en pdf
 */
function printFacture(id){

    var url = Routing.generate('interne_fiances_print_factures') + '/' + id;

    /*
     * Ouvre le pdf dans une nouvelle fenetre
     */
    window.open(url,'Facture PDF');
}

