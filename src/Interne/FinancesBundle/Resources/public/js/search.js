jQuery(document).ready(function() {
    //affiche le modal une fois que le chargement de la page est terminé
    $('#modal-facture-searchForm').modal('show');


    $('#search-infos-context .menu .item').tab({
        context: $('#search-infos-context')
    });

});

function sendSearch()
{
    var data = $('#searchForm').serialize();

    $.ajax({
        type: "POST",
        url: Routing.generate('interne_fiances_search'),
        data: data,
        error: function(jqXHR, textStatus, errorThrown) { alerte.send('erreur','danger',4000); },
        success: function(htmlResponse) {

            $('#modal-facture-searchForm').modal('hide');

            //cherche le nouveau contenu
            $factureSearchContent = $(htmlResponse).find('#factureSearchContent');

            //rempalce le nouveau contenu
            $('#factureSearchContent').replaceWith($factureSearchContent);

            //cherche le nouveau contenu
            $newCreanceContent = $(htmlResponse).find('#creanceSearchContent');

            //rempalce le nouveau contenu
            $('#creanceSearchContent').replaceWith($newCreanceContent);
        }
    });

}

function switchFactureForm(element){

    var value = $(element).find('option:selected').val();

    if(value == 'no')
    {
        $('#searchFactureForm').hide();
    }
    else
    {
        $('#searchFactureForm').show();
    }

}

