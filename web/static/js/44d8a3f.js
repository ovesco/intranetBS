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
jQuery(document).ready(function() {

    //activation du menu
    $('#finances-infos-context .menu .item').tab({
        context: $('#finances-infos-context')
    });

});

function deleteFactureFromInterface(element){
    var id = $(element).data("id");

    if(deleteFacture(id))
    {
        //suppresion réussie
        reloadContentInterface();
        alerte.send('Facture supprimée','info',2000);
    }
    else
    {
        //erreur
        alerte.send('Erreur lors de la suppresion','danger');
    }

}


function deleteCreanceFromInterface(element){
    var id = $(element).data("id");

    if(deleteCreance(id))
    {
        //suppresion réussie
        reloadContentInterface();
        alerte.send('Créance supprimée','info',2000);
    }
    else
    {
        //erreur
        alerte.send('Erreur lors de la suppresion','danger');
    }

}

function reloadContentInterface()
{
    var id = $('#owner-entity-id').val();
    var type = $('#owner-entity-type').val();
    var data = { ownerId: id, ownerType: type};
    $.ajax({
        type: "POST",
        url: Routing.generate('interne_fiances_interface_reload_ajax'),
        data: data,
        error: function(jqXHR, textStatus, errorThrown) { alerte.send('Erreur lors du chargement','danger');  },
        success: function(htmlResponse) {
            //cherche le nouveau contenu
            $listeCreanceContent = $(htmlResponse).find('#listeCreanceContent');

            //rempalce le nouveau contenu
            $('#listeCreanceContent').replaceWith($listeCreanceContent);

            //cherche le nouveau contenu
            $listeFactureContent = $(htmlResponse).find('#listeFactureContent');

            //rempalce le nouveau contenu
            $('#listeFactureContent').replaceWith($listeFactureContent);

            //cherche le nouveau contenu
            $nombre = $(htmlResponse).find('#nombreFacture');

            //rempalce le nouveau contenu
            $('#nombreFacture').replaceWith($nombre);

            //cherche le nouveau contenu
            $nombre = $(htmlResponse).find('#nombreCreance');

            //rempalce le nouveau contenu
            $('#nombreCreance').replaceWith($nombre);

            //activation du menu
            $('#finances-infos-context .menu .item').tab({
                context: $('#finances-infos-context')
            });

        }
    });
}


function addCreanceFromInterface(idForm)
{
    if(addCreance(idForm))
    {
        modalDisplayClose();
        reloadContentInterface();
        alerte.send('Creance ajoutée','info',2000);
    }
    else
    {
        alerte.send('Erreur','danger');
    }
}

function addRappelFromInterface(idFacture,idForm)
{
    if(addRappel(idFacture,idForm))
    {
        modalDisplayClose();
        reloadContentInterface();
        alerte.send('Rappel ajouté','info',2000);
    }
    else
    {
        alerte.send('Erreur','danger');
    }
}





function validateAddCreanceForm()
{

    var titre = 'InterneFinancesBundle_creanceAddType[titre]';

    var settings = {
        inline : true,
        on     : 'blur',
        onSuccess : addCreance(),
        titre : {
            identifier : titre,
            rules : [{
                type : 'empty',
                prompt : 'Please enter a name'
            }]
        }

    };

    $('#addCreanceForm .ui .form').form(settings);
}


function createFactureFromInterface() {

    var listeCreance = [];

    //on récupère la liste des créances cochée
    $('.selectCreance:checked').each(function () {
        listeCreance.push($(this).val());
    });

    if(createFactureWithListeCreances(listeCreance))
    {
        reloadContentInterface();
        alerte.send('Facture crée','info',2000);
    }
    else
    {
        alerte.send('Erreur','danger');
    }
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