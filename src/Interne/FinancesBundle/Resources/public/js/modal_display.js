function getModal(data,url,sizeClass)
{
    sizeClass = typeof sizeClass !== 'undefined' ? sizeClass : 'small';

    $.ajax({
        type: "POST",
        url: url,
        data: data,
        error: function() { alerte.send('Erreur','danger'); },
        success: function(htmlResponse) {

            modalDisplayNewContent(htmlResponse);
            modalDisplayClass(sizeClass)
            modalDisplayOpen();

        }
    });
}


/**
 *
 * @param id
 */
function openCreanceShow(id)
{
    var data = { idCreance: id};
    var url = Routing.generate('interne_fiances_creance_show_ajax');

    getModal(data,url);

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
/**
 *
 * @param idFacture
 * @param option
 */
function openRappelForm(idFacture,option)
{
    var data = { idFacture: idFacture , option:option};
    var url = Routing.generate('interne_finance_rappel_get_form_ajax');

    getModal(data,url);
}
/**
 *
 * @param ownerId
 * @param ownerType
 */
function openCreanceForm(ownerId,ownerType)
{
    var data = { ownerId: ownerId, ownerType:ownerType};
    var url = Routing.generate('interne_fiances_creance_get_form_ajax');

    getModal(data,url);


}

/**
 *
 */
function openSearchForm()
{
    var data = null;
    var url = Routing.generate('interne_fiances_search_load_form_ajax');

    getModal(data,url,'fullscreen');

}

function openPayementSearchForm()
{
    var data = null;
    var url = Routing.generate('interne_fiances_payement_get_search_form_ajax');

    getModal(data,url);
}


function openPayementRepartitionForm(idPayement)
{
    var data = {idPayement: idPayement};
    var url = Routing.generate('interne_fiances_payement_repartition_ajax');

    getModal(data,url);
}

function modalDisplayClose()
{
    $('#modal-display').empty().modal('hide');
}

function modalDisplayOpen()
{
    $('#modal-display').modal('show');
}

function modalDisplayClass(sizeClass)
{
    $('#modal-display').removeClass().addClass('ui modal '+sizeClass);
}

function modalDisplayNewContent(htmlResponse)
{
    //rempalce par le nouveau contenu
    $('#modal-display').empty().append(htmlResponse);
    initModalActions();
}


function initModalActions()
{
    var $modal = $('#modal-display');
    $modal.find('.addRappelFromModal').click(function(){


        var idFacture = $(this).data('id');
        var idForm = $(this).data('form');


        if(idFacture != ''){
            addRappelFromPage(idFacture,idForm)
        }
        else{
            //si l'idFacture vide alors ajout en masse.
            addRappelToListeFromPage(idForm)
        }
        modalDisplayClose();


    });
    $modal.find('.addCreanceFromModal').click(function(){

        var idForm = $(this).data('form');

        addCreanceFromPage(idForm);

        modalDisplayClose();


    });

}
