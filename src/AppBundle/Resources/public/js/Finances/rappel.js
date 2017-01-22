//todo NUR ce fichier est probablement obselet
function addRappel(idFacture,idForm,reload,loader){

    //default value
    reload = typeof reload !== 'undefined' ? reload : true;
    loader = typeof loader !== 'undefined' ? loader : null;

    var data = $('#'+idForm).serialize()+'&idFacture='+idFacture;
    $.ajax({
        type: "POST",
        url: Routing.generate('interne_finance_rappel_add_ajax'),
        data: data,
        error: function(jqXHR, textStatus, errorThrown) {
            alerte.send('Erreur lors de l\'ajout d\'un rappel','error');
        },
        success: function(response) {
            if(response == 'success')
            {
                if(reload){
                    reloadPage();
                }
                if(loader != null){
                    loader.increment();
                }
            }
            else
            {
                alerte.send('Erreur lors de l\'ajout d\'un rappel','error');
            }

        }
    });
}

/**
 *
 * @param idForm
 * @returns {boolean}
 */
function addRappelToListeOfFacture(idForm)
{
    var idArray = getTemporaryStorage();

    var loader = new Loader(idArray.length,'Ajout de rappel en masse');


    idArray.forEach(function(id){
        addRappel(id,idForm,false,loader);

    });

    reloadPage();
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