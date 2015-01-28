function deleteFacture(id){

    var data = { idFacture: id};
    var success;
    $.ajax({
        type: "POST",
        url: Routing.generate('interne_fiances_facture_delete_ajax'),
        data: data,
        async: false, //option utiliée pour retourner la valeur de success en dehors de la requete ajax
        error: function(jqXHR, textStatus, errorThrown) { success = false; },
        success: function(htmlResponse) { success = true; }
    });
    return success;
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

    $.ajax({
        type: "POST",
        url: Routing.generate('interne_fiances_facture_envoi_ajax'),
        data: data,
        async: false, //option utiliée pour retourner la valeur de success en dehors de la requete ajax
        error: function(jqXHR, textStatus, errorThrown) { success = false; },
        success: function(response) { success = (response == 'success');}
    });
    return success;
}

/*
 * imprimer la facture en pdf
 */
function printFacture(id){

    //todo faire que ca télécharge ou mettre dans un nouvelle onglet

    var data = { idFacture: id};
    var success;
    $.ajax({
        type: "POST",
        url: Routing.generate('interne_fiances_print_factures'),
        data: data,
        async: false, //option utiliée pour retourner la valeur de success en dehors de la requete ajax
        error: function(jqXHR, textStatus, errorThrown) { success = false; },
        //success: function(pdf) {
/*
            var win=window.open('about:blank');
            with(win.document)
            {
                open();
                write(pdf);
                close();
            }

            success = true; }
  */  });
    return success;
}

