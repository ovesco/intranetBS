/*
 * Activation de l'accordeon
 */
$('.ui.accordion').accordion();

function openSearchForm(){
    $('#modalPayementSearchForm').modal('show');
}



/*
 * Fonction pour l'ajout "manuel" des factures dans la liste de validation des factures.
 * Envoie du formulaire avec numéro de référence et montant.
 */
function addManualy(){

    $idFacture = $('#form_num_ref').val();
    $montantRecuFacture = $('#form_montant_recu').val();

    if(($idFacture != '') && ($montantRecuFacture != ''))
    {
        var data = { idFacture: $idFacture, montantRecu: $montantRecuFacture};

        $.ajax({
            type: "POST",
            url: Routing.generate('interne_fiances_payement_add_manualy'),
            data: data,
            error: function(jqXHR, textStatus, errorThrown) { alerte.send('erreur','danger'); },
            success: function(htmlResponse) {

                //$('#validation_liste_widget').show();

                //petite partie pour remetre le curseur a la bonne place
                //afin de rentré une nouvelle facture plus rappidement.
                $('#form_num_ref').focus();
                $('#form_num_ref').val('');//clear form
                $('#form_montant_recu').val('');

                $('#payementTable > tbody:first').append(htmlResponse);


            }
        });



    }
    else
    {
        //petite partie pour remetre le curseur a la bonne place
        //afin de rentré une nouvelle facture plus rappidement.
        $('#form_num_ref').focus();
        $('#form_num_ref').val('');//clear form
        $('#form_montant_recu').val('');

        //TODO: faire une validation de ce formulaire
        alerte.send('erreur','danger');

    }

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
        error: function(jqXHR, textStatus, errorThrown) { alerte.send('erreur','danger'); },
        success: function(htmlResponse) {

            $('#payementTable > tbody:first').append(htmlResponse);


        }
    });
}

function sendPayementSearch()
{
    var data = $('#payementSearchForm').serialize();
    $.ajax({
        type: "POST",
        url: Routing.generate('interne_fiances_payement_search_ajax'),
        data: data,
        error: function(jqXHR, textStatus, errorThrown) { alerte.send('erreur','danger'); },
        success: function(htmlResponse) {

            $('#payementTable > tbody > tr').remove();
            $('#payementTable > tbody:first').append(htmlResponse);
            $('#modalPayementSearchForm').modal('hide');

        }
    });

}