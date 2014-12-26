function validation(element){

    var state = $(element).data("state");
    var idPayement = $(element).data("idpayement");

    var data;

    if((state == 'found_lower_valid') || (state == 'found_lower_new_creances') || (state == 'found_upper'))
    {
        var creancesIdArray = [];
        var creancesMontantArray = [];
        var rappelsIdArray = [];
        var rappelsMontantArray = [];

        //récupère le modal qui contient la répartition
        $modalOfRepartition = $('#modal-repartition-'+idPayement);

        $modalOfRepartition.find('#repartitionMontantsTable').find('input').each(function(){
            var id = $(this).data("id");
            var type = $(this).data("type");
            var montant = $(this).val();

            if(type == 'creance')
            {
                creancesIdArray.push(id);
                creancesMontantArray.push(montant);
            }
            if(type == 'rappel')
            {
                rappelsIdArray.push(id);
                rappelsMontantArray.push(montant);
            }



        });

        data = {    idPayement: idPayement,
                    state: state,
                    creancesIdArray: creancesIdArray,
                    creancesMontantArray: creancesMontantArray,
                    rappelsIdArray: rappelsIdArray,
                    rappelsMontantArray: rappelsMontantArray};
    }
    else
    {
        data = {    idPayement: idPayement,
                    state: state};

    }

    $.ajax({
        type: "POST",
        url: Routing.generate('interne_finances_validation_process'),
        data: data,
        error: function(jqXHR, textStatus, errorThrown) { alerte.send('erreur','danger'); },
        success: function(htmlResponse) {


            //Enlève la ligne
            $('#line-'+idPayement).remove();

            //ferme le modal si il existe
            $('#modal-repartition-'+idPayement).modal('hide');

        }
    });




}

function modifySolde(element)
{

    var montantrecu = $(element).closest('table').find('td.montantRecu').data('montantrecu');
    var solde = montantrecu;

    $(element).closest('table').find('input').each(function(){
        solde = solde - $(this).val();
    });
    //on récupère le modal pour pouvoir affichier/cacher les boutons de validation
    $modal = $(element).closest('.modal');

    var responseString = '';
    if(solde == 0){
        responseString = '<div class="ui label green">'+parseFloat(solde).toFixed(2)+'</div>';

        $modal.find('.button').each(function(){$(this).show();});

    }
    else if(solde > 0){
        responseString = '<div class="ui label orange">'+parseFloat(solde).toFixed(2)+'</div>';

        $modal.find('.button').each(function(){$(this).hide();});

    }
    else if(solde < 0){
        responseString = '<div class="ui label red">'+parseFloat(solde).toFixed(2)+'</div>';

        $modal.find('.button').each(function(){$(this).hide();});
    }

    $(element).closest('table').find('td.montantRecu').html(responseString);

}

function openModalRepartition(id){
    $('#'+id).modal('show');
    $('#'+id).find('.button').each(function(){$(this).hide();});
}


