function deleteFacture(element){
    var id = $(element).data("id");
    var data = { idFacture: id};
    $.ajax({
        type: "POST",
        url: Routing.generate('interne_fiances_facture_delete_ajax'),
        data: data,
        error: function(jqXHR, textStatus, errorThrown) { alerte.send('Erreur','danger'); },
        success: function(htmlResponse) {

            //cherche le nouveau contenu
            $listeCreanceContent = $(htmlResponse).filter('#listeFactureContent');

            //rempalce le nouveau contenu
            $('#listeFactureContent').replaceWith($listeCreanceContent);

            //cherche le nouveau contenu
            $listeCreanceContent = $(htmlResponse).filter('#listeCreanceContent');

            //rempalce le nouveau contenu
            $('#listeCreanceContent').replaceWith($listeCreanceContent);

            alerte.send('Facture supprimée','info',2000);


        }
    });
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