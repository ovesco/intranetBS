jQuery(document).ready(function() {


    //activation du menu
    $('#search-infos-context .menu .item').tab({
        context: $('#search-infos-context')
    });

    /**
     * Gestion des évenement envoyé par les liste de créance et de facture.
     */
    document.addEventListener('data-liste-event', function (e) {

        switch(e.detail.name){
            case 'event_voir_facture':
                openFactureShow(e.detail.data,'search');
                break;
            case 'event_delete_facture':
                //todo a faire
                break;
            case 'event_voir_creance':
                openCreanceShow(e.detail.data,'search');
                break;

            case 'event_masse_delete_facture':
                //todo
                alert(e.detail.data);
                break;

        }

    }, false);


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

            modalDisplayClose();

            loadResults();
        }
    });
}

function loadResults()
{

    $.ajax({
        type: "POST",
        data: 1,
        url: Routing.generate('interne_fiances_search_load_results_ajax'),
        error: function(jqXHR, textStatus, errorThrown) { alerte.send('erreur','danger',4000); },
        success: function(htmlResponse) {

            //rempalce le nouveau contenu
            $('#search-infos-context').replaceWith(htmlResponse);


            //activation du menu
            $('#search-infos-context .menu .item').tab({
                context: $('#search-infos-context')
            });
        }
    });

}

function searchSwitch(element){

    $(element).closest('.searchSwitch').find('.optionSearch').each(function(){
        if($(this).is(':visible'))
        {
            $(this).hide();
        }
        else
        {
            $(this).show();
        }
    });
}

function switchFactureForm(element){

    var value = $(element).find('option:selected').val();

    if(value == 'yes')
    {
        $('#searchFactureForm').hide();
    }
    else
    {
        $('#searchFactureForm').show();
    }

}


function deleteCreanceFromSearch(id){
    if(deleteCreance(id))
    {
        //suppresion réussie
        loadResults()
        alerte.send('Créance supprimée','info',2000);
    }
    else
    {
        //erreur
        alerte.send('Erreur lors de la suppresion','danger');
    }
}

function deleteFactureFromSearch(id){
    if(deleteFacture(id))
    {
        //suppresion réussie
        loadResults();
        alerte.send('Facture supprimée','info',2000);
    }
    else
    {
        //erreur
        alerte.send('Erreur lors de la suppresion','danger');
    }
}

function deleteListeFacture()
{
    $('#factureSearchTable').find('input.selectFacture').each(function () {
        if(this.checked)
        {
            var id = $(this).val();
            if(!deleteFacture(id))
            {
                //erreur
                alerte.send('Erreur','danger');
            }
        }
    })
    loadResults();
}

function addRappelFromSearch(idFacture,idForm)
{
    if(addRappel(idFacture,idForm))
    {
        loadResults();
        alerte.send('Rappel ajouté','info',2000);
    }
    else
    {
        //erreur
        alerte.send('Erreur','danger');
    }
}

function addRappelToListeFromSearch(idForm)
{
    $('#factureSearchTable').find('input.selectFacture').each(function () {
        if(this.checked)
        {
            var idFacture = $(this).val();
            if(!addRappel(idFacture,idForm))
            {
                //erreur
                alerte.send('Erreur','danger');
            }
        }

    });
    loadResults();
    alerte.send('Rappel ajouté','info',2000);
}

function selectAllFacture(box)
{
    $('#factureSearchTable').find('input.selectFacture').each(function () {
        if(box.checked)
        {
            this.checked = true;
        }
        else
        {
            this.checked = false;
        }

    });
}

function deleteListeCreance()
{
    alerte.send('Seulement, les cérances "en attente de facturation" sont supprimées','info',3000);
    $('#creanceSearchTable').find('input.selectCreance').each(function () {
        if(this.checked)
        {
            var id = $(this).val();
            if(!deleteCreance(id))
            {
                //erreur
                alerte.send('Erreur','danger');
            }
        }
    })
    loadResults();
}

function createFactureFromSearch() {

    alerte.send('Seulement, les cérances "en attente de facturation" sont facturée','info',3000);

    var listeCreance = [];

    //on récupère la liste des créances cochée
    $('#creanceSearchTable').find('input.selectCreance').each(function () {
        if(this.checked) {
            listeCreance.push($(this).val());
        }
    });

    if(createFactureWithListeCreances(listeCreance))
    {
        loadResults();
        alerte.send('Facture crée','info',2000);
    }
    else
    {
        alerte.send('Erreur','danger');
    }
}

function outOfSearch(id,type)
{
    var data = { id: id, type: type};
    $.ajax({
        type: "POST",
        url: Routing.generate('interne_fiances_search_out_of_search_ajax'),
        data: data,
        error: function(jqXHR, textStatus, errorThrown) { alerte.send('Erreur','danger'); },
        success: function(htmlResponse) {

            loadResults();

        }
    });
}

