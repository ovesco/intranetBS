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



function printFacture(element){

    var id = $(element).data("id");

    //liste of ids
    var idsFacture = [];
    idsFacture.push(id);

    var data = { idsFacture: idsFacture};
    $.ajax({
        type: "POST",
        url: Routing.generate('interne_fiances_print_factures_ajax'),
        data: data,
        error: function(jqXHR, textStatus, errorThrown) { alerte.send('Erreur','danger'); },
        success: function(htmlResponse) {

            var win=window.open('about:blank');
            with(win.document)
            {
                open();
                write(htmlResponse);
                close();
            }

        }
    });
}