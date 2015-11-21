jQuery(document).ready(function() {

    initPage();

    /**
     * Gestion des évenement envoyé par les liste de créance et de facture.
     */
    document.addEventListener('data-liste-event', function (e) {


        switch(e.detail.name){
            case 'event_ignore_payement':
                validationPayement('ignore',e.detail.data);
                break;
            case 'event_validate_payement':
                validationPayement('validate',e.detail.data);
                break;
            case 'event_repartition_payement':
                openPayementRepartitionForm(e.detail.data);
                break;



        }
    }, false);

});

function initPage(){

    //activation du menu
    $('#payement-infos-context .menu .item').tab({
        context: $('#payement-infos-context')
    });

    /**
     * Gestion des bouttons de la page
     */
    $('.openModalPayementSearch').click(function(){
        openPayementSearchForm();
    });

    $('.saisieAddManualy').click(function(){
        addManualy();
    });

    $('.saisieUploadFile').click(function(){
        uploadFile();
    });

    $('.showFacture').click(function(){
        var id = $(this).data('id');
        id = id.replace(/ /g,"");
        id = id.replace(/\n|\r|(\n\r)/g,' ');
        openFactureShow(id);
    });


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

function sendPayementSearch()
{
    var data = $('#payementSearchForm').serialize();
    $.ajax({
        type: "POST",
        url: Routing.generate('interne_fiances_payement_search_ajax'),
        data: data,
        error: function() { alerte.send('Erreur lors de la recherche de payement','danger'); },
        success: function(htmlResponse) {

            replacePayementsListe(htmlResponse);
            modalDisplayClose();

        }
    });

}

function replacePayementsListe(newliste) {

    var $payementListe = $('#payementsSearchResults');
    $payementListe.children().remove();
    $payementListe.append(newliste);
    initDataListe();
}

function replaceWaitingListe(newliste) {

    var $liste = $('#payementsWaitingListe');
    $liste.children().remove();
    $liste.append(newliste);
    initDataListe();
}

function removeLineInWaitingListe(idPayement)
{
    var $liste = $('#payementsWaitingListe');
    $liste.find('tbody').find('tr').each(function(){
        if($(this).data('id') == idPayement)
        {
            $(this).remove();
        }
    });
}


/*
 * Fonction pour l'ajout "manuel" des factures dans la liste de validation des factures.
 * Envoie du formulaire avec numéro de référence et montant.
 */
function addManualy(){

    var $num_ref = $('#form_num_ref');
    var $montant = $('#form_montant_recu');

    $idFacture = $num_ref.val();
    $montantRecuFacture = $montant.val();

    if(($idFacture != '') && ($montantRecuFacture != ''))
    {
        var data = { idFacture: $idFacture, montantRecu: $montantRecuFacture};

        $.ajax({
            type: "POST",
            url: Routing.generate('interne_fiances_payement_add_manualy'),
            data: data,
            error: function() { alerte.send('erreur','danger'); },
            success: function(response) {

                reloadWaitingListe();
            }
        });
    }
    else{
        alerte.send('Erreur dans le formulaire','danger');
    }

    //petite partie pour remetre le curseur a la bonne place
    //afin de rentré une nouvelle facture plus rappidement.
    $num_ref.focus();
    $num_ref.val('');//clear form
    $montant.val('');

}

function reloadWaitingListe()
{
    var data = null;
    $.ajax({
        type: "POST",
        url: Routing.generate('interne_fiances_payement_waiting_liste'),
        data: data,
        error: function() { alerte.send('Erreur lors du chargement de la liste de validation','danger'); },
        success: function(htmlResponse) {

            replaceWaitingListe(htmlResponse);

        }
    });

}

function uploadFile(){

    var file = $('#form_file')[0].files[0];
    var name = file.name;
    var size = file.size;
    var type = file.type;
    //Your validation
    //TODO: validation

    var formData = new FormData();
    formData.append('file',file);

    $.ajax({
        type: "POST",
        url: Routing.generate('interne_fiances_payement_upload_file'),
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        error: function() { alerte.send('erreur','danger'); },
        success: function() {

            reloadWaitingListe();

        }
    });
}


function validationPayement(action,idPayement){



    var data = null;
    var $form = $('#repartitionMontantRecuForm');

    switch(action){
        case 'ignore':
            data = {idPayement:idPayement, action:action};
            break;
        case 'validate':
            data = {idPayement:idPayement, action:action};
            break;
        case 'repartition':
            data = {idPayement:idPayement, action:action, form: $form.serialize()};
            break;
        case 'repartition_and_new_facture':
            data = {idPayement:idPayement, action:action, form: $form.serialize()};
            break;
    }

    $.ajax({
        type: "POST",
        url: Routing.generate('interne_finances_payement_validation'),
        data: data,
        error: function(jqXHR, textStatus, errorThrown) {  },
        success: function(response) {
            removeLineInWaitingListe(idPayement);
        }
    });


}