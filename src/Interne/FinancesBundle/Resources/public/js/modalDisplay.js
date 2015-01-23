function openCreanceShow(id)
{
    var data = { idCreance: id};
    $.ajax({
        type: "POST",
        url: Routing.generate('interne_fiances_creance_show_ajax'),
        data: data,
        error: function(jqXHR, textStatus, errorThrown) { alerte.send('Erreur','danger'); },
        success: function(htmlResponse) {

            modalDisplayNewContent(htmlResponse);
            modalDisplayOpen();

        }
    });

}

function openFactureShow(idFacture,fromPage)
{
    var data = { idFacture: idFacture, fromPage:fromPage};
    $.ajax({
        type: "POST",
        url: Routing.generate('interne_fiances_facture_show_ajax'),
        data: data,
        error: function(jqXHR, textStatus, errorThrown) { alerte.send('Erreur','danger'); },
        success: function(htmlResponse) {

            modalDisplayNewContent(htmlResponse);
            modalDisplayOpen();
        }
    });
}

function openRappelForm(idFacture,fromPage)
{
    var data = { idFacture: idFacture, fromPage:fromPage};
    $.ajax({
        type: "POST",
        url: Routing.generate('interne_finance_rappel_get_form_ajax'),
        data: data,
        error: function(jqXHR, textStatus, errorThrown) { alerte.send('Erreur','danger'); },
        success: function(htmlResponse) {

            modalDisplayNewContent(htmlResponse);
            modalDisplayOpen();

        }
    });
}

function openCreanceForm(ownerId,ownerType,fromPage)
{
    var data = { ownerId: ownerId, ownerType:ownerType, fromPage:fromPage};
    $.ajax({
        type: "POST",
        url: Routing.generate('interne_fiances_creance_get_form_ajax'),
        data: data,
        error: function(jqXHR, textStatus, errorThrown) { alerte.send('Erreur','danger'); },
        success: function(htmlResponse) {

            modalDisplayNewContent(htmlResponse);
            modalDisplayOpen();

        }
    });
}

function openSearchForm()
{
    var data = null;
    $.ajax({
        type: "POST",
        url: Routing.generate('interne_fiances_search_load_form_ajax'),
        data: data,
        error: function(jqXHR, textStatus, errorThrown) { alerte.send('Erreur','danger'); },
        success: function(htmlResponse) {

            modalDisplayNewContent(htmlResponse);
            modalDisplayOpen();

            $('.ui.accordion').accordion();

        }
    });
}

function modalDisplayClose()
{
    $('#modal-display').empty();
    $('#modal-display').modal('hide');
}

function modalDisplayOpen()
{
    $('#modal-display').modal('show');
}

function modalDisplayRefresh()
{
    $('#modal-display').modal('show');
}

function modalDisplayNewContent(htmlResponse)
{
    //rempalce par le nouveau contenu
    $('#modal-display').empty();
    $('#modal-display').append(htmlResponse);
}

