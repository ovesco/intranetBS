function deleteFacture(id,reload){

    //default value
    reload = typeof reload !== 'undefined' ? reload : true;

    var data = { idFacture: id};
    return $.ajax({
        type: "POST",
        url: Routing.generate('interne_fiances_facture_delete_ajax'),
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

function deleteListeFacture(idArray)
{


    idArray.forEach(function(id){ deleteFacture(id,false); });
    reloadPage();
}


/*
 * envoie en Ajax de la liste des créances à facturer
 */
function createFactureWithListeCreances(listeCreance){

    var data = {listeCreance:listeCreance};

    $.ajax({
        type: "POST",
        url: Routing.generate('interne_fiances_facture_create_ajax'),
        data: data,
        error: function(jqXHR, textStatus, errorThrown) {
            alerte.send('Erreur lors de la création de la facture','error');
        },
        success: function(response) {
            reloadPage();
            alerte.send('Facture crée','info',2000);
        }
    });

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

    var url = Routing.generate('interne_fiances_facture_print') + '/' + id;

    /*
     * Ouvre le pdf dans une nouvelle fenetre
     */
    window.open(url,'Facture PDF');
}

/**
 *
 * @param idFacture
 */
function openFactureShow(idFacture)
{
    var data = { idFacture: idFacture};
    var url = Routing.generate('interne_fiances_facture_show_ajax');

    getModal(data,url);
}