jQuery(document).ready(function() {


    //activation du menu
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

            modalDisplayClose();

            loadResults();
        }
    });
}

function loadResults()
{
    var data = 1;
    $.ajax({
        type: "POST",
        data: data,
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


function deleteCreance(id){

    var data = { idCreance: id};
    var success;
    $.ajax({
        type: "POST",
        url: Routing.generate('interne_fiances_creance_delete_ajax'),
        data: data,
        async: false, //option utiliée pour retourner la valeur de success en dehors de la requete ajax
        error: function(jqXHR, textStatus, errorThrown) { success = false; },
        success: function(htmlResponse) { success = true; }
    });
    return success;
}


/*
 * Ajoute une créance à un Membre ou une Famille
 *
 *
 */
function addCreance(idForm){

    //on récupère les valeur du formulaire
    var form = $('#'+idForm).serialize();
    var success;
    $.ajax({
        type: "POST",
        url: Routing.generate('interne_fiances_creance_add_ajax'),
        data: form,
        async: false, //option utiliée pour retourner la valeur de success en dehors de la requete ajax
        error: function(jqXHR, textStatus, errorThrown) { success = false;  },
        success: function(htmlResponse) { success = true; }
    });
    return success;
}


/*
 * Selection/deséléction de toutes les créances d'une table
 */

function selectAllCreances(box)
{
    var $table = $(box).closest('.table');

    if(box.checked)
    {
        $table.find('input.selectCreance').each(function() {
            this.checked = true;
        });
    }
    else
    {
        $table.find('input.selectCreance').each(function() {
            this.checked = false;

        });
    }
}







function deleteFacture(id){

    var data = { idFacture: id};
    var success;
    $.ajax({
        type: "POST",
        url: Routing.generate('interne_fiances_facture_delete_ajax'),
        data: data,
        async: false, //option utiliée pour retourner la valeur de success en dehors de la requete ajax
        error: function(jqXHR, textStatus, errorThrown) { success = false; },
        success: function(htmlResponse) { success = true; }
    });
    return success;
}

/*
 * Selection/deséléction de toutes les factures d'une table
 */

function selectAllFactures(box)
{
    var $table = $(box).closest('.table');

    if(box.checked)
    {
        $table.find('input.selectFacture').each(function() {
            this.checked = true;

        });
    }
    else
    {
        $table.find('input.selectFacture').each(function() {
            this.checked = false;

        });
    }
}


/*
 * envoie en Ajax de la liste des créances à facturer
 */
function createFactureWithListeCreances(listeCreance){

    var success;
    var data = {listeCreance:listeCreance};

    $.ajax({
        type: "POST",
        url: Routing.generate('interne_fiances_facture_create_ajax'),
        data: data,
        async: false, //option utiliée pour retourner la valeur de success en dehors de la requete ajax
        error: function(jqXHR, textStatus, errorThrown) { success = false; },
        success: function(htmlResponse) { success = true;}
    });

    return success;
}


/*
 * Ajoute la facture au service d'envoi
 */
function factureEnvoi(idFacture){

    var success;
    var data = {idFacture:idFacture};

    $.ajax({
        type: "POST",
        url: Routing.generate('interne_fiances_facture_envoi_ajax'),
        data: data,
        async: false, //option utiliée pour retourner la valeur de success en dehors de la requete ajax
        error: function(jqXHR, textStatus, errorThrown) { success = false; },
        success: function(htmlResponse) { success = true;}
    });

    return success;
}
function addRappel(idFacture,idForm){

    var form = $('#'+idForm).serialize()+'&idFacture='+idFacture;
    var data = form;
    var success;
    $.ajax({
        type: "POST",
        url: Routing.generate('interne_finance_rappel_add_ajax'),
        data: data,
        async: false, //option utiliée pour retourner la valeur de success en dehors de la requete ajax
        error: function(jqXHR, textStatus, errorThrown) { success = false; },
        success: function(htmlResponse) { success = true; }
    });
    return success;
}